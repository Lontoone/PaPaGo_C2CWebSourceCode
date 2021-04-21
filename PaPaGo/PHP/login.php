<?php
//登入
require_once("../PHP/connectDB.php");
$tablename = "member";

mysqli_query($conn, 'SET NAMES utf8');

$mail_post = mysqli_real_escape_string($conn, $_POST["mail_input"]);
$passwd_post = mysqli_real_escape_string($conn, $_POST["passwd_input"]);

$sql = "SELECT * FROM $tablename WHERE Email='$mail_post'";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        //驗證password hash
        if (password_verify($passwd_post, @$row["passwd"])) {

            session_start();
            $_SESSION['id'] = @$row['ID'];
            setcookie('id', @$row['ID'], time() + 60 * 60 * 7);
            
            //echo "<script>alert('登入成功')</script>";
            echo "<script> location.href='../index.php'  </script>";
        }
        else{
            echo "<script>alert('密碼錯誤')</script>";
            echo "<script>location.href='../index.php'</script>";
        }
    }
}
