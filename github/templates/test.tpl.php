<h3>Testing the Cmfive GitHub interface</h3>

<?php if ($testing_github) { ?>
    <h4>Github API:</h4>

    <p><b>Get Repos from Github for <?php echo "$owner";?>:</b>
    <?php
        if (isset($git_repos)) {
            foreach ($git_repos as $gr) {
                echo  $gr['id']."/".$gr['name']."/".$gr['description']; 
            }
        } else {
            echo "None found";
        }?>
    </p>


    <p><b>Get Repo from Github for <?php echo "$owner/$repo";?>:</b>
    <?php echo isset($git_repo) ? $git_repo['id']."/".$git_repo['name']."/".$git_repo['description'] : "Not found"; ?>
    </p>

    <p><b>Get Repo Collaorators from Github for <?php echo "$owner/$repo";?>:</b>
    <?php
        if (isset($git_repo_colls)) {
            foreach ($git_repo_colls as $c) {
                echo  $c['login']; 
            }
        } else {
            echo "None found";
        }?>
    </p>

    <p><b>Get Issues from Github for <?php echo "$owner/$repo";?>:</b>
    <?php
        if (isset($git_issues)) {
            foreach ($git_issues as $issue) {
                echo  $issue['id']."/<pre>".$issue['title']."/".$issue['body']."</pre>"; 
            }
        } else {
            echo "None found";
        }?>
    </p>
<?php 
} else {
    echo "<h4>Bypassing Github API</h4>";    
}
?>

<h4>Local Methods:</h4>
    
<p><b>Get Repo by TG Id for <?php echo "($tg->id)$tg->title";?>:</b>
<?php echo isset($tg_repo) ? $tg_repo->owner."/".$tg_repo->repo."/".$tg_repo->url : "Not found"; ?>
</p>
    
<p><b>Get Repo by Github owner and repo name: <?php echo "$owner/$repo";?>:</b>
<?php echo isset($or_repo) ? $or_repo->owner."/".$or_repo->repo."/".$or_repo->url : "Not found"; ?>
</p>
    
<p><b>Get Users:</b>
<?php
    if (isset($users)) {
        foreach ($users as $u) {
            //var_dump($u);
            echo  $u->login."/".$u->local_user_id."/";
            echo isset($u->local_user_id) ? $w->Auth->getUser($u->local_user_id)->login : 'Not mapped yet'; 
        }
    } else {
        echo "None found";
    }?>
</p>

<p><b>Get User for  <?php echo $user->id;?>:</b>
<?php echo isset($user) ? $user->login: "Not found"; ?>
</p>
    
<p><b>Get Github User for  <?php echo $owner;?>:</b>
<?php 
    echo  $user_1->login."/".$user_1->local_user_id."/";
    echo isset($user_1->local_user_id) ? $w->Auth->getUser($user_1->local_user_id)->login : 'Not mapped yet'; 
?>
</p>
    
<p><b>Get Github User for local id <?php echo $local_user_id;?>:</b>
<?php 
    echo  $user_2->login."/".$user_2->local_user_id."/";
    echo isset($user_2->local_user_id) ? $w->Auth->getUser($user_2->local_user_id)->login : 'Not mapped yet'; 
?>
</p>
    
<p><b>Get Github Issue # for task <?php echo "($task_1->id) $task_1->title";?>:</b>
<?php 
    echo $task_1_issue_no;
?>
</p>
