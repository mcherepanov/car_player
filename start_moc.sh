#!/bin/bash
# временно включение AUX
/root/set_aux.sh
#
MUSIC_DIR=/media/music/ # сюда положить надо хоть что-то, музыку какую-то
VOLUME=99
/usr/bin/mocp --server
/usr/bin/mocp -c -a $MUSIC_DIR -v $VOLUME
/usr/bin/mocp -p
exit 0
