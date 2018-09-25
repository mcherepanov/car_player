<?php
/**
 * Created by PhpStorm.
 * User: proton
 * Date: 07.09.18
 * Time: 15:00
 */
$picture = file_get_contents('/tmp/current_image.jpg');
$base64picture = base64_encode($picture);
file_put_contents('/tmp/data', $base64picture);