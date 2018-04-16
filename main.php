<?php
include "settings.php";
date_default_timezone_set("Asia/Shanghai");
header('content-type:application/json;charset=utf8');
$arr['count'] = 0;
$sql = mysqli_connect($sqladdr, $sqluser, $sqlpass);// or die("Database Connection Failed");
mysqli_query($sql, "set names utf8mb4;");
$cat = $_GET['cat'];
mysqli_select_db($sql, $sqldbnm);// or die("Select Database Failed.");
$table = mysqli_query($sql, "select COLUMN_NAME from information_schema.COLUMNS where table_name = '" . $cat . "';");// or die("Get titles Failed");
mysqli_data_seek($table, 0);
$title = null;
$count = 0;

if ($client_maintenance) $arr['maintenance'] = 1;

while ($tablerow = mysqli_fetch_row($table)) $title[$count++] = $tablerow[0];
//for ($i=0;$i<sizeof($title);$i++) echo ($title[$i]."\n");
$result = mysqli_query($sql, "select * from " . $cat . ";");// or die("Query ".$cat." Failed");
if (!mysqli_num_rows($result) or $cat == "log" or $cat == "admin" or $client_maintenance or substr($cat, 0, 5) == "#user") die (json_encode($arr));
if ($cat == "ver") {
    mysqli_data_seek($result, 0);
    $row = mysqli_fetch_row($result);
    $arr['count'] = 1;
    $arr['version'] = $row[0];
    $arr['size'] = $row[1];
    die (json_encode($arr, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES));
} else {
    mysqli_data_seek($result, 0);

    $arr['count'] = null;
    $count = 0;
    while ($row = mysqli_fetch_row($result)) {
        $count++;
        for ($i = 0; $i < mysqli_num_fields($result); $i++)
            $arr[$title[$i] . $count] = $row[$i];
    }
    $arr['count'] = $count;
    die (json_encode($arr, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES));
}
?>