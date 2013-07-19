<?php
/**
 * ElggCoffee class
 *
 */

class ElggCoffee {

    /**
     *
     * method that will create a post
     *
     * @param string $post
     * @param array $attachment An array of guid
     * @param string $type The post subtype
     */
    public static function new_post($post, $attachment = false, $mentioned_user = false, $type = COFFEE_SUBTYPE) {
        if (strlen($post) > 0 || $attachment) {
            $post = prepare_message($post);
            $message = $post['message'];
            if ($type === COFFEE_SUBTYPE_BROADCAST_MESSAGE && !elgg_is_admin_logged_in()) {
                return false;
            }
            $new_post = new ElggObject();
            $new_post->subtype = $type;
            $new_post->access_id = COFFEE_DEFAULT_ACCESS_ID;
            $new_post->title = $message;
            $new_post->description = $message;
            $new_post->tags  = $post['tags'];
            if (!$new_post->save()) {
                return false;
            }
            //add_to_river('coffee/river/new_post', 'create', elgg_get_logged_in_user_guid(), $new_post->guid);
            ElggCoffee::_add_attachment($new_post->guid,$attachment);
            ElggCoffee::_add_mentioned($new_post->guid,$mentioned_user);
            ElggCoffee::update_site_trigger(COFFEE_SITE_FEED_UPDATE);
            return array('guid' => $new_post->guid);
        }
        return false;
    }

    public static function new_comment($guid, $comment, $mentioned_user) {
        $post = get_entity($guid);
        $message = prepare_message($comment);
        if ($post instanceof ElggObject && strlen($message['message']) > 0) {
            $comment_id = $post->annotate(COFFEE_COMMENT_TYPE, $message['message'], COFFEE_DEFAULT_ACCESS_ID);
            if ($comment_id) {
                $post->time_updated = time();
                $post->description = $post->description  .  ' ' . $message['message'];
                $post->save();
                ElggCoffee::_add_mentioned($post->guid,$mentioned_user,COFFEE_COMMENT_MENTIONED_RELATIONSHIP);
                //add_to_river('coffee/river/new_comment', 'create', elgg_get_logged_in_user_guid(), $post->guid,$comment_id);
                if ($message['tags']) {
                    $currentTags = $post->tags;
                    if ($currentTags) {
                        $tags = array_merge((array)$currentTags,(array)$message['tags'] );
                    } else {
                        $tags = $message['tags'];
                    }
                    $post->tags = $tags;
                    $post->save();
                }
                ElggCoffee::update_site_trigger(COFFEE_SITE_FEED_UPDATE);
                return true;
            }
        }
        return false;
    }

    public static function get_post($guid) {
        if (!$guid) {
            return false;
        }
        return static::get_posts(0,0,10,false,array(COFFEE_SUBTYPE,COFFEE_SUBTYPE_BROADCAST_MESSAGE), $guid);
    }

    public static function get_site_data () {
        $site_guid      = $GLOBALS['CONFIG']->site_guid;
        $site           = get_entity($site_guid);
        $user_ent       = elgg_get_logged_in_user_entity();
        if ($site instanceof ElggSite) {
            $options  = array('types'=>'object','subtypes'=>'file','limit'=>1);
            $options['joins']   = array("Inner join {$GLOBALS['CONFIG']->dbprefix}objects_entity obj_ent On e.guid = obj_ent.guid");
            $options['wheres']   = array("obj_ent.title = 'logo'");
            $site_logo = elgg_get_entities($options);
            $options['wheres']   = array("obj_ent.title = 'background'");
            $site_background = elgg_get_entities($options);
            $options['wheres']   = array("obj_ent.title = 'css'");
            $custom_css = elgg_get_entities($options);
            $viewtype = elgg_get_viewtype();
            return array(
                    'user_guid' => elgg_get_logged_in_user_guid()
                    , 'is_admin' => elgg_is_admin_logged_in()?'true':'false'
                    , 'name' => $site->name
                    , 'logo_url' => ElggCoffee::_get_dwl_url($site_logo[0]->guid)
                    , 'background_url' => ElggCoffee::_get_dwl_url($site_background[0]->guid)
                    , 'custom_css' => ElggCoffee::_get_dwl_url($custom_css[0]->guid)
                    , 'system_update' => datalist_get('simplecache_lastupdate_default')
                    , 'corporate_tags' => ElggCoffee::get_corporate_tags()
            );

        }

    }

    public static function get_posts($newer_than = 0, $offset = 0, $limit = 10, $owner_guids = array(), $type = array(), $guid = false, $tags = array()) {
        $join = $where = $return = array();
        $db_prefix = elgg_get_config('dbprefix');
        $where[] = 'e.time_updated > ' . $newer_than;
        $lastTimeUpdated = false;
        if (preg_match('/\d{1,7}/', $tags[0])) {
            $guid = $tags[0];
        } else if (is_array($tags) && count($tags) > 0) {
            $tags_string = "'" . implode("','", $tags) . "'";
            $tags_meta_id = get_metastring_id('tags')?get_metastring_id('tags'):0;
            $join[] = "Left Join {$db_prefix}metadata tag_used On tag_used.name_id = $tags_meta_id And tag_used.entity_guid = e.guid
                        Left Join {$db_prefix}metastrings tag_name On tag_used.value_id = tag_name.id
                        Left Join {$db_prefix}objects_entity obj On e.guid = obj.guid";
            $where[] = "(tag_name.string In ($tags_string)
                            Or MATCH (obj.title,obj.description) AGAINST ('$tags[0]'))";
        }
        if ($type === false) {
            $type = array(COFFEE_SUBTYPE,COFFEE_SUBTYPE_BROADCAST_MESSAGE);
        }
        $options  = array('types'=>'object'
                            , 'subtypes'=> $type
                            , 'limit'=> $limit
                            , 'offset'=> $offset
                            , 'owner_guids' => count($owner_guids) > 0 ? $owner_guids : false
                            , 'joins' => $join
                            , 'wheres' => $where);
        if ($guid && $guid > 0) {
            $posts = array(get_entity($guid));
            $site = elgg_get_site_entity();
            if ($site instanceof ElggSite) {
                $lastTimeUpdated = $site->{COFFEE_SITE_FEED_UPDATE};
            }
        } else {
            $posts = elgg_get_entities($options);
        }
        if(is_array($posts)) {
            foreach ($posts as $key => $post) {
                if ($post instanceof ElggObject) {
                    $return[$key]['guid'] = $post->guid;
                    $return[$key]['content']['type'] = $post->getSubtype();
                    $return[$key]['content']['text'] = nl2br($post->title);
                    $return[$key]['content']['time_created'] = $post->time_created;
                    $return[$key]['content']['friendly_time'] = elgg_get_friendly_time($post->time_created);
                    $return[$key]['content']['time_updated'] = $lastTimeUpdated?$lastTimeUpdated:$post->time_updated;
                    $user = get_user($post->owner_guid);
                    if ($user instanceof ElggUser) {
                        $return[$key]['user']['guid'] = $user->guid;
                        $return[$key]['user']['username'] = $user->username;
                        $return[$key]['user']['name'] = $user->name;
                        $return[$key]['user']['baseline'] = $user->headline;
                        $return[$key]['user']['icon_url'] = ElggCoffee::_get_user_icon_url($user,'medium');
                        $return[$key]['user']['icon_url_small'] = ElggCoffee::_get_user_icon_url($user,'small');
                        $return[$key]['user']['cover_url'] = ElggCoffee::_get_user_cover_url($user);
                    }

                    $return[$key]['likes'] = ElggCoffee::get_likes ($post->guid, 0, 10);
                    $return[$key]['comment'] = ElggCoffee::get_comments ($post->guid, 0, 2);
                    $return[$key]['attachment'] = ElggCoffee::get_attachment ($post->guid);
                    $return[$key]['mentioned'] = ElggCoffee::get_mentioned ($post->guid);
                    $return[$key]['tags'] = $post->tags;
                }
            }
        }
        return $return;
    }

    public function get_activity ($offset = 0, $limit = 10) {}

    public static function get_user_data ($guid, $extended = false) {
        if (!$guid) {
            $user_ent           = elgg_get_logged_in_user_entity();
        } else {
            $user_ent           = get_user($guid);
        }
        if ($user_ent instanceof ElggUser
                && $user_ent->site_guid == $GLOBALS['CONFIG']->site_guid) {
            if (is_array($extended)) {
                $extended = static::get_user_extra_info($extended);
            }
            return array(   'id'               => $user_ent->guid
                            , 'username'       => $user_ent->username
                            , 'name'           => $user_ent->name
                            , 'email'          => $user_ent->email
                            , 'icon_url'       => ElggCoffee::_get_user_icon_url($user_ent)
                            , 'cover_url'      => ElggCoffee::_get_user_cover_url($user_ent)
                            , 'login_count'    => (int)$user_ent->getPrivateSetting('login_count')
                            , 'language'       => $user_ent->language
                            , 'created'        => $user_ent->time_created
                            , 'extended'       => $extended
                    );
        }
        return false;
    }

    public static function get_comments ($guid, $offset = 0, $limit = 2) {
        $comments = ElggCoffee::_get_comments ($guid, $offset, $limit);
        if ($comments['count'] > 0) {
            $return['total'] = $comments['count'];
            foreach ($comments['details'] as $comment) {
                $user = get_user($comment->owner_guid);
                if ($user instanceof ElggUser && $comment instanceof ElggAnnotation) {
                    $return['comments'][] = array('id' => $comment->id
                                                            , 'owner_guid' => $user->guid
                                                            , 'name' => $user->name
                                                            , 'icon_url' => ElggCoffee::_get_user_icon_url($user,'small')
                                                            , 'icon_medium' => ElggCoffee::_get_user_icon_url($user,'medium')
                                                            , 'time_created' => $comment->time_created
                                                            , 'friendly_time' => elgg_get_friendly_time($comment->time_created)
                                                            , 'text' => $comment->value
                                                            , 'mentioned' => ElggCoffee::get_mentioned ($guid,COFFEE_COMMENT_MENTIONED_RELATIONSHIP));
                }
            }
        } else {
            $return['total'] = 0;
            $return['comments'] = false;
        }
        return $return;
    }

    public static function get_likes ($guid, $offset = 0, $limit = 3) {
        $likes = ElggCoffee::_get_likes ($guid, $offset, $limit);
        if (is_array($likes['details'])) {
            $return['total'] = $likes['count'];
            foreach ($likes['details'] as $like) {
                $user = get_user($like->guid_one);
                if ($user instanceof ElggUser) {
                    $return['users'][] = array('owner_guid' => $user->guid
                                                , 'name' => $user->name
                                                , 'time_created' => $like->time_created
                                                , 'friendly_time' => elgg_get_friendly_time($like->time_created));
                }
            }
        } else {
            $return['total'] = 0;
            $return['users'] = false;
        }
        return $return;
    }

    public static function get_attachment ($guid) {
        $return = false;
        $attachment = coffee_get_relationships($guid, COFFEE_POST_ATTACHMENT_RELATIONSHIP);
        if (!is_array($attachment)) {
            return $return;
        }
        foreach ($attachment as $key => $attached) {
            $return[] = ElggCoffee::_get_file_details($attached->guid_two);
        }
        return $return;
    }

    public static function get_mentioned ($guid, $type = COFFEE_POST_MENTIONED_RELATIONSHIP,$offset = 0, $limit = 30) {
        $return = array();
        $mentioned = coffee_get_relationships($guid, $type, false,$offset, $limit);
        if (is_array($mentioned)) {
            foreach ($mentioned as $mention) {
                $user = get_user($mention->guid_two);
                if ($user instanceof ElggUser) {
                    $return[] = array('owner_guid' => $user->guid
                                                , 'name' => $user->name);
                }
            }
        } else {
            $return['users'] = false;
        }
        return $return;
    }

    public static function set_relationship ($guid_parent, $guid_child, $type) {
        $guid_parent = (int)$guid_parent;
        $guid_child = (int)$guid_child;
        $type = sanitise_string($type);
        if (!empty($type)) {
            $return = add_entity_relationship($guid_parent, $type, $guid_child);
            if ($return) {
                //add_to_river('coffee/river/' . $type, 'create', $guid_parent, $guid_child);
                $post = get_entity($guid_child);
                if ($post instanceof ElggEntity) {
                    $post->time_updated = time();
                    $post->save();
                }
                ElggCoffee::update_site_trigger(COFFEE_SITE_FEED_UPDATE);
                return true;
            } elseif (!$return) {
                $return = check_entity_relationship($guid_parent, $type, $guid_child);
                if ($return) {
                    return true;
                }
            }
        }
        return $return;
    }

     public static function remove_relationship ($guid_parent, $guid_child, $type) {
        $guid_parent = (int)$guid_parent;
        $guid_child = (int)$guid_child;
        $type = sanitise_string($type);
        if (!empty($type)) {
            $return = remove_entity_relationship($guid_parent, $type, $guid_child);
            if ($return) {
                //add_to_river('coffee/river/' . $type, 'remove', $guid_parent, $guid_child);
                ElggCoffee::update_entity_time($guid_child);
                ElggCoffee::update_site_trigger(COFFEE_SITE_FEED_UPDATE);
                return true;
            } elseif (!$return) {
                $return = check_entity_relationship($guid_parent, $type, $guid_child);
                if ($return) {
                    return false;
                }
            }
        }
        return $return;
    }

    public static function disable_object ($guid) {
        $ent = get_entity($guid);
        if ($ent instanceof ElggObject
                && (elgg_is_admin_logged_in() || $ent->owner_guid == elgg_get_logged_in_user_guid())) {
            if ($ent->disable()) {
                ElggCoffee::update_site_trigger(COFFEE_SITE_FEED_UPDATE);
                return true;
                //find a solution to trigger a refresh for other opened session
            }
        }
        throw new SecurityException(elgg_echo('SecurityException:cantremoveobject'));
    }

    public static function disable_annotation ($id) {
        $annotation = get_annotation($id);
        if ($annotation instanceof ElggAnnotation
                && (elgg_is_admin_logged_in() || $annotation->owner_guid == elgg_get_logged_in_user_guid())) {
            if ($annotation->disable()) {
                static::update_entity_time($annotation->entity_guid);
                ElggCoffee::update_site_trigger(COFFEE_SITE_FEED_UPDATE);
                return true;
            }
        }
        throw new SecurityException(elgg_echo('SecurityException:cantremoveannotation'));
    }

    public static function upload_data () {
        if (empty($_FILES['upload']['name'])) return false;
        $file = new FilePluginFile();
        $file->subtype = "file";
        $file->title = $_FILES['upload']['name'];
        $file->access_id = COFFEE_DEFAULT_ACCESS_ID;
        $prefix = "file/";
        $filestorename = elgg_strtolower(time().$_FILES['upload']['name']);
        $mime_type = $file->detectMimeType($_FILES['upload']['tmp_name'], $_FILES['upload']['type']);
        $file->setFilename($prefix . $filestorename);
        $file->setMimeType($mime_type);
        $file->originalfilename = $_FILES['upload']['name'];
        $file->simpletype = file_get_simple_type($mime_type);
        // Open the file to guarantee the directory exists
        $file->open("write");
        $file->close();
        move_uploaded_file($_FILES['upload']['tmp_name'], $file->getFilenameOnFilestore());
        $guid = $file->save();
        //add_to_river('river/object/file/create', 'create', elgg_get_logged_in_user_guid(), $file->guid);
        if ($guid && $file->simpletype == "image") {
            $file->icontime = time();
            $thumbnail = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 60, 60, true);
            if ($thumbnail) {
                $thumb = new ElggFile();
                $thumb->setMimeType($_FILES['upload']['type']);

                $thumb->setFilename($prefix."thumb".$filestorename);
                $thumb->open("write");
                $thumb->write($thumbnail);
                $thumb->close();

                $file->thumbnail = $prefix."thumb".$filestorename;
                unset($thumbnail);
            }

            $thumbsmall = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 153, 153, true);
            if ($thumbsmall) {
                $thumb->setFilename($prefix."smallthumb".$filestorename);
                $thumb->open("write");
                $thumb->write($thumbsmall);
                $thumb->close();
                $file->smallthumb = $prefix."smallthumb".$filestorename;
                unset($thumbsmall);
            }

            $thumblarge = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 600, 600, false);
            if ($thumblarge) {
                $thumb->setFilename($prefix."largethumb".$filestorename);
                $thumb->open("write");
                $thumb->write($thumblarge);
                $thumb->close();
                $file->largethumb = $prefix."largethumb".$filestorename;
                unset($thumblarge);
            }
        }
        $file->save();
        $guid = $file->guid;
        unset($file,$thumb);
        echo json_encode(array('status' => 1, 'result' => ElggCoffee::_get_file_details($guid)));
        exit();
    }

    public static function get_url_data ($url) {
        $file = elgg_get_entities_from_metadata(array('metadata_names' => 'url', 'metadata_values' => $url));
        if ($file) {
            return ElggCoffee::_get_file_details($file[0]->guid);
        }
        require_once elgg_get_plugins_path() . "coffee/vendors/link_data/Embedly.php";
        require_once elgg_get_plugins_path() . "coffee/vendors/link_data/EmbedUrl.php";
        $return = false;
        $duration = false;
        $type = 'url';
        if (preg_match("/\.(bmp|jpeg|gif|png|jpg|pdf)$/i", $url)) {
            $title = parse_url($url,PHP_URL_PATH);
            $title = basename($title);
            $return = array('title' => $title
                                , 'thumbnail' => $url);
            $type = 'url_document';
        } elseif (preg_match("/(dailymotion|vimeo|youtu|slide|scrib)/", $url)) {
            $type = 'url_media';
            try {
                $api = new Embedly_API(array('user_agent' => 'Mozilla/5.0 (compatible; embedly/example-app; support@embed.ly)'));
                $oembed = $api->oembed(array('url' => $url, 'maxwidth' => 530));
                if (!isset($oembed[0]->error_code)) {
                    $return = array('title' => $oembed[0]->title
                                    , 'description' => $oembed[0]->description
                                    , 'thumbnail' => $oembed[0]->thumbnail_url
                                    , 'width' => $oembed[0]->width
                                    , 'height' => $oembed[0]->height
                                    , 'html' => $oembed[0]->html);
                }
            } catch (Exception $e) {}
        }
        if (is_null($return['title'])) {
            $type = 'url_article';
            require_once elgg_get_plugins_path() . "coffee/vendors/Readability.inc.php";
            $embedUrl = new Embed_url(array('url' => $url));
            $embedUrl->embed();
            //Readability
            $readability = new Readability($embedUrl->html, $embedUrl->encoding);
            $content = $readability->getContent();
            $return = array('title' => $embedUrl->title
                                    , 'description' => $embedUrl->description
                                    , 'thumbnail' => $embedUrl->sortedImage[0]
                                    , 'html' => $content['content']);
        }
        if (is_array($return)) {
            $youtubeData = getYoutubeData($url);
            if (preg_match("/(youtu)/", $url) && isset($youtubeData['id'])) {
                    $type = 'youtube';
            }
            $link = new ElggObject();
            $link->subtype = COFFEE_LINK_SUBTYPE;
            $link->access_id = COFFEE_DEFAULT_ACCESS_ID;
            $link->title = $return['title'];
            $link->description = $return['description'];
            $link->thumbnail = $return['thumbnail'];
            $link->simpletype = $type;
            $link->html = $return['html'];
            $link->url = $url;
            if ($youtubeData['duration']) $link->duration = $youtubeData['duration'];
            if ($youtubeData['id']) $link->video_id = $youtubeData['id'];
            $link->save();
            $return['guid'] = $link->guid;
        }
        return $return;
    }

    public static function send_new_password ($username) {
        if (strpos($username, '@') !== false && ($users = get_user_by_email($username))) {
            $username = $users[0]->username;
        }
        $user = get_user_by_username($username);
        if ($user) {
            // generate code
            $code = generate_random_cleartext_password();
            $user->setPrivateSetting('passwd_conf_code', $code);
            // generate link
            $url = parse_url($_SERVER['HTTP_REFERER']);

            $link = $url['scheme'] . '://' . $url['host'] . "/#resetPassword/" . $user->guid . "/$code";
            // generate email
            $email = elgg_echo('email:resetreq:body', array($user->name, $_SERVER['REMOTE_ADDR'], $link));

            return notify_user($user->guid, $GLOBALS['CONFIG']->site->guid,
                elgg_echo('email:resetreq:subject'), $email, NULL, 'email');
        }

        return false;
    }

    public static function reset_password ($user_guid = false, $code = false) {
        return execute_new_password_request($user_guid, $code);
    }

    public static function upload_user_avatar ($square = false) {
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] != 0) return false;
        $guid = elgg_get_logged_in_user_guid();
        $owner = get_entity($guid);
        $icon_sizes = elgg_get_config('icon_sizes');
        $files = array();
        foreach ($icon_sizes as $name => $size_info) {
            $resized = get_resized_image_from_uploaded_file('avatar', $size_info['w'], $size_info['h'], $size_info['square'], $size_info['upscale']);
            if ($resized) {
                //@todo Make these actual entities.  See exts #348.
                $file = new ElggFile();
                $file->owner_guid = $guid;
                $file->setFilename("profile/{$guid}{$name}.jpg");
                $file->open('write');
                $file->write($resized);
                $file->close();
                $files[] = $file;
            } else {
                // cleanup on fail
                foreach ($files as $file) {
                    $file->delete();
                }
            }
        }

        // reset crop coordinates
        $owner->x1 = 0;
        $owner->x2 = 0;
        $owner->y1 = 0;
        $owner->y2 = 0;

        $owner->icontime = time();
        if (elgg_trigger_event('profileiconupdate', $owner->type, $owner)) {
            $view = 'river/user/default/profileiconupdate';
            elgg_delete_river(array('subject_guid' => $owner->guid, 'view' => $view));
            //add_to_river($view, 'update', $owner->guid, $owner->guid);
        }
        if (is_array($square)
                && isset($square['x1'])
                && isset($square['x2'])
                && isset($square['y1'])
                && isset($square['y2'])
                ) {
            $filehandler = new ElggFile();
            $filehandler->owner_guid = $owner->getGUID();
            $filehandler->setFilename("profile/" . $owner->guid . "master" . ".jpg");
            $filename = $filehandler->getFilenameOnFilestore();
            $files = array();
            foreach ($icon_sizes as $name => $size_info) {
                $resized = get_resized_image_from_existing_file($filename, $size_info['w'], $size_info['h'], $size_info['square'], $square['x1'], $square['y1'], $square['x2'], $square['y2'], $size_info['upscale']);
                if ($resized) {
                    //@todo Make these actual entities.  See exts #348.
                    $file = new ElggFile();
                    $file->owner_guid = $guid;
                    $file->setFilename("profile/{$guid}{$name}.jpg");
                    $file->open('write');
                    $file->write($resized);
                    $file->close();
                    $files[] = $file;
                } else {
                    // cleanup on fail
                    foreach ($files as $file) {
                        $file->delete();
                    }
                }
            }

            $owner->icontime = time();

            $owner->x1 = $square['x1'];
            $owner->x2 = $square['x2'];
            $owner->y1 = $square['y1'];
            $owner->y2 = $square['y2'];

        }

        echo json_encode(array('status' => 1, 'result' => ElggCoffee::_get_user_icon_url($owner)));
        exit();
    }

    public static function upload_user_cover() {
        if (!isset($_FILES['cover']) || $_FILES['cover']['error'] != 0) return false;
        $guid = elgg_get_logged_in_user_guid();
        $owner = get_user($guid);
        $file = new ElggFile();
        $file->owner_guid = $guid;
        $file->setFilename("cover/{$guid}.jpg");
        $file->open('write');
        $file->close();
        $file->save();
        $owner->covertime = time();
        $owner->save();
        move_uploaded_file($_FILES['cover']['tmp_name'], $file->getFilenameOnFilestore());
        echo json_encode(array('status' => 1, 'result' => ElggCoffee::_get_user_cover_url($owner)));
        exit();
    }

    public static function set_user_extra_info ($name, $value, $guid = false) {
        $value = strip_tags($value,'<br><br/><em><strong>');
        if ($guid == false) {
            $guid = elgg_get_logged_in_user_guid();
        } elseif ($guid && !elgg_is_admin_logged_in()) {
            return false;
        }
        $user_ent = get_user($guid);
        if ($user_ent instanceof ElggUser && strlen($value)>0) {
            $user_ent->$name = $value;
            if ($user_ent->save()) {
                return $value;
            }
        }
        return false;
    }

    public static function get_user_extra_info ($names, $guid = false) {
        $guid = $guid ? $guid:elgg_get_logged_in_user_guid();
        $user_ent = get_user($guid);
        $names = is_array($names)?$names:array($names);
        if ($user_ent instanceof ElggUser) {
            $return = array();
            foreach ($names as $name) {
                if (isset( $user_ent->$name)) {
                    $return[$name] = nl2br($user_ent->$name);
                }
            }
            return $return;
        }
        return array();
    }

    public static function edit_user_detail($language = false, $name = false, $curent_password = false, $password = false) {
        set_input('guid', elgg_get_logged_in_user_guid());
        set_input('language', $language);
        set_input('current_password', $curent_password);
        set_input('password', $password);
        set_input('password2', $password);
        set_input('name', $name);
        $return = true;
        if ($language) {
            $return = elgg_set_user_language();
        }
        if ($name) {
            $return = elgg_set_user_name();
        }
        if ($password && $curent_password) {
            $return = elgg_set_user_password();
        }
        if (!$return && !is_null($return)) {
            throw new Exception ($_SESSION['msg']['error'][0]);
        }
        return true;
    }

    public static function get_user_list ($query, $offset = 0, $limit = 10) {
        $return = array();
        $results = search_users($query, $offset, $limit);
        $return['count'] = $results['count'];
        if ($return['count'] > 0) {
            $return['users'] = array();
            foreach ($results['entities'] as $key => $user) {
                if ($user instanceof ElggUser) {
                    $return['users'][$key] = array (
                        'id' => $user->guid
                        , 'username' => $user->username
                        , 'email' => $user->email
                        , 'name' => $user->name
                        , 'avatar' => ElggCoffee::_get_user_icon_url($user,'medium')
                        , 'icon_url_small' => ElggCoffee::_get_user_icon_url($user,'small')
                        , 'cover_url' => ElggCoffee::_get_user_cover_url($user)
                        , 'type' => 'user'
                        , 'profile' => ElggCoffee::get_user_extra_info(array('headline','cellphone','phone','location'),$user->guid)
                        , 'count' => $return['count']

                    );
                }
            }
        }
        return $return['users'];

    }

    public static function register_user ($displayname, $email, $password, $password2, $language, $make_admin=false, $send_email=false) {
        admin_gatekeeper();
        $logged_in_user = elgg_get_logged_in_user_entity();
        $username = str_replace(array("-","_",".","@","+"), '', $email);
        $guid = register_user(
                    $username,
                    $password,
                    $displayname,
                    $email
                    );
        if ($guid) {
            $user = get_user($guid);
            if ($user instanceof ElggUser) {
                $user->language = $language;
                if ($make_admin == '1') {
                    $user->makeAdmin();
                }
                $user->save();
                if ($send_email) {
                    $site_guid = $GLOBALS['CONFIG']->site_guid;
                    $site_ent = get_entity($site_guid);
                    $subject = elgg_echo('useradd:subject', array($displayname),$language);
                    $body = elgg_echo('useradd:body', array(
                        $displayname,
                        $site_ent->name,
                        $site_ent->name,
                        $email,
                        $password,
                        $logged_in_user->name,
                    ),$language);

                    notify_user($user->guid, $site_guid, $subject, $body);
                }
            }
        }

        return array('guid' => $guid);
    }

    public static function edit_site_settings ($language) {
        admin_gatekeeper();
        $return = array();
        if (is_array($_FILES)) {
            foreach ($_FILES as $name=>$values) {
                if ($values["error"]==0) {
                    $file = new FilePluginFile();
                    $file->subtype = "file";
                    $file->title = $name;
                    $file->access_id = COFFEE_DEFAULT_ACCESS_ID;
                    $prefix = "file/";
                    $filestorename = elgg_strtolower(time().$values['name']);
                    $mime_type = $values['type'];
                    $file->setFilename($prefix . $filestorename);
                    $file->setMimeType($mime_type);
                    $file->originalfilename = $values['name'];
                    $file->simpletype = file_get_simple_type($mime_type);
                    // Open the file to guarantee the directory exists
                    $file->open("write");
                    $file->close();
                    move_uploaded_file($values['tmp_name'], $file->getFilenameOnFilestore());
                    $guid = $file->save();
                    if ($guid) {
                        $return[] = array($name => ElggCoffee::_get_dwl_url($file->guid));
                    }
                }
            }
        }
        $site_guid = $GLOBALS['CONFIG']->site_guid;
        $site_ent = get_entity($site_guid);
        if ($site_ent instanceof ElggSite) {
            if (set_config('language', $language, $site_ent->getGUID())) {
                $return[] = array('language' => $language);
            }
        }
        return $return;
    }

   public static function ban_user ($guid) {
        admin_gatekeeper();
        $user_ent = get_user($guid);
        if ($user_ent instanceof ElggUser) {
            //return $user_ent->ban();
            return $user_ent->delete();
        }
        return false;
    }

    public static function get_tags ($query=false, $offset=0, $limit=10) {

        $db_prefix = elgg_get_config('dbprefix');
        $tags_meta_id = get_metastring_id('tags')?get_metastring_id('tags'):0;
        $query = sanitise_string($query);
        if ($query) {
            $where = "And tag_name.string Like '%$query%'";
        }
        $query = "Select  tag_name.string as tag
                            , tag_name.id as tag_id
                            , count(tag_used.id)
                    From {$db_prefix}metadata tag_used
                        Inner Join {$db_prefix}metastrings tag_name On tag_used.value_id = tag_name.id And tag_used.name_id = $tags_meta_id
                        Inner Join {$db_prefix}entities ent On tag_used.entity_guid = ent.guid And ent.site_guid = " . $GLOBALS['CONFIG']->site_guid . "
                    Where tag_name.string != ''
                    $where
                    Group By tag_name.string
                    Order By count(tag_used.id) Desc
                    Limit $offset,$limit";
       $result = get_data($query);
       if ($result) {
           foreach ($result as $tags) {
               if (is_object($tags)) {
                $return[] = array('id' => $tags->id
                                     , 'type' => 'tag'
                                     , 'name' => '#'. $tags->tag);
               }
           }
           return $return;
       }

       return false;
    }

    public static function get_corporate_tags () {
        $return = false;
        $site_guid = $GLOBALS['CONFIG']->site_guid;
        $site_ent = get_entity($site_guid);
        if ($site_ent instanceof ElggSite) {
            $corporateTags = $site_ent->corporate_tags;
            if (is_array($corporateTags)) {
                foreach ($corporateTags as $key=>$tag) {
                    $return[] = array('id' =>$key
                                    , 'type' => 'tag'
                                    , 'name' => $tag);
                }
            } else {
                $return[] = array('id' =>0
                                    , 'type' => 'tag'
                                    , 'name' => $corporateTags);
            }
        }
        return $return;
    }

    public static function set_corporate_tags ($tags = array()) {
        $site_guid = $GLOBALS['CONFIG']->site_guid;
        $site_ent = get_entity($site_guid);
        if ($site_ent instanceof ElggSite && is_array($tags)) {
            $site_ent->corporate_tags = $tags;
            if ($site_ent->save()) {
                ElggCoffee::update_site_trigger(COFFEE_CORPORATE_TAGS_UPDATE);
                return true;
            }
        }
        return false;
    }

    public static function get_translation_table ($locale) {
        if (!require elgg_get_plugins_path() . 'coffee/languages/' . $locale . '.php') {
            require elgg_get_plugins_path() . 'coffee/languages/en.php';
        }
        return $translation;
    }

    public static function get_site_trigger ($type = array(COFFEE_SITE_FEED_UPDATE)) {
        $return = false;
        $site_guid = $GLOBALS['CONFIG']->site_guid;
        $site_ent = get_entity($site_guid);
        $user_ent = elgg_get_logged_in_user_entity();
        if ($site_ent instanceof ElggSite && is_array($type)) {
            foreach ($type as $key=>$type) {
                $return[$key] = array($type => $site_ent->$type);
            }
        }
        $return[++$key] = array('system_update' => datalist_get('simplecache_lastupdate_default'));
        $return[++$key] = array('corporate_tags_update' => $site_ent->corporate_tags_update);
        $return[++$key] = array('lastNotifChecked' => $user_ent->lastNotifChecked);
        return $return;
    }

    public static function get_tv_post () {
        $return = array();
        $return = ElggCoffee::get_tv_channel();
        if (empty($return['feed_data'])) {
            $user_ent = elgg_get_logged_in_user_entity();
            if ($user_ent instanceof ElggUser) {
                $tv_filters = $user_ent->tvAppSettings;
                $tv_filters = json_decode($tv_filters);
                $tv_filters_tags = $tv_filters->tags;
                $tv_filters_users = $tv_filters->users;
            }
            $return['site_data'] = ElggCoffee::get_site_data();
            $return['posts'] = ElggCoffee::get_posts(0,0,10,$tv_filters_users,FALSE,FALSE,$tv_filters_tags);
        } else {
            $items = count($return['feed_data'])-1;
            $randomChannel = rand(0,$items);
            if ($return['feed_data'][$randomChannel]['feed_type'] === 'static_url') {
                return ElggCoffee::get_tv_post();
            }
            $return['posts'] = $return['feed_data'][$randomChannel]['feeds'];
            unset($return['feed_data']);
            //In case of to many post (>10)
            if (count($return['posts']) > 9) {
                $tmp = array_chunk($return['posts'],10,true);
                $return['posts'] = $tmp[0];
            }
        }
        return $return;
    }

    public static function get_tv_channel () {
        global $CONFIG;
        elgg_set_context('api/tv');
        $return = array();
        $user_ent = elgg_get_logged_in_user_entity();
        if ($user_ent instanceof ElggUser) {
            $tv_channels = $user_ent->tvChannelsSettings;
            $tv_channels = json_decode($tv_channels);
        }
        $return['site_data'] = ElggCoffee::get_site_data();
        $return['feed_data'] = array();
        $backgroundGallery = ElggCoffee::get_posts(0, 0, 10, false, false, false, array('backgroundChatter','backgroundGallery'));
        $backgrounds[] = $return['site_data']['background_url'];
        if (is_array($backgroundGallery)) {
            foreach ($backgroundGallery as $key => $background) {
                $backgrounds[] = $background['attachment'][0]['url'];
            }
        }
        $i = 0;
        foreach ($tv_channels as $key => $channel) {
            $return['feed_data'][$i]['feed_name'] = $channel->ChannelName;
            $return['feed_data'][$i]['feed_id'] = $channel->ChannelName . '_' . $key;
            $return['feed_data'][$i]['feed_type'] = 'social_feed';
            $return['feed_data'][$i]['feed_url_icon'] = 'http://cdn.coffeepoke.com/static/img/connector/' . strtolower($channel->ChannelName) . '_small.png';
            $return['feed_data'][$i]['feed_url_background'] = 'http://cdn.coffeepoke.com/static/img/connector/' . strtolower($channel->ChannelName) . '_big.png';
            switch ($channel->ChannelName) {
                case 'Twitter':
                    if (!class_exists($channel->ChannelName)) _elgg_autoload($channel->ChannelName);
                    $feed = new Twitter($channel->consumer_key, $channel->consumer_secret);
                    $feed->setOAuthToken($channel->oauth_token);
                    $feed->setOAuthTokenSecret($channel->oauth_token_secret);
                    $post = $feed->searchTweets($channel->query,null,null,null,'recent',10,false,false,false,true);
                    //$post = $feed->statusesUserTimeline(null,'antoinepic',null,10);
                    if (is_array($post['statuses'])) {
                        foreach ($post['statuses'] as $key=>$row) {
                            $crawled = false;
                            if(isset($row['entities']['media'])) {
                                $crawled = array(
                                                'time_created' => $row['created_at']
                                                , 'friendly_time' => $row['created_at']
                                                , 'title' => $row['entities']['media'][0]['media_url']
                                                , 'description' => ''
                                                , 'html' => null
                                                , 'type' => 'image'
                                                , 'mime' => 'image/jpg'
                                                , 'url' => $row['entities']['media'][0]['media_url']
                                                , $backgrounds[rand(0,count($backgrounds)-1)] . '/2000x2000/');
                                                //, 'thumbnail' => $row['entities']['media'][0]['media_url']. ':thumb');
                            }
                            if(is_array($row['entities']['urls']) && isset($row['entities']['urls'][0]['expanded_url'])) {
                               $crawled = ElggCoffee::get_url_data($row['entities']['urls'][0]['expanded_url']);
                               $crawled['thumbnail'] = $backgrounds[rand(0,count($backgrounds)-1)] . '/2000x2000/';
                            }
                            $return['feed_data'][$i]['feeds'][$key] = format_post_array($row['text']
                                                                            , $row['created_at']
                                                                            , $row['user']['id']
                                                                            , $row['user']['screen_name']
                                                                            , $row['user']['name']
                                                                            , str_replace('_normal','', $row['user']['profile_image_url'])
                                                                            , $row['user']['profile_background_image_url']
                                                                            , $row['user']['description']
                                                                            , $crawled);
                        }
                    }
                    break;
                case 'Chatter':
                    define("SOAP_CLIENT_BASEDIR", elgg_get_plugins_path().'coffee/vendors/external_api/chatter/soapclient');
                    require_once (SOAP_CLIENT_BASEDIR.'/SforcePartnerClient.php');
                    require_once (SOAP_CLIENT_BASEDIR.'/SforceHeaderOptions.php');
                    $mySforceConnection = new SforcePartnerClient();
                    $mySoapClient = $mySforceConnection->createConnection(SOAP_CLIENT_BASEDIR.'/partner.wsdl.xml');
                    $mylogin = $mySforceConnection->login($channel->user_login,$channel->password );
                    $sessionId = $mySforceConnection->getSessionId();
                    $result = $mySforceConnection->query($channel->query);
                    if (is_array($result->records)) {
                        foreach ($result->records as $key=>$row) {
                            $FeedIem = new SObject($row);
                            $comments = array();
                            foreach($FeedIem->queryResult[0]->records As $comment) {
                                $FeedComments = new SObject($comment);
                                $user = $mySforceConnection->query('Select FullPhotoUrl From User Where Id =\'' . $FeedComments->fields->CreatedById . '\'');
                                $user = new SObject($user->records[0]);
                                $comments[] = array('owner_guid' => $FeedComments->fields->CreatedById
                                                        , 'display_name' => $FeedComments->fields->CreatedBy->fields->Name
                                                        , 'icon_url' => $user->fields->FullPhotoUrl . '?oauth_token=' . $sessionId
                                                        , 'time_created' => $FeedComments->fields->CreatedDate
                                                        , 'text' => $FeedComments->fields->CommentBody);
                            }
                            $user = $mySforceConnection->query('Select FullPhotoUrl From User Where Id =\'' . $FeedIem->fields->CreatedById . '\'');
                            $user = new SObject($user->records[0]);
                            $crawled = false;
                            /*if(isset($row['entities']['media'])) {
                                $crawled = array(
                                                'time_created' => $row['created_at']
                                                , 'friendly_time' => $row['created_at']
                                                , 'title' => $row['entities']['media'][0]['media_url']
                                                , 'description' => ''
                                                , 'html' => null
                                                , 'type' => 'image'
                                                , 'mime' => 'image/jpg'
                                                , 'url' => $row['entities']['media'][0]['media_url']
                                                , 'thumbnail' => $row['entities']['media'][0]['media_url']. ':thumb');
                            }*/
                            if(isset($FeedIem->fields->LinkUrl)) {
                               $crawled = ElggCoffee::get_url_data($FeedIem->fields->LinkUrl);
                               $crawled['thumbnail'] = $backgrounds[rand(0,count($backgrounds)-1)] . '/2000x2000/';
                            }
                            $return['feed_data'][$i]['feeds'][$key] = format_post_array($FeedIem->fields->Body
                                                                            , $FeedIem->fields->CreatedDate
                                                                            , $FeedIem->fields->CreatedById
                                                                            , $FeedIem->fields->CreatedBy->Name
                                                                            , $FeedIem->fields->CreatedBy->Name
                                                                            , $user->fields->FullPhotoUrl . '?oauth_token=' . $sessionId
                                                                            , $backgrounds[rand(0,count($backgrounds)-1)] . '/2000x2000/'
                                                                            , false
                                                                            , $crawled
                                                                            , format_post_comments($comments));
                        }
                    }
                    break;
                case 'Facebook':
                    if (!class_exists($channel->ChannelName)) _elgg_autoload($channel->ChannelName);
                    $feed = new Facebook(array('appId'  => $channel->app_id,'secret' => $channel->app_secret));
                    $post = $feed->api($channel->query, array('access_token' => $channel->access_token,'limit'=>10));
                    if (is_array($post['data'])) {
                        foreach ($post['data'] as $key=>$row) {
                            $crawled = false;
                            switch ($row['type']) {
                                case 'photo':
                                    $attachment  = $feed->api('/' . $row['object_id'] . '?fields=images', array('access_token' => $channel->access_token));
                                    $crawled = array(
                                                        'time_created' => $attachment['created_time']
                                                        , 'friendly_time' => $attachment['created_time']
                                                        , 'title' => $row['name']
                                                        , 'description' => $row['caption']
                                                        , 'html' => null
                                                        , 'type' => 'image'
                                                        , 'mime' => 'image/jpg'
                                                        , 'url' => $attachment['images'][0]['source']
                                                        , 'thumbnail' => $row['picture']);
                                    break;
                                case 'video':
                                    $youtubeData = getYoutubeData($row['source']);
                                    $crawled = array(
                                                        'time_created' => $row['created_time']
                                                        , 'friendly_time' => $row['created_time']
                                                        , 'title' => $row['name']
                                                        , 'description' => $row['description']
                                                        , 'html' => null
                                                        , 'type' => (preg_match("/(youtu)/", $row['source']) && isset($youtubeData['id']))?'youtube':'url_media'
                                                        , 'duration' => $youtubeData['duration']
                                                        , 'video_id' => $youtubeData['id']
                                                        , 'mime' => null
                                                        , 'url' => $row['source']
                                                        , 'thumbnail' => $row['picture']);
                                    break;
                                case 'link':
                                    $crawled = array(
                                                        'time_created' => $row['created_time']
                                                        , 'friendly_time' => $row['created_time']
                                                        , 'title' => $row['name']
                                                        , 'description' => $row['description']
                                                        , 'html' => null
                                                        , 'type' => 'url_article'
                                                        , 'mime' => null
                                                        , 'url' => $row['link']
                                                        , 'thumbnail' => $backgrounds[rand(0,count($backgrounds)-1)] . '/2000x2000/');
                                                        //, 'thumbnail' => $row['picture']);
                                    break;

                            }
                            $profile  = $feed->api('/' . $row['from']['id'] . '?fields=picture,cover', array('access_token' => $channel->access_token));

                            $return['feed_data'][$i]['feeds'][$key] = format_post_array($row['message']
                                                                            , $row['created_time']
                                                                            , $row['from']['id']
                                                                            , $row['from']['name']
                                                                            , $row['from']['name']
                                                                            , $profile['picture']['data']['url']
                                                                            , $profile['cover']['source']
                                                                            , false
                                                                            , $crawled);
                        }
                    }
                    break;
                case 'Yammer':
                    if (!class_exists($channel->ChannelName)) _elgg_autoload($channel->ChannelName);
                    $feed = new Yammer(array('consumer_key'  => $channel->consumer_key,'consumer_secret' => $channel->consumer_secret,'oauth_token' => $channel->oauth_token));
                    $post = $feed->get($channel->query);
                    if (is_array($post->messages)) {
                        foreach ($post->messages as $key=>$row) {
                            $profile  = $feed->get('/users/' . $row->sender_id . '.json');
                            $return['feed_data'][$i]['feeds'][$key] = format_post_array($row->body->plain
                                                                            , $row->created_at
                                                                            , $row->sender_id
                                                                            , $profile->name
                                                                            , $profile->full_name
                                                                            , $profile->mugshot_url
                                                                            , false);
                        }
                    }
                    break;
                case 'BlueKiwi':
                    $super_token = $channel->super_token;
                    // Sign the request
                    $time = time();
                    $strParams = "access_token=".$super_token."&oauth_timestamp=".$time;
                    $signature = sha1($channel->app_id."&".$strParams."&".$channel->app_secret);
                    $data['access_token'] = $super_token;
                    //$data['a_parameter'] = 'parameter_value';
                    $data['oauth_timestamp'] = $time;
                    $data['oauth_signature'] = $signature;
                    $data_stream = $channel->url . $channel->query . '?' . http_build_query($data);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $data_stream);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $post = @json_decode(curl_exec($ch));

                    if (is_array($post->items)) {
                        foreach ($post->items as $key=>$row) {
                            $return['feed_data'][$i]['feeds'][$key] = format_post_array($row->object->content
                                                                            , $row->object->published
                                                                            , $row->actor->id
                                                                            , $row->actor->displayName
                                                                            , $row->actor->displayName
                                                                            , $row->actor->image->url
                                                                            , false);
                        }
                    }
                    break;

                case 'CoffeePoke':
                    $tags = $owner_guid = array();
                    $tv_filters_users = $channel->users;
                    foreach ($tv_filters_users as $user) {
                        $owner_guid [] = $user->id;
                    }
                    $tv_filters_tags = $channel->tags;
                    foreach ($tv_filters_tags as $tag) {
                        $tags [] = $tag->name;
                    }
                    foreach (ElggCoffee::get_posts(0,0,$channel->limit?$channel->limit:10,$owner_guid,($channel->broadcastMessages?array(COFFEE_SUBTYPE_BROADCAST_MESSAGE):false),FALSE,$tags) as $key=>$row) {
                        $row['user']['cover_url'] = str_replace("?icontime", "/2000x2000?icontime", $row['user']['cover_url']);
                        if(is_array($row['attachment'])) {
                            if ($row['attachment'][0]['type'] === 'image') {
                                $row['attachment'][0]['url'] .= "/2000x2000/";
                            }
                            $row['attachment'][0]['thumbnail'] = $backgrounds[rand(0,count($backgrounds)-1)] . '/2000x2000/';
                        }
                        $return['feed_data'][$i]['feeds'][$key] = $row;
                    }
                    break;
                case 'StaticURL':
                    $return['feed_data'][$i]['feed_type'] = 'static_url';
                    $return['feed_data'][$i]['feeds'] = array('url' => $channel->staticURL, 'duration' => $channel->duration, 'display_name' => $channel->displayName);
                    break;
                default:
                    break;
            }
            if(count($return['feed_data'][$i]['feeds']) === 0) {
                unset($return['feed_data'][$i]);
            } else {
                $i++;
            }
        }
        return $return;
    }

    public static function get_notifications ($until, $limit = 10) {
        $user_guid = elgg_get_logged_in_user_guid();
        $return = array();
        $query = "
        (
             Select from_unixtime(rel.time_created) as created,'notification::like' as action, rel.guid_one as user, ent.guid as entity
             From {$GLOBALS['CONFIG']->dbprefix}entity_relationships rel
             Inner Join {$GLOBALS['CONFIG']->dbprefix}entities ent On rel.guid_two = ent.guid
             Where ent.owner_guid = $user_guid
             And ent.enabled = 'yes'
             And ent.site_guid = {$GLOBALS['CONFIG']->site_guid}
             And rel.guid_one != $user_guid
             And rel.relationship = '" . COFFEE_LIKE_RELATIONSHIP . "'
             Order By rel.time_created Desc
             Limit $limit
         )
             Union All
         (
             Select from_unixtime(a.time_created) as created,'notification::comment' as action, a.owner_guid as user, a.id as entity
             From {$GLOBALS['CONFIG']->dbprefix}annotations a
             Inner Join {$GLOBALS['CONFIG']->dbprefix}entities ent On a.entity_guid = ent.guid
             Where ent.owner_guid = $user_guid
             And ent.enabled = 'yes'
             And ent.site_guid = {$GLOBALS['CONFIG']->site_guid}
             And a.owner_guid != $user_guid
             Order By a.time_created Desc
             Limit $limit
         )
             Union All
         (
             Select from_unixtime(ent.time_created) as created,'notification::post::mentioned' as action, ent.owner_guid as user, ent.guid as entity
             From {$GLOBALS['CONFIG']->dbprefix}entity_relationships rel
             Inner Join {$GLOBALS['CONFIG']->dbprefix}entities ent On rel.guid_one = ent.guid
             Where rel.guid_two = $user_guid
             And ent.enabled = 'yes'
             And ent.site_guid = {$GLOBALS['CONFIG']->site_guid}
             And rel.relationship = '" . COFFEE_POST_MENTIONED_RELATIONSHIP . "'
             Order By ent.time_created Desc
             Limit $limit
         )
             Union All
         (
             Select from_unixtime(a.time_created) as created,'notification::comment::mentioned' as action, a.owner_guid as user, a.id as entity
             From {$GLOBALS['CONFIG']->dbprefix}entity_relationships rel
             Inner Join {$GLOBALS['CONFIG']->dbprefix}annotations a On rel.guid_one = a.entity_guid
             Where rel.guid_two = $user_guid
             And a.owner_guid != $user_guid
             And a.enabled = 'yes'
             And rel.relationship = '" . COFFEE_COMMENT_MENTIONED_RELATIONSHIP . "'
             Order By a.time_created Desc
             Limit $limit
         )
             Union All
         (

             Select from_unixtime(likes.time_created) as created,'notification::mentioned::liked' as action, likes.guid_one as user, ent.guid as entity
             From {$GLOBALS['CONFIG']->dbprefix}entity_relationships rel
             Inner Join {$GLOBALS['CONFIG']->dbprefix}entities ent On rel.guid_one = ent.guid
             Inner Join {$GLOBALS['CONFIG']->dbprefix}entity_relationships likes On likes.guid_two = ent.guid And likes.relationship = '" . COFFEE_LIKE_RELATIONSHIP . "'
             Where rel.guid_two = $user_guid
             And ent.enabled = 'yes'
             And ent.site_guid = {$GLOBALS['CONFIG']->site_guid}
             And likes.guid_one != $user_guid
             And rel.relationship = '" . COFFEE_POST_MENTIONED_RELATIONSHIP . "'
             Order By likes.time_created Desc
             Limit $limit
         )
         Union All
         (
             Select from_unixtime(a.time_created) as created,   'notification::post::mention::comment' as action, a.owner_guid as user, a.id as entity
             From {$GLOBALS['CONFIG']->dbprefix}entity_relationships rel
             Inner Join {$GLOBALS['CONFIG']->dbprefix}entities ent On rel.guid_one = ent.guid
             Inner Join {$GLOBALS['CONFIG']->dbprefix}annotations a On rel.guid_one = a.entity_guid
             Where rel.guid_two = $user_guid
             And ent.enabled = 'yes'
             And ent.site_guid = {$GLOBALS['CONFIG']->site_guid}
             And rel.relationship = '" . COFFEE_POST_MENTIONED_RELATIONSHIP . "'
             And a.owner_guid != $user_guid
             Order By a.time_created Desc
             Limit $limit
         )
         Union All
         (
             Select Distinct from_unixtime(also.time_created) as created,'notification::comment::alsocommented' as action, also.owner_guid as user, also.id as entity
             From {$GLOBALS['CONFIG']->dbprefix}annotations a
             Inner Join {$GLOBALS['CONFIG']->dbprefix}annotations also On a.entity_guid = also.entity_guid
             Inner Join {$GLOBALS['CONFIG']->dbprefix}entities ent On a.entity_guid = ent.guid And ent.owner_guid != $user_guid And also.time_created > a.time_created
             Where also.owner_guid != $user_guid
             And a.owner_guid = $user_guid
             And a.enabled = 'yes'
             Order By also.time_created Desc
             Limit $limit
         )
         Union All
         (
             Select from_unixtime(also.time_created) as created,'notification::like::alsoliked' as action, also.guid_one as user, rel.guid_two as entity
             From {$GLOBALS['CONFIG']->dbprefix}entity_relationships rel
             Inner Join {$GLOBALS['CONFIG']->dbprefix}entity_relationships also On rel.guid_two = also.guid_two And also.time_created > rel.time_created
             Where rel.guid_one = $user_guid
             And also.guid_one != $user_guid
             And rel.relationship = '" . COFFEE_LIKE_RELATIONSHIP . "'
             And also.relationship ='" . COFFEE_LIKE_RELATIONSHIP . "'
             Order By also.time_created Desc
             Limit $limit
         )
         Order By created Desc
         Limit $limit;";
        $notifications = get_data($query);
        if (is_array($notifications)) {
            foreach ($notifications as $key => $notification) {
                $user = get_user($notification->user);
                $text = '';
                switch ($notification->action) {
                    case 'notification::comment':
                    case 'notification::post::mention::comment':
                    case 'notification::comment::mentioned':
                    case 'notification::comment::alsocommented':
                        $annotation = get_annotation($notification->entity);
                        $entity = get_entity($annotation->entity_guid);
                        $created = $annotation->time_created;
                        break;
                    default:
                        $entity = get_entity($notification->entity);
                        $created = $entity->time_created;
                        break;
                }
                if ($entity instanceof ElggEntity) {
                    $guid = $entity->guid;
                    if (!empty($entity->title)) {
                        $text = $entity->title;
                    } else {
                        $coffeePokePost = ElggCoffee::get_post($guid);
                        if (isset($coffeePokePost[0]['attachment'])) {
                            $text = $coffeePokePost[0]['attachment'][0]['title'];
                        }
                    }
                    if (0 && $annotation instanceof ElggAnnotation) {
                        if (empty($text)) {
                           $text = $annotation->value;
                        } else {
                            $text .= "<br />\n - " . $annotation->value;
                        }
                    }
                }
                $return[$key] = array('guid' => $guid
                                        , 'owner_guid' => $entity->owner_guid
                                        , 'text' => $text
                                        , 'type' => $notification->action
                                        , 'time_created' => $created
                                        , 'friendly_time' => elgg_get_friendly_time($created));
                $return[$key]['user']['username']       = $user->username;
                $return[$key]['user']['name']           = $user->name;
                $return[$key]['user']['icon_url']       = ElggCoffee::_get_user_icon_url($user,'medium');
                $return[$key]['user']['icon_url_small'] = ElggCoffee::_get_user_icon_url($user,'small');
                unset($entity,$annotation);
            }
        }
        return $return;

    }
    private static function _add_attachment ($guid_parent, $attachment) {
        if (!is_array($attachment)) return false;
        $type = COFFEE_POST_ATTACHMENT_RELATIONSHIP;
        foreach ($attachment as $key => $guid_child) {
            add_entity_relationship ($guid_parent, $type, $guid_child);
        }
    }

    private static function _add_mentioned ($guid_parent, $mentioned_user, $type = COFFEE_POST_MENTIONED_RELATIONSHIP) {
        if (!is_array($mentioned_user)) return false;
        foreach ($mentioned_user as $key => $guid_child) {
            add_entity_relationship ($guid_parent, $type, $guid_child);
        }
    }

    private static function _get_user_icon_url ($entity,$size = 'medium') {
        if ($entity instanceof ElggUser) {
            return $GLOBALS['CONFIG']->url . 'userIcon/' . $GLOBALS['CONFIG']->auth_token . '/' . $entity->guid . '/' . $size . '?icontime=' . $entity->icontime;
        }
    }

    private static function _get_user_cover_url ($entity) {
        if ($entity instanceof ElggUser) {
            return $GLOBALS['CONFIG']->url . 'userCover/' . $GLOBALS['CONFIG']->auth_token . '/' . $entity->guid. '?icontime=' . $entity->covertime;
        }
    }

    private static function _get_dwl_url ($guid,$type) {
        if ($guid > 0) {
            switch ($type) {
                case 'document':
                    $url = $GLOBALS['CONFIG']->url . 'dwl/' . $GLOBALS['CONFIG']->auth_token . '/' . $guid;
                    break;
                case 'image':
                default:
                    $url = $GLOBALS['CONFIG']->url . 'dwl/' . $GLOBALS['CONFIG']->auth_token . '/' . $guid;
                    break;
            }
        return $url;
        }
    }

    private static function _get_thumbnail_url ($guid,$size='medium') {
        return $GLOBALS['CONFIG']->url . 'thumbnail/' . $GLOBALS['CONFIG']->auth_token . '/' . $guid . '/' . $size;
    }

    private static function _get_likes ($guid, $offset = 0, $limit = 3) {
        $likes = array();
        $likes = array();
        $likes['details'] = coffee_get_relationships($guid, COFFEE_LIKE_RELATIONSHIP, true , $offset, $limit);
        $options_like = array(
                        'relationship' => COFFEE_LIKE_RELATIONSHIP,
                        'relationship_guid' => $guid,
                        'inverse_relationship' => true,
                        'order_by' => 'time_created desc',
                        'limit' => $limit,
                        'offset' => $offset,
                        'site_guid' => $GLOBALS['CONFIG']->site_guid,
                        'count' => true
                    );
        $likes['count'] = elgg_get_entities_from_relationship($options_like);
        return $likes;
    }

    private static function _get_comments ($guid, $offset = 0, $limit = 2) {
        $post = get_entity($guid);
        $annotations['count'] = (int)$post->countAnnotations(COFFEE_COMMENT_TYPE);
        if ($annotations['count'] > 0) {
            $annotations['details'] = $post->getAnnotations(COFFEE_COMMENT_TYPE, $limit, $offset, 'desc');
        } else {
            $annotations['count'] = 0;
            $annotations['details'] = false;
        }
        return $annotations;
    }

    private static function update_entity_time ($guid) {
        $entity = get_entity($guid);
        if ($entity instanceof ElggEntity) {
            $entity->time_updated = time();
            $entity->save();
            return true;
        }
        return false;
    }

    private static function update_site_trigger ($type = COFFEE_SITE_FEED_UPDATE) {
        $site_guid = $GLOBALS['CONFIG']->site_guid;
        $site_ent = get_entity($site_guid);
        if ($site_ent instanceof ElggSite) {
            $site_ent->$type = time();
            if($site_ent->save()) {
                return true;
            }
        }
        return false;
    }

    private static function _get_file_details ($guid) {
        $attached_ent = get_entity($guid);
        //if (!$attached_ent instanceof ElggFile) return false;
        switch ($attached_ent->simpletype) {
            case 'url':
            case 'url_media':
            case 'url_article':
            case 'url_document':
            case 'youtube':
                $thumbnail = $attached_ent->thumbnail;
                $url = $attached_ent->url;
                break;
            case 'image':
                $thumbnail = static::_get_thumbnail_url($attached_ent->guid,'medium');
                $url = static::_get_dwl_url($attached_ent->guid);
                break;
            case 'document':
            default:
                $thumbnail = $attached_ent->getIconURL();
                $url = static::_get_dwl_url($attached_ent->guid,'document');
                break;
        }
        $return = array(
                            'guid' => $attached_ent->guid
                            , 'time_created' => $attached_ent->time_created
                            , 'friendly_time' => elgg_get_friendly_time($attached_ent->time_created)
                            , 'title' => $attached_ent->title
                            , 'description' => $attached_ent->description
                            , 'html' => $attached_ent->html
                            , 'type' => $attached_ent->simpletype
                            , 'mime' => $attached_ent->mimetype
                            , 'url' => $url
                            , 'thumbnail' => $thumbnail
                            , 'duration' => ($attached_ent->duration)>0?$attached_ent->duration:30
                            , 'video_id' => trim($attached_ent->video_id)
            );
       return $return;
    }
}
