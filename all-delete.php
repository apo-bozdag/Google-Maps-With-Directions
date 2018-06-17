<?php
$ip = $_SERVER['REMOTE_ADDR'];
$files = glob( "history/".$ip.'/*'); // get all file names
foreach($files as $file){ // iterate files
    if(is_file($file))
        unlink($file); // delete file
}

    header("Location: https://www.sanalyer.com/maps/");
    die();

