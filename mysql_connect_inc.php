<?php
//資料庫設定
//資料庫位置
$db_server = "120.113.174.17";
//資料庫名稱
$db_name = "";
//資料庫管理者帳號
$db_user = "";
//資料庫管理者密碼
$db_passwd = "";

$con= mysqli_connect($db_server, $db_user, $db_passwd);
//對資料庫連線
if(mysqli_connect_errno()) {
    die("無法對資料庫連線");
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
        
//資料庫連線採UTF8
mysqli_query($con,"SET NAMES utf8");

//選擇資料庫
if(!@mysqli_select_db($con,$db_name)){
	    die("無法使用資料庫");
}
?> 