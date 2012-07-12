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
    public function new_post($post, $attachment = false, $type = COFFEE_SUBTYPE) {
        if (strlen($post) > 0) {
            $new_post = new ElggObject();
            $new_post->subtype = $type;
            $new_post->access_id = COFFEE_DEFAULT_ACCESS_ID;
            $new_post->title = $post;
            if (!$new_post->save()) {
                return false;
            }
            add_to_river('coffee/river/new_post', 'create', elgg_get_logged_in_user_guid(), $new_post->guid);
            ElggCoffee::_add_attachment($new_post->guid,$attachment);
            return array('guid' => $new_post->guid);
        }
        return false;
    }

    public function new_comment($guid, $comment) {
        $post = get_entity($guid);
        if ($post instanceof ElggObject && strlen($comment) > 0) {
            $comment_id = $post->annotate(COFFEE_COMMENT_TYPE, $comment, COFFEE_DEFAULT_ACCESS_ID);
            if ($comment_id) {
                $post->time_updated = time();
                $post->save();
                add_to_river('coffee/river/new_comment', 'create', elgg_get_logged_in_user_guid(), $post->guid,$comment_id);
                return true;
            }
        }
        return false;
    }

    public function get_post($guid) {
        if (!$guid) {
            return false;
        }
        return static::get_posts(0,0,10,false,COFFEE_SUBTYPE, $guid);
    }

    public function get_site_data () {
        $site           = elgg_get_site_entity();
        $user_ent       = elgg_get_logged_in_user_entity();
        if ($site instanceof ElggSite) {
            $options  = array('types'=>'object','subtypes'=>'file','limit'=>1);
            $options['joins']   = array("Inner join {$GLOBALS['CONFIG']->dbprefix}objects_entity obj_ent On e.guid = obj_ent.guid");
            $options['wheres']   = array("obj_ent.title = 'logo'");
            $site_logo = elgg_get_entities($options);
            $options['wheres']   = array("obj_ent.title = 'background'");
            $site_background = elgg_get_entities($options);
            $custom_css = $site_background[0]->description;
            $viewtype = elgg_get_viewtype();
            return array(
                    'user_guid' => elgg_get_logged_in_user_guid()
                    , 'name' => $site->name
                    , 'logo_url' => ElggCoffee::_get_dwl_url($site_logo[0]->guid)
                    , 'background_url' => ElggCoffee::_get_dwl_url($site_background[0]->guid)
                    , 'custom_css' => $custom_css
                    , 'translations' => json_encode($GLOBALS['CONFIG']->translations[$user_ent->language])
            );

        }

    }

    public static function get_posts($newer_than = 0, $offset = 0, $limit = 10, $owner_guids = array(), $type = COFFEE_SUBTYPE, $guid = false) {
        $return = array();
        $options  = array('types'=>'object'
                            , 'subtypes'=> $type
                            , 'limit'=> $limit
                            , 'offset'=> $offset
                            , 'owner_guids' => count($owner_guids) > 0 ? $owner_guids : false
                            , 'wheres' => 'e.time_updated > ' . $newer_than);
        if ($guid && $guid > 0) {
            $posts = array(get_entity($guid));
        } else {
            $posts = elgg_get_entities($options);
        }
        if(is_array($posts)) {
            foreach ($posts as $key => $post) {
                if ($post instanceof ElggObject) {
                    $return[$key]['guid'] = $post->guid;
                    $return[$key]['content']['type'] = $post->subtype;
                    $return[$key]['content']['text'] = nl2br($post->title);
                    $return[$key]['content']['time_created'] = $post->time_created;
                    $return[$key]['content']['friendly_time'] = elgg_get_friendly_time($post->time_created);
                    $return[$key]['content']['time_updated'] = $post->time_updated;
                    $user = get_user($post->owner_guid);
                    if ($user instanceof ElggUser) {
                        $return[$key]['user']['guid'] = $user->guid;
                        $return[$key]['user']['username'] = $user->username;
                        $return[$key]['user']['name'] = $user->name;
                        $return[$key]['user']['icon_url'] = ElggCoffee::_get_user_icon_url($user,'medium');
                    }

                    $return[$key]['likes'] = ElggCoffee::get_likes ($post->guid, 0, 3);
                    $return[$key]['comment'] = ElggCoffee::get_comments ($post->guid, 0, 2);
                    $return[$key]['attachment'] = ElggCoffee::get_attachment ($post->guid);
                }

            }
        }
        return $return;
    }

    public function get_activity ($offset = 0, $limit = 10) {}

    public function get_user_data ($guid, $extended = false) {
        if (!$guid) {
            $user_ent           = elgg_get_logged_in_user_entity();
        } else {
            $user_ent           = get_entity($guid);
        }
        if ($user_ent instanceof ElggUser) {
            if (is_array($extended)) {
                $extended = static::get_user_extra_info($extended);
            }
            return array('id'         => $user_ent->guid
                            , 'username'       => $user_ent->username
                            , 'name'           => $user_ent->name
                            , 'email'          => $user_ent->email
                            , 'icon_url'       => ElggCoffee::_get_user_icon_url($user_ent)
                            , 'cover_url'      => ElggCoffee::_get_user_cover_url($user_ent)
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
                    $return['comments'][] = array('owner_guid' => $user->guid
                                                            , 'name' => $user->name
                                                            , 'icon_url' => ElggCoffee::_get_user_icon_url($user,'small')
                                                            , 'time_created' => $comment->time_created
                                                            , 'friendly_time' => elgg_get_friendly_time($comment->time_created)
                                                            , 'text' => $comment->value);
                }
            }
        } else {
            $return['total'] = 0;
            $return['comments'] = false;
        }
        return $return;
    }

    public static function get_likes ($guid, $offset = 0, $limit = 3) {
        $likes = ElggCoffee::_get_likes ($guid, $offset = 0, $limit = 3);
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
            $attached_ent = get_entity($attached->guid_two);
            $return[] = array(
                                                'guid' => $attached_ent->guid
                                                , 'time_created' => $attached_ent->time_created
                                                , 'friendly_time' => elgg_get_friendly_time($attached_ent->time_created)
                                                , 'title' => $attached_ent->title
                                                , 'description' => $attached_ent->description
                                                , 'html' => $attached_ent->html
                                                , 'type' => $attached_ent->simpletype
                                                , 'mime' => $attached_ent->mimetype
                                                , 'url' => $attached_ent->simpletype === 'url'?$attached_ent->url:static::_get_dwl_url($attached_ent->guid)
                                                , 'thumbnail' => $attached_ent->simpletype === 'url'?$attached_ent->thumbnail:$attached_ent->getIconURL('medium')
                );
        }
        return $return;
    }

    public function set_relationship ($guid_parent, $guid_child, $type) {
        $guid_parent = (int)$guid_parent;
        $guid_child = (int)$guid_child;
        $type = sanitise_string($type);
        if (!empty($type)) {
            $return = add_entity_relationship($guid_parent, $type, $guid_child);
            if ($return) {
                add_to_river('coffee/river/' . $type, 'create', $guid_parent, $guid_child);
                $post = get_entity($guid_child);
                if ($post instanceof ElggEntity) {
                    $post->time_updated = time();
                    $post->save();
                }
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

     public function remove_relationship ($guid_parent, $guid_child, $type) {
        $guid_parent = (int)$guid_parent;
        $guid_child = (int)$guid_child;
        $type = sanitise_string($type);
        if (!empty($type)) {
            $return = remove_entity_relationship($guid_parent, $type, $guid_child);
            if ($return) {
                add_to_river('coffee/river/' . $type, 'remove', $guid_parent, $guid_child);
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

    public function disable_object ($guid) {
        $ent = get_entity($guid);
        if ($ent instanceof ElggObject) {
            return $ent->disable();
        }
        return false;
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
        add_to_river('river/object/file/create', 'create', elgg_get_logged_in_user_guid(), $file->guid);
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

        return array('guid' => $file->guid
                        , 'url' => ElggCoffee::_get_dwl_url($file->guid)
                        , 'title' => $file->title
                        , 'thumbnail' => $file->getIconURL()
                        , 'mime_type' => $mime_type);
    }

    public static function get_url_data ($url) {
        require_once elgg_get_plugins_path() . "coffee/vendors/link_data/Embedly.php";
        require_once elgg_get_plugins_path() . "coffee/vendors/link_data/EmbedUrl.php";
        $return = false;
        try {
            $api = new Embedly_API(array('user_agent' => 'Mozilla/5.0 (compatible; embedly/example-app; support@embed.ly)'));
            $oembed = $api->oembed(array('url' => $url));
            if (!isset($oembed[0]->error_code)) {
                $return = array('title' => $oembed[0]->title
                                , 'description' => $oembed[0]->description
                                , 'thumbnail' => $oembed[0]->thumbnail_url
                                , 'width' => $oembed[0]->width
                                , 'height' => $oembed[0]->height
                                , 'html' => $oembed[0]->html);
            }
        } catch (Exception $e) {}
        if (!is_array($return)) {
            $embedUrl = new Embed_url(array('url' => $url));
            $embedUrl->embed();
            $return = array('title' => $embedUrl->title
                                    , 'description' => $embedUrl->description
                                    , 'thumbnail' => $embedUrl->sortedImage[0]);
        }
        if (is_array($return)) {
            $link = new ElggObject();
            $link->subtype = COFFEE_LINK_SUBTYPE;
            $link->access_id = COFFEE_DEFAULT_ACCESS_ID;
            $link->title = $return['title'];
            $link->description = $return['description'];
            $link->thumbnail = $return['thumbnail'];
            $link->simpletype = 'url';
            $link->html = $return['html'];
            $link->url = $url;
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
            if (send_new_password_request($user->guid)) {
                return true;
            } else {
                return false;
            }
        }
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
            add_to_river($view, 'update', $owner->guid, $owner->guid);
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

            system_message(elgg_echo('avatar:crop:success'));
            $view = 'river/user/default/profileiconupdate';
            elgg_delete_river(array('subject_guid' => $owner->guid, 'view' => $view));
            add_to_river($view, 'update', $owner->guid, $owner->guid);
        }
        return static::_get_user_icon_url($owner);
    }

    public static function upload_user_cover() {
        if (!isset($_FILES['cover']) || $_FILES['cover']['error'] != 0) return false;
        $guid = elgg_get_logged_in_user_guid();
        $owner = get_entity($guid);
        $file = new ElggFile();
        $file->owner_guid = $guid;
        $file->setFilename("cover/{$guid}.jpg");
        $file->open('write');
        $file->close();
        $file->save();
        $owner->covertime = time();
        $owner->save();
        move_uploaded_file($_FILES['cover']['tmp_name'], $file->getFilenameOnFilestore());
        return static::_get_user_cover_url($owner);
    }

    public static function set_user_extra_info ($name, $value) {
        $guid = elgg_get_logged_in_user_guid();
        $user_ent = get_user($guid);
        if ($user_ent instanceof ElggUser) {
            $user_ent->$name = $value;
            if ($user_ent->save()) {
                return true;
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

    public static function edit_user_detail($language = false, $name = false, $email = false, $curent_password = false, $password = false) {
        set_input('language', $language);
        set_input('current_password', $curent_password);
        set_input('password', $password);
        set_input('password2', $password);
        set_input('name', $name);
        set_input('email', $email);
        if ($language) {
            elgg_set_user_language();
        }
        if ($name) {
            elgg_set_user_name();
        }
        if ($email) {
            elgg_set_user_email();
        }
        if ($password && $curent_password) {
            elgg_set_user_password();
        }
        if (count_messages("error") === 0) {
            return true;
        }
        return false;
    }

    private static function _add_attachment ($guid_parent, $attachment) {
        if (!is_array($attachment)) return false;
        $type = COFFEE_POST_ATTACHMENT_RELATIONSHIP;
        foreach ($attachment as $key => $guid_child) {
            add_entity_relationship ($guid_parent, $type, $guid_child);
        }
    }

    private static function _get_user_icon_url ($entity,$size = 'medium') {
        if ($entity instanceof ElggUser) {
            return $GLOBALS['CONFIG']->url . 'userIcon/' . $GLOBALS['CONFIG']->auth_token . '/' . $entity->guid . '/' . $size . '?icontime=' . $entity->icontime;
        }
    }

    private static function _get_user_cover_url ($entity) {
        return $GLOBALS['CONFIG']->url . 'userCover/' . $GLOBALS['CONFIG']->auth_token . '/' . $entity->guid. '?icontime=' . $entity->covertime;
    }

    private static function _get_dwl_url ($guid) {
        return $GLOBALS['CONFIG']->url . 'dwl/' . $GLOBALS['CONFIG']->auth_token . '/' . $guid;
    }

    private static function _get_likes ($guid, $offset = 0, $limit = 3) {
        $likes = array();
        $likes = array();
        $likes['details'] = coffee_get_relationships($guid, COFFEE_LIKE_RELATIONSHIP, true , $offset = 0, $limit = 3);
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
}