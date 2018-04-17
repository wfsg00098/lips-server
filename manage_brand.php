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

if ($server_maintenance) {
    echo("<script language=\"JavaScript\">alert(\"后台系统维护中！\");</script>");
    die();
}

?>

<!-- Header -->
<header id="header">
    <a href="#" class="logo"><strong>品牌管理</strong></a>
    <nav>
        <a href="admininfo.php">欢迎您，<?php echo($_COOKIE[$nickcookie]); ?></a>
        <a href="exit.php" class="icon fa-reply">退出登录</a>
        <a href="#menu">其他</a>
    </nav>
</header>

<?php
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
?>

<!-- Nav -->
<nav id="menu">
    <ul class="links">
        <li><a href="update.php">更新APP</a></li>
        <li><a href="log.php">操作历史</a></li>
    </ul>
</nav>


<!-- Main -->
<section id="main">
    <div class="inner">
        <form method="post" enctype="multipart/form-data">
            <p>请选择品牌</p>
            <select name="chosen">
                <?php
                $out = null;
                $result = mysqli_query($sql, "select * from main;");
                mysqli_data_seek($result, 0);
                while ($row = mysqli_fetch_row($result)) {
                    $out = $out . "<option value=\"" . $row[0] . "\">" . $row[1] . "</option>\n";
                }
                echo($out);
                ?>
            </select><br>
            <input type="submit" name="choose" value="选择"><br><br>
            <hr>
            <p>新增品牌</p><br>
            储存名（可使用中文、英文、数字和下划线）<input type="text" name="brand_name"><br>
            显示名<input type="text" name="brand_show"><br>
            品牌LOGO图<input type="file" name="brand_icon"><br>
            <input type="submit" name="add" value="新增">
            <input type="reset" value="清空"><br>
        </form>
        <?php
        if (isset($_POST["choose"])) {
            islogged($sql, $usercookie, $tokencookie, $sqldbnm);
            $result = mysqli_query($sql, "select * from main where name='" . $_POST["chosen"] . "';");
            mysqli_data_seek($result, 0);
            $name_show = mysqli_fetch_row($result)[1];
            echo("<script language=\"JavaScript\">window.location.href='manage_series.php?brand=" . covert2($_POST["chosen"]) . "&brand_show=" . covert2($name_show) . "';</script>");
        }
        if (isset($_POST["add"])) {
            islogged($sql, $usercookie, $tokencookie, $sqldbnm);
            $name = covert($_POST["brand_name"]);
            $name_show = covert2($_POST["brand_show"]);
            if ($name == "" or $name_show == "" or !isvalidstr($name) or $_FILES["brand_icon"]["name"] == "") {
                echo("<script language=\"JavaScript\">alert(\"请将信息填写完整！\");</script>");
            } else {
                $result = mysqli_query("select * from main where name like '" . $name . "%';");
                if (mysqli_num_rows($result)) {
                    echo("<script language=\"JavaScript\">alert(\"品牌储存名已存在或与现有的储存名相近！\");</script>");
                } else {

                    if (!((($_FILES["brand_icon"]["type"] == "image/gif")
                            || ($_FILES["brand_icon"]["type"] == "image/jpeg")
                            || ($_FILES["brand_icon"]["type"] == "image/pjpeg")
                            || ($_FILES["brand_icon"]["type"] == "image/png"))
                        && ($_FILES["brand_icon"]["size"] < $file_max_size))) {
                        echo("<script language=\"JavaScript\">alert(\"文件过大或类型不符！\");</script>");
                    } else if ($_FILES["brand_icon"]["error"] > 0) {
                        echo("<script language=\"JavaScript\">alert(\"上传失败！\");</script>");
                    } else {
                        $tempname = explode(".", $_FILES["brand_icon"]["name"]);
                        $extname = $tempname[sizeof($tempname) - 1];
                        $content = $name . "_LOGO_" . date("Y_m_d_H_i_s") . "_" . msectime() . "." . $extname;
                        $content = covert($content);
                        move_uploaded_file($_FILES["brand_icon"]["tmp_name"], $file_upload_location . $content);


                        $result = mysqli_query($sql, "insert into main values('" . $name . "','" . $name_show . "','" . $file_save_location . $content . "');");
                        $result = mysqli_query($sql, "create table `" . $name . "` (name text,describ text,img text);");
                        if ($log_operation) logger($sql, $_COOKIE[$usercookie], "添加品牌：" . $name . "(" . $name_show . ")");
                        echo("<script language=\"JavaScript\">alert(\"添加成功！\");</script>");
                        echo "<script language=\"JavaScript\"> location.replace(location.href);</script>";
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