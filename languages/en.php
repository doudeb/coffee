<?php
/**
 * Core English Language
 *
 * @package Elgg.Core
 * @subpackage Languages.English
 */

$english = array(


/**
 * User add
 */

	'useradd:subject' => 'User account created',
	'useradd:body' => '
%1$s,

A user account has been created for you at %2$s. To log in, visit:

http://%2$s.coffeepoke.com/

And log in with these user credentials:

Username: %4$s
Password: %5$s

Enjoy coffeePoke!!',

);

add_translation("en",$english);
