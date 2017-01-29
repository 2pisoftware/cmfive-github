<?php 
if ($hasAdmin) {
    echo Html::ab("/github-user/refresh", "Refresh GitHub Users", true);
}
    
if (!empty($users)) {
        $table_header = array("Github User", Config::get("main.application_name")." User");

    if ($hasAdmin) {
        $table_header[] = "Actions";
    }

    $table_data = array();

    // Build table data
    foreach ($users as $user) {
        $table_line = array();
        $table_line[] = $user->login;
        $table_line[] = $user->local_user_id ? $w->Auth->getUser($user->local_user_id)->login : "Not mapped yet";
        if ($hasAdmin) {
            $table_line[] = 
                Html::abox("/github-user/edit/".$user->id, "Edit", true);    
        }

        $table_data[] = $table_line;
    }

    echo Html::table($table_data, null, "tablesorter", $table_header);

} else { 
    echo "<h3><small>No GitHub users found.</small></h3>";
}