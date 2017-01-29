<div class="tabs">
    <div class="tab-head">
        <a href="#repos">Known Repositories</a>
        <a href="#users">Known Github Logins</a>
    </div>
    <div class="tab-body">
        <div id="repos"> <?php     
            if ($hasAdmin && $canAdd) {
                echo Html::ab("/github-repo/edit", "Add new repository", true);
            }

            if (!empty($repos)) {
                $table_header = array("Github Login", "Repository", "Taskgroup", "URL",  "Token", "Actions");
                $table_data = array();
                
                // Build table data
                foreach ($repos as $repo) {

                    // Get list of tasks for repo
                    $tasks = $w->Task->getTasksByGroupId($repo->taskgroup_id);

                    $table_line = array();
                    $table_line[] = $repo->owner;
                    $table_line[] = $repo->repo;
                    $table_line[] = Html::a("/task-group/viewmembergroup/".$repo->taskgroup_id."#members",
                                            $w->Task->getTaskGroup($repo->taskgroup_id)->title);
                    $table_line[] = Html::a($repo->url,$repo->url);
                    $table_line[] = (!empty($repo->access_token)) ? "Yes" : "No";

                    $default_actions = Html::ab("/task/tasklist/?task_group_id=".$repo->taskgroup_id."#tasklist",
                                            "Tasks(".count($tasks).")");

                    if ($hasAdmin) {                    
                        $table_line[] = $default_actions." ".
                            //"<a class='button tiny editbutton' href='/github-repo/edit/".$user->id."' >Edit</a> ".
                            Html::ab("/github-repo/edit/".$repo->id, "Edit", true)." ".    
                            "<a class='button tiny deletebutton' href='/github-repo/delete/".$repo->id."' onclick='return confirm(\"Are you sure to delete this user?\");' >Delete</a>";
                    } else {
                        $table_line[] = $default_actions;
                    }
                    $table_data[] = $table_line;
                }
                
                echo Html::table($table_data, null, "tablesorter", $table_header);
                
            } else { ?>
                <h3><small>No GitHub repositories found.</small></h3>
            <?php } ?>
        </div>
        <div id="users" class="clearfix"> 
            <?php echo $w->partial("listusers", array(), "github"); ?>            
        </div>
    </div>
</div>