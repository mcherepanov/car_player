<?php
$music_dir = '/media/music';
if (count(scandir($music_dir)) == 2) $music_dir = '/root/flash_absent';
$setup_dir = "/tmp";
$setup_file = $setup_dir . '/mocp_setup_options';
if (!file_exists($setup_file)) {
    $setup['shuffle'] = 'off';
    $setup['repeat'] = 'off';
} else $setup = unserialize(file_get_contents($setup_file));
$volume = "99";
if (isset ($_POST) && ($_POST != '')) {
    // отладка
    ob_start();
    var_dump($_POST);
    file_put_contents('/tmp/commands', ob_get_clean());
    // отладка
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

    // shuffle
    if ($_POST['command'] == "shuffle-true") {
        $setup['shuffle'] = 'on';
        (`mocp -o s`);
        file_put_contents($setup_file, serialize($setup));

    }
    if ($_POST['command'] == "shuffle-false") {
        $setup['shuffle'] = 'off';
        (`mocp -u s`);
        file_put_contents($setup_file, serialize($setup));

    }
    // repeat
    if ($_POST['command'] == "repeat-true") {
        $setup['repeat'] = 'on';
        (`mocp -o r`);
        file_put_contents($setup_file, serialize($setup));
    }
    if ($_POST['command'] == "repeat-false") {
        $setup['repeat'] = 'off';
        (`mocp -u r`);
        file_put_contents($setup_file, serialize($setup));
    }
//======================================
    if ($_POST['command'] == "poweroff") {
        (`poweroff`);
    }
    if ($_POST['command'] == "reboot") {
        (`reboot`);
    }
//======================================

    if ($_POST['command'] == "eject") {
        //(`mocp -s -c`); // пока скрипт не готов - не делать ничего
        // отмонтировать флешку и запустить скрипт сканирования на подключение новой флешки
    }
//=================================
    if (strpos($_POST['command'], 'changedir') !== false) {
        (`rm /tmp/test/*`);
        $subdir = $music_dir . '/' . explode(':', $_POST['command'])[1];
        file_put_contents('/tmp/test/1_changedir', $subdir); // тут должны быть файлы, вложение 2 уровня

        $array = scandir($subdir);
        array_shift($array);
        array_shift($array);
        ob_start();
        var_dump($array);
        file_put_contents('/tmp/test/2_array', ob_get_clean());

        $alboms = [];

        foreach ($array as $k => $row) {
            //$newrow = $music_dir . '/' . str_replace(' ', "\ ", $row);
            $newrow = $subdir . '/' . $row;
            file_put_contents('/tmp/test/3_' . $k, $newrow);
            if (is_dir($newrow)) {
                array_push($alboms, $newrow);
            }
        }
        // на выходе должен быть массив директорий, или пустой массив, если там их нет
        ob_start();
        var_dump($alboms);
        file_put_contents('/tmp/test/4_alboms', ob_get_clean());
        if (count($alboms) != 0) {
            $res = true;

        } else {
            $res = false;
        }
        file_put_contents('/tmp/test/5_is_albom', $res);
    }
    // -------------------------------------------
    if (strpos($_POST['command'], 'select_albom') !== false) {
        (`mocp -s`);
        if ($_POST['command'] == 'select_albom:all') $directory = $music_dir;
        else {
            $albom = str_replace(' ', "\ ", explode(':', $_POST['command'])[1]);
            $directory = $music_dir . '/' . $albom;
        }
        $command = "ls " . $directory;
        $list = (`$command`);
        file_put_contents('/tmp/albom', $list);
        $command = "mocp -c -a " . $directory . " -v " . $volume;
        file_put_contents('/tmp/command_albom', $command);
        (`$command`);
        (`mocp -p`);

    }
}
//----------
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
$data['temp_cpu'] = $temp_cpu;
// добавление информации из файла настроек
$data = array_merge($data, $setup);
// картинка альбома, если она есть
$picture = base64_encode(file_get_contents('/tmp/current_image.jpg'));
$data['picture'] = $picture;

// отладка

if (count($alboms) != 0) {
    ob_start();
    $data['alboms'] = $alboms;
    var_dump(array_merge($data));
    file_put_contents('/tmp/test/data', ob_get_clean());
}

//
echo json_encode($data);

