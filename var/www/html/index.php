<?php
echo "<img src='images/kenwood.png' height='50'/><div id='state'></div>";
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
            var temp_cpu = answer[answer.length - 2];
            setDiv('temp_cpu', temp_cpu);

            var picture = answer[answer.length - 1];
            var image = new Image();
            image.src = 'data:image/jpg;base64,' + picture;
            image.height = '150';
            var div_image = document.getElementById('img');
            while (div_image.firstChild) div_image.removeChild(div_image.firstChild);
            div_image.appendChild(image);

            var state = answer[0].split(':')[1];
            setDiv('state', '<h3>Статус: ' + state + '</h3>');

            var total_time = answer[6].split(':')[1] + ':' + answer[6].split(':')[2];
            var artist_title = answer[3].split(':')[1] + ': ' + answer[4].split(':')[1];
            setDiv('artist_title', artist_title + ' (' + total_time + ' )');

            //var current_time = answer[9].split(':')[1] + ':' + answer[9].split(':')[2];
            //setDiv('current_time', current_time);

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
        function selectAlbom() {
            var albom = document.getElementById('alboms').value;
            console.log(albom);
            getdata('select_albom:' + albom);
        }

    </script>
    <style>
        progress { /* контейнер */
            -webkit-appearance: none; /* убрать вид, который задан браузером по умолчанию */
            border: none; /* убрать рамку в Firefox */
            width: 100%;
            height: 20px;
            border-radius: 3px;
            color: #4d7198; /* цветная линия-индикатор в IE */
            background: #eee;
            background-image: linear-gradient(to right, #e4e4e4 1px, transparent 1px),
            linear-gradient(rgba(0, 0, 0, .2) 1px, rgba(255, 255, 255, .4) 1px, rgba(0, 0, 0, .2));
            background-size: 10% 100%, 100% 100%;
        }

        .progress::-webkit-progress-bar { /* контейнер в Webkit */
            border-radius: 3px;
            background: #eee;
            background-image: linear-gradient(to right, #e4e4e4 1px, transparent 1px),
            linear-gradient(rgba(0, 0, 0, .2) 1px, rgba(255, 255, 255, .4) 1px, rgba(0, 0, 0, .2));
            background-size: 10% 100%, 100% 100%;
        }

        .progress::-moz-progress-bar { /* разноцветная линия-индикатор в Firefox */
            border-radius: 3px 5px 5px 3px;
            background: #4d7198;
            background-image: linear-gradient(135deg, transparent, transparent 33%, rgba(0, 0, 0, .1) 33%, rgba(0, 0, 0, .1) 66%, transparent 66%),
            linear-gradient(rgba(0, 0, 0, .2) 1px, rgba(255, 255, 255, .4) 1px, rgba(0, 0, 0, .2)),
            linear-gradient(to left, #00baff, #4d7198);
            background-size: 35px 20px, 100% 100%, 100% 100%;
        }

        .progress::-webkit-progress-value { /* разноцветная линия-индикатор в Webkit */
            border-radius: 3px 5px 5px 3px;
            background: #4d7198;
            background-image: linear-gradient(135deg, transparent, transparent 33%, rgba(0, 0, 0, .1) 33%, rgba(0, 0, 0, .1) 66%, transparent 66%),
            linear-gradient(rgba(0, 0, 0, .2) 1px, rgba(255, 255, 255, .4) 1px, rgba(0, 0, 0, .2)),
            linear-gradient(to left, #00baff, #4d7198);
            background-size: 35px 20px, 100% 100%, 100% 100%;
            -webkit-animation: animate-stripes 4s linear infinite;
        }

        @-webkit-keyframes animate-stripes {
            0% {
                background-position: 0 0, 0 0, 0 0;
            }
            100% {
                background-position: -105px 0, 0 0, 0 0;
            }
        }
    </style>
<?php
echo "<table border='1'>
<tr>
<td colspan='3'><div id='artist_title'></div></td><td rowspan='3'><div id='img'></div></td>
</td>
</tr>
<tr>

<td colspan='2'><progress id='progress'></progress></td>
<td><div><div id='left_time'></td></div></td></tr>
</tr>
<tr>
<td colspan='3'><input id='previous' type='image'src='images/previous.png' height='48' onclick='getdata(\"previous\");' />
<input id='stop' type='image'src='images/stop.png' height='48' onclick='getdata(\"stop\");' />
<input id='pause' type='image'src='images/pause.png' height='48' onclick='getdata(\"pause\");' />
<input id='play' type='image'src='images/play.png' height='48' onclick='getdata(\"play\");' />
<input id='next' type='image'src='images/next.png' height='48' onclick='getdata(\"next\");' />
<input id='eject' type='image'src='images/eject.png' height='48' onclick='getdata(\"eject\");' /></td>
</tr>";
$music_dir = '/media/music';
$alboms = scandir($music_dir);
array_shift($alboms);
array_shift($alboms);
echo "<tr><td>Папка</td></tr>
<tr><td colspan='3'><select id='alboms' name='alboms'>";
foreach ($alboms as $k => $row) {
    echo "<option>" . $row . "</option>";
}
echo "</select>
&nbsp;&nbsp;&nbsp;<input id='select_albom' type='button' value='Выбрать' onclick='selectAlbom();'></td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr><td>Температура процессора</td><td><div id='temp_cpu'></div></td></tr>
<tr>
<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
<td><input id='reboot' type='button' value='Перезагрузить' onclick='getdata(\"reboot\");'></td>
<td><input id='poweroff' type='button' value='Выключить' onclick='getdata(\"poweroff\");'></td>
</tr>
</table>";

