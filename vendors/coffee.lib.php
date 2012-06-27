<?php

function coffee_api_set_site_id () {
	global $CONFIG;
    // user token can also be used for user authentication
    register_pam_handler('pam_auth_usertoken');
    $method = get_input('method');
	$token = get_input('auth_token');
	if ($method == 'auth.gettoken') {
		$username = get_input('username');
		$password = get_input('password');
		if (elgg_authenticate($username, $password)) {
			$user_ent = get_user_by_username($username);
		} else {
			return false;
		}
	} elseif (isset($token)) {
        $time = time();
		$user_session = get_data_row("SELECT * from {$CONFIG->dbprefix}users_apisessions where token='$token' and $time < expires");
		$user_guid = validate_user_token($token, $user_session->site_guid);
		$user_ent = get_entity($user_guid);
        if (!$user_ent instanceof ElggUser) {
            return false;
        }
        login($user_ent);
        $CONFIG->auth_token = $token;
        $time_refresh = $time + (60*60);
        update_data("Update {$CONFIG->dbprefix}users_apisessions Set expires = $time_refresh Where id = " . $user_session->id);
	} elseif (elgg_is_logged_in()) {
        $time = time();
        $user_ent = elgg_get_logged_in_user_entity();
        $time_refresh = $time + (60*60);
        $user_token = get_data_row("SELECT * from {$CONFIG->dbprefix}users_apisessions where user_guid='$user_ent->guid'");
        update_data("Update {$CONFIG->dbprefix}users_apisessions Set expires = $time_refresh Where id = " . $user_token->id);
        $CONFIG->auth_token = $user_token->token;
        set_input('auth_token', $user_token->token);
    }
	if (isset($user_ent->site_guid)) {
		$CONFIG->site_guid = $CONFIG->site_id = $user_ent->site_guid;
		return true;
	}
	return false;
}


/**
 * Extend the public pages range
 *
 */
function coffee_api_public_pages($hook, $handler, $return, $params){
	$pages = array('userIcon/.*','dwl/*','upl/*', 'testApi');
	return array_merge($pages, $return);
}


/**
 *
 * @global type $CONFIG
 * @param type $page
 * @todo replace exit with a real empty page layout
 */
function coffee_page_handler($page,$handler) {
    global $CONFIG;
    $CONFIG->auth_token = $page[0];
	switch ($handler) {
		case "userIcon":
            set_input('auth_token', $page[0]);
            set_input('size', $page[2]);
            if (!coffee_api_set_site_id ()) break;
            $user_guid = isset($page[1]) ? $page[1]:elgg_get_logged_in_user_guid();
            elgg_set_page_owner_guid($user_guid);
			include_once ($CONFIG->path . 'pages/avatar/view.php');
            //echo elgg_view_page('toto', 'toto','default');
            exit();
			break;
		case "userCover":
            set_input('auth_token', $page[0]);
            $user_guid = isset($page[1]) ? $page[1]:elgg_get_logged_in_user_guid();
            if (!coffee_api_set_site_id ()) break;
            $filehandler = new ElggFile();
            $filehandler->owner_guid = $user_guid;
            $filehandler->setFilename("cover/{$user_guid}.jpg");
            try {
                if ($filehandler->open("read")) {
                    if ($contents = $filehandler->read($filehandler->size())) {
                        header("Content-type: image/jpeg", true);
                        header('Expires: ' . date('r', strtotime("+6 months")), true);
                        header("Pragma: public", true);
                        header("Cache-Control: public", true);
                        header("Content-Length: " . strlen($contents));

                        echo $contents;
                    }
                }
            } catch (InvalidParameterException $e) {}
            exit();
			break;
 		case "dwl":
            set_input('auth_token', $page[0]);
            set_input('file_guid', $page[1]);
            if (!coffee_api_set_site_id ()) break;
            elgg_set_page_owner_guid($user_guid);
			include_once elgg_get_plugins_path() . 'file/download.php';
            exit();
			break;
   		case "testApi":
			$body =  elgg_view('coffee/test/testApi', array('exposed' => $CONFIG->exposed));
            echo page_draw('API coffee test', $body);
			break;
        default:
			break;
    }
}


/**
 * Return relationship(s) for an id and a relationship type
 *
 * @param int $guid_one The GUID of the entity "owning" the relationship
 * @param string $relationship The type of relationship
 * @param
 * @param
 *

 */
function coffee_get_relationships($guid, $relationship, $inverse = false, $offset = 0, $limit = 3) {
        $guid = (int)$guid;
        $relationship = sanitise_string($relationship);
        $offset = (int)$offset;
        $limit = (int)$limit;
        $rel = 'guid_one';
        if($inverse) {
            $rel = 'guid_two';
        }
        if ($row = get_data("SELECT * FROM {$GLOBALS['CONFIG']->dbprefix}entity_relationships WHERE $rel=$guid AND relationship='$relationship' Limit $offset, $limit")) {
                return $row;
        }

        return false;
}

function _convert($content) {
     /*if(!mb_check_encoding($content, 'UTF-8')
         OR !($content === mb_convert_encoding(mb_convert_encoding($content, 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32'))) {

         $content = mb_convert_encoding($content, 'UTF-8');

         if (mb_check_encoding($content, 'UTF-8')) {
             // log('Converted to UTF-8');
         } else {
             // log('Could not converted to UTF-8');
         }
     } else {
         $content = utf8_decode($content);
     }*/
    $content =  html_entity_decode($content, ENT_QUOTES);
    $content =  html_entity_decode($content, ENT_QUOTES);
    $from = mb_detect_encoding($content);
    return mb_convert_encoding ($content , 'UTF-8', $from);
     //return iconv(mb_detect_encoding($content), "UTF-8", $content);
 }

/**

 * Exposed function for ws api
 */

if (is_array($exposed)) {
    foreach ($exposed as $key => $expose) {
        expose_function($expose['method']
                        ,$expose['function']
                        ,$expose['params']
                        ,$expose['comment']
                        ,$expose['call_method']
                        ,$expose['require_api_auth']
                        ,$expose['require_user_auth']);
    }
}