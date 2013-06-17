<?php
$sitename = 'technocentre';
$_SERVER['REQUEST_URI'] = '/';
//$_SERVER['SERVER_PORT'] = '443';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['HTTP_HOST'] = $sitename . '.api.coffeepoke.com';
//$_SERVER['HTTP_HOST'] = 'http://api.coffee.enlightn.doudeb/';

include_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");

global $CONFIG;
var_dump($CONFIG->site_guid);
if (php_sapi_name() !== 'cli') exit("To be runned under commande line");
$users = file_get_contents('./user.csv');
$users = explode(chr(10), $users);

$title = '[CoffeePoke] Your account details';
$headers = 'From: evrard@coffeepoke.com' . "\r\n" .
    'Reply-To: evrard@coffeepoke.com' . "\r\n" .
    'Content-Type:text/html;charset=utf-8' . "\r\n";

foreach($users as $key=>$user) {
	$user = explode(";",$user);
	$temp = explode(".",$user[0]);
	$user = array(0=> ucfirst($temp[0])
			, 1=> ucfirst(substr($temp[1],0,strpos($temp[1],'@')))
			, 2=> $user[0]
		);
	$username = _convert(trim($user[2]));
    	$username = str_replace(array("-","_",".","@"), '', $username);
    	if (strlen($username) <= $CONFIG->minusername) $username = str_pad ($username, $CONFIG->minusername,'_');
	$password = substr(md5(rand(1,666)),0,8);
	$displayname = trim($user[0]) . ' ' . trim($user[1]);
	$email = trim($user[2]);
    	$headline = $user[3];
    	$location = $user[4] . ', ' . $user[5];
    	$phone = $user[6];
    	$cellphone = $user[7];
	$exist = get_user_by_email($email);
	if ($exist) continue;
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
        $message = "Hi,\n<br />
\n<br /> 
\n<br />Kavya Ramulu has invited you to join Coffeepoke.
\n<br /> 
\n<br />Please click on the link below:
\n<br />http://dmcompanies.coffeepoke.com
\n<br /> 
\n<br />and use the following details:
\n<br />loggin: $email
\n<br />pwd: $password
\n<br /> 
\n<br />More information about Coffeepoke
\n<br /> 
\n<br />What is CoffeePoke ?
\n<br /> 
\n<br />Coffee Poke is a new type of Enterprise Social Network. CoffeePoke is the 1st Cross-Platform application (Web +Mobile App + TV) dedicated to strengthening the bonds between employees in the workplace.
\n<br /> 
\n<br />Why the concept of Coffee break is at the centre of our reflection?
\n<br /> 
\n<br />Beyond occasional (and often expensive) initiatives as teambuilding, ski trips or weekend in the countryside... we have noticed on a day-to-day basis, informal exchanges during a coffee break are, so far, the best way to create those new bonds.
\n<br />Moreover too often, these exchanges are ephemerals, isolated (geographical borders, organisational silos) or lost (no archives)
\n<br /> 
\n<br />Therefore we have created a new concept, the coffee break 2.0 for a new productive and useful way to have a break 2.0.
\n<br />  
\n<br />Main characteristics:
\n<br /> 
\n<br />Refine, highly intuitive, original and friendly (this last characteristic aims to recreate the atmosphere at the coffee machine).
\n<br /> 
\n<br />Our strength:
\n<br /> 
\n<br />A TV application allowing extending the coffee break experience by broadcasting the last 10 messages posted on the platform.
\n<br />In different locations in the workplace (open space, cafeteria, vending machines…) Coffee Poke TV application creates more transparency, more conviviality and more team spirit within the organisation.
\n<br /> 
\n<br />The small plus:
\n<br /> 
\n<br />On a profile page, “coffepoke” functionality allows you to invite a colleague for a real coffee in a click (let’s not forget the goal is to create more bonds between people of the same company)
\n<br /> 
\n<br />Give a go! Have a Coffeebreak 2.0 !!!
\n<br /> 
\n<br />Best regards";
        $email = 'edouard@coffeepoke.com';
        //mail($email, $title, $message, $headers);
    }
}
