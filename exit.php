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


<!-- Header -->
<header id="header">
    <a href="#" class="logo"><strong>退出登录</strong></a>
    <nav>
        <a href="login.php" class="icon fa-reply">重新登录</a>
    </nav>
</header>


<!-- Main -->
<section id="main">
    <div class="inner">
        <?php
        include "settings.php";
        date_default_timezone_set("Asia/Shanghai");
        setcookie($tokencookie,"",0);
        echo("<script language=\"JavaScript\">alert(\"退出成功！\");</script>");
        ?>
        <h3 style="text-align: center">您已成功退出登录</h3>

    </div>
</section>

<!-- Footer -->
<footer id="footer">
    <div class="copyright">Copyright &copy; 2017-<?php echo(date("YY")); ?>. 王七喜♏ All rights reserved.</div>
</footer>

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/jquery.scrolly.min.js"></script>
<script src="assets/js/skel.min.js"></script>
<script src="assets/js/util.js"></script>
<script src="assets/js/main.js"></script>



</body>
</html>