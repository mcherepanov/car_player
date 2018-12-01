<?php
ob_start();
var_dump($_POST);
file_put_contents('/tmp/save_form', ob_get_clean());