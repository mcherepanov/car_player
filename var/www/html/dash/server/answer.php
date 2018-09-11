<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 10.09.18
 * Time: 15:12
 */
$music_dir = '/media/music';
$volume = "99";
$answer = (`mocp -i`);
$answer = explode("\n", $answer);
$data = [];
foreach ($answer as $row) {
    $index = explode(':', $row)[0];
    $value = explode(':', $row)[1];
    if ($index == 'TotalTime' || $index == 'TimeLeft' || $index == 'CurrentTime') {
        $value = explode(':', $row)[1] . ':' . explode(':', $row)[2];
    }
    if (($index != '') && ($value != '')) {
        //echo $index . "=" . $value . '<br>';
        $data[$index] = $value;
    }

}
// добавление различной информации
// температура
$temp_cpu = (`cat /sys/class/thermal/thermal_zone0/temp`) / 1000;
$data['Temp_cpu'] = $temp_cpu;
echo json_encode($data);