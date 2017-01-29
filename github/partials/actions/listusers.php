<?php
function listusers_ALL(Web $w, $params) {
    // Need to let template know if user has admin rights
    $hasadmin = $w->Auth->hasRole("github_admin");
    $w->ctx("hasAdmin",$hasadmin); 
    
    // Get the known github users
    $users = $w->Github->getUsers();
    $w->ctx("users",$users); 
}