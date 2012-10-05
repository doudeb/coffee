<?php
function coffee_api_set_site_id () {
	global $CONFIG;
    // user token can also be used for user authentication
    register_pam_handler('pam_auth_usertoken');
    $method = get_input('method');
	$token = get_input('auth_token');
	if (in_array($method,array('auth.gettoken','coffee.getTokenByEmail'))) {
		$username   = get_input('username');
		$email      = get_input('email');
		$password = get_input('password');
        if ($email && !$username) {
            if ($user_ent = get_user_by_email($email)) {
                $username = $user_ent[0]->username;
            } else {
                return false;
            }
        }
		if (elgg_authenticate($username, $password)) {
			$user_ent = get_user_by_username($username);
            $login_count = (int)$user_ent->getPrivateSetting('login_count') +1;
            $user_ent->setPrivateSetting('login_count', $login_count);
		} else {
			return false;
		}
	} elseif (!empty($token)) {
        $time = time();
		$user_session = get_data_row("SELECT * from {$CONFIG->dbprefix}users_apisessions where token='$token' and $time < expires");
		$user_guid = validate_user_token($token, $user_session->site_guid);
		$user_ent = get_entity($user_guid);
        if (!$user_ent instanceof ElggUser) {
            return false;
        }
        elgg_unregister_event_handler('login', 'user','user_login');
        login($user_ent);
        $CONFIG->auth_token = $token;
        $time_refresh = $time + (60*60*24*30);
        update_data("Update {$CONFIG->dbprefix}users_apisessions Set expires = $time_refresh Where id = " . $user_session->id);
	} elseif (elgg_is_logged_in()) {
        $time = time();
        $user_ent = elgg_get_logged_in_user_entity();
        $time_refresh = $time + (60*60*24*30);
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
function coffee_api_public_pages($hook, $handler, $return, $params) {
	$pages = array('userIcon/.*','dwl/.*','upl/.*', 'testApi','userCover/.*','thumbnail/*');
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
 		case "thumbnail":
            $guid = $page[1];
            $size = $page[2];
            set_input('auth_token', $page[0]);
            set_input('file_guid', $page[1]);
            set_input('size', $page[2]);
            if (!coffee_api_set_site_id ()) break;
            elgg_set_page_owner_guid($user_guid);
			render_thumbnail($guid,$size);
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

function _convert($content, $from = false) {
    $content =  html_entity_decode($content, ENT_QUOTES);
    if (!$from) {
        $from = mb_detect_encoding($content);
    }
    return mb_convert_encoding ($content , 'UTF-8', $from);
 }

 /**
 * Extend permissions checking to extend can-update for write users.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 */
function coffee_write_permission_check($hook, $entity_type, $returnvalue, $params) {
    if ($params['entity']->getSubtype() == COFFEE_SUBTYPE && $params['entity']->access_id == ACCESS_LOGGED_IN) {
        return true;
    }
}

function _get_translation_table ($country_code = 'en') {
    include_once elgg_get_plugins_path() . 'coffee/helper/translations/' . $country_code . '.php';
    return $translations;
}

function lock_index($hook, $type, $return, $params) {
	if (elgg_is_admin_logged_in()) {
        forward('admin/users/online');
		return true;
    } elseif (elgg_is_logged_in()) {
		forward('settings/user/');
		return true;
	}
}

function user_login ($action,$type,$user) {
    if (function_exists('get_site_id') && $user instanceof ElggUser) {
        $site = get_site_id();
        if ($site === null || $site === false) {
            $site = (int) datalist_get('default_site');
        }
        if ($user->site_guid == $site) {
            return true;
        }
    }
    return false;
}

function modify_header() {
    if(in_array(elgg_get_viewtype(),array('json','php','xml'))) {
        header('Access-Control-Allow-Origin: *');
    }
}

function auth_gettoken_by_email ($email,$password) {
    if($user_entity = get_user_by_email($email)) {
        $username = $user_entity[0]->username;
        return auth_gettoken($username,$password);
    } else {
        throw new SecurityException(elgg_echo('SecurityException:authenticationfailed'));
    }
}

function create_attachement ($filename, $content) {
    $prefix             = "file/";
    $file               = new FilePluginFile();
	$file->subtype      = "file";
    $file->title        = $filename;
	$file->access_id    = COFFEE_DEFAULT_ACCESS_ID;
    $filestorename      = elgg_strtolower(time().$filename);
    $file->setFilename($prefix.$filestorename);
    $file->setMimeType($content['mime_type']);
    $file->originalfilename = $filename;
    $file->description  = $file->originalfilename;
    $file->simpletype   = get_general_file_type($content['mime_type']);
    // Open the file to guarantee the directory exists
    $file->open("write");
    $file->close();
    // move using built in function to allow large files to be uploaded
    file_put_contents($file->getFilenameOnFilestore(), $content['content']);
    $guid = $file->save();
    // if image, we need to create thumbnails (this should be moved into a function)
    if ($guid && $file->simpletype == "image") {
        $thumbnail = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),60,60, true);
        if ($thumbnail) {
            $thumb = new ElggFile();
            $thumb->setMimeType($content['mime_type']);

            $thumb->setFilename($prefix."thumb".$filestorename);
            $thumb->open("write");
            $thumb->write($thumbnail);
            $thumb->close();

            $file->thumbnail = $prefix."thumb".$filestorename;
            unset($thumbnail);
        }

        $thumbsmall = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),153,153, true);
        if ($thumbsmall) {
            $thumb->setFilename($prefix."smallthumb".$filestorename);
            $thumb->open("write");
            $thumb->write($thumbsmall);
            $thumb->close();
            $file->smallthumb = $prefix."smallthumb".$filestorename;
            unset($thumbsmall);
        }

        $thumblarge = get_resized_image_from_existing_file($file->getFilenameOnFilestore(),600,600, false);
        if ($thumblarge) {
            $thumb->setFilename($prefix."largethumb".$filestorename);
            $thumb->open("write");
            $thumb->write($thumblarge);
            $thumb->close();
            $file->largethumb = $prefix."largethumb".$filestorename;
            unset($thumblarge);
        }
    }
    return $file->guid;
}


function prepare_message ($message) {
    //first clean the message
    $message = strip_tags($message,'<br><br/><em><strong>');
    //detect tags (starting by #
    $tags = false;
    preg_match_all('/#(\\w+)/',$message, $tags);
    $tags = $tags[1];

    return array('message' => $message, 'tags' => $tags);

}


function render_thumbnail ($guid, $size) {
    $file = get_entity($guid);
    if (!$file || $file->getSubtype() != "file") {
        exit;
    }
    $simpletype = $file->simpletype;
    if ($simpletype == "image") {

        // Get file thumbnail
        switch ($size) {
            case "small":
                $thumbfile = $file->thumbnail;
                break;
            case "medium":
                $thumbfile = $file->smallthumb;
                break;
            case "large":
            default:
                $thumbfile = $file->largethumb;
                break;
        }

        // Grab the file
        if ($thumbfile && !empty($thumbfile)) {
            $readfile = new ElggFile();
            $readfile->owner_guid = $file->owner_guid;
            $readfile->setFilename($thumbfile);
            $mime = $file->getMimeType();
            $contents = $readfile->grabFile();

            // caching images for 10 days
            header("Content-type: $mime");
            header('Expires: ' . date('r',time() + 864000));
            header("Pragma: public", true);
            header("Cache-Control: public", true);
            header("Content-Length: " . strlen($contents));

            echo $contents;
            exit;
        }
    }
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

add_translation("fr",array());
add_translation("es",array());

global $CONFIG;

unset($CONFIG->menus['page'][0]);
unset($CONFIG->menus['page'][1]);
unset($CONFIG->menus['page'][2]);
unset($CONFIG->menus['page'][3]);
unset($CONFIG->menus['page'][8]);
unset($CONFIG->menus['page'][9]);
unset($CONFIG->menus['page'][15]);
unset($CONFIG->menus['page'][16]);
unset($CONFIG->menus['page'][17]);
//setting default file permission mask
umask(002);
//lock site navigation
if (!in_array(elgg_get_context(), array('rest','coffee','usericon','file','dwl','testapi','thumbnail'))) {
    //logout();
}