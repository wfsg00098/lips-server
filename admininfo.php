<!DOCTYPE HTML>
<html>
<head>
    <title>资料设置</title>
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
?>

<!-- Header -->
<header id="header">
    <a href="#" class="logo"><strong>资料设置</strong></a>
    <nav>
        <a href="#">欢迎您，<?php echo($_COOKIE[$nickcookie]);  ?></a>
        <a href="exit.php" class="icon fa-reply">退出登录</a>
    </nav>
</header>
<!-- Main -->
<section id="main">
    <div class="inner">
        <form method="post">
            若要修改资料，请输入旧密码
            <input type="password" name="old">
            <hr>
            新密码(不修改请留空)<input type="password" name="new">
            再次输入新密码<input type="password" name="new2">
            新昵称(不修改请留空)<input type="text" name="nick"><br>
            <input type="submit" name="submit" value="提交">
            <input type="submit" name="back" value="返回">
        </form>
        <?php
        if (isset($_POST["submit"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            $user = $_COOKIE[$usercookie];
            $old = covert($_POST["old"]);
            $new = covert($_POST["new"]);
            $new2 = covert($_POST["new2"]);
            $nick = covert($_POST["nick"]);
            if ($new != "")
            {
                if ($new != $new2)
                {
                    echo("<script language=\"JavaScript\">alert(\"两次输入的密码不一致！\");</script>");
                }
                else
                {
                    $result = mysqli_query($sql, "select * from admin where username = '" . $user . "' and password = '" . sha1($old) . "';");
                    mysqli_data_seek($result,0);
                    if (!mysqli_num_rows($result)) {
                        echo("<script language=\"JavaScript\">alert(\"旧密码错误！\");</script>");
                    }
                    else
                    {
                        $result = mysqli_query($sql, "update admin set password='" . sha1($new) . "' where username='" . $user . "';");
                        echo("<script language=\"JavaScript\">alert(\"密码修改成功！\");</script>");
                    }
                }
            }
            if ($nick!="")
            {
                $result = mysqli_query($sql, "select * from admin where username = '" . $user . "' and password = '" . sha1($old) . "';");
                mysqli_data_seek($result,0);
                if (!mysqli_num_rows($result))
                {
                    echo("<script language=\"JavaScript\">alert(\"旧密码错误！\");</script>");
                }
                else
                {
                    $result = mysqli_query($sql, "update admin set nickname='" . $nick . "' where username='" . $user . "';");
                    setcookie($nickcookie,$nick,0);
                    echo("<script language=\"JavaScript\">alert(\"昵称修改成功！\");</script>");
                }
            }
        }
        if (isset($_POST["back"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            echo("<script language=\"JavaScript\">window.location.href='manage_brand.php';</script>");
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