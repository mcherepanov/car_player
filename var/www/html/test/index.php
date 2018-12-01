<?php
echo "<img src='images/kenwood.png' height='50'/><div id='state'></div>";
?>
    <script>
        window.onload = setup();
        function getdata(command) {
            if (typeof (command) !== 'undefined') console.log(command);
            xhr = new XMLHttpRequest();
            var query = new FormData('main_form');
            query.append("command", command);
            xhr.open("post", "answer.php", false);
            xhr.send(query);
            var answer = JSON.parse(xhr.responseText);
            //console.log(answer)

            // температура процессора
            setDiv('temp_cpu', answer.temp_cpu);
            // картинка альбома
            var image = new Image();
            image.src = 'data:image/jpg;base64,' + answer.picture;
            image.height = '150';
            var div_image = document.getElementById('img');
            while (div_image.firstChild) div_image.removeChild(div_image.firstChild);
            div_image.appendChild(image);

            // состояние плеера
            setDiv('state', '<h3>Статус: ' + answer.State + '</h3>');
            // сводная информация - артист, композиция и общее время
            setDiv('artist_songtitle_time', answer.Artist + ': ' + answer.SongTitle + ' (' + answer.TotalTime + ' )');
            // осталось до конца песни
            setDiv('time_left', answer.TimeLeft);
            // прогресс-бар
            document.getElementById('progress').max = answer.TotalSec;
            document.getElementById('progress').value = answer.CurrentSec;
            //
            document.getElementById('shuffle').checked = answer.shuffle === 'on' ? true : false;
            document.getElementById('repeat').checked = answer.repeat === 'on' ? true : false;

            // альбомы
            if (typeof (answer.alboms) !== 'undefined') {
                //console.log(answer.alboms.length);
                //console.log(answer.alboms);
                //document.getElementById('subalboms').innerHTML() // дописать
                var select = document.getElementById('subalboms');
                // чистим старое
                while (select.firstChild) select.removeChild(select.firstChild);
                // вставим один элемент по умолчанию
                var allAlboms = new Option('Все альбомы', 'all');
                select.appendChild(allAlboms);
                // добавление перечня альбомов
                for (var i in answer.alboms) {
                    //console.log(answer.alboms[i])
                    var arrayAlbom = answer.alboms[i].split('/');
                    var arrayLength = arrayAlbom.length;
                    var albom = arrayAlbom[arrayLength - 1];
                    console.log(albom);
                    var newOption = new Option(albom, albom);
                    select.appendChild(newOption);
                }
            }
            // конец альбомов


        }
        function sendForm() {
            console.log('отправка формы');
            xhr = new XMLHttpRequest();
            var oldForm = document.forms.main_form, query = new FormData(oldForm);
            console.log(query);
            xhr.open("post", "save_form.php", true);
            xhr.send(query);
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
        function changeDirectory(directory) {
            console.log(directory);
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
echo "<form id='main_form' name='main_form'><table border='0'>
<tr>
<td colspan='3'><div id='artist_songtitle_time'></div></td><td rowspan='3'><div id='img'></div></td>
</td>
</tr>
<tr>

<td colspan='2'><progress id='progress'></progress></td>
<td><div><div id='time_left'></td></div></td></tr>
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
echo "<tr><td>Исполнитель</td><td>&nbsp;&nbsp;Случайно<input id='shuffle' type='checkbox' onchange='getdata(\"shuffle-\" + this.checked)'/></td>
<td>&nbsp;&nbsp;Повтор<input id='repeat' type='checkbox' onchange='getdata(\"repeat-\" + this.checked)'/></td>
</tr>

<tr><td colspan='3'><select id='alboms' name='alboms' onchange='getdata(\"changedir:\" + this.value)'>
<option value='all'>Вся коллекция</option>";
foreach ($alboms as $k => $row) {
    echo "<option>" . $row . "</option>";
}
echo "</select>
&nbsp;&nbsp;&nbsp;<input id='select_albom' type='button' value='Выбрать' onclick='selectAlbom();'>
&nbsp;&nbsp;<input type='button' value='Отправка формы' onclick='sendForm();'></td>
</tr>
<tr>
<td>
<select id='subalboms' name='subalboms' onchange='getdata(\"select_albom:\" + this.value)'>
<option value='all'>Все альбомы</option>
</td>
</tr>

<tr><td>&nbsp;</td></tr>
<tr><td>Температура процессора</td><td><div id='temp_cpu'></div></td></tr>
<tr>
<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
<td><input id='reboot' type='button' value='Перезагрузить' onclick='getdata(\"reboot\");'></td>
<td><input id='poweroff' type='button' value='Выключить' onclick='getdata(\"poweroff\");'></td>
</tr>
</table></form>";

