<?php

use mod_forum\subscriptions;

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

list($options, $unrecognized) = cli_get_params(array(
    'forum' => '',
));

($forum_id = $options['forum']) || cli_error('No discussion provided.');

/** @var \moodle_database $DB */
$forum   = $DB->get_record('forum', array('id' => $forum_id), '*', MUST_EXIST);
$course  = $DB->get_record('course', array('id' => $forum->course), '*', MUST_EXIST);
$cm      = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
$context = context_module::instance($cm->id);

$users = subscriptions::get_potential_subscribers($context, 0, 'u.id');

foreach (array_keys($users) as $userid) {
    subscriptions::subscribe_user($userid, $forum, $context);
}
