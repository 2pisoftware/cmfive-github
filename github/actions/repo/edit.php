<?php

function edit_GET(Web $w) {
	
    $p = $w->pathMatch("id");

    $repo = !empty($p['id']) ? $w->Github->getRepo($p['id']) : new GithubRepo($w);
    $w->ctx("repo",$repo);

    $taskgroups = $w->Github->getTaskGroups();
    
    // Set default taskgroup
    $taskgroup_id = empty($repo->taskgroup_id) ? (count($taskgroups) > 0 ? null : $taskgroups[0]->id) : $repo->taskgroup_id;
    
    // Can only set a default task type if we have a taskgroup
    if ($taskgroup_id) {
        $taskgroup = $w->Task->getTaskGroup($taskgroup_id);
        $tasktypes = ($taskgroup != "") ? $w->Task->getTaskTypes($taskgroup->task_group_type) : array();
        $tasktype = $repo->tasktype == '' ? (sizeof($tasktypes) === 1 ? $tasktypes[0][1] : null) : $repo->tasktype; 
    } else {
        $tasktypes = [];
        $tasktype = null;
    }

    // Set the default tasktype for the form
        
            //$validation["search"] = array('required');
    $validation = GithubRepo::$_validation;
    $form = [(!empty($p["id"]) ? 'Edit repository '.$repo->owner.'/'.$repo->repo : "Map a new repo") => [
                [
                    ["Task Group", "autocomplete", "taskgroup_id", $taskgroup_id, $taskgroups],
                    ["Task Type",  "select", "task_type", $tasktype, $tasktypes],
                ],
                [
                    ["Owner", "text", "owner", !empty($repo->owner) ? $repo->owner : null],
                    ["Repo", "text", "repo", !empty($repo->repo) ? $repo->repo : null],
                ],
                [
                    ["Github URL", "text", "-url", !empty($repo->url) ? $repo->url : null]
                ],
                [
                    ["Access Token", "text", "access_token", !empty($repo->access_token) ? $repo->access_token : null]
                ],
                [
                    ["Description", "textarea", "description", $repo->description]
                ]
            ]
    ];

    $w->ctx("form", Html::multiColForm($form, "/github-repo/edit/" . $repo->id , "POST", "Save", "repo_edit_form", null, null, "_self", true, $validation));
}

function edit_POST(Web $w) {

    $p = $w->pathMatch("id");

    $repo = !empty($p['id']) ? $w->Github->getRepo($p['id']) : new GithubRepo($w);
    
    $repo->fill($_POST);
    $repo->insertOrUpdate();
    
    $w->msg("<div id='saved_record_id' data-id='".$repo->id."' >Repo saved</div>", "/github/index#repos");
    
}
