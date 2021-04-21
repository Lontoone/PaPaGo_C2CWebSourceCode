<?php
//TODO:儲存草稿功能?
require("../PHP/connectDB.php");
if (!(isset($_SESSION))) {
    session_start();
    if (!isset($_SESSION['id'])) {
        die("die");
    }
}
$userID = $_SESSION['id'];
//建立新產品
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

$deleted_picID_POST = mysqli_real_escape_string($conn, $_POST['deleted_picID']);

$action = mysqli_real_escape_string($conn, $_POST['action']); //若是要編輯: ="edit"

$pim = "pim_";
require('../PHP/common.php');

if ($action == "edit") { //模式:編輯:
    //檢查是不是產品擁有人
    $productID = mysqli_real_escape_string($conn, $_POST['pid']);



    //更新:
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
    updateData_inrow('product', $product_basic_info, "ID", $productID);

    //刪除資料:
    removeData("daycontent", "productID", $productID);
    removeData("activity", "productID", $productID);
    removeData("productbillcontent", "productID", $productID);
    removeData("productregion", "productID", $productID);

    //圖片=>//TODO[debug?]:也要刪圖檔
    //刪除被移除的產品圖片:
    $delet_pic_list = explode(",", $deleted_picID_POST);
    for ($i = 0; $i < count($delet_pic_list); $i++) {
        if (empty($delet_pic_list[$i])) {
            continue;
        }
        echo "刪除" . $delet_pic_list[$i];
        unlink($product_img_FolderPath . $delet_pic_list[$i]);
        removeData("productimage", "photo", $delet_pic_list[$i]);
    }
    //若縮圖有更改=>刪除原本的
    //if ($_FILES["product_thumb_pic"]["error"] == 0) {
    if (!empty($_POST["hasChange_thumbnail"])) {
        $photo_name = getData("productthumbnail", "photo", "productID", $productID, "");
        if (isset($photo_name)) {
            unlink($product_thumb_FolderPath . $photo_name);
            echo "縮圖:刪除" . $product_thumb_FolderPath . $photo_name;
        }
        removeData("productthumbnail", "productID", $productID);
        echo "縮圖刪除" . $productID;

        //上傳新的
        //縮圖
        uploadImage("product_thumb_pic", $product_thumb_FolderPath, $pim, "productthumbnail", "photo", "productID", $productID, true);
    }
} else { //模式: 新建
    //建立賣家-商品資料
    $sellerID = $_SESSION['id'];
    $tablename = 'product';
    $idPrefix = "p-" . $sellerID . "-";
    $productID = uniqid($idPrefix); //商品名稱格式: 'p-賣家ID-流水號'


    $product_basic_info = array(
        new Insert_DATA("ID", $productID),
        new Insert_DATA("price", $bill_total_POST),
        new Insert_DATA("sellerID", $sellerID),
        new Insert_DATA("title", $title_POST),
        new Insert_DATA("state", "published"),
        new Insert_DATA("days", $duration_POST),
        new Insert_DATA("people", $people_POST),
        new Insert_DATA("other", $declear_POST),
        new Insert_DATA("weekSelect", $week_select_POST),
        new Insert_DATA("info", $productInfo_POST)
    );
    Do_Insert("product", $product_basic_info);

    if (isset($_FILES["product_thumb_pic"])) {
        //縮圖
        uploadImage("product_thumb_pic", $product_thumb_FolderPath, $pim, "productthumbnail", "photo", "productID", $productID, true);
    }
}

//上傳其他內容
//活動區域
$product_region_post = array(
    new Insert_DATA("productID", $productID),
    new Insert_DATA("country", $country_POST),
    new Insert_DATA("county", $county_POST)
);
Do_Insert("productregion", $product_region_post);


//拆解行程內容-日程|標題
$dayTitle_unwrap = explode("@", $agenda_dayTitle_POST);

for ($i = 0; $i < count($dayTitle_unwrap) - 1; $i++) {
    $data = explode("|", $dayTitle_unwrap[$i]);
    $postData = array(
        new Insert_DATA("productID", $productID),
        new Insert_DATA("day", $data[0]),
        new Insert_DATA("title", $data[1])
    );
    $r = Do_Insert("daycontent", $postData);
    //var_dump($r);
    echo ("daycontent:" . $r . "\n");
}

//拆解行程內容 1|標題|內容@
$dayActivity_unwrap = explode("@", $agenda_dayActivity_POST);

for ($i = 0; $i < count($dayActivity_unwrap) - 1; $i++) {
    $data = explode("|", $dayActivity_unwrap[$i]);
    $postData = array(
        new Insert_DATA("productID", $productID),
        new Insert_DATA("sequence", $data[0]),
        new Insert_DATA("day", $data[1]),
        new Insert_DATA("title", $data[2]),
        new Insert_DATA("content", $data[3])
    );
    $r = Do_Insert("activity", $postData);
    echo ("activity:" . $r . "\n");
}


//拆解收費項目-天數|內容|價格|數量|@
$bill_unwrap = explode("@", $bill_content_POST);

for ($i = 0; $i < count($bill_unwrap) - 1; $i++) {
    $data = explode("|", $bill_unwrap[$i]);
    $postData = array(
        new Insert_DATA("productID", $productID),
        new Insert_DATA("day", $data[0]),
        new Insert_DATA("content", $data[1]),
        new Insert_DATA("price", $data[2]),
        new Insert_DATA("quantity", $data[3]),
    );
    $r = Do_Insert("productbillcontent", $postData);
}

//圖片:
//照片檔名:pim_[商品ID]_ //舊
//$pim = "pim_" . $productID;//舊

if (isset($_FILES["product_pics_form"])) {
    //產品圖片
    uploadImage("product_pics_form", $product_img_FolderPath, $pim, "productimage", "photo", "productID", $productID, true);
}

/*
if (isset($_FILES["product_thumb_pic"])) {
    //縮圖
    uploadImage("product_thumb_pic", $product_thumb_FolderPath, $pim, "productthumbnail", "photo", "productID", $productID, true);
}
*/
