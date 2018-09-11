#!/bin/bash

while inotifywait -r /home/proton/player/*
do

HOSTS=('172.24.1.1')

for HOST in ${HOSTS[@]}
do

FILES=('/etc/' '/var/' '/root/')

for FILE in ${FILES[@]}
do
rsync --delete -e "ssh -l root" -avz /home/proton/player$FILE $HOST:$FILE
done

done

done
