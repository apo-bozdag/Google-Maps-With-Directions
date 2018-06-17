<?php
    if(isset($_GET["file"])){
        $ip = $_SERVER['REMOTE_ADDR'];
        $file = $_GET["file"];
        $folder = "history/".$ip.'/'.$file;
        unlink($folder);
        header("Location: https://www.sanalyer.com/maps/");
        die();
    }else{
        header("Location: https://www.sanalyer.com/maps/");
        die();
    }
