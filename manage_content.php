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
$brand = $_GET["brand"];
$brand_show = $_GET["brand_show"];
$series = $_GET["series"];
$series_show = $_GET["series_show"];
$number = $_GET["number"];
$result = mysqli_query($sql,"select * from `".$brand."_".$series."` where number='".$number."';");
if (!mysqli_fetch_row($result))
{
    echo("<script language=\"JavaScript\">alert(\"请先选择色号！\");</script>");
    echo("<script language=\"JavaScript\">window.location.href='manage_number.php?brand=".covert2($brand)."&brand_show=".covert2($brand_show)."&series=".covert2($series)."&series_show=".covert2($series_show)."';</script>");
    die();
}
?>

<!-- Header -->
<header id="header">
    <a href="#" class="logo"><strong>内容管理</strong>&nbsp;&nbsp;(当前色号：<?php echo($brand_show."&nbsp;-&nbsp;".$series_show."&nbsp;-&nbsp;".$number); ?>)</a>
    <nav>
        <a href="userinfo.php">欢迎您，<?php echo($_COOKIE[$nickcookie]);  ?></a>
        <a href="exit.php" class="icon fa-reply">退出登录</a>
    </nav>
</header>


<!-- Main -->
<section id="main">
    <div class="inner">
        <form method="post">
            <p>请选择内容</p>
            <select name="chosen">
                <?php
                $out=null;
                $result = mysqli_query($sql,"select * from `".$brand."_".$series."_".$number."`;");
                mysqli_data_seek($result,0);
                while ($row = mysqli_fetch_row($result))
                {
                    $out=$out."<option value=\"".$row[2]."\">".$row[0]."&nbsp;-&nbsp;".$row[2]."</option>\n";
                }
                echo ($out);
                ?>
            </select><br>
            <input type="submit" name="modify" value="修改">
            <br><br><hr>
            <p>新增内容</p>
            类型<select name="type">
                <option value="str">文本</option>
                <option value="img">图片</option>
                <option value="link">链接</option>
            </select><br>
            <input type="submit" name="add" value="新增"><br><hr>
            <input type="submit" name="back" value="返回"><br><hr>
            删除验证密码<input type="password" name="delpass"><br>
            <input type="submit" name="del_content" value="删除当前选中的内容">
            <input type="submit" name="del" value="删除当前色号"><br>
        </form>

        <?php
        if (isset($_POST["back"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            echo("<script language=\"JavaScript\">window.location.href='manage_series.php?brand=".covert2($brand)."&brand_show=".covert2($brand_show)."&series=" . covert2($series) . "&series_show=" . covert2($series_show)."';</script>");
        }
        if (isset($_POST["del"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            $delpassword = covert($_POST["delpass"]);
            if ($delpass == covert($delpassword))
            {
                $result = mysqli_query($sql,"delete from `".$brand."_".$series."` where number='".$number."';");
                $result = mysqli_query($sql,"drop table `".$brand."_".$series."_".$number."`;");
                if ($log_operation) logger($sql,$_COOKIE[$usercookie],"删除色号".$brand." - ".$series." - ".$number."(".$brand_show." - ".$series_show." - ".$number.")");
                echo("<script language=\"JavaScript\">alert(\"删除成功！\");</script>");
                echo("<script language=\"JavaScript\">window.location.href='manage_series.php?brand=".covert2($brand)."&brand_show=".covert2($brand_show)."&series=" . covert2($series) . "&series_show=" . covert2($series_show)."';</script>");

            }
            else
            {
                echo("<script language=\"JavaScript\">alert(\"验证密码错误，不能删除！\");</script>");
            }

        }

        if (isset($_POST["modify"]))
        {
            islogged($sql, $usercookie, $tokencookie, $no_need_login);
            $describ = $_POST["chosen"];
            $result = mysqli_query($sql, "select * from `" . $brand . "_" . $series . "_" . $number . "` where describ='" . $describ . "';");
            mysqli_data_seek($result, 0);
            $type = mysqli_fetch_row($result)[0];
            if ($type == "str")
            {
                echo("<script language=\"JavaScript\">window.location.href='edit_str.php?brand=" . covert2($brand) . "&brand_show=" . covert2($brand_show) . "&series=" . covert2($series) . "&series_show=" . covert2($series_show) . "&number=" . covert2($number) . "&describ=" . covert2($describ) . "';</script>");
            }
            else if ($type == "link")
            {
                echo("<script language=\"JavaScript\">window.location.href='edit_link.php?brand=" . covert2($brand) . "&brand_show=" . covert2($brand_show) . "&series=" . covert2($series) . "&series_show=" . covert2($series_show) . "&number=" . covert2($number) . "&describ=" . covert2($describ) . "';</script>");
            }
            else
            {
                echo("<script language=\"JavaScript\">window.location.href='edit_img.php?brand=" . covert2($brand) . "&brand_show=" . covert2($brand_show) . "&series=" . covert2($series) . "&series_show=" . covert2($series_show) . "&number=" . covert2($number) . "&describ=" . covert2($describ) . "';</script>");
            }
        }
        if (isset($_POST["del_content"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            $delpassword = covert($_POST["delpass"]);
            $describ = $_POST["chosen"];
            if ($delpass == covert($delpassword))
            {
                $result = mysqli_query($sql,"delete from `".$brand."_".$series."_".$number."` where describ='".$describ."';");
                if ($log_operation) logger($sql,$_COOKIE[$usercookie],"删除色号下的内容：".$brand." - ".$series." - ".$number." - ".$describ."(".$brand_show." - ".$series_show." - ".$number.")");
                echo("<script language=\"JavaScript\">alert(\"删除成功！\");</script>");
                echo ("<script language=JavaScript> location.replace(location.href);</script>");
            }
            else
            {
                echo("<script language=\"JavaScript\">alert(\"验证密码错误，不能删除！\");</script>");
            }
        }
        if (isset($_POST["add"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            $type = $_POST["type"];
            if ($type == "str")
            {
                echo("<script language=\"JavaScript\">window.location.href='add_str.php?brand=" . covert2($brand) . "&brand_show=" . covert2($brand_show) . "&series=" . covert2($series) . "&series_show=" . covert2($series_show) . "&number=" . covert2($number) . "';</script>");
            }
            elseif ($type == "link")
            {
                echo("<script language=\"JavaScript\">window.location.href='add_link.php?brand=" . covert2($brand) . "&brand_show=" . covert2($brand_show) . "&series=" . covert2($series) . "&series_show=" . covert2($series_show) . "&number=" . covert2($number) . "';</script>");

            }elseif ($type == "img")
            {
                echo("<script language=\"JavaScript\">window.location.href='add_img.php?brand=" . covert2($brand) . "&brand_show=" . covert2($brand_show) . "&series=" . covert2($series) . "&series_show=" . covert2($series_show) . "&number=" . covert2($number) . "';</script>");
            }
            else
            {
                echo("<script language=\"JavaScript\">alert(\"不存在这个类型的！\");</script>");
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