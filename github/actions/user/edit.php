<?php

function edit_GET(Web $w) {

    $p = $w->pathMatch("id");

    $user = $w->Github->getUser($p['id']);
    $users = $w->Auth->getUsers();

    $validation = GithubUser::$_validation;
    $form = [(!empty($p["id"]) ? 'Change mapped user' : "Map a new user") => [
                [
                    ["Github Login", "text", "-login", $user->login],
                    [Config::get("main.application_name")." Login", "autocomplete", "local_user_id", !empty($user->local_user_id) ? $user->local_user_id : null, $users],
                ]
            ]
    ];

    $w->ctx("form", Html::multiColForm($form, "/github-user/edit/" . $user->id , "POST", "Save", "user_edit_form", null, null, "_self", true, $validation));
}

function edit_POST(Web $w) {

    $p = $w->pathMatch("id");

    $user = $w->Github->getUser($p['id']);
    
    $local_user_id = $_POST["local_user_id"];
    
    if (local_user_id) {
        $user->local_user_id= $local_user_id;
        $user->update();
        $msg = "Github login '".$user->login."' mapped to local login '".$w->Auth->getUser($user->local_user_id)->login."'";
        $w->msg("<div id='saved_record_id' data-id='".$user->id."' >".$msg."</div>", "/github/index#users");
    } else {
        $w->msg("<div>Nothing changed</div>", "/github/index#users");
    }
    
}
