<?php

function webhook_POST(Web $w) {

    $w->setLayout(null);
        
    $payload = json_decode(file_get_contents("php://input"));

    if (!empty($payload)) {
        $webhook_event = $_SERVER["HTTP_X_GITHUB_EVENT"];
    
        $w->Log->info("Github $webhook_event event, repo=".$payload->repository->name."issue= ".$payload->issue->number);        

        if ($webhook_event == "issues") {
            $new_task = $w->Github->handleIssueEvent($payload);

            if ($new_task) {
                $w->Log->info("Github created task:".$new_task->title."[".$new_task->id."]");        
            } else {
                $w->Log->info("Github create task failed");        
            }
        } elseif ($webhook_event == "issue_comment") {
            $new_comment = $w->Github->handleIssueCommentEvent($payload);

            if ($new_comment) {
                $w->Log->info("Github new comment added");        
            } else {
                $w->Log->info("Github add comment failed");        
            }
        }        
    } else {
        $w->Log->info("Github issue event parse payload failed");
    }
}
