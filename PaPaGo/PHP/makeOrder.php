<?php
//下訂單:
//require("../PHP/connectDB.php");
if (!(isset($_SESSION))) {
    session_start();
    if(!isset( $_SESSION['id'])){
        die("die");
    }
};

include("../PHP/common.php");

//格式檢查給js做
$productID = $_POST["productID"];
$startDate = $_POST["startDate"];
$endDate = $_POST["endDate"];
$price = $_POST["price"];
$people = $_POST["people"];
$payMethod = $_POST["payMethod"];
$buyerID = $_SESSION['id'];
$sellerrID = $_POST['sellerID'];

//TODO:檢查商品資料
$recordNO=uniqid("o");
$state=0; //狀態: 0=待確認 1=確認 -1=取消
if ($buyerID != "") {
    $data=array(
        new Insert_DATA("buyerID",$buyerID),
        new Insert_DATA("sellerID",$sellerrID),
        new Insert_DATA("productID",$productID),
        new Insert_DATA("recordNO",$recordNO),
        new Insert_DATA("startDate",$startDate),
        new Insert_DATA("endDate",$endDate),
        new Insert_DATA("price",$price),
        new Insert_DATA("people",$people),
        new Insert_DATA("state",$state),
        new Insert_DATA("payMethod",$payMethod),
    );
    Do_Insert("orderrecord",$data);
    //echo $startDate." ~ ".$endDate;
}
else{
    echo "<script>alert('請先登入')</script>";
}
