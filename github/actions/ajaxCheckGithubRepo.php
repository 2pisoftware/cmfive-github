<?php
// Check Github Repo: Check if the user/repo exists in GitHub, and also that it
// is not already mapped to to TG

function ajaxCheckGithubRepo_ALL(Web $w) {

    $p = $w->pathMatch("owner","repo","id");
    $owner = $p['owner'];
    $repo = $p['repo'];
    $id   = $p['id'];

    $w->setLayout(null);
    
    if (!($owner && $repo)) {
        return;
    }

    // First off, does it already exist in DB?
    $local_repo = $w->Github->getRepoByOwnerRepo($owner,$repo);
    
    // If retrieved repo is the same as the current then we ignore call
    if ($local_repo) {
        if ($local_repo->id == $id) {
            return;
        } else {
            $errmsg = "Repo already exists";
            $w->out(json_encode(array('errmsg'=>$errmsg)));
        }
    } else {
        if ($owner && $repo) {
            $github_repo = $w->Github->getGithubRepo($owner, $repo);
        }

        $w->out(json_encode($github_repo));
    }
}


