<?php

function refresh_ALL (Web $w) {
    $w->Github->refreshGithubUsers();
    $w->msg("<div id='saved_record_id' >Users Refreshed</div>", "/github/index#users");    
}