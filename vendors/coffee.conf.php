<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define('COFFEE_SUBTYPE', 'coffee_subtype');
define('COFFEE_COMMENT', 'coffee_comment');
define('COFFEE_DEFAULT_ACCESS_ID', ACCESS_LOGGED_IN);
define('COFFEE_LIKE_RELATIONSHIP', 'coffee_like');
define('COFFEE_POKE_RELATIONSHIP', 'coffee_poke');
define('COFFEE_POST_ATTACHMENT_RELATIONSHIP', 'coffee_post_attachment');
define('COFFEE_COMMENT_ATTACHMENT_RELATIONSHIP', 'coffee_comment_attachment');
define('COFFEE_COMMENT_TYPE', 'generic_comment');


/*
 * expose function array
 *
 */
$i                                  = 0;
$exposed[++$i]['method']            = "coffee.getPosts";
$exposed[$i]['function']            = "ElggCoffee::get_post";
$exposed[$i]['params']          	= array(
                                            "offset" => array('type' => 'int'
                                                                , 'required' => false )
                                            ,"limit" => array('type' => 'int'
                                                                , 'required' => false ));
$exposed[$i]['comment']          	= 'Retreive all last post.';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.getUserData";
$exposed[$i]['function']            = "ElggCoffee::get_user_data";
$exposed[$i]['params']          	= array(
                                             "id" => array('type' => 'int'
                                                            , 'required' => false ));
$exposed[$i]['comment']          	= 'Retreive user data.';
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
                                                            , 'required' => true )
                                                , "attachment" => array('type' => 'array'
                                                            , 'required' => false )
                                                , "type" => array('type' => 'string'
                                                            , 'required' => false )
                                                );
$exposed[$i]['comment']          	= 'Allow user to create a new post.
                                        @param attachment array : contain guid
                                        @param type string : could be a comment, a headline';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = true;

$exposed[++$i]['method']            = "coffee.getPosts";
$exposed[$i]['function']            = "ElggCoffee::get_posts";
$exposed[$i]['params']          	= array(
                                             "offset" => array('type' => 'int'
                                                            , 'required' => false )
                                            , "limit" => array('type' => 'int'
                                                        , 'required' => false )
                                            , "owner_guids" => array('type' => 'array'
                                                            , 'required' => false ));
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
                                                            , 'required' => false )
                                            , "limit" => array('type' => 'int'
                                                        , 'required' => false ));
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
                                                            , 'required' => true ));
$exposed[$i]['comment']          	= 'Post a comment';
$exposed[$i]['call_method']      	= 'GET';
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
                                        Will return the download url on success';
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

$exposed[++$i]['method']            = "coffee.sendNewPassword";
$exposed[$i]['function']            = "ElggCoffee::send_new_password";
$exposed[$i]['params']          	= array("username" => array('type' => 'string'
                                                            , 'required' => true));
$exposed[$i]['comment']          	= 'Send a new password by email to the user.
                                        Input could be username or email';
$exposed[$i]['call_method']      	= 'GET';
$exposed[$i]['require_api_auth']    = false;
$exposed[$i]['require_user_auth']   = false;