<?php
include "settings.php";
date_default_timezone_set("Asia/Shanghai");
header('content-type:application/json;charset=utf8');
$sql = mysqli_connect($sqladdr, $sqluser, $sqlpass);
mysqli_query($sql, "set names utf8mb4;");
mysqli_select_db($sql, $sqldbnm);
$username = covert($_GET["username"]);
$item = $_GET["item"];

$result = mysqli_query($sql, "delete from `#user_" . $username . "_like` where item = '" . $item . "';");
$result = mysqli_query($sql, "select * from `#user_" . $username . "_like` where item = '" . $item . "';");
mysqli_data_seek($result, 0);
if (mysqli_num_rows($result)) {
    $arr['status'] = "failed";
    die(json_encode($arr));
} else {
    $arr['status'] = "success";
    die(json_encode($arr));
}