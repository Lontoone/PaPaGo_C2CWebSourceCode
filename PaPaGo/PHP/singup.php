<?php
//註冊

require_once("../PHP/connectDB.php");
require_once("../PHP/common.php");
$tableName = "member";

//檢查和消毒輸入格式
//$mail_post = mysqli_real_escape_string($conn, $_POST["mail_input"]);
$mail_post = $_POST["mail_input"];
if (!filter_var($mail_post, FILTER_VALIDATE_EMAIL)) {
    die("1");
};
$passwd_post = mysqli_real_escape_string($conn, $_POST["passwd_input"]);

mysqli_query($conn, 'SET NAMES utf8');

//檢查重複email
if (checkHasData("member", "Email", "Email", $mail_post)) {
    die("2");
}
//if($result->num_rows>0){die("<script>資料重複</script>");}

//不重複=>創建:
if ($result->num_rows == 0) {
    $uId = uniqid("u");
    $passwd_hash = password_hash($passwd_post, PASSWORD_DEFAULT);

    $sql = "INSERT INTO $tableName(ID,Email,passwd,name)
        VALUES('" . $uId . "','" . $mail_post . "','" . $passwd_hash . "','" . $uId . "')";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    if ($result) {
        //成功:

        echo "<script>alert('Enroll Successed')</script>";
        echo "<script>location.href='../index.php'</script>";
    
    } else {
        //失敗:
        echo "<script>location.href='../index.php'</script>";
        echo ("Enroll Faild");
    }
}

$conn->close();
