<?php
/* 
 * 
 * 
 */

class GithubService extends DbService {

    protected $_client = null; // github api client
    protected $_git_webhook = false; // Are we currently handling webhook?
    
    function getClient() {
        if ($this->_client == null) {
            $this->_client = new GithubAPI($this->w);            
        }
        return $this->_client;
    }

    function inWebHook() {
        return $this->_git_webhook;
    }
    
    /**
     * Get the known GitHub repos
     * 
     * @param 
     * @return array DbObject github_repo
     */
    function getRepos () {
        return $this->getObjects("GithubRepo");
    }
    
    /**
     * Get a given GitHub repos
     * 
     * @param 
     * @return DbObject github_repo
     */
    function getRepo ($id) {
        return $this->getObject("GithubRepo",$id);
    }
    
    /**
     * Get a given GitHub repos by task group type
     * 
     * @param 
     * @return DbObject github_repo
     */
    function getRepoByTaskGroup ($tg_id) {
        return $this->getObject("GithubRepo",array('taskgroup_id' => $tg_id));
    }
    
    /**
     * Get a given GitHub repo by Github owner and repo name
     * 
     * @param 
     * @return DbObject github_repo
     */
    function getRepoByOwnerRepo ($owner, $repo) {
        return $this->getObject("GithubRepo",array('owner' => $owner, 'repo' => $repo));
    }
    
    /**
     * Get the known GitHub users
     * 
     * @param 
     * @return array DbObject github_user
     */
    function getUsers () {
        return $this->getObjects("GithubUser");
    }
    
    /**
     * Get a given GitHub repos
     * 
     * @param 
     * @return DbObject github_repo
     */
    function getUser ($id) {
        return $this->getObject("GithubUser",$id);
    }
    
    /**
     * Get a given GitHub repos
     * 
     * @param 
     * @return DbObject github_repo
     */
    function getUserByGithubLogin ($login) {
        return $this->getObject("GithubUser", array("login" => $login, "is_deleted" => 0));
    }
    
    /**
     * Get a GitHub user given a local user id
     * 
     * @param 
     * @return DbObject github_repo
     */
    function getUserByLocalId ($id) {
        return $this->getObject("GithubUser", array("local_user_id" => $id, "is_deleted" => 0));
    }
    
    /**
     * Refresh known Github users - for each known repo we get all the 
     * collaborators and add to list known it missing
     * 
     * @param none
     * @return 
     */
    function refreshGithubUsers () {

        $repos = $this->getRepos();
        
        foreach ($repos as $repo) {
            $owners = $this->getGithubCollaborators($repo);

            foreach ($owners as $owner) {
                $gu = $this->getUserByGithubLogin($owner['login']);
                // If this login is not found then insert it
                if (!$gu){
                    $gu = new GithubUser($this->w);
                    $gu->login = $owner['login'];
                    $gu->local_user_id = 0; // 0 = Not mapped yet
                    $gu->insertOrUpdate();
                }
            }
        }
        
        return $this->getUsers();
    }
    
    /**
     * Get the Github issue number for a given task
     * 
     * @param 
     * @return 
     */
    function getIssueNo ($task) {
        return ($task->w->Github->getTaskDataArray($task)["issue_no"]);
    }
    
    /**
     * Retrieve list of repos for given user
     * 
     * @param repo owner, repo name
     * @return array repos
     */
    function getGithubRepo($owner, $repo) {
        return $this->getClient()
                ->getRepo($owner, $repo);        
    }
    
    /**
     * Retrieve list of repos for given user
     * 
     * @param github repository $repo
     * @return array repos
     */
    function getGithubRepos($owner) {
        return $this->getClient()
                ->getRepos($owner);        
    }
    
    /**
     * Add comment
     * 
     * @param 
     * @return 
     */
    function addComment ($repo, $issue_no, $comment) {
        $this->getClient()
                ->authenticate($repo->access_token)
                ->addComment($repo->owner, $repo->repo, $issue_no, $comment);
    }
    
    /**
     * Add comment
     * 
     * @param 
     * @return 
     */
    function openIssueFromTask ($task) {
        
        // Get owner and repo
        $repo = $this->getRepoByTaskGroup($task->task_group_id);       
        
        $this->getClient()
                ->authenticate($repo->access_token)
                ->openIssue($repo->owner, $repo->repo, $task->title, $task->description);
        
        // Add a comment to the issue 
        $this->addComment("Issue created from ".Config::get('main.application_name')." task ".$task->id);
    }
    
    /**
     * Assign Issue
     * 
     * @param 
     * @return 
     */
    function assignIssueFromTask ($issue_no, $task) {
        
        $repo = $this->getRepoByTaskGroup($task->task_group_id);

        // Do we have a GitHub user for the task assignee?
        if ($task->assignee_id == 0) { // Unassigned
            $github_user = "";
        } else {
            $github_user = $this->getUserByLocalId($task->assignee_id);
            $github_login = empty($github_user) ? "" : $github_user->login;
        }
        
        $this->getClient()
                ->authenticate($repo->access_token)
                ->assignIssue($repo, $issue_no, $github_login);
    }
    
    /**
     * Retrieve issues for a given repo
     * 
     * @param github repository $repo
     * @return array issues
     */
    function getGithubIssues ($owner, $repo) {
        return $this->getClient()
                    ->getIssues($owner, $repo);
    }
    
    /**
     * Retrieve list of collaborators for a given repo
     * 
     * @param 
     * @return array GitHub users
     */
    function getGithubCollaborators($repo) {
        return $this->getClient()
                ->authenticate($repo->access_token)
                ->getCollaborators($repo->owner, $repo->repo);        
    }
    
    /**
     * Handle an issue event from github
     * 
     * @param issue payload from github webhook
     * @return Task
     */
    function handleIssueEvent($payload) {

        $this->_git_webhook = true;
        
        // Get the repo     
        $repo = $this->w->Github->getRepoByOwnerRepo($payload->repository->owner->login,
                                                     $payload->repository->name);
        
        if (!isset($repo)) {
            $this->Log->error("Received issue from unknown Github repo: ".
                               $payload->repository->owner->login.".".$payload->repository->name);            
        }

        // Get default assignee from taskgroup
        $tg = $this->Task->getTaskGroup($repo->taskgroup_id);

        if ($payload->action == "opened") {
        echo "Here ".$repo->task_type.".".$this->parseMarkdown($payload->issue->body);    
                $new_task = $this->w->Task->createTask($repo->task_type,
                                          $tg->id,
                                          $payload->issue->title,
                                          $this->parseMarkdown($payload->issue->body),
                                          $tg->default_priority,
                                          null,
                                          $tg->default_assignee_id);

            // Now add the github fields...
            $new_task->setDataValue('issue_no',$payload->issue->number);
            $new_task->setDataValue('issue_url',$payload->issue->html_url);
            $new_task->setDataValue('creator',$payload->sender->login);
            $new_task->setDataValue('labels',json_encode($payload->issue->labels));

            $this->_git_webhook = false;
            return $new_task;
        }
    }

    /**
     * Handle an issue_comment event from github
     * 
     * @param issue payload from github webhook
     * @return Task
     */
    function handleIssueCommentEvent($payload) {

        $this->_git_webhook = true;
        
        // Get the task for this new comment
        $task = $this->getTaskByRepoIssue($payload->repository->name,$payload->issue->number);
                
        // Get the taskgroup for the new task
        $tg = $this->w->Task->getTaskGroup(Config::get('github.taskgroup_id'));

        // Check if the github user is allowed to create tasks
        if (in_array($payload->sender->login,Config::get('github.issue_creators'))) {
            if ($payload->action == "created") {
                // See if we can get a valid user id
                $user_id = 0;
                $cmfive_login = Config::get('github.user_map')[$payload->sender->login];
                if ($cmfive_login) {
                    $user = $this->w->Auth->getUserForLogin($cmfive_login);
                    if ($user) {
                        $user_id = $user->id;
                    }
                }
                
                $comment = new Comment($this->w);
                $comment->obj_table = $task->getDbTableName();
                $comment->obj_id    = $task->id;
                $comment->is_system = 1;
                $comment->comment   = $this->parseMarkdown($payload->comment->body);
                $comment->insert();

                // Creator id not set if not logged in - should be removed??    
                $comment->creator_id = $user_id;
                $comment->update();
                
                $this->_git_webhook = false;
                return $comment;
            }
        } else {
            $this->_git_webhook = false;
            echo "Github user $payload->sender->login is not authorised to create tasks";
        }
    }
    
    /**
     * Find all taskgroups associated with GitHub
     * 
     * @param 
     * @return array TaskGroups
     */
    function getTaskGroups() {
                
        $tgts = implode(",",Config::get('github.task_group_types'));
        $query = "select * from task_group where task_group_type in (:tgts) ";
        
        // Execute SQL to get the task id for the repo/issue
        $stmt = $this->_db->prepare($query);
        $stmt->bindParam('tgts', $tgts);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        
        return $this->getObjectsFromRows('TaskGroup', $rows);
    }

    /**
     * Find a task given a github repo and issue number
     * 
     * @param github issue number
     * @return Task
     */
    function getTaskByRepoIssue($repo, $issue_no) {
                
        // Validate args...        
        if ((!is_numeric($issue_no)) || 
            (preg_match('/^[a-zA-Z]+[a-zA-Z0-9._]+$/', $repo))) {
            $w->Log->error("getTaskByRepoIssue: invalid issue number of repo name");        
            return null;
        }

        // We are looking for tasks that have rows in task_data with the given repo and issue number
        $query = "select task_id from task_data where data_key='repo' and value= '$repo' ".
                 " and task_id in ( ".  
                 " select task_id from task_data where data_key='issue_no' and value='$issue_no');";
        
        // Execute SQL to get the task id for the repo/issue
        $stmt = $this->_db->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        
        if (count($rows) > 1) {
            $w->Log->error("getTaskByRepoIssue: $repo issue $issue_no matched multiple tasks");        
            return null;
        } elseif (count($rows) == 0) {
            $w->Log->info("ERROR:getTaskByRepoIssue: $repo issue $issue_no matched 0 tasks");        
            return null;
        } else {
            $task_id = intval($rows[0]["task_id"]);
            $task = $this->w->Task->getTask($task_id);
            return $task;
        }
    }

    /**
     * List fields that have changed for a given Task
     * 
     * @param task to be updated
     * @return array field names
     */
    function listChanges($task) {
        
        // Get the existing task from the DB, use_cache=false
        $old_task = $task->w->Task->getObject("Task",$task->id,false);
        
        $changes = [];
        
        foreach ($task->getobjectvars() as $prop) {
            if ($task->$prop !== $old_task->$prop) {
                $changes[] = $prop;
            }
        }
        
        return $changes;
    }
    
    
    function parseMarkdown($markdown_text) {
        $parser = new \cebe\markdown\Markdown();
        return $parser->parse($markdown_text);
    }


    function getTaskDataArray($task) {
        
        if (!empty($task)) {
            $taskdata = $this->w->Task->getTaskData($task->id);
        } else {
            return null;
        }               

        if (!is_array($taskdata)) {
            return null;
        }

        $arr = [];
        foreach ($taskdata as $data) {
            $arr[$data->data_key] = $data->value;
        }
        var_dump($arr);
        return $arr;
    }

}