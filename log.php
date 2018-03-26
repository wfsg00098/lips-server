<!DOCTYPE HTML>
<html>
<head>
    <title>系统管理</title>
    <meta name="theme-color" content="#1c1c1c"/>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="assets/css/main.css"/>
</head>
<body>
<?php include "settings.php";
function islogged($sql, $usercookie, $tokencookie, $no_need_login)
{
    if ($no_need_login) return;
    $user = $_COOKIE[$usercookie];
    $token = $_COOKIE[$tokencookie];
    $result = mysqli_query($sql, "select * from user where username = '" . $user . "' and token = '" . $token . "';");
    if (!mysqli_num_rows($result)) {
        echo("<script language=\"JavaScript\">alert(\"请先登录！\");</script>");
        echo("<script language=\"JavaScript\">window.location.href='login.php';</script>");
        die();
    }
}

date_default_timezone_set("Asia/Shanghai");
$sql = mysqli_connect($sqladdr, $sqluser, $sqlpass);// or die("Database Connection Failed");
mysqli_query($sql, "set names utf8mb4;");
mysqli_select_db($sql, $sqldbnm);
islogged($sql, $usercookie, $tokencookie, $no_need_login);
?>


<!-- Header -->
<header id="header">
    <a href="#" class="logo"><strong>操作历史</strong></a>
    <nav>
        <a href="userinfo.php">欢迎您，<?php echo($_COOKIE[$nickcookie]); ?></a>
        <a href="exit.php" class="icon fa-reply">退出登录</a>
    </nav>
</header>


<!-- Main -->
<section id="main">
    <div class="inner">
    <table border="0">
        <tr>
            <th>序号</th>
            <th>操作人</th>
            <th>时间</th>
            <th>操作内容</th>
        </tr>
        <?php

        $result = mysqli_query($sql,"select * from log;");
        mysqli_data_seek($result,0);
        $count = 0;
        while ($row = mysqli_fetch_row($result)){
            $count++;
            echo ("<tr><td>".$count."</td><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td></tr>");
        }
        ?>
    </table>
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