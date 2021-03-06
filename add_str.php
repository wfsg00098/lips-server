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
    <a href="#" class="logo"><strong>内容添加(文本)</strong>&nbsp;&nbsp;(当前色号：<?php echo($brand_show."&nbsp;-&nbsp;".$series_show."&nbsp;-&nbsp;".$number); ?>)</a>
    <nav>
        <a href="admininfo.php">欢迎您，<?php echo($_COOKIE[$nickcookie]); ?></a>
        <a href="exit.php" class="icon fa-reply">退出登录</a>
    </nav>
</header>


<!-- Main -->
<section id="main">
    <div class="inner">
        <form method="post">
            简要说明<input type="text" name="describ"><br>
            文本内容<textarea style="height:500px " name="content"></textarea><br>
            文本大小
            <select name="size">
                <?php
                for ($i=$start;$i<=$end;$i=$i+$gap){
                    echo ('<option value="'.$i.'"');
                    if($i==$defa) echo('selected = "selected"') ;
                    echo ('>'.$i.'sp</option>');
                }
                ?>
            </select>
            文本对齐
            <select name="align">
                <option value="left">左对齐</option>
                <option value="center">居中</option>
                <option value="right">右对齐</option>
            </select>
            文本粗体
            <select name="bold">
                <option value="false">否</option>
                <option value="true">是</option>
            </select>
            文本斜体
            <select name="italic">
                <option value="false">否</option>
                <option value="true">是</option>
            </select>
            文本下划线
            <select name="underline">
                <option value="false">否</option>
                <option value="true">是</option>
            </select>
            文本颜色<input type="color" name="color" value="#000000">
            <br>
            <input type="submit" name="add" value="提交">
            <input type="reset" value="清空"><br><hr>
            <input type="submit" name="back" value="返回">
        </form>

        <?php
        if (isset($_POST["back"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            echo("<script language=\"JavaScript\">window.location.href='manage_content.php?brand=".covert2($brand)."&brand_show=".covert2($brand_show)."&series=".covert2($series)."&series_show=".covert2($series_show)."&number=".covert2($number)."';</script>");
        }

        if (isset($_POST["add"]))
        {
            islogged($sql, $usercookie, $tokencookie, $no_need_login);
            $describ = covert($_POST["describ"]);
            $content = covert2($_POST["content"]);
            $size = covert($_POST["size"]);
            $align = covert($_POST["align"]);
            $bold = covert($_POST["bold"]);
            $italic = covert($_POST["italic"]);
            $underline = covert($_POST["underline"]);
            $color = covert($_POST["color"]);
            if ($describ == "" or $content == "")
            {
                echo("<script language=\"JavaScript\">alert(\"请将内容填写完整！\");</script>");
            }
            else
            {
                $result = mysqli_query($sql,"select * from `".$brand."_".$series."_".$number."` where describ='".$describ."';");
                if (!mysqli_num_rows($result))
                {
                    $result = mysqli_query($sql,"insert `".$brand."_".$series."_".$number."` values('str','".$content."','".$describ."','".$size."','".$align."','".$bold."','".$italic."','".$underline."','".$color."');");
                    if ($log_operation) logger($sql,$_COOKIE[$usercookie],"添加文本：".$brand." - ".$series." - ".$number." - ".$describ."(".$brand_show." - ".$series_show." - ".$number.")");
                    echo("<script language=\"JavaScript\">alert(\"添加成功！\");</script>");
                    echo("<script language=\"JavaScript\">window.location.href='manage_content.php?brand=".covert2($brand)."&brand_show=".covert2($brand_show)."&series=".covert2($series)."&series_show=".covert2($series_show)."&number=".covert2($number)."';</script>");
                }
                else
                {
                    echo("<script language=\"JavaScript\">alert(\"简要说明重复！\");</script>");
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
