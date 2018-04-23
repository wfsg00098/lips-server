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
$result = mysqli_query($sql,"select * from main where name='".$brand."';");
if (!mysqli_fetch_row($result))
{
    echo("<script language=\"JavaScript\">alert(\"请先选择品牌！\");</script>");
    echo("<script language=\"JavaScript\">window.location.href='manage_brand.php';</script>");
    die();
}
?>

<!-- Header -->
<header id="header">
    <a href="#" class="logo"><strong>系列管理</strong>&nbsp;&nbsp;(当前品牌：<?php echo($brand_show); ?>)</a>
    <nav>
        <a href="admininfo.php">欢迎您，<?php echo($_COOKIE[$nickcookie]); ?></a>
        <a href="exit.php" class="icon fa-reply">退出登录</a>
    </nav>
</header>


<!-- Main -->
<section id="main">
    <div class="inner">
        <form method="post" enctype="multipart/form-data">
            <p>请选择系列</p>
            <select name="chosen">
                <?php
                $out=null;
                $result = mysqli_query($sql,"select * from `".$brand."`;");
                mysqli_data_seek($result,0);
                while ($row = mysqli_fetch_row($result))
                {
                    $out=$out."<option value=\"".$row[0]."\">".$row[1]."</option>\n";
                }
                echo ($out);
                ?>
            </select><br>
            <input type="submit" name="choose" value="选择"><br><br><hr>
            <p>新增系列</p>
            系列储存名（可使用中文、英文、数字和下划线）<input type="text" name="name"><br>
            系列描述<input type="text" name="describ"><br>
            系列LOGO图<input type="file" name="icon"><br>
            <input type="submit" name="add" value="新增">
            <input type="reset" value="清空"><br><br><hr>
            <input type="submit" name="back" value="返回"><br><hr>
            删除验证密码<input type="password" name="delpass"><br>
            <input type="submit" name="del" value="删除当前品牌"><br>
        </form>
        <?php
        if (isset($_POST["back"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            echo("<script language=\"JavaScript\">window.location.href='manage_brand.php';</script>");
        }
        if (isset($_POST["del"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            $delpassword = covert($_POST["delpass"]);
            if ($delpass == covert($delpassword))
            {
                $result = mysqli_query($sql,"delete from main where name='".$brand."';");
                $result = mysqli_query($sql,"select CONCAT( 'drop table `', table_name, '`;' ) FROM information_schema.tables Where table_name LIKE '".$brand."%';");
                mysqli_data_seek($result,0);
                while ($row = mysqli_fetch_row($result))
                {
                    mysqli_query($sql,$row[0]);
                }
                if ($log_operation) logger($sql,$_COOKIE[$usercookie],"删除品牌：".$brand."(".$brand_show.")");
                echo("<script language=\"JavaScript\">alert(\"删除成功！\");</script>");
                echo("<script language=\"JavaScript\">window.location.href='manage_brand.php';</script>");

            }
            else
            {
                echo("<script language=\"JavaScript\">alert(\"验证密码错误，不能删除！\");</script>");
            }
        }
        if (isset($_POST["choose"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            $result = mysqli_query($sql,"select * from `".$brand."` where name='".$_POST["chosen"]."';");
            mysqli_data_seek($result,0);
            $series_show = mysqli_fetch_row($result)[1];
            echo("<script language=\"JavaScript\">window.location.href='manage_number.php?brand=".covert2($brand)."&brand_show=".covert2($brand_show)."&series=".covert2($_POST["chosen"])."&series_show=".covert2($series_show)."';</script>");
        }
        if (isset($_POST["add"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            $name = covert($_POST["name"]);
            $describ = covert2($_POST["describ"]);
            if ($describ == "" or $name == "" or !isvalidstr($name) or $_FILES["icon"]["name"] == "")
            {
                echo("<script language=\"JavaScript\">alert(\"请将信息填写完整！\");</script>");
            }
            else
            {
                $result = mysqli_query($sql, "select * from `" . $brand . "` where describ='" . $name . "';");
                $count = mysqli_num_rows($result);
                if ($count != 0)
                {
                    echo("<script language=\"JavaScript\">alert(\"系列储存名已存在或与现有的储存名相近！\");</script>");
                }
                else
                {
                    if (!((($_FILES["icon"]["type"] == "image/gif")
                            || ($_FILES["icon"]["type"] == "image/jpeg")
                            || ($_FILES["icon"]["type"] == "image/pjpeg")
                            || ($_FILES["icon"]["type"] == "image/png"))
                        && ($_FILES["icon"]["size"] < $file_max_size))) {
                        echo("<script language=\"JavaScript\">alert(\"文件过大或类型不符！\");</script>");
                    } else if ($_FILES["icon"]["error"] > 0) {
                        echo("<script language=\"JavaScript\">alert(\"上传失败！\");</script>");
                    } else {


                        $tempname = explode(".", $_FILES["icon"]["name"]);
                        $extname = $tempname[sizeof($tempname) - 1];
                        $content = $brand . "_" . $name . "_LOGO_" . date("Y_m_d_H_i_s") . "_" . msectime() . "." . $extname;
                        $content = covert($content);
                        move_uploaded_file($_FILES["icon"]["tmp_name"], $file_upload_location . $content);


                        $result = mysqli_query($sql, "insert into `" . $brand . "` values('" . $name . "','" . $describ . "','" . $file_save_location . $content . "');");
                        $result = mysqli_query($sql, "create table `" . $brand . "_" . $name . "` (number varchar(10),describ text,color varchar(10),img text);");
                    if ($log_operation) logger($sql,$_COOKIE[$usercookie],"添加系列：".$brand." - ".$name."(".$brand_show." - ".$describ.")");
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