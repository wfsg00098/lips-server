<!DOCTYPE HTML>
<html>
<head>
    <title>Beautiful Lips</title>
    <meta name="theme-color" content="#1c1c1c"/>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="assets/css/main.css"/>
</head>
<body>


<!-- Header -->
<header id="header">
    <a href="#" class="logo"><strong>Beautiful Lips Project</strong></a>
</header>
<?php
include "settings.php";
date_default_timezone_set("Asia/Shanghai");
$sql = mysqli_connect($sqladdr,$sqluser,$sqlpass);// or die("Database Connection Failed");
mysqli_query($sql,"set names utf8mb4;");
mysqli_select_db($sql,$sqldbnm);
$result = mysqli_query($sql,"select * from ver;");
mysqli_data_seek($result,0);
$row = mysqli_fetch_row($result);
$currentver = $row[0];
?>
<!-- Main -->
<section id="main">
    <div class="inner">
        <p style="text-align: center;font-size: 20px;">A SINGLE IMAGE CAN EXPLAIN EVERYTHING !</p>
        <div class="image fit">
            <img src="image/title.png" />
        </div>

        <p>下载链接：</p><a href='update/<?php echo($currentver) ?>.apk'>点此下载(安卓)</a>
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