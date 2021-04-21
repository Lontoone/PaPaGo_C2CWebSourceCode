<?php
//更新使用者帳戶資料:
require("../PHP/connectDB.php");
if (!(isset($_SESSION))) {
    session_start();
}
mysqli_query($conn, 'SET NAMES utf8');

$tablename = "member";

$name_POST = mysqli_real_escape_string($conn, $_POST['name']);
//$Email_POST = mysqli_real_escape_string($conn, $_POST['mail']);
$phone_POST = mysqli_real_escape_string($conn, $_POST['phone']);
//$pic = $_FILES["headShot_file"];


//基本資料
include("../PHP/common.php");
updateData($tablename, 'name', $name_POST, "ID", $_SESSION['id']);
updateData($tablename, 'phone', $phone_POST, "ID", $_SESSION['id']);

//大頭照
$pic_id = "ava_" . $_SESSION['id']; //照片檔名:ava+用戶ID
uploadImage("headShot_file", $userAva_FolderPath, $pic_id, $tablename, "photo", "ID", $_SESSION['id'], false);
echo "<script>location.href='" . $_SERVER['HTTP_REFERER'] . "'</script>";

/*
$sql = "UPDATE $tablename 
    SET 
        name='" . $name_POST . "',
        phone='" . $phone_POST . "'
    WHERE id='" . $_SESSION['id'] . "'";

//TODO:更完善
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if ($result) {
    //檢查上傳圖片:
    if ($pic['tmp_name'] != "") {
        $pic_id = "p" . $_SESSION['id']; //照片檔名:p+用戶ID

    }

    echo "<script>location.href='" . $_SERVER['HTTP_REFERER'] . "'</script>";
}*/
