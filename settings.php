
<?php

function covert2($str)
{
    $str1 = $str;
    $str1 = str_replace("'","\'",$str1);
    return $str1;
}
function isvalidstr($str)
{
    if (preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u", $str) && $str != "admin" && $str != "log" && substr($str, 0, 5) != "#user") return true;
    else return false;
}
function covert($str)
{
    $str1=$str;
    $str1=trim($str1);
    $str1=addslashes($str1);
    //$str1=str_replace("_","\_",$str1);
    //$str1=str_replace("%","\%",$str1);
    $str1=nl2br($str1);
    $str1=htmlspecialchars($str1);
    return $str1;
}
function msectime() {
    list($msec, $sec) = explode(' ', microtime());
    $msectime =  (float)sprintf('%.0f', floatval($msec) * 1000);
    return $msectime;
}
function logger($sql,$user,$operation){
    mysqli_query($sql,"insert log values('".$user."','".date("Y_m_d_H_i_s")."_".msectime()."','".$operation."');");
}

    $sqladdr = "localhost";
$sqluser = "lips";
$sqlpass = "kab3l7986519";
$sqldbnm = "lips";
$usercookie = "4da5f456af465as4fds45f49w8afe";
$tokencookie = "av4zb6gnu464gfv6a4f654ds6dsa4";
$nickcookie = "h34pfsd98a8u14jcjkds9acji4jqc";
$delpass = "wwwwwww";
    $sqlbkfn = "/root/lips/MySQL_lips_backup_";
    $file_upload_location = "/var/www/lips/upload/";
$file_save_location = "https://lips.guaiqihen.top/upload/";
    $no_need_login = false;
    $log_operation = true;
    $file_max_size = 4096000;

$server_maintenance = false;
$client_maintenance = false;

    //Font Size
    $start = 6;
    $end = 66;
    $gap = 2;
    $defa = 20;
