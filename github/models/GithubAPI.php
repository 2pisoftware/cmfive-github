<?php
/**
 * Github - wrapper class for PHP Github API
 * 
 * @author Ged Nash Dec 2015
 */

class GithubAPI_debug {
    
    function __call($name, $args)
    {
        $msg = $name."(";
        
        foreach ($args as $arg) {
            if (gettype($arg) == 'array') {
                $msg .= "(".implode(",",$arg)."),";
            } else {
                $msg .= $arg.",";
            }                        
        }    
        $this->output($msg);
        return $this;
    }
    
    
    function output($msg){
        echo "GithubAPI_debug:".$msg;
    }
}


class GithubAPI {
	
	// object properties
    public $w;
    protected $_client;
    protected $_access_token;
    protected $_debug = false;
    
    function __construct($w, $debug=false) {
        $this->w = $w;
        if ($debug) { 
            $this->_client = new GithubAPI_debug();
        } else {
            $this->_client = new \Github\Client();
        }
    }

    /**
     * Authenticate the github api so we can update if needed
     * 
     * @param 
     * @return 
     */
    function authenticate($token) {
        try {
            $this->_client->authenticate($token, Github\Client::AUTH_HTTP_TOKEN);
        } catch ( Exception $e ) {
            $this->handleApiError( $e );
        }            
        return $this;
    }	

    /**
    * Get a repo for a given user and repo
    *
    * @return 
    */    
    function getRepo($owner, $repo) {
        try {
            return ($this->_client->api('repo')->show($owner, $repo));   
        } catch ( Exception $e ) {
            $this->handleApiError( $e );
        }
    }	
    
    
    /**
    *  Add comment
    * 
    * @param 
    * @return 
    */
    function addComment ($owner, $repo, $issue_no, $comment) {
        try {
            $this->_client->api('issue')->comments()->create($owner, $repo, $issue_no, array('body' => $comment));
        } catch ( Exception $e ) {
            $this->handleApiError( $e );
        }            
    }

    /**
     * Open a new GitHub issue
     * 
     * @param github user, github repo, issue title, issue body
     * @return 
     */
    function openIssue ($owner, $repo, $title, $body) {
        try {
            $this->_client->api('issue')->create($owner, $repo, array('title' => $title, 'body' => $body));    
        } catch ( Exception $e ) {
            $this->handleApiError( $e );
        }            
    }

    /**
     * Asssign a GitHub issue
     * 
     * @param github user, github repo, issue title, issue body
     * @return 
     */
    function assignIssue ($repo, $issue_no, $assignee) {
        try {
            $this->_client->api('issue')->update($repo->owner, $repo->repo, $issue_no, array('assignee' => $assignee));    
        } catch ( Exception $e ) {
            $this->handleApiError( $e );
        }            
    }

    /** 
    * @return 
    */    
    function getRepos($owner) {
        try {
            return ($this->_client->api('user')->repositories($owner));  

        } catch ( Exception $e ) {
            $this->handleApiError( $e );
        }
    }	
    
    /** 
    * @return 
    */    
    function getIssues($owner, $repo) {
        try {
            return ($this->_client
                         ->api('issue')
                         ->all($owner, $repo, array('state' => 'open')));  

        } catch ( Exception $e ) {
            $this->handleApiError( $e );
        }
    }	
    
    /** 
    * @return 
    */    
    function getCollaborators($owner, $repo) {
        try {
            return ($this->_client->api('repo')->collaborators()->all($owner, $repo));
        } catch ( Exception $e ) {
            $this->handleApiError( $e );
        }
    }	
    
    /** 
    * @return API error handler
    */    
    function handleApiError($e) {
        echo $e->getMessage() . PHP_EOL;
        //echo $e->getCode() . PHP_EOL;

        $this->w->error("Github API Failed:".$e->getMessage());
    }	
}