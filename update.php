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
<?php include "settings.php";
function islogged($sql,$usercookie,$tokencookie,$no_need_login)
{
    if ($no_need_login) return;
    $user = $_COOKIE[$usercookie];
    $token = $_COOKIE[$tokencookie];
    $result = mysqli_query($sql, "select * from user where username = '" . $user . "' and token = '" . $token . "';");
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

$result = mysqli_query($sql,"select * from ver;");
mysqli_data_seek($result,0);
$row = mysqli_fetch_row($result);
$currentver = $row[0];
?>


<!-- Header -->
<header id="header">
    <a href="#" class="logo"><strong>APP更新发布(当前版本:<?php echo ($currentver);?>)</strong></a>
    <nav>
        <a href="userinfo.php">欢迎您，<?php echo($_COOKIE[$nickcookie]);  ?></a>
        <a href="exit.php" class="icon fa-reply">退出登录</a>
    </nav>
</header>


<!-- Main -->
<section id="main">
    <div class="inner">
        <form method="post" enctype="multipart/form-data">
            新版本号：
            <input type="text" name="version">
            <br>
            文件大小：
            <input type="text" name="size">
            <br>
            APK文件：
            <input type="file" name="apk">
            <br><br>
            <input type="submit" name="add" value="确定上传">
            <input type="submit" name="back" value="返回">
        </form>
        <?php
        if (isset($_POST["back"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            echo("<script language=\"JavaScript\">window.location.href='manage_brand.php';</script>");
        }
        if (isset($_POST["add"]))
        {
            islogged($sql,$usercookie,$tokencookie,$sqldbnm);
            $version = covert($_POST["version"]);
            $size = covert($_POST["size"]);

            if ($version == "" or $size == "" or $_FILES["apk"]["name"] == "")
            {
                echo("<script language=\"JavaScript\">alert(\"请将内容填写完整！\");</script>");
            }
            elseif ($version <= $currentver)
            {
                echo("<script language=\"JavaScript\">alert(\"版本号不能低于当前版本！\");</script>");
            }
            else
            {
                move_uploaded_file($_FILES["apk"]["tmp_name"],"/var/www/lips/update/".$version.".apk");
                mysqli_query($sql,"update ver set version='".$version."' , size='".$size."';");
                echo("<script language=\"JavaScript\">alert(\"更新成功！\");</script>");
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