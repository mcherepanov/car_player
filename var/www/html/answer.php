<?php
$music_dir = '/media/music';
$volume = "99";
if (isset ($_POST) && ($_POST != '')) {
    file_put_contents('/tmp/command', $_POST['command']);
    if ($_POST['command'] == "next") {
        (`mocp -f`);
    }
    if ($_POST['command'] == "stop") {
        (`mocp -s`);
    }
    if ($_POST['command'] == "pause") {
        (`mocp -P`);
    }
    if ($_POST['command'] == "play") {
        (`mocp -G`);
    }
    if ($_POST['command'] == "previous") {
        (`mocp -r`);
    }
    if ($_POST['command'] == "eject") {
        (`mocp -s -c`);
        // отмонтировать флешку и запустить скрипт сканирования на подключение новой флешки
    }
    if ($_POST['command'] == "poweroff") {
        (`poweroff`);
    }
    if ($_POST['command'] == "reboot") {
        (`reboot`);
    }
    if (strpos($_POST['command'], 'select_albom') !== false) {
        $albom = str_replace(' ', "\ ", explode(':', $_POST['command'])[1]);
        $directory = $music_dir . '/' . $albom;
        $command = "ls " . $directory;
        $list = (`$command`);
        file_put_contents('/tmp/albom', $list );
        $command = "/usr/bin/mocp -c -a " . $directory . " -v " . $volume;
        file_put_contents('/tmp/command_albom', $command );
        (`$command`);
        (`/usr/bin/mocp -p`);

    }
}
$answer = (`mocp -i`);
$answer = explode("\n", $answer);
// добавление различной информации
// температура
$temp_cpu = (`cat /sys/class/thermal/thermal_zone0/temp`)/1000;
array_push($answer, $temp_cpu);
// картинка альбома, если она есть
$picture = file_get_contents('/tmp/current_image.jpg');
$base64picture = base64_encode($picture);
file_put_contents('/tmp/data', $base64picture);
array_push($answer,$base64picture);
//
echo json_encode($answer);

