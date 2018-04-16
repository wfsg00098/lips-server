<!DOCTYPE HTML>
<html>
<head>
    <title>系统登录</title>
    <meta name="theme-color" content="#1c1c1c" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="assets/css/main.css" />
</head>
<body>


<!-- Header -->
<header id="header">
    <a href="#" class="logo"><strong>系统登录</strong></a>
</header>

<?php include "settings.php";


if ($server_maintenance) {
    echo("<script language=\"JavaScript\">alert(\"后台系统维护中！\");</script>");
    die();
}
?>
<!-- Main -->
<section id="main">
    <div class="inner">
        <form method="post">
            <p>用户名：</p>
            <input type="text" name="user" value='<?php echo($_COOKIE[$usercookie]) ?>'>
            <br>
            <p>密码：</p>
            <input type="password" name="pass">
            <br>
            <input type="submit" name="login" value="登录">
        </form>
        <?php
        if (isset($_POST["login"]))
        {
            date_default_timezone_set("Asia/Shanghai");
            $sql = mysqli_connect($sqladdr,$sqluser,$sqlpass);// or die("Database Connection Failed");
            mysqli_query($sql,"set names utf8mb4;");
            mysqli_select_db($sql,$sqldbnm);// or die("Select Database Failed.");
            $username = covert($_POST["user"]);
            $password = sha1(covert($_POST["pass"]));
            $result = mysqli_query($sql, "select * from admin where username = '" . $username . "' and password = '" . $password . "';");// or die("Query failed.");
            if (!mysqli_num_rows($result))
            {
                echo("<script language=\"JavaScript\">alert(\"登录失败！\");</script>");
            }
            else
            {
                mysqli_data_seek($result,0);
                $nickname = mysqli_fetch_row($result)[3];
                $token = sha1(sha1(sha1($password).$username).date("YmdHis").msectime());
                setcookie($usercookie,$username,0);
                setcookie($tokencookie,$token,0);
                setcookie($nickcookie,$nickname,0);
                mysqli_query($sql, "update admin set token='" . $token . "' where username='" . $username . "';");// or die("Set Token Failed.");
                if ($log_operation) logger($sql,$username,"登录");
		echo("<script language=\"JavaScript\">alert(\"登录成功！\");</script>");
                echo("<script language=\"JavaScript\">window.location.href='manage_brand.php';</script>");
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
