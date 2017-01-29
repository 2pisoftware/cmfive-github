<?php
/**
 * Store info needed to map a Github user to CmFive
 * @author ged
 *
 */
class GithubUser extends DbObject {

    public $login;
    public $local_user_id;
	
    public static $_validation = array(
        "login" => array('required')
    );
    public static $_db_table = "github_user";
}
