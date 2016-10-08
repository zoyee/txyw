<?php
define('IN_ECS', true);
error_reporting(0);
require(dirname(__FILE__) . '/../../includes/init.php');

require_once 'scene_process.php';
$p = new scene_processor($db);
$p->process(63791, "oceJrwK76ZGJbNqGWUqct6exGY3w", "gh_ebb5f1b98707", "");