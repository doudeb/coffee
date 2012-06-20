<?php
die('blabla');
elgg_register_plugin_hook_handler('system', 'init', 'coffee_api_set_site_id');

$user_guid = get_input('user_id',elgg_get_logged_in_user_guid());
elgg_set_page_owner_guid($user_guid);
include_once ($CONFIG->path . 'pages/avatar/view.php');