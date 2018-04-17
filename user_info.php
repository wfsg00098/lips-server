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
    $result = mysqli_query($sql, "select * from admin where username = '" . $user . "' and token = '" . $token . "';");
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
$username = $_GET["username"];
?>


<!-- Header -->
<header id="header">
    <a href="#" class="logo"><strong>用户管理 - <?php echo($username); ?></strong></a>
    <nav>
        <a href="admininfo.php">欢迎您，<?php echo($_COOKIE[$nickcookie]); ?></a>
        <a href="exit.php" class="icon fa-reply">退出登录</a>
    </nav>
</header>


<!-- Main -->
<section id="main">
    <div class="inner">
        <form method="post">
            <p>修改密码</p>
            <input type="text" name="pass">
            <input type="submit" name="change_pass" value="确认修改密码">
            <hr>
            <p>修改昵称</p>
            <input type="text" name="nick">
            <input type="submit" name="change_nick" value="确认修改昵称">
            <hr>
            <p>删除用户</p>
            删除验证密码<input type="password" name="delpass">
            <input type="submit" name="del_user" value="确认删除用户">
            <hr>
            <input type="submit" name="back" value="返回">
        </form>
        <hr>
        <p>用户操作历史</p>
        <table border="0">
            <tr>
                <th>序号</th>
                <th>操作</th>
                <th>时间</th>
            </tr>
            <?php
            $result = mysqli_query($sql, "select * from `#user_" . $username . "_log`;");
            mysqli_data_seek($result, 0);
            $count = 0;
            while ($row = mysqli_fetch_row($result)) {
                $count++;
                echo("<tr><td>" . $count . "</td><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>");
            }
            ?>
        </table>
        <hr>
        <p>用户收藏夹</p>
        <table border="0">
            <tr>
                <th>序号</th>
                <th>类型</th>
                <th>内容</th>
            </tr>
            <?php
            $result = mysqli_query($sql, "select * from `#user_" . $username . "_like`;");
            mysqli_data_seek($result, 0);
            $count = 0;
            while ($row = mysqli_fetch_row($result)) {
                $count++;
                echo("<tr><td>" . $count . "</td><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>");
            }
            ?>
        </table>
    </div>
</section>


<?php
if (isset($_POST["change_pass"])) {
    islogged($sql, $usercookie, $tokencookie, $no_need_login);
    $newpass = sha1(covert($_POST["pass"]));
    $result = mysqli_query($sql, "udpate `user` set password = '" . $newpass . "';");
    $result = mysqli_query($sql, "select * from `user` where password = '" . $newpass . "' and username = '" . $username . "';");
    if (mysqli_fetch_row($result)) {
        if ($log_operation) logger($sql, $_COOKIE[$usercookie], "修改用户密码：" . $username);
        echo("<script language=\"JavaScript\">alert(\"修改成功！\");</script>");
    } else {
        echo("<script language=\"JavaScript\">alert(\"失败成功！\");</script>");
    }
}
if (isset($_POST["change_nick"])) {
    islogged($sql, $usercookie, $tokencookie, $no_need_login);
    $newnick = covert2($_POST["nick"]);
    $result = mysqli_query($sql, "udpate `user` set nickname = '" . $newnick . "';");
    $result = mysqli_query($sql, "select * from `user` where nickname = '" . $newnick . "' and username = '" . $username . "';");
    if (mysqli_fetch_row($result)) {
        if ($log_operation) logger($sql, $_COOKIE[$usercookie], "修改用户昵称：" . $username);
        echo("<script language=\"JavaScript\">alert(\"修改成功！\");</script>");
    } else {
        echo("<script language=\"JavaScript\">alert(\"失败成功！\");</script>");
    }
}
if (isset($_POST["del_user"])) {
    islogged($sql, $usercookie, $tokencookie, $no_need_login);
    $delpassword = covert($_POST["delpass"]);
    if ($delpass == covert($delpassword)) {
        $result = mysqli_query($sql, "delete from user where username='" . $username . "';");
        $result = mysqli_query($sql, "drop table `#user_" . $username . "_log`;");
        $result = mysqli_query($sql, "drop table `#user_" . $username . "_like`;");

        if ($log_operation) logger($sql, $_COOKIE[$usercookie], "删除用户：" . $username);
        echo("<script language=\"JavaScript\">alert(\"删除成功！\");</script>");
        echo("<script language=\"JavaScript\">window.location.href='user_manager.php';</script>");

    } else {
        echo("<script language=\"JavaScript\">alert(\"验证密码错误，不能删除！\");</script>");
    }
}

if (isset($_POST["back"])) {
    echo("<script language=\"JavaScript\">window.location.href='user_manager.php';</script>");
}
?>

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