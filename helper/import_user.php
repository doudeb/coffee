<?php
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PORT'] = '443';
$_SERVER['HTTP_HOST'] = 'api.coffee.enlightn.doudeb';

include_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");

global $CONFIG;

if (php_sapi_name() !== 'cli') exit("To be runned under commande line");
$users = file_get_contents('./user.csv');
$users = explode(chr(10), $users);

$title = '[CoffeePoke] Your account details';
$headers = 'From: evrard@coffeepoke.com' . "\r\n" .
    'Reply-To: evrard@coffeepoke.com' . "\r\n";

foreach($users as $key=>$user) {
	$user = explode(";",$user);
	$username = _convert(trim(substr($user[2],0,strpos($user[2],'@'))));
	$password = substr(md5(rand(1,666)),0,8);
	$displayname = _convert(trim($user[0]) . ' ' . trim($user[1]));
	$email = _convert(trim($user[2]));
    $headline = $user[3];
    $location = $user[4] . ', ' . $user[5];
    $phone = $user[6];
    $cellphone = $user[7];
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
        $user_ent = get_user($guid);
        if (strlen($headline) > 0) $user_ent->headline = $headline;
        if (strlen($location) > 0) $user_ent->location = $location;
        if (strlen($phone) > 0) $user_ent->phone = $phone;
        if (strlen($cellphone) > 0) $user_ent->cellphone = $cellphone;
        $user_ent->save();
        $email = 'edouard@coffeepoke.com';
        $message = "
Hello $displayname,\n
You have been invited to join the playsoft community on CoffeePoke.\n
\n
To access your community, please click on the following link:\n
http://playsoft.coffeepoke.com/\n
Login: $email\n
Password: $password\n
    \n
Enjoy...";
        mail($email, $title, $message, $headers);
    }
}
