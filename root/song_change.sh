#!/bin/bash

PICTURE="/tmp/current_image.jpg"
FILE='"'`mocp --format=%file`'"'
COMMAND="/usr/bin/exiftool -b -Picture "$FILE" > "$PICTURE
rm $PICTURE
eval $COMMAND
COMMAND="mogrify -resize 150x150 $PICTURE"
eval $COMMAND
exit 0
