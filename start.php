<?php
 /**
 * coffee - a twitter like api

 * @package enlightn
 */


function coffee_init() {

    include_once(elgg_get_plugins_path() . 'coffee/vendors/coffee.conf.php');
    include_once(elgg_get_plugins_path() . 'coffee/vendors/coffee.lib.php');

    elgg_register_entity_type('object', COFFEE_SUBTYPE);
	elgg_register_plugin_hook_handler('rest', 'init', 'coffee_api_set_site_id');
	elgg_register_plugin_hook_handler('public_pages', 'walled_garden', 'coffee_api_public_pages');

    elgg_register_class('ElggCoffee', elgg_get_plugins_path() . 'coffee/vendors/coffee.class.php');

    // Register a page handler, so we can have nice URLs
    elgg_register_page_handler('userIcon','coffee_page_handler');
    elgg_register_page_handler('dwl','coffee_page_handler');
    elgg_register_page_handler('upl','coffee_page_handler');

}

elgg_register_event_handler('init', 'system', 'coffee_init');