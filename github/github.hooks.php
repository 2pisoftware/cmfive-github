<?php

/**
 * Hook to notify relevant people when a task has been updated
 * 
 * @param Web $w
 * @param Comment $comment
 */
function github_comment_comment_added_task(Web $w, $comment) {

    // If we are currently in webhook then ignore comment
    if (!$w->Github->inWebHook()) {
        // Add the comment to github
        $task = $w->Task->getTask($comment->obj_id);
        $repo = $w->Github->getRepoByTaskGroup($task->task_group_id);
        $issue_no = $w->Github->getIssueNo($task);
        $w->Github->addComment($repo, $issue_no, $comment->comment);
    }    
}
