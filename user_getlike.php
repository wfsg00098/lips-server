<?php
include "settings.php";
date_default_timezone_set("Asia/Shanghai");
header('content-type:application/json;charset=utf8');
$sql = mysqli_connect($sqladdr, $sqluser, $sqlpass);
mysqli_query($sql, "set names utf8mb4;");
mysqli_select_db($sql, $sqldbnm);
$username = covert($_GET["username"]);
$item = $_GET["item"];


if ($item == "!all") {
    $table = mysqli_query($sql, "select COLUMN_NAME from information_schema.COLUMNS where table_name = '#user_" . $username . "_like';");// or die("Get titles Failed");
    mysqli_data_seek($table, 0);
    $title = null;
    while ($tablerow = mysqli_fetch_row($table)) $title[$count++] = $tablerow[0];
    $result = mysqli_query($sql, "select * from `#user_" . $username . "_like`;");

    $arr['count'] = null;
    $count = 0;
    while ($row = mysqli_fetch_row($result)) {
        $count++;
        for ($i = 0; $i < mysqli_num_fields($result); $i++)
            $arr[$title[$i] . $count] = $row[$i];
    }
    $arr['count'] = $count;
    die(json_encode($arr, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES));
} else {
    $result = mysqli_query($sql, "select * from `#user_" . $username . "_like` where item = '" . $item . "';");
    mysqli_data_seek($result, 0);
    if (mysqli_num_rows($result)) {
        $arr['status'] = "true";
        die(json_encode($arr));
    } else {
        $arr['status'] = "false";
        die(json_encode($arr));
    }
}