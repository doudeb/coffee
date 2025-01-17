<?php

define('COFFEE_SUBTYPE', 'coffee_subtype');
define('COFFEE_SUBTYPE_BROADCAST_MESSAGE', 'coffee_broadcast_message');
define('COFFEE_LINK_SUBTYPE', 'coffee_link');
define('COFFEE_COMMENT', 'coffee_comment');
define('COFFEE_DEFAULT_ACCESS_ID', ACCESS_LOGGED_IN);
define('COFFEE_LIKE_RELATIONSHIP', 'coffee_like');
define('COFFEE_POKE_RELATIONSHIP', 'coffee_poke');
define('COFFEE_POST_ATTACHMENT_RELATIONSHIP', 'coffee_post_attachment');
define('COFFEE_COMMENT_ATTACHMENT_RELATIONSHIP', 'coffee_comment_attachment');
define('COFFEE_POST_MENTIONED_RELATIONSHIP', 'post::mentioned');
define('COFFEE_COMMENT_MENTIONED_RELATIONSHIP', 'comment::mentioned');
define('COFFEE_COMMENT_TYPE', 'generic_comment');
define('COFFEE_SITE_FEED_UPDATE', 'feed_last_update');
define('COFFEE_CORPORATE_TAGS_UPDATE', 'corporate_tags_update');


/*
 * exposed function array
 *
 */
$i                                  = 0;

$exposed[++$i]['method']            = "coffee.getUserData";
$exposed[$i]['function']            = "ElggCoffee::get_user_data";
$exposed[$i]['params']          	= array("guid" => array('type' => 'int'
                                                            , 'required' => true )
                                                    , "extended" => array('type' => 'array'
                                                            , 'required' => false )
                                                );
$exposed[$i]['comment']          	= 'Retreive user data.
                                        @param extended is an array of profil name eg : [hobbies, headline, location, introduction, phone, cellphone];';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.getSiteData";
$exposed[$i]['function']            = "ElggCoffee::get_site_data";
$exposed[$i]['params']          	= array();
$exposed[$i]['comment']          	= 'Retreive site data.';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.createNewPost";
$exposed[$i]['function']            = "ElggCoffee::new_post";
$exposed[$i]['params']          	= array(
                                             "post" => array('type' => 'string'
                                                            , 'required' => false
                                                            , 'default' => '')
                                                , "attachment" => array('type' => 'array'
                                                            , 'required' => false
                                                            , 'default' => array())
                                                , "mentionedUser" => array('type' => 'array'
                                                            , 'required' => false
                                                            , 'default' => array())
                                                , "type" => array('type' => 'string'
                                                            , 'required' => false
                                                            , 'default' => COFFEE_SUBTYPE)
                                                );
$exposed[$i]['comment']          	= 'Allow user to create a new post.
                                        @param attachment array : contain guid
                                        @param type string : could be a comment, a headline';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.getPost";
$exposed[$i]['function']            = "ElggCoffee::get_post";
$exposed[$i]['params']          	= array("guid" => array('type' => 'int'
                                                            , 'required' => true ));
$exposed[$i]['comment']          	= 'get one single post with guid';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.getPosts";
$exposed[$i]['function']            = "ElggCoffee::get_posts";
$exposed[$i]['params']          	= array( "newer_than" => array('type' => 'int'
                                                            , 'required' => false
                                                            , 'default' => 0)
                                            , "offset" => array('type' => 'int'
                                                            , 'required' => false
                                                            , 'default' => 0)
                                            , "limit" => array('type' => 'int'
                                                        , 'required' => false
                                                        , 'default' => 10)
                                            , "owner_guids" => array('type' => 'array'
                                                            , 'required' => false
                                                            , 'default' => array())
                                            , "type" => array('type' => 'array'
                                                            , 'required' => false
                                                            , 'default' => array(COFFEE_SUBTYPE,COFFEE_SUBTYPE_BROADCAST_MESSAGE))
                                            , "guid" => array('type' => 'int'
                                                            , 'required' => false
                                                            , 'default' => false)
                                            , "tags" => array('type' => 'array'
                                                            , 'required' => false
                                                            , 'default' => array()));
$exposed[$i]['comment']          	= 'Get news feed items, ordered from newest to oldest';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.getComments";
$exposed[$i]['function']            = "ElggCoffee::get_comments";
$exposed[$i]['params']          	= array(
                                            "guid" => array('type' => 'int'
                                                            , 'required' => true)
                                            , "offset" => array('type' => 'int'
                                                            , 'required' => false
                                                            , 'default' => 0)
                                            , "limit" => array('type' => 'int'
                                                        , 'required' => false
                                                        , 'default' => 10));
$exposed[$i]['comment']          	= 'Get all comments for an update';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.comment";
$exposed[$i]['function']            = "ElggCoffee::new_comment";
$exposed[$i]['params']          	= array(
                                            "guid" => array('type' => 'int'
                                                            , 'required' => true)
                                            , "comment" => array('type' => 'string'
                                                            , 'required' => true )
                                            , "mentionedUser" => array('type' => 'array'
                                                            , 'required' => false
                                                            , 'default' => array()));
$exposed[$i]['comment']          	= 'Post a comment';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.setRelationship";
$exposed[$i]['function']            = "ElggCoffee::set_relationship";
$exposed[$i]['params']          	= array(
                                            "guid_parent" => array('type' => 'int'
                                                            , 'required' => true)
                                            , "guid_children" => array('type' => 'int'
                                                            , 'required' => true)
                                            , "type" => array('type' => 'string'
                                                            , 'required' => true ));
$exposed[$i]['comment']          	= 'Set a relationship between two objects (could be an user, a comment, etc) of a specified type. Eg : user #1 has a relationship with comment #34 of type "Like"';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.removeRelationship";
$exposed[$i]['function']            = "ElggCoffee::remove_relationship";
$exposed[$i]['params']          	= array(
                                            "guid_parent" => array('type' => 'int'
                                                            , 'required' => true)
                                            , "guid_children" => array('type' => 'int'
                                                            , 'required' => true)
                                            , "type" => array('type' => 'string'
                                                            , 'required' => true ));
$exposed[$i]['comment']          	= 'Remove a relationship between two objects';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.disableObject";
$exposed[$i]['function']            = "ElggCoffee::disable_object";
$exposed[$i]['params']          	= array(
                                            "guid" => array('type' => 'int'
                                                            , 'required' => true));
$exposed[$i]['comment']          	= 'Disable an object';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.getUrlData";
$exposed[$i]['function']            = "ElggCoffee::get_url_data";
$exposed[$i]['params']          	= array(
                                            "url" => array('type' => 'string'
                                                            , 'required' => true));
$exposed[$i]['comment']          	= 'Will return data crawled from an url.
                                        Response example : title => html meta title
                                                            , description => html meta description
                                                            , thumbnail => image url
                                                            , html => (optional) Will contain html code to embed rich media like youtube flash player, etc)';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.uploadData";
$exposed[$i]['function']            = "ElggCoffee::upload_data";
$exposed[$i]['params']          	= array();
$exposed[$i]['comment']          	= 'Upload a file. Name must be "upload".
                                        Will return the download url on success and the associated id';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.uploadUserAvatar";
$exposed[$i]['function']            = "ElggCoffee::upload_user_avatar";
$exposed[$i]['params']          	= array("square" => array('type' => 'array'
                                                            , 'required' => false));
$exposed[$i]['comment']          	= 'Upload a file. Name must be "avatar".
                                        square params must contain x1,y1,x2,y2 in oder to resize properly the image';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.uploadUserCover";
$exposed[$i]['function']            = "ElggCoffee::upload_user_cover";
$exposed[$i]['params']          	= array();
$exposed[$i]['comment']          	= 'Upload a file. Name must be "cover".';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.sendNewPassword";
$exposed[$i]['function']            = "ElggCoffee::send_new_password";
$exposed[$i]['params']          	= array("username" => array('type' => 'string'
                                                            , 'required' => true));
$exposed[$i]['comment']          	= 'Send a new password by email to the user.
                                        Input could be username or email';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = false;

$exposed[++$i]['method']            = "coffee.getUserExtraInfo";
$exposed[$i]['function']            = "ElggCoffee::get_user_extra_info";
$exposed[$i]['params']          	= array("names" => array('type' => 'array'
                                                            , 'required' => true)
                                                        , "guid" => array('type' => 'int'
                                                            , 'required' => false));
$exposed[$i]['comment']          	= 'Get user extra information.
                                        @names array|string that will contain profile names.';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.setUserExtraInfo";
$exposed[$i]['function']            = "ElggCoffee::set_user_extra_info";
$exposed[$i]['params']          	= array("name" => array('type' => 'string'
                                                            , 'required' => true)
                                                        , "value" => array('type' => 'string'
                                                            , 'required' => true)
                                                        , "guid" => array('type' => 'int'
                                                            , 'required' => false));
$exposed[$i]['comment']          	= 'Set user extra information.
                                        @name string profile name
                                        @value string profile value';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.editUserDetail";
$exposed[$i]['function']            = "ElggCoffee::edit_user_detail";
$exposed[$i]['params']          	= array("language" => array('type' => 'string'
                                                            , 'required' => true)
                                                        , "name" => array('type' => 'string'
                                                            , 'required' => true
                                                            , 'default' => false)
                                                        , "current_password" => array('type' => 'string'
                                                            , 'required' => false
                                                            , 'default' => false)
                                                        , "password" => array('type' => 'string'
                                                            , 'required' => true
                                                            , 'default' => false));
$exposed[$i]['comment']          	= '';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.disableAnnotation";
$exposed[$i]['function']            = "ElggCoffee::disable_annotation";
$exposed[$i]['params']          	= array("id" => array('type' => 'int'
                                                            , 'required' => true));
$exposed[$i]['comment']          	= '';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.avaibleLanguage";
$exposed[$i]['function']            = "get_installed_translations";
$exposed[$i]['params']          	= array();
$exposed[$i]['comment']          	= '';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = false;

$exposed[++$i]['method']            = "coffee.getTokenByEmail";
$exposed[$i]['function']            = "auth_gettoken_by_email";
$exposed[$i]['params']          	= array("email" => array('type' => 'string'
                                                            , 'required' => true)
                                            , "password" => array('type' => 'string'
                                                            , 'required' => true));
$exposed[$i]['comment']          	= 'Authenticate a user by email and password';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = false;

$exposed[++$i]['method']            = "coffee.getUserList";
$exposed[$i]['function']            = "ElggCoffee::get_user_list";
$exposed[$i]['params']          	= array("query" => array('type' => 'string'
                                                            , 'required' => false
                                                            , 'default' => false)
                                            , "offset" => array('type' => 'int'
                                                            , 'required' => false
                                                            , 'default' => 0)
                                            , "limit" => array('type' => 'int'
                                                        , 'required' => false
                                                        , 'default' => 10));
$exposed[$i]['comment']          	= 'Search for user';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.registerUser";
$exposed[$i]['function']            = "ElggCoffee::register_user";
$exposed[$i]['params']          	= array("display_name" => array('type' => 'string'
                                                            , 'required' => true)
                                            , "email" => array('type' => 'string'
                                                            , 'required' => true)
                                            , "password" => array('type' => 'string'
                                                            , 'required' => true)
                                            , "password2" => array('type' => 'string'
                                                            , 'required' => true)
                                            , "language" => array('type' => 'string'
                                                            , 'required' => true)
                                            , "make_admin" => array('type' => 'string'
                                                            , 'required' => true
                                                            , 'default'=>false)
                                            , "send_email" => array('type' => 'string'
                                                            , 'required' => true
                                                            , 'default'=>true));
$exposed[$i]['comment']          	= 'Create a new user. Will return guid in case of success';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.editSiteSettings";
$exposed[$i]['function']            = "ElggCoffee::edit_site_settings";
$exposed[$i]['params']          	= array("language" => array('type' => 'string'
                                                            , 'required' => true));
$exposed[$i]['comment']          	= 'Update site settings';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.banUser";
$exposed[$i]['function']            = "ElggCoffee::ban_user";
$exposed[$i]['params']          	= array("guid" => array('type' => 'int'
                                                            , 'required' => true));
$exposed[$i]['comment']          	= 'Remove a user from the site';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;


$exposed[++$i]['method']            = "coffee.getTagList";
$exposed[$i]['function']            = "ElggCoffee::get_tags";
$exposed[$i]['params']          	= array("query" => array('type' => 'string'
                                                            , 'required' => false
                                                            , 'default' => false)
                                            , "offset" => array('type' => 'int'
                                                            , 'required' => false
                                                            , 'default' => 0)
                                            , "limit" => array('type' => 'int'
                                                        , 'required' => false
                                                        , 'default' => 10));
$exposed[$i]['comment']          	= 'Search for tags';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.getSiteUpdate";
$exposed[$i]['function']            = "ElggCoffee::get_site_trigger";
$exposed[$i]['params']          	= array("type" => array('type' => 'array'
                                                            , 'required' => true
                                                            , 'default' => array(COFFEE_SITE_FEED_UPDATE)));
$exposed[$i]['comment']          	= 'Return site last update (Feed, system)';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.getTranslationTable";
$exposed[$i]['function']            = "ElggCoffee::get_translation_table";
$exposed[$i]['params']          	= array("locale" => array('type' => 'string'
                                                            , 'required' => true
                                                            , 'default' => 'en'));
$exposed[$i]['comment']          	= 'Search for tags';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = false;

$exposed[++$i]['method']            = "coffee.getCorporateHashtags";
$exposed[$i]['function']            = "ElggCoffee::get_corporate_tags";
$exposed[$i]['params']          	= array();
$exposed[$i]['comment']          	= 'Search for corporateHashtags';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.setCorporateHashtags";
$exposed[$i]['function']            = "ElggCoffee::set_corporate_tags";
$exposed[$i]['params']          	= array("tags" => array('type' => 'array'
                                                            , 'required' => true
                                                            , 'default' => array()));
$exposed[$i]['comment']          	= 'Set corporate tags';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.getTVPosts";
$exposed[$i]['function']            = "ElggCoffee::get_tv_post";
$exposed[$i]['params']          	= array();
$exposed[$i]['comment']          	= 'Load post and data for TV app';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.sendNewPassword";
$exposed[$i]['function']            = "ElggCoffee::send_new_password";
$exposed[$i]['params']          	= array("user" => array('type' => 'string'
                                                            , 'required' => true
                                                            , 'default' => false));
$exposed[$i]['comment']          	= 'Send a reset password link to a user';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = false;

$exposed[++$i]['method']            = "coffee.resetPassword";
$exposed[$i]['function']            = "ElggCoffee::reset_password";
$exposed[$i]['params']          	= array("user_guid" => array('type' => 'int'
                                                            , 'required' => true
                                                            , 'default' => false)
                                            , "code" => array('type' => 'string'
                                                            , 'required' => true
                                                            , 'default' => false));
$exposed[$i]['comment']          	= 'Send a reset password link to a user';
$exposed[$i]['call_method']      	= 'POST';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = false;

$exposed[++$i]['method']            = "coffee.getTVChannels";
$exposed[$i]['function']            = "ElggCoffee::get_tv_channel";
$exposed[$i]['comment']          	= "Retreive all TV channels and content for a TV account";
$exposed[$i]['call_method']      	= "GET";
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.getNotifications";
$exposed[$i]['function']            = "ElggCoffee::get_notifications";
$exposed[$i]['params']          	= array("until" => array('type' => 'int'
                                                            , 'required' => true
                                                            , 'default' => false)
                                            , "limit" => array('type' => 'int'
                                                            , 'required' => true
                                                            , 'default' => 10));
$exposed[$i]['comment']          	= "Retreive all notifications for the logged in user";
$exposed[$i]['call_method']      	= "GET";
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$GLOBALS['CONFIG']->exposed                    = $exposed;