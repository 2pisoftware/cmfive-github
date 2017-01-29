<?php

function test_GET(Web $w) {
	
    $owner = 'gedtn';
    $w->ctx("owner",$owner);
    $repo  = 'solar-logger';
    $w->ctx("repo",$repo);
    $local_user_id = 2;
    $w->ctx("local_user_id",$local_user_id);
    
    $testing_github = false;
    
    if ($testing_github) {
        // Get Repos from GitHub
        $git_repos = $w->Github->getGithubRepos($owner);
        $w->ctx("git_repos", $git_repos);

        // Get Repo from GitHub
        $git_repo = $w->Github->getGithubRepo($owner, $repo);
        $w->ctx("git_repo", $git_repo);

        // Get Repo collaborators from GitHub
        $git_repo_colls = $w->Github->getGithubCollaborators($tg_repo);
        $w->ctx("git_repo_colls", $git_repo_colls);

        // Get Repos from GitHub
        $git_issues = $w->Github->getGithubIssues($owner, $repo);
        $w->ctx("git_issues", $git_issues);
    }
    
    // Get Repo by task group
    $tg = $w->Task->getTaskGroup(2);
    $w->ctx("tg", $tg);
    $tg_repo = $w->Github->getRepoByTaskGroup($tg->id);
    $w->ctx("tg_repo", $tg_repo);
    
    // Get Repo by owner and repo name
    $or_repo = $w->Github->getRepoByOwnerRepo($owner, $repo);
    $w->ctx("or_repo", $or_repo);
    
    // Get Users
    $users = $w->Github->getUsers();
    $w->ctx("users", $users);

    // Get User
    $user = $w->Github->getUser($local_user_id);
    $w->ctx("user", $user);

    // Get User by Github login
    $user_1 = $w->Github->getUserByGithubLogin($owner);
    $w->ctx("user_1", $user_1);

    // Get User by local id
    $user_2 = $w->Github->getUserByLocalId($local_user_id);
    $w->ctx("user_2", $user_2);

    // Get Github Issue for a given task
    $task_1 = $w->Task->getTask(24);
    $task_1_issue_no = $w->Github->getIssueNo($task_1);
    $w->ctx("task_1", $task_1);
    $w->ctx("task_1_issue_no", $task_1_issue_no);


    //echo $w->Github->getUserByLocalId(2)->login;
    //$task = $w->Task->getTask(24);
    //$repo = $w->Github->getRepoByTaskGroupId($task->task_group_id);
    //$issue_no = $w->Github->getIssueNo($task);
    //$w->Github->addComment($repo, $issue_no , "Piggy");
    
    //$user = $w->_paths[0];
    //$repo = $w->_paths[1];
    //echo "Repo=".$user."/".$repo."</br>";
    
    //$repo = $w->Github->getGithubRepo($user, $repo);
    //echo "Repo:";
    //var_dump($repo);
    //$w->Github->openIssue("Another new issue", "Testing the creation of issue after task creation in cmfive");    
    // $w->Github->addComment("What the...");    

    //$task = $w->Task->getObject("Task",24,false);
 
    //var_dump($w->Github->getGithubUser($task->w->Auth->getUser(2)->login));
    //var_dump($w->Github->getGithubUser("ss"));

    //echo "</br>Issue No=".$w->Github->getTaskDataArray($task)["issue_no"];

    //echo "Getting task more directly ".$task->title;
    
    //$repo = $w->Github->getRepo(2);
    //echo "</br>Get Repos: gedtn</br>";
    //$resp = $w->Github->getGithubRepos("gedtn");
    ////var_dump($resp);
    //foreach ($resp as $repo) {
    //    echo "</br>".$repo['name'];
    //}

    //echo "</br>Refreshing: ";
    //print_r($w->Github->refreshGithubUsers());
    

    //echo "</br>TaskGroups??: ";
    //var_dump($w->Github->getTaskGroups());
    //$repo = $w->Github->getRepoByTaskGroupId(2);
    //echo "Repo:".$repo->owner."/".$repo->repo;

    //$issues = array();
    //$w->ctx("issues", $issues);        
}
