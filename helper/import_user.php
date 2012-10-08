<?php
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PORT'] = '443';
$_SERVER['HTTP_HOST'] = 'api.coffee.enlightn.doudeb';

include_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");

global $CONFIG;

if (php_sapi_name() !== 'cli') exit("To be runned under commande line");
$users = file_get_contents('./user.csv');
$users = explode(chr(10), $users);

foreach($users as $key=>$user) {
	$user = explode(";",$user);
	$username = _convert(trim(substr($user[2],0,strpos($user[2],'@'))));
	$password = substr(md5(rand(1,666)),0,8);
	$displayname = _convert(trim($user[0]) . ' ' . trim($user[1]));
	$email = _convert(trim($user[2]));
	echo $username . ";";
	echo $password . ";";
	echo $displayname . ";";
	echo $email . ";";
	echo "\n";
	$guid = register_user($username,
                    $password,
                    $displayname,
                    $email
                    );
    if ($guid) {
        mail($email, '[CoffeePoke] Your account details', '');
    }
}
