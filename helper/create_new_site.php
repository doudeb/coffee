<?php
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PORT'] = '';
$_SERVER['HTTP_HOST'] = '/';

include_once("../../../engine/start.php");
global $CONFIG;


if (php_sapi_name() !== 'cli') exit("To be runned under commande line");


if(cli_prompt("This will create a new site... sure : [y/N]") !== 'y') exit("Thanks anyway....\n");

input:
$sitename   =  cli_prompt("\nSite name : ");
$url        =  cli_prompt("\nSite url (including trailling slash), must be http://something.api.coffepoke.com/ : ");
$username   =  cli_prompt("\nSite admin username : ");
$password   =  cli_prompt("\nSite admin password (min " . $CONFIG->min_password_length. " chars): ");
$email      =  cli_prompt("\nSite admin email : ");
$displayname=  cli_prompt("\nSite admin display name : ");


if (in_array('', array($sitename,$url,$username,$password,$email,$displayname))) {
    print("Looks like you miss something....\n");
    goto input;
}

if(cli_prompt("Confirm the new site value : " . var_export(array('sitename'=>$sitename,'url'=>$url,'username'=>$username,'password'=>$password,'email'=>$email,'displayname'=>$displayname),true) . "[y/N]") !== 'y') exit("Thanks anyway....\n");

echo "Now create the site entity....";
elgg_set_ignore_access(TRUE);
try {
    $site            = new ElggSite();
    $site->name      = $sitename;
    $site->url       = $url;
    $site->access_id = ACCESS_PUBLIC;
    $site->email     = $email;
    $guid            = $site->save();

    $CONFIG->site_guid  = $site->guid;
    $CONFIG->site       = $site;

    set_config('view', 'default', $site->getGUID());
    set_config('language', 'en', $site->getGUID());
    set_config('default_access', ACCESS_LOGGED_IN, $site->getGUID());
    set_config('allow_registration', FALSE, $site->getGUID());
    set_config('walled_garden', TRUE, $site->getGUID());
    set_config('allow_user_default_access', '', $site->getGUID());
    set_config('https_login', FALSE, $site->getGUID());
    set_config('api', TRUE, $site->getGUID());
    set_config('sitename', $sitename, $site->getGUID());
    set_config('siteemail', $email, $site->getGUID());
    set_config('site_guid', $site->getGUID(), $site->getGUID());
    set_config('site_id', $site->getGUID(), $site->getGUID());
    set_config('site_id', $site->getGUID(), $site->getGUID());

    elgg_generate_plugin_entities();
    $plugins = elgg_get_plugins('any');
    foreach ($plugins as $plugin) {
        if ($plugin->getManifest()) {
            if ($plugin->getManifest()->getActivateOnInstall() && in_array($plugin->title, array('uservalidationbyemail','search','logbrowser','file','coffee'))) {
                $plugin->activate();
            }
        }
    }

} catch (Exception $e) {
    exit ("\nFatal : " . $e->getMessage());
}

echo "\nNew site entity created : " . $site->guid;
echo "\nNow create site admin....";
try {
    if (!$guid) {
        register_error(elgg_echo('install:error:createsite'));
        return FALSE;
    }

    $guid = register_user(
                    $username,
                    $password,
                    $displayname,
                    $email
                    );

    if (!$guid) {
        register_error(elgg_echo('install:admin:cannot_create'));
        return FALSE;
    }

    $user = get_entity($guid);
    if (!$user) {
        register_error(elgg_echo('install:error:loadadmin'));
        return FALSE;
    }

    elgg_set_ignore_access(TRUE);
    if ($user->makeAdmin() == FALSE) {
        register_error(elgg_echo('install:error:adminaccess'));
    } else {
        datalist_set('admin_registered', 1);
    }
    elgg_set_ignore_access(FALSE);

    // add validation data to satisfy user validation plugins
    create_metadata($guid, 'validated', TRUE, '', 0, ACCESS_PUBLIC);
    create_metadata($guid, 'validated_method', 'admin_user', '', 0, ACCESS_PUBLIC);

    if ($login) {
        if (login($user) == FALSE) {
            register_error(elgg_echo('install:error:adminlogin'));
        }
    }

} catch (Exception $e) {
    exit ("Fatal : " . $e->getMessage());
}
echo "\nAdmin created...\nGood luck luc....\n";
elgg_set_ignore_access(false);
function cli_prompt ($message) {
    print $message;
    flush();
    ob_flush();
    return  trim( fgets( STDIN ) );
}