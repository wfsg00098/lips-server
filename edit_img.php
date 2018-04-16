<!DOCTYPE HTML>
<html>
<head>
    <title>系统管理</title>
    <meta name="theme-color" content="#1c1c1c" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="assets/css/main.css" />
</head>
<body>
<?php
include "settings.php";

if ($server_maintenance) {
    echo("<script language=\"JavaScript\">alert(\"后台系统维护中！\");</script>");
    die();
}

function islogged($sql,$usercookie,$tokencookie,$no_need_login)
{
    if ($no_need_login) return;
    $user = $_COOKIE[$usercookie];
    $token = $_COOKIE[$tokencookie];
    $result = mysqli_query($sql, "select * from admin where username = '" . $user . "' and token = '" . $token . "';");
    if (!mysqli_num_rows($result))
    {
        echo("<script language=\"JavaScript\">alert(\"请先登录！\");</script>");
        echo("<script language=\"JavaScript\">window.location.href='login.php';</script>");
        die();
    }
}
date_default_timezone_set("Asia/Shanghai");
$sql = mysqli_connect($sqladdr,$sqluser,$sqlpass);// or die("Database Connection Failed");
mysqli_query($sql,"set names utf8mb4;");
mysqli_select_db($sql,$sqldbnm);
islogged($sql,$usercookie,$tokencookie,$no_need_login);
$brand = $_GET["brand"];
$brand_show = $_GET["brand_show"];
$series = $_GET["series"];
$series_show = $_GET["series_show"];
$number = $_GET["number"];
$predescrib = $_GET["describ"];
$result = mysqli_query($sql,"select * from `".$brand."_".$series."_".$number."` where describ='".$predescrib."';");
if (!mysqli_fetch_row($result))
{
    echo("<script language=\"JavaScript\">alert(\"请先选择内容！\");</script>");
    echo("<script language=\"JavaScript\">window.location.href='manage_content.php?brand=".covert2($brand)."&brand_show=".covert2($brand_show)."&series=".covert2($series)."&series_show=".covert2($series_show)."&number=".covert2($number)."';</script>");
    die();
}
else
{
    mysqli_data_seek($result,0);
    $row = mysqli_fetch_row($result);
    $type = $row[0];
    if ($type != "img")
    {
        echo("<script language=\"JavaScript\">alert(\"内容类型不符，请选择相应的编辑器！\");</script>");
        echo("<script language=\"JavaScript\">window.location.href='manage_content.php?brand=".covert2($brand)."&brand_show=".covert2($brand_show)."&series=".covert2($series)."&series_show=".covert2($series_show)."&number=".covert2($number)."';</script>");
        die();
    }
    else
    {
        $precontent = $row[1];
    }
}
?>

<!-- Header -->
<header id="header">
    <a href="#" class="logo"><strong>内容编辑(图片)</strong>&nbsp;&nbsp;(当前色号：<?php echo($brand_show."&nbsp;-&nbsp;".$series_show."&nbsp;-&nbsp;".$number); ?>)</a>
    <nav>
        <a href="admininfo.php">欢迎您，<?php echo($_COOKIE[$nickcookie]); ?></a>
        <a href="exit.php" class="icon fa-reply">退出登录</a>
    </nav>
</header>


<!-- Main -->
<section id="main">
    <div class="inner">
        <div class="image fit">
            <img src='<?php echo($precontent); ?>'/>
        </div>
        <form method="post" enctype="multipart/form-data">
            简要说明<input type="text" name="describ" value='<?php echo($predescrib); ?>'><br>
            新图片（支持jpg、gif、png格式，大小≤2MB）<br><input type="file" name="content"><br><br>
            <input type="submit" name="add" value="提交">
            <input type="reset" value="重置"><br><br><hr>
            <input type="submit" name="back" value="返回">
        </form>

        <?php
        if (isset($_POST["back"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            echo("<script language=\"JavaScript\">window.location.href='manage_content.php?brand=".covert2($brand)."&brand_show=".covert2($brand_show)."&series=".covert2($series)."&series_show=".covert2($series_show)."&number=".covert2($number)."';</script>");
        }

        if (isset($_POST["add"]))
        {
            islogged($sql, $usercookie, $tokencookie, $no_need_login);
            $describ = covert($_POST["describ"]);
            if ($describ == "" or $_FILES["content"]["name"] == "")
            {
                echo("<script language=\"JavaScript\">alert(\"请将内容填写完整！\");</script>");
            }
            else
            {
                if (!((($_FILES["content"]["type"] == "image/gif")
                        || ($_FILES["content"]["type"] == "image/jpeg")
                        || ($_FILES["content"]["type"] == "image/pjpeg")
                        || ($_FILES["content"]["type"] == "image/png"))
                        && ($_FILES["content"]["size"] < $file_max_size)))
                {
                    echo("<script language=\"JavaScript\">alert(\"文件过大或类型不符！\");</script>");
                }
                else if ($_FILES["content"]["error"] > 0)
                {
                    echo("<script language=\"JavaScript\">alert(\"上传失败！\");</script>");
                }
                else
                {
                    $result = mysqli_query($sql, "select * from `" . $brand . "_" . $series . "_" . $number . "` where describ='" . $describ . "';");
                    if (!mysqli_num_rows($result))
                    {
                        $tempname = explode(".",$_FILES["content"]["name"]);
                        $extname = $tempname[sizeof($tempname)-1];
                        $content = $brand."_".$series."_".$number."_".date("Y_m_d_H_i_s")."_".msectime().".".$extname;
                        $content = covert($content);
                        move_uploaded_file($_FILES["content"]["tmp_name"],$file_upload_location.$content);
                        $result = mysqli_query($sql, "update `" . $brand . "_" . $series . "_" . $number . "` set describ='" . $describ . "' , content='" .$file_save_location . $content . "' where describ='" . $predescrib . "';");
                        if ($log_operation) logger($sql,$_COOKIE[$usercookie],"修改图片：".$brand." - ".$series." - ".$number."-".$describ."(".$brand_show." - ".$series_show." - ".$number.")");
                        echo("<script language=\"JavaScript\">alert(\"修改成功！\");</script>");
                        echo("<script language=\"JavaScript\">window.location.href='manage_content.php?brand=".covert2($brand)."&brand_show=".covert2($brand_show)."&series=".covert2($series)."&series_show=".covert2($series_show)."&number=".covert2($number)."';</script>");
                    }
                    else
                    {
                        mysqli_data_seek($result, 0);
                        if (mysqli_fetch_row($result)[2] != $predescrib)
                        {
                            echo("<script language=\"JavaScript\">alert(\"简要说明重复！\");</script>");
                        }
                        else
                        {
                            $tempname = explode(".",$_FILES["content"]["name"]);
                            $extname = $tempname[sizeof($tempname)-1];
                            $content = $brand."_".$series."_".$number."_".date("Y_m_d_H_i_s")."_".msectime().".".$extname;
                            $content = covert($content);
                            move_uploaded_file($_FILES["content"]["tmp_name"],$file_upload_location.$content);
                            $result = mysqli_query($sql, "update `" . $brand . "_" . $series . "_" . $number . "` set describ='" . $describ . "' , content='" .$file_save_location . $content . "' where describ='" . $predescrib . "';");
                            if ($log_operation) logger($sql,$_COOKIE[$usercookie],"修改图片：".$brand." - ".$series." - ".$number." - ".$describ."(".$brand_show." - ".$series_show." - ".$number.")");
                            echo("<script language=\"JavaScript\">alert(\"修改成功！\");</script>");
                            echo("<script language=\"JavaScript\">window.location.href='manage_content.php?brand=".covert2($brand)."&brand_show=".covert2($brand_show)."&series=".covert2($series)."&series_show=".covert2($series_show)."&number=".covert2($number)."';</script>");
                        }
                    }
                }
            }
        }

        ?>

    </div>
</section>

<!-- Footer -->
<footer id="footer">
    <div class="copyright">Copyright &copy; 2017-<?php echo(date("Y")); ?>. 王七喜♏ All rights reserved.</div>
</footer>

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/jquery.scrolly.min.js"></script>
<script src="assets/js/skel.min.js"></script>
<script src="assets/js/util.js"></script>
<script src="assets/js/main.js"></script>



</body>
</html>