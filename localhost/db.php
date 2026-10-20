<?php
    $servername = "localhost";
    $hostusername = "root";
    $hostpassword = "";
    $dbname = "game-shop";
    
    $con = mysqli_connect($servername, $hostusername, $hostpassword, $dbname);

    if(!$con){
        die("Ошибка подключения" . mysqli_connect_error());
    }
    if(session_status()===PHP_SESSION_NONE){
        session_start();
    }