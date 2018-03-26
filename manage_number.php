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
$result = mysqli_query($sql,"select * from `".$brand."` where name='".$series."';");
if (!mysqli_fetch_row($result))
{
    echo("<script language=\"JavaScript\">alert(\"请先选择系列！\");</script>");
    echo("<script language=\"JavaScript\">window.location.href='manage_series.php?brand=".covert2($brand)."&brand_show=".covert($brand_show)."';</script>");
    die();
}
?>

<!-- Header -->
<header id="header">
    <a href="#" class="logo"><strong>色号管理</strong>&nbsp;&nbsp;(当前系列：<?php echo($brand_show."&nbsp;-&nbsp;".$series_show); ?>)</a>
    <nav>
        <a href="userinfo.php">欢迎您，<?php echo($_COOKIE[$nickcookie]);  ?></a>
        <a href="exit.php" class="icon fa-reply">退出登录</a>
    </nav>
</header>


<!-- Main -->
<section id="main">
    <div class="inner">
        <form method="post">
            <p>请选择色号</p>
            <select id="chosen" name="chosen">
                <?php
                $out=null;
                $result = mysqli_query($sql,"select * from `".$brand."_".$series."`;");
                mysqli_data_seek($result,0);
                while ($row = mysqli_fetch_row($result))
                {
                    $out=$out."<option value=\"".$row[0]."\" style='background-color:".$row[2]."'>".$row[0]."&nbsp;-&nbsp;".$row[1]."</option>\n";
                }
                echo ($out);
                ?>
            </select><br>
            <input type="submit" name="choose" value="选择">
            <br><br><hr>
            <p>新增色号</p>
            色号（可使用中文、英文、数字和下划线）<input type="text" name="number"><br>
            色号描述<input type="text" name="number_des"><br>
            色号颜色<input type="color" name="color" value="#FF0000">
            <input type="submit" name="add" value="新增">
            <input type="reset" value="清空"><br><br><hr>
            <input type="submit" name="back" value="返回"><br><hr>
            删除验证密码<input type="password" name="delpass"><br>
            <input type="submit" name="del" value="删除当前系列"><br>
        </form>

        <script>
            window.onload = function(){
                var chosen = document.getElementById("chosen");
                chosen.style.backgroundColor=chosen.options[chosen.selectedIndex].style.backgroundColor;
                chosen.addEventListener('change',function()
                {
                    this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor;
                });
            }
        </script>

        <?php
        if (isset($_POST["back"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            echo("<script language=\"JavaScript\">window.location.href='manage_series.php?brand=".covert2($brand)."&brand_show=".covert($brand_show)."';</script>");
        }
        if (isset($_POST["del"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            $delpassword = covert($_POST["delpass"]);
            if ($delpass == covert($delpassword))
            {
                $result = mysqli_query($sql,"delete from `".$brand."` where name='".$series."';");
                $result = mysqli_query($sql,"select CONCAT( 'drop table `', table_name, '`;' ) FROM information_schema.tables Where table_name LIKE '".$brand."_".$series."%';");
                mysqli_data_seek($result,0);
                while ($row = mysqli_fetch_row($result))
                {
                    mysqli_query($sql,$row[0]);
                }
                if ($log_operation) logger($sql,$_COOKIE[$usercookie],"删除系列：".$brand." - ".$series."(".$brand_show." - ".$series_show.")");
                echo("<script language=\"JavaScript\">alert(\"删除成功！\");</script>");
                echo("<script language=\"JavaScript\">window.location.href='manage_series.php?brand=".covert2($brand)."&brand_show=".covert($brand_show)."';</script>");

            }
            else
            {
                echo("<script language=\"JavaScript\">alert(\"验证密码错误，不能删除！\");</script>");
            }


        }
        if (isset($_POST["choose"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            echo("<script language=\"JavaScript\">window.location.href='manage_content.php?brand=".covert2($brand)."&brand_show=".covert2($brand_show)."&series=".covert2($series)."&series_show=".covert2($series_show)."&number=".covert2($_POST["chosen"])."';</script>");
        }
        if (isset($_POST["add"]))
        {
            islogged($sql,$usercookie,$tokencookie,$no_need_login);
            $number = covert($_POST["number"]);
            $describ = covert2($_POST["number_des"]);
            $color = covert($_POST["color"]);
            if ($number == "" or $describ == "" or $color == "" or !isvalidstr($number))
            {
                echo("<script language=\"JavaScript\">alert(\"请将信息填写完整！\");</script>");
            }
            else
            {
                $result = mysqli_query($sql,"select * from `".$brand."_".$series."` where name='".$number."';");
                if (mysqli_num_rows($result))
                {
                    echo("<script language=\"JavaScript\">alert(\"色号已存在！\");</script>");
                }
                else
                {
                    $result = mysqli_query($sql,"insert into `".$brand."_".$series."` values('".$number."','".$describ."','".$color."');");
                    $result = mysqli_query($sql,"create table `".$brand."_".$series."_".$number."` (type text,content text,describ text,size text,align text,bold text,italic text,underline text,color text);");
                    if ($log_operation) logger($sql,$_COOKIE[$usercookie],"添加色号：".$brand." - ".$series." - ".$number."(".$brand_show." - ".$series_show." - ".$number.")");
                    echo("<script language=\"JavaScript\">alert(\"添加成功！\");</script>");
                    echo "<script language=\"JavaScript\"> location.replace(location.href);</script>";
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