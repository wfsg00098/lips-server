<?php
include "settings.php";
date_default_timezone_set("Asia/Shanghai");
header('content-type:application/json;charset=utf8');
$sql = mysqli_connect($sqladdr, $sqluser, $sqlpass);
mysqli_query($sql, "set names utf8mb4;");
mysqli_select_db($sql, $sqldbnm);
$username = covert($_GET["username"]);
$password = covert($_GET["password"]);

$result = mysqli_query($sql, "select * from user where username = '" . $username . "' and password = '" . $password . "';");
mysqli_data_seek($result, 0);
if (mysqli_num_rows($result)) {
    $arr['nickname'] = mysqli_fetch_row($result)[2];
    $arr['status'] = "success";
    die(json_encode($arr, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES));
} else {
    $arr['status'] = "failed";
    die(json_encode($arr));
}