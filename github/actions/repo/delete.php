<?php

function delete_ALL(Web &$w) {
    
    $p = $w->pathMatch("id");    
    $id = $p['id'];
    
    if ($id) {
        // Repo is updated first retrieve it
        $repo = $w->Github->getRepo($id);

        // if repo exists, continue
        if ($repo->id) {
            // Get list of tasks for repo
            $tasks = $w->Task->getTasksByGroupId($repo->taskgroup_id);

            // if there are not active tasks, continue
            if (!$tasks) {
                $repo->is_deleted = 1;
                //$repo->update();
                $w->msg("Repo: " . $repo->owner."/".$repo->repo."(".$repo->id.") has been deleted.", "/github/index#repos");
            } else {
                $w->error("Repo still has active tasks.", "/github/index#repos");
            }        
        } else {
            $w->error("Repo could not be found.", "/github/index#repos");
        }        
    } else {
        $w->error("No id supplied", "/github/index#repos");
    }

}
