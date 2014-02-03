#coffeePoke

##An internal wall for your compagny !



coffeePoke INSTALLATION INSTRUCTIONS

TECHNICAL REQUIREMENTS

coffeePoke runs on a combination of the Nginx web server, MySQL database 
system and the PHP interpreted scripting language. This is the most 
powerfull web server environment in the world. (coffeePoke can also run on
other web servers such a apache and IIS, but requires further configuration).

Due to coffeePoke's advanced functionality, there are some extra 
configuration requirements:

    * The Nginx web server needs to be installed.
    * MySQL 5.5+ or MariaDb.
    * PHP 5.4+ needs to be installed in FPM mode.
      with the following libraries:
          o GD (for graphics processing such as avatar cropping)
          o Multibyte String support (for internationalization)

It is recommended that you increase the memory available to PHP 
threads beyond the standard 8 or 12M, and increase the maximum 
uploaded filesize (which defaults to 2M). In both cases, this can be
found in your php.ini.

Please refer to the INSTALL.txt file in order to setup your web server.

INSTALLING coffeePoke


##1. Install Elgg

Refer to the Elgg instalation guide.
[http://docs.elgg.org/wiki/Installation]

Once you've done your installation, please add the following code to the end of your lib/settings.php file in order to enable multisite feature : 
```
function get_site_id() {
    global $CONFIG, $DATALIST_CACHE;
    establish_db_link();
    $port           = $_SERVER["SERVER_PORT"];
    $host           = $_SERVER["HTTP_HOST"];
    $protocol       = $port==='443'?'https://':'http://';
    $CONFIG->protocol = $protocol;
    $url            = $protocol . $host . '/';
    elgg_set_ignore_access(true);
    $site           = get_site_by_url($url);
    elgg_set_ignore_access(false);
    if ($site instanceof ElggSite) {
        $site_guid      = $site->guid;
        //run this shit once, in order to load all cache variables..
        datalist_get('default_site');
        $DATALIST_CACHE['default_site'] = $site_guid;
        return $site_guid;
    }
    return 1;
}
```

##2. Create a mod coffeepoke

CoffeePoke work as an elgg module.
You just have to checkout, copy the coffeepoke source code to the elgg module directory (mod)


##3. Visit your Elgg site

Once you've performed these steps, got to your Elgg admin section in your web 
browser. [https://yourelgginstalation/admin/]

  *. Make sure that the rest api is enabled (Configure => Settings => Advanced Settings) :  Enable Elgg's web services API must be checked.
  *. Enable the coffeepoke module throught the plugins section.

All done, now enjoy coffeepoke.

##4. Create a new site entity

In ssh, got to /your/coffeepoke/installation/path and execute : 
```
cd helper;
php -e create_new_site.php
```
Follow the steps.