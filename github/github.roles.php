<?php

// given user interface employs tab, templates control display of tabs based on user role.

function role_github_admin_allowed(Web $w,$path) {
    return preg_match("/github(-.*)?\//",$path);
}

function role_github_user_allowed(Web $w,$path) {
    return $w->checkUrl($path, "github", null, "*");
}

