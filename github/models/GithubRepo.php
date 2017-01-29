<?php
/**
 * Store info needed to map a Github repo to CmFive
 * @author ged
 *
 */
class GithubRepo extends DbObject {

    public $owner;          // Login of Github user that owns the repo
    public $repo;
    public $url;
    public $access_token;
    public $taskgroup_id;
    public $default_issue_creator;  // When issues are created, which Github login will they be attributed,
                                    // if the current user does not map to a github login

    public $task_type;              // The task type to be used with github issues create local tasks
    public $description;
    
    public static $_validation = array(
        "owner" => array('required'),
        "repo" => array('required'),
        "url" => array('required'),
        "access_token" => array('required'),
        //"default_issue_creator" => array('required'),
        //"task_type" => array('required'),
        //"taskgroup_id" => array('required')
    );
    public static $_db_table = "github_repo";
}
