<?php
include "settings.php";
date_default_timezone_set("Asia/Shanghai");
header('content-type:application/json;charset=utf8');
$sql = mysqli_connect($sqladdr, $sqluser, $sqlpass);
mysqli_query($sql, "set names utf8mb4;");
mysqli_select_db($sql, $sqldbnm);
$username = covert($_GET["username"]);
$password = covert($_GET["password"]);
$nickname = covert($_GET["nickname"]);
$date = date("Y_m_d_H_i_s") . "_" . msectime();

$result = mysqli_query($sql, "select * from user where username='" . $username . "';");
mysqli_data_seek($result, 0);
if (mysqli_num_rows($result)) {
    $arr['status'] = "duplicated";
    die(json_encode($arr));
}

$result = mysqli_query($sql, "insert into user values('" . $username . "','" . $password . "','" . $nickname . "','" . $date . "');");
$result = mysqli_query($sql, "select * from user where username='" . $username . "';");
mysqli_data_seek($result, 0);
if (mysqli_num_rows($result)) {
    $result = mysqli_query($sql, "create table `#user_" . $username . "_log` (`operation` text, `date` text);");
    $result = mysqli_query($sql, "create table `#user_" . $username . "_like` (`type` text, `item` text, `show` text, `color` text);");
    $arr['status'] = "success";
    die(json_encode($arr));
} else {
    $arr['status'] = "failed";
    die(json_encode($arr));
}

