#Coffeepoke web server recommendation.


We recommend to use 2 web servers :
  * One for the static files (webapp)
  * One for the api that will handle php.

If you are using nginx (recommended) do the following configurations :

##1/ Set up the static server :

Create the following virtual host.
```
server {
        listen                  80;
        listen                  443;
        ssl                     on;
        ssl_certificate         /etc/nginx/ssl/coffeepoke.com.crt;
        ssl_certificate_key     /etc/nginx/ssl/coffeepoke.com.key;

        index index.html;
        server_name             *.coffeepoke.com;
        root                    /var/www/enlightn_host/coffee_api/www/mod/coffee/vendors/front_end;

        error_log               /var/www/enlightn_host/coffee_api/log/error.log;
        access_log              /var/www/enlightn_host/coffee_api/log/access.log;
        location /services/api/rest/ {
                        access_log off;
                        proxy_pass  http://coffee_api_cluster;
        }
}
```
##2/ Set up the nginx upstream to the coffeepoke api server :

Add the following instruction to your nginx.conf fine.
```
        upstream coffee_api_cluster  {
                # server 88.190.37.129:8001;
                server ryze.nexen.net:8001;
        }
```

##3/ Set up the api server

Create the following virtual host.
```
server {
        listen                  8001;
        index                   index.php;

        error_log               /var/www/coffeepoke/log/error.log;
        access_log              /var/www/coffeepoke/log/access.log;
        location / {
                root  /var/www/coffeepoke/www/;
                include /etc/nginx/conf.d/elgg_rewrites;
        }
}
```
The elgg_rewrites file should like this :
```
#rewrites rules for elgg
rewrite_log on;
rewrite ^/pg\/([A-Za-z0-9\_\-]+)$ /engine/handlers/page_handler.php?handler=$1&$args;
rewrite ^/pg\/([A-Za-z0-9\_\-]+)\/(.*)$ /engine/handlers/page_handler.php?handler=$1&page=$2&$args;
rewrite ^/tag\/(.+)\/?$ /engine/handlers/page_handler.php?handler=search&page=$1;
rewrite ^/action\/([A-Za-z0-9\_\-\/]+)$ /engine/handlers/action_handler.php?action=$1&$args;
rewrite ^/cache\/(.*)$ /engine/handlers/cache_handler.php?request=$1&$args;
rewrite ^/services\/api\/([A-Za-z0-9\_\-]+)\/(.*)$ /engine/handlers/service_handler.php?handler=$1&request=$2&$args;
rewrite ^/export\/([A-Za-z]+)\/([0-9]+)\/?$ /engine/handlers/export_handler.php?view=$1&guid=$2;
rewrite ^/export\/([A-Za-z]+)\/([0-9]+)\/([A-Za-z]+)\/([A-Za-z0-9\_]+)\/$ /engine/handlers/export_handler.php?view=$1&guid=$2&type=$3&idname=$4;
rewrite /xml-rpc.php /engine/handlers/xml-rpc_handler.php;
rewrite /mt/mt-xmlrpc.cgi /engine/handlers/xml-rpc_handler.php;
rewrite ^/rewrite.php$ /install.php;
if (!-d $request_filename){
    set $rule_11 1$rule_11;
}
if (!-f $request_filename){
    set $rule_11 2$rule_11;
}
if ($rule_11 = "21"){
    rewrite ^/([A-Za-z0-9\_\-]+)$ /engine/handlers/page_handler.php?handler=$1;
}
if (!-d $request_filename){
    set $rule_12 1$rule_12;
}
if (!-f $request_filename){
    set $rule_12 2$rule_12;
}
if ($rule_12 = "21"){
    rewrite ^/([A-Za-z0-9\_\-]+)\/(.*)$ /engine/handlers/page_handler.php?handler=$1&page=$2;
}
# Do not put CSS there or it will break simplecache
location ~* \.(bmp|js|gif|ico|jpg|jpeg|png)$ {
    expires max;
    log_not_found off;
    access_log off;
}
index                   index.php index.html;
fastcgi_index           index.php;

location ~ \.php$ {
  # Throttle requests to prevent abuse
 
  # Zero-day exploit defense.
  # http://forum.nginx.org/read.php?2,88845,page=3
  # Won't work properly (404 error) if the file is not stored on this server, which is entirely possible with php-fpm/php-fcgi.
  # Comment the 'try_files' line out if you set up php-fpm/php-fcgi on another machine.  And then cross your fingers that you won't get hacked.
  try_files $uri =404;
 
  fastcgi_split_path_info ^(.+\.php)(/.+)$;
  include /etc/nginx/fastcgi_params;
 
  # As explained in http://kbeezie.com/view/php-self-path-nginx/ some fastcgi_param are missing from fastcgi_params.
  # Keep these parameters for compatibility with old PHP scripts using them.
  fastcgi_param PATH_INFO       $fastcgi_path_info;
  fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
  fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
 
  # Some default config
  fastcgi_connect_timeout        60;
  fastcgi_send_timeout          180;
  fastcgi_read_timeout          180;
  fastcgi_buffer_size          128k;
  fastcgi_buffers            4 256k;
  fastcgi_busy_buffers_size    256k;
  fastcgi_temp_file_write_size 256k;
 
  fastcgi_intercept_errors    on;
  fastcgi_ignore_client_abort off;
 
  #fastcgi_pass 127.0.0.1:9000;
  fastcgi_pass unix:/var/lib/php5-fpm/www.sock;
}
```
