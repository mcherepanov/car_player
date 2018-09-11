#!/bin/bash

# контрольный контакт для управления с плеера
# контакт CON
CON_PIN=1
gpio mode $CON_PIN in
# пин ответа на плеер, что это AUX
# это REQC
REQC_PIN=15
gpio mode $REQC_PIN out
# пин отключения звука при остановке во избежание фона
# это пин MUTE
MUTE_PIN=16
gpio mode $MUTE_PIN out
# частота опроса процесса
SLEEP=1
FLAG="OFF"

while true

do # основной бесконечный цикл

# командный блок
COMMAND=`gpio read $CON_PIN`
echo $COMMAND
#if [ "$COMMAND" != "1" ] # это отладка без машины
if [ "$COMMAND" == "1" ] # это работающая строка
then
	if [ "$FLAG" != "ON" ] # значит запуска не было, это включение зажигания
	then mocp -U
	FLAG="ON" # в следующий раз не будет запускать
	fi
#gpio write $MUTE_PIN 0
else
FLAG="OFF" # флаг для последующего запуска
mocp -P
#gpio write $MUTE_PIN 1
fi
# конец командного блока

# блок контроля, можно удалить или использовать в php-js
# STATUS=`mocp --info | grep PLAY | awk '{print $2}'`
STATUS=`mocp --format=%state`
# echo $STATUS
if [ "$STATUS" == "PLAY" ]
then echo "Работает"
else echo "Остановлен"
fi
# переписать с использованием форматирования строки, например  mocp --format=%t' '%state
# конец блока контроля

sleep $SLEEP
done

exit 0
