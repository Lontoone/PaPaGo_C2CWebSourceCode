<?php
//編輯/修改產品

require("../PHP/connectDB.php");
require('../PHP/common.php');
if (!(isset($_SESSION))) {
    session_start();
    if(!isset( $_SESSION['id'])){
        die("die");
    }
}

$pid = mysqli_real_escape_string($conn, $_POST['pid']);

$title_POST = mysqli_real_escape_string($conn, $_POST['input-producttitle']);
$country_POST = mysqli_real_escape_string($conn, $_POST['country']);
$county_POST = mysqli_real_escape_string($conn, $_POST['county']);
$duration_POST = mysqli_real_escape_string($conn, $_POST['input-duration']);
$people_POST = mysqli_real_escape_string($conn, $_POST['input-people']);
$productInfo_POST = mysqli_real_escape_string($conn, $_POST['input-productinfo']);
$bill_total_POST = mysqli_real_escape_string($conn, $_POST['bill_total']);
$declear_POST = mysqli_real_escape_string($conn, $_POST['input-declear']);

$agenda_dayTitle_POST = mysqli_real_escape_string($conn, $_POST['agenda_dayTitle_wrap']);
$agenda_dayActivity_POST = mysqli_real_escape_string($conn, $_POST['agenda_dayActivity_wrap']);
$bill_content_POST = mysqli_real_escape_string($conn, $_POST['bill_content']);
$week_select_POST = mysqli_real_escape_string($conn, $_POST['week_select']);

//Product基本資料:
$product_basic_info = array(
    new Insert_DATA("price", $bill_total_POST),
    new Insert_DATA("title", $title_POST),
    new Insert_DATA("state", "published"),
    new Insert_DATA("days", $duration_POST),
    new Insert_DATA("people", $people_POST),
    new Insert_DATA("other", $declear_POST),
    new Insert_DATA("weekSelect", $week_select_POST),
    new Insert_DATA("info", $productInfo_POST)
);
updateData_inrow('product', $product_basic_info, "ID", $pid);


//活動區域
$product_region_post = array(
    new Insert_DATA("country", $country_POST),
    new Insert_DATA("county", $county_POST)
);
updateData_inrow("productregion", $product_region_post, "ID", $pid);


//需要先刪除所有舊資料再上傳的資料:vvvvvvvvvv

//日程:
//  [刪除舊資料] 
removeData("daycontent","productID",$pid);
//  [上傳] 拆解行程內容-日程|標題
$dayTitle_unwrap = explode("@", $agenda_dayTitle_POST);

for ($i = 0; $i < count($dayTitle_unwrap) - 1; $i++) {
    $data = explode("|", $dayTitle_unwrap[$i]);
    $postData = array(
        new Insert_DATA("productID", $pid),
        new Insert_DATA("day", $data[0]),
        new Insert_DATA("title", $data[1])
    );
    $r = Do_Insert("daycontent", $postData);
    //var_dump($r);
    echo ("daycontent:" . $r . "\n");
}

