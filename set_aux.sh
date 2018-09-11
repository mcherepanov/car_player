#!/bin/bash

# пин ответа на плеер, что это AUX
# это REQC
REQC_PIN=15
/usr/local/bin/gpio mode $REQC_PIN out
/usr/local/bin/gpio write $REQC_PIN 1
exit 0


