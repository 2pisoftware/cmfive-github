<?php


/**
 * GitHub issue tracking taskgroup
 * all properties are defined in the config.php
 *
 * @author ged
 *
 */
class TaskGroupType_GithubGitHub extends TaskGroupType {

    function getTaskGroupTypeTitle() {
        return "Software Development via GitHub";
    }

    function getTaskGroupTypeDescription() {
        return "Use this for tracking software development tasks via GitHub issues";
    }

    function getTaskTypeArray() {
        return array("GithubTicket" => "Github Issue");
    }

    function getStatusArray() {
        return array(
		array("Idea", false),
		array("On Hold", false),
		array("Backlog", false),
		array("Todo", false),
		array("WIP", false),
		array("Testing", false),
		array("Review", false),
		array("Deploy", false),
		array("Live", true), // is closing
		array("Rejected", true), // is closing
        );
    }

    function getTaskPriorityArray() {
        return array("Urgent", "Normal", "Nice to have");
    }

    function getCanTaskGroupReopen() {
        return true;
    }

}

/**
 * 
 * Github Programming Ticket
 * 
 * Modules can be added via the Lookup table:
 * Type = "<TaskGroupTitle> Modules"
 * 
 * @author ged
 *
 */
class TaskType_GithubTicket extends TaskType {

    function getCanTaskGroupReopen() {
        return true;
    }

    function getTaskTypeTitle() {
        return "Github Dev Ticket";
    }

    function getTaskTypeDescription() {
        return "Used to track issues and features in Github.";
    }

    function getFieldFormArray(TaskGroup $taskgroup, Task $task = null) {

        // Get task data for task in array[key=>value];
        $taskdata_arr = $task->w->Github-> getTaskDataArray($task);
        
        $labels = '';
        $issue_labels = json_decode($taskdata_arr["labels"]);
        foreach ($issue_labels as $label) {
            $labels .= $label->name.",";
        }
      
        return array(
            array("GitHub Details", "section"),
            array("<b>Repo:</b>", "static", "repo", $taskdata_arr["repo"]),
            array("<b>Issue:</b>", "static", "issue_no", Html::a($taskdata_arr["issue_url"],
                                                          $taskdata_arr["issue_no"])),
            array("<b>Labels:</b>", "static", "labels", substr($labels,0,-1))
            //array("Creator", "static", "creator", $this->getTaskDataValueForKey($taskdata, "creator")),
        );
    }

    private function getTaskDataValueForKey($taskdata, $key) {
        if (empty($taskdata) || empty($key)) {
            return null;
        }

        if (!is_array($taskdata)) {
            return null;
        }

        foreach ($taskdata as $data) {
            if ($data->data_key == $key) {
                return $data->value;
            }
        }
    }

    function on_before_update(Task $task) {
        
        // If we are in the webhook then ignore the update
        if (!$task->w->Github->inWebHook()) {
            $issue_no = $task->w->Github->getIssueNo($task);

            // Get a list of the fields in Task that have changed
            $changes = $task->w->Github->listChanges($task);

            // We are looking for assignee changes
            // Should also be checking for changed description?
            if (in_array("assignee_id",$changes)) {
                    file_put_contents("assign.txt","\nGot assignee_id changed",FILE_APPEND);                

                $task->w->Github->assignIssueFromTask($issue_no, $task);
            }
        }
        
    }

    /**function on_before_insert(Task $task) {
        // Get REQUEST object instead
        if (!empty($_REQUEST["b_or_f"]) && ($_REQUEST["b_or_f"] == 'Issue' || $_REQUEST["b_or_f"] == 'Task')) {
            $task->status = "Todo";
        }
    }*/
    
    /**
     * Hook to create GitHub issue when task is created
     * 
     * @param Task $task
     */
    function on_after_insert(Task $task) {
        // If the task is being created by a github webhook then we 
        // don't need create an issue in github!!
        if (!$task->w->Github->inWebHook()) {
            $task->w->Github->openIssueFromTask($task);
        }
    }
    
}