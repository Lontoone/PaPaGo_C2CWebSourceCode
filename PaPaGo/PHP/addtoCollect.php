<?php
//管理"收藏"
include("../PHP/common.php");
require("../PHP/connectDB.php");
if (!(isset($_SESSION))) {
    session_start();
};

$pid = $_POST['pid'];
$sql = "";


if (checkHasData("collect", "userID", "productID", $pid)) {
    //刪除
    $sql = "DELETE FROM collect 
        WHERE userID='" . $_SESSION['id'] . "' AND productID= '" . $pid . "'";
} else {
    //插入:
    $sql = "INSERT INTO collect(userID,productID)
    VALUES('" . $_SESSION['id'] . "','" . $pid . "')";
}

$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
