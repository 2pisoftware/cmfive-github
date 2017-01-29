<?php

function index_ALL(Web $w) {

    History::add("GitHub");
    
    // Users are via partial

    // Get the known repositories
    $repos = $w->Github->getRepos();

    // Need to let template know if user has admin rights
    $hasadmin = $w->Auth->hasRole("github_admin");
    $w->ctx("hasAdmin",$hasadmin); 
    
    // Check if there are taskgroups. If not, then we cannot yet map any 
    // repos
    $tgs = $w->Github->getTaskGroups();
    if (count($tgs) == 0) {
        $w->ctx("error","Cannot map any repositories until a taskgroup has been created.");
        $w->ctx('canAdd',false);
    } else {
        $w->ctx('canAdd',true);
    }
    
    $w->ctx("repos",$repos); 
    
}
