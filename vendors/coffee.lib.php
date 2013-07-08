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
        $login_result = elgg_authenticate($username, $password);
		if ($login_result === true) {
			$user_ent = get_user_by_username($username);
            $login_count = (int)$user_ent->getPrivateSetting('login_count') +1;
            $user_ent->setPrivateSetting('login_count', $login_count);
            $CONFIG->language = $user_ent->language;
		} else {
			return $login_result;
		}
	} elseif (!empty($token)) {
        $time = time();
		$user_session = get_data_row("SELECT * from {$CONFIG->dbprefix}users_apisessions where token='$token' and $time < expires");
		$user_guid = validate_user_token($token, $user_session->site_guid);
		$user_ent = get_user($user_guid);
        if (!$user_ent instanceof ElggUser) {
            return false;
        }
        elgg_unregister_event_handler('login', 'user','user_login');
        login($user_ent);
        $CONFIG->auth_token = $token;
        $CONFIG->language = $user_ent->language;
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
	$pages = array('userIcon/.*','dwl/.*','upl/.*', 'testApi','userCover/.*','thumbnail/*','dwlLarge/*');
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
 		case "dwlLarge":
            set_input('auth_token', $page[0]);
            set_input('file_guid', $page[1]);
            if (!coffee_api_set_site_id ()) break;
            elgg_set_page_owner_guid($user_guid);
            render_dwl($page[1]);
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
    umask(002);
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
    preg_match_all('/#(\\w+)/u',$message, $tags);
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

function render_dwl ($guid) {
    $file = get_entity($guid);
    if (!$file) {
            register_error(elgg_echo("file:downloadfailed"));
            forward();
    }
    $mime = $file->getMimeType();
    if (!$mime) {
            $mime = "application/octet-stream";
    }

    $filename = $file->originalfilename;

    // fix for IE https issue
    header("Pragma: public");

    header("Content-type: $mime");
    header('Expires: ' . date('r', strtotime("+6 months")), true);
    header('Cache-Control: max-age=28800');
    if (strpos($mime, "image/") !== false || $mime == "application/pdf") {
            header("Content-Disposition: inline; filename=\"$filename\"");
    } else {
            header("Content-Disposition: attachment; filename=\"$filename\"");
    }

    ob_clean();
    flush();
    readfile($file->getFilenameOnFilestore());
    exit;
}

function format_post_array ($text,$time_created,$user_id,$username,$display_name,$icon_url,$cover_url,$baseline=false,$crawled=false,$comments=false) {
    $return = array();
    $return['content']['text'] = nl2br($text);
    $return['content']['time_created'] = $time_created;
    $return['content']['friendly_time'] = $time_created;
    $return['user']['guid'] = $user_id;
    $return['user']['username'] = $username;
    $return['user']['name'] = $display_name;
    $return['user']['baseline'] = $baseline;
    $return['user']['icon_url'] = $icon_url;
    $return['user']['icon_url_small'] = $icon_url;
    $return['user']['cover_url'] = $cover_url;
    if (is_array($crawled)) {
        $return['attachment'][] = $crawled;
    } else {
        $return['attachment'] = false;
    }
    if (is_array($comments)) {
        $return['comment'] = $comments;
    } else {
        $return['comment'] = false;
    }

    return $return;
}

function format_post_comments ($comments) {
    $return['total'] = count($comments);
    $return['comments'] = false;
    foreach ($comments as $comment) {
        $return['comments'][] = array( 'id' => rand(0,666)
                                                , 'owner_guid' => $comment['owner_guid']
                                                , 'name' => $comment['display_name']
                                                , 'icon_url' => $comment['icon_url']
                                                , 'icon_medium' => $comment['icon_url']
                                                , 'time_created' => $comment['time_created']
                                                , 'friendly_time' => elgg_get_friendly_time($comment['time_created'])
                                                , 'text' => $comment['text']);
    }
    return $return;
}

/**
 * Return default results for searches on users.
 *
 * @todo add profile field MD searching
 *
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $value
 * @param unknown_type $params
 * @return unknown_type
 */
function search_users($query, $offset, $limit) {
	$db_prefix = elgg_get_config('dbprefix');

    $params['query'] = $query;
    $params['offset'] = $offset;
    $params['limit'] = $limit;

	$query = sanitise_string($params['query']);

	$params['joins'] = array(
		"JOIN {$db_prefix}users_entity ue ON e.guid = ue.guid",
		"JOIN {$db_prefix}metadata md on e.guid = md.entity_guid",
		"JOIN {$db_prefix}metastrings msv ON n_table.value_id = msv.id"
	);

	// username and display name
	$fields = array('username', 'name');
	$where = search_get_where_sql('ue', $fields, $params, FALSE);

	// profile fields
	$profile_fields = array_merge(array_keys(elgg_get_config('profile_fields')), array_values(array('hobbies', 'languages', 'socialmedia', 'headline', 'department', 'location', 'introduction', 'phone', 'cellphone')));
	// get the where clauses for the md names
	// can't use egef_metadata() because the n_table join comes too late.
	$clauses = elgg_entities_get_metastrings_options('metadata', array(
		'metadata_names' => $profile_fields,
	));

	$params['joins'] = array_merge($clauses['joins'], $params['joins']);
	// no fulltext index, can't disable fulltext search in this function.
	// $md_where .= " AND " . search_get_where_sql('msv', array('string'), $params, FALSE);
	$md_where = "(({$clauses['wheres'][0]}) AND msv.string LIKE '%$query%')";

	$params['wheres'] = array("(($where) OR ($md_where))");

	// override subtype -- All users should be returned regardless of subtype.
	$params['subtype'] = ELGG_ENTITIES_ANY_VALUE;
	$params['count'] = true;
	$count = elgg_get_entities($params);

	// no need to continue if nothing here.
	if (!$count) {
		return array('entities' => array(), 'count' => $count);
	}

	$params['count'] = FALSE;
	$entities = elgg_get_entities($params);

	// add the volatile data for why these entities have been returned.
	foreach ($entities as $entity) {

		$title = search_get_highlighted_relevant_substrings($entity->name, $query);

		// include the username if it matches but the display name doesn't.
		if (false !== strpos($entity->username, $query)) {
			$username = search_get_highlighted_relevant_substrings($entity->username, $query);
			$title .= " ($username)";
		}

		$entity->setVolatileData('search_matched_title', $title);

		$matched = '';
		foreach ($profile_fields as $md) {
			$text = $entity->$md;
			if (stristr($text, $query)) {
				$matched .= elgg_echo("profile:{$md}") . ': '
						. search_get_highlighted_relevant_substrings($text, $query);
			}
		}

		$entity->setVolatileData('search_matched_description', $matched);
	}

	return array(
		'entities' => $entities,
		'count' => $count,
	);
}


function getYoutubeData($url){
    $pattern = '/(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+/';
    $result = preg_match($pattern, $url, $matches);

    if (false == $result) {
        return false;
    }
    $video_id = trim($matches[0]);

    $data_stream='http://gdata.youtube.com/feeds/api/videos/'.$video_id.'?v=2&alt=jsonc';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $data_stream);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    $obj=json_decode($data);
    if (false===$data || $obj->error) return false;
    $duration = round($obj->data->duration*1.10);
    return array('id' =>$video_id, 'duration' => $duration);
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

global $CONFIG;

//setting default file permission mask
umask(002);
//Register external classe
elgg_register_classes(elgg_get_plugins_path() . 'coffee/vendors/external_api');

//lock site navigation
if (!in_array(elgg_get_context(), array('rest','coffee','usericon','file','dwl','testapi','thumbnail','dwlLarge'))) {
    logout();
}
