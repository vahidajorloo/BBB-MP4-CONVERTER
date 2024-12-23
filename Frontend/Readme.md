this project adds a gui to your bbb server to help you convert a single recording to mp4 and delete or download it from the list it will create.

how to use:
first install the bbb-mp4 plugin on the server
disable the automation script:

mv /var/bigbluebutton/playback/presentation/2.3/index_default.html /var/bigbluebutton/playback/presentation/2.3/index.html

mv /usr/local/bigbluebutton/core/scripts/post_publish/bbb_mp4.rb /usr/local/bigbluebutton/core/scripts/post_publish/bbb_mp4.rb.old


then install php 8.3 and php83-fpm

edit the nginx file:

location /recording {
    root /var/www/bigbluebutton-default;

    index index.php index.html index.htm;

    # Handle PHP scripts
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock; # Adjust PHP version if needed
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    # Deny access to sensitive files
    location ~ /\.ht {
        deny all;
    }
}

upload and extract the files to /var/www/bigbluebutton-default/recording

ensure that nginx have full access to this directory:

chmod -R 755 /var/www/bigbluebutton-default/recording
chown -R www-data:www-data /var/www/bigbluebutton-default/recording

in case of error when running the script grant nginx to access docker:

sudo usermod -aG docker www-data

or add this line to sudo visudo:

www-data ALL=(ALL) NOPASSWD: /usr/bin/docker

run these commands:

systemctl reload nginx
systemctl restart php8.3-fpm

access the converter page:

https://serveraddress.com/recording/index.php


