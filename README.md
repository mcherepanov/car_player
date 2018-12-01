# car_player - плеер в машину вместо чейнджера Kenwood на SoC Orange Pi Zero
# установка операционной системы
# берем на https://www.armbian.com/orange-pi-zero/
# распаковываем
# пишем на карту
dcfldd if=Armbian_5.65_Orangepizero_Debian_stretch_next.img of=/dev/sdc bs=1024; sync
# ставим пакеты
sudo su
apt-get install nginx php-fpm mc moc moc-ffmpeg-plugin git curl

# ---------
# ========== веб-сервер ================

# php-fpm
nano /etc/php/7.0/fpm/pool.d/www.conf
# меняем строки, чтобы было так:
;
user = root
group = root
;
listen = /run/php/php7.2-fpm.sock
; 7.2 может быть 7.0 или другое, от версии fpm
# сохраняем

nano /lib/systemd/system/php7.0-fpm.service
# меняем так (добавляем -R):
ExecStart=/usr/sbin/php-fpm7.0 -R --nodaemonize --fpm-config /etc/php/7.0/fpm/php-fpm.conf
# сохраняем

# перечитываем конфиги и перезапускаем демона
systemctl daemon-reload && systemctl restart php7.0-fpm.service
# проверим
ps aux | grep fpm
# все процессы должны быть от root

# nginx
nano /etc/nginx/sites-available/default
# этот блок должен выглядеть так:
        root /var/www/html;

        # Add index.php to the list if you are using PHP
        index index.html index.htm index.php;


        server_name _;

        location / {
                # First attempt to serve request as file, then
                # as directory, then fall back to displaying a 404.
                try_files $uri $uri/ =404;
        }

        # pass PHP scripts to FastCGI server
        #
        location ~ \.php$ {
                include snippets/fastcgi-php.conf;

                # With php-fpm (or other unix sockets):
                fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
                # With php-cgi (or other tcp sockets):
                #fastcgi_pass 127.0.0.1:9000;
        }
# то, что за решетками, можно удалить
service nginx restart
# в /var/www/html положить какой-нибудь скрипт на php и проверить





