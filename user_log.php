<?php
include "settings.php";
date_default_timezone_set("Asia/Shanghai");
header('content-type:application/json;charset=utf8');
$sql = mysqli_connect($sqladdr, $sqluser, $sqlpass);
mysqli_query($sql, "set names utf8mb4;");
mysqli_select_db($sql, $sqldbnm);
$username = covert($_GET["username"]);
$operation = $_GET["operation"];
$date = date("Y_m_d_H_i_s") . "_" . msectime();

$result = mysqli_query($sql, "insert into `#user_" . $username . "_log` values('" . $operation . "','" . $date . "');");
die();