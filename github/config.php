<?php
Config::set('github', array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'hooks' => array(
        'comment'
    ),
    
    //'search' => array(
    //        "roofit" => "ExampleData",
    //),
    //'widgets' => array(),
    //'hooks' => array('core_dbobject','example'),
    "dependencies" => array(
        "knplabs/github-api" => "~1.4",
        "guzzlehttp/guzzle" => "~6.0"
     )
 ));

Config::set('task.TaskGroupType_GithubGitHub', array(
	'title' => 'Software Development via GitHub',
	'description' => 'Use this for tracking software development tasks via GitHub issues',
	'can-task-reopen' => true,
	'tasktypes' => array(
	    "GithubTicket" => "Github Issue"),
	'statuses' => array(
		array("Idea", false),
		array("On Hold", false),
		array("Backlog", false),
		array("Todo", false),
		array("WIP", false),
		array("Testing", false),
		array("Review", false),
		array("Deploy", false),
		array("Live", true), // is closing
		array("Rejected", true)), // is closing
	'priorities' => array("Urgent", "Normal", "Nice to have"),
));
