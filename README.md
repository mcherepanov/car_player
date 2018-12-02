# car_player - плеер в машину вместо чейнджера Kenwood на SoC Orange Pi Zero
# установка операционной системы
# берем на https://www.armbian.com/orange-pi-zero/
# распаковываем
# пишем на карту
dcfldd if=Armbian_5.65_Orangepizero_Debian_stretch_next.img of=/dev/sdc bs=1024; sync
# после записи на компе карту не открывать и не подмонтировать - вынуть сразу после записи!
# вставлять уже сразу в плату SoC

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
# =====================================

# ======== аудио ================
# включить аудиодрайверы в ядре:
nano /boot/armbianEnv.txt
# добавить строку, если нет
overlays=analog-codec
# перезагрузить
# проверка:
aplay -l
# должны быть видны все аудиокарты:
**** List of PLAYBACK Hardware Devices ****
card 0: Codec [H3 Audio Codec], device 0: CDC PCM Codec-0 []
  Subdevices: 1/1
  Subdevice #0: subdevice #0


# Распаковать конфигурационный файл MOC
# Для начала проверить, есть ли в директории пользователя, от которого будет запускаться проигрыватель, скрытая папка .moc, если нет - создать.
# Потом
gunzip -c /usr/share/doc/moc/examples/config.example.gz > .moc/config

# Далее его надо отредактировать
nano .moc/config 

#SoundDriver = JACK:ALSA:OSS - ставим драйвер ALSA
SoundDriver = ALSA 

ALSADevice = default
ALSAMixer1 = "Line Out"
ALSAMixer2 = DAC

ALSAStutterDefeat = no

# для смены картинки при смене песни, скрипт должен быть на месте
# скрипт требует пакетов (см ниже)
OnSongChange = "/root/song_change.sh"

# передача картинки на веб-панель
apt install exiftool imagemagick


# Вручную запускаем mocp
mocp --server; mocp -c -a /music/ -p -v 99 -o r,s
# В начале стартуем сервер, потом очищаем плейлист, добавляем в него папку /music/, начинаем с первого файла в плейлисте, громкость в 99%, (-o r,s)


# скрипт для кронтаба
/usr/bin/mocp --server; /usr/bin/mocp -c -a /music/ -v 99


7. В домашней директории создаем скрипт запуска сервера при старте

nano start_moc.sh

#!/bin/bash
MUSIC_DIR=/music/ # сюда положить надо хоть что-то, музыку какую-то
VOLUME=50
/usr/bin/mocp --server
/usr/bin/mocp -c -a $MUSIC_DIR -v $VOLUME
#/usr/bin/mocp -p
exit 0

chmod +x start_moc.sh
crontab -e (под рутом)
добавим строку
@reboot /root/start_moc.sh
раскоментируем строку с параметром -p для теста и перезагрузим

# передача картинки на веб-панель
apt install exiftool imagemagick








