<?php
echo "<h3>Панель управления плеером</h3>";
?>
    <script>
        window.onload = setup();
        function getdata(command) {
            xhr = new XMLHttpRequest();
            var query = new FormData();
            query.append("command", command);
            xhr.open("post", "answer.php", false);
            xhr.send(query);
            var answer = JSON.parse(xhr.responseText);
            //console.log(answer)
            var temp_cpu = answer[answer.length-2];
            setDiv('temp_cpu', temp_cpu);

            var picture = answer[answer.length-1];
            var image = new Image();
            image.src = 'data:image/jpg;base64,' + picture;
            image.height = '150';
            var div_image = document.getElementById('img');
            while (div_image.firstChild) div_image.removeChild(div_image.firstChild);
            div_image.appendChild(image);

            var state = answer[0].split(':')[1];
            setDiv('state', state);

            var title = answer[4].split(':')[1];
            setDiv('title', title);

            var artist = answer[3].split(':')[1];
            setDiv('artist', artist);

            var current_time = answer[9].split(':')[1] + ':' + answer[9].split(':')[2];
            setDiv('current_time', current_time);

            var total_time = answer[6].split(':')[1] + ':' + answer[6].split(':')[2];
            setDiv('total_time', total_time);

            var left_time = answer[7].split(':')[1] + ':' + answer[7].split(':')[2];
            setDiv('left_time', left_time);

            var total_sec = answer[8].split(':')[1];
            var current_sec = answer[10].split(':')[1];
            document.getElementById('progress').max = total_sec;
            document.getElementById('progress').value = current_sec;

        }
        function setup() {
            setInterval(getdata, 1000);
        }
        function setDiv(div, value) {
            document.getElementById(div).innerHTML = value;
        }
        function selectAlbom(){
            var albom = document.getElementById('alboms').value;
            console.log(albom);
            getdata('select_albom:' + albom);
        }

    </script>
<?php
echo "<table border='0'>
<tr>
<td><div id='state'></div></td><td><div id='artist'></div></td>
<td><div id='title'></div><td rowspan='4'><div id='img'></div></td>
</td>
</tr>
<tr><td><div id='current_time'></div></td><td><progress id='progress'></progress></td><td><div id='left_time'></div></td></tr>
<tr><td><div id='total_time'></div></td></tr>
<tr>
<td colspan='3'><input id='previous' type='image'src='images/previous.png' height='48' onclick='getdata(\"previous\");' />
<input id='stop' type='image'src='images/stop.png' height='48' onclick='getdata(\"stop\");' />
<input id='pause' type='image'src='images/pause.png' height='48' onclick='getdata(\"pause\");' />
<input id='play' type='image'src='images/play.png' height='48' onclick='getdata(\"play\");' />
<input id='next' type='image'src='images/next.png' height='48' onclick='getdata(\"next\");' />
<input id='eject' type='image'src='images/eject.png' height='48' onclick='getdata(\"eject\");' /></td>
</tr>";
$music_dir = '/media/music';
$alboms = scandir($music_dir);array_shift($alboms);array_shift($alboms);
echo "<tr><td>Выбрать плейлист</td></tr>
<tr><td colspan='2'><select id='alboms' name='alboms'>";
foreach ($alboms as $k=>$row) {
    echo "<option>" . $row . "</option>";
}
echo "</select></td>
<td><input id='select_albom' type='button' value='Выбрать' onclick='selectAlbom();'></td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr><td>Температура процессора</td><td><div id='temp_cpu'></div></td></tr>
<tr>
<td><input id='reboot' type='button' value='Перезагрузить' onclick='getdata(\"reboot\");'></td>
<td><input id='poweroff' type='button' value='Выключить' onclick='getdata(\"poweroff\");'></td>
</tr>
</table>";

