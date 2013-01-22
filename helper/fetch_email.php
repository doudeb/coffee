<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * @todo
 * *All cc and to are not retreived look into the php doc and comment
 * *Code a kind of replies formater (quoted replies)
 * *UTF8 damned....
 */


$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PORT'] = '';
$_SERVER['HTTP_HOST'] = '/';

include_once("../../../engine/start.php");
global $CONFIG,$DATALIST_CACHE;
elgg_unregister_event_handler('login', 'user','user_login');//remove login check...

if (php_sapi_name() !== 'cli') exit("To be runned under commande line");

require_once(dirname(dirname(__FILE__)) . "/vendors/imap.class.php");

$imapStream = new IMAP('mail.enlightn.com','143','post@enlightn.com','topsecure','tls/novalidate-cert');
$messages = $imapStream->imap_search("UNSEEN");
//$imapBox = $imapStream->imap_check();
foreach ($messages as $key=>$email_msgno) {
    $guid               = false;
    $email_headers      = $imapStream->imap_fetch_overview($email_msgno);
    $email_full_headers = $imapStream->imap_headerinfo( $email_headers[0]->msgno);
    $email_body         = $imapStream->view_message($email_headers[0]->uid);
    $email_attachement  = $imapStream->get_attachments($email_headers[0]->uid);
    $message_id         = $email_full_headers->message_id;

    if ($email_body['mime_type'] == 'html') {
        $message    = filter_tags($email_body["content"]);
    } else {
        $message    = nl2br($email_body["content"]);
    }

    $owner_email    = $email_full_headers->from[0]->mailbox . '@' . $email_full_headers->from[0]->host;

    $user_ent       = get_user_by_email(trim($owner_email));
    if ($user_ent[0] instanceof ElggUser) {
        $user_ent = $user_ent[0];
        if (login($user_ent)) {
            $DATALIST_CACHE['default_site'] = $CONFIG->site_guid = $CONFIG->site_id = $user_ent->site_guid;
            $CONFIG->site = get_entity($CONFIG->site_guid);
            //proceed attachement
            $attachment_guids = array();
            foreach ($email_attachement as $key => $attachement) {
                if (isset($attachement["filename"])) {
                    $attachment_guids[] = create_attachement($attachement["filename"],$imapStream->get_attachments($email_headers[0]->uid,$attachement["filename"]));
                }
            }
            $new_message = ElggCoffee::new_post($message,$attachment_guids);
        }
    }
}