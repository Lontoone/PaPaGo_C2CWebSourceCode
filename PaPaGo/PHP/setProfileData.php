<?php
//設定profile資料總集:

require("../PHP/connectDB.php");
require("../PHP/common.php");

if (!(isset($_SESSION))) {
    session_start();
    if(!isset( $_SESSION['id'])){
        die("die");
    }
}

$dataType = @$_POST["dataType"];

$userID = $_SESSION['id'];

//[遊客]評價
if ($dataType == "productDone-review") {
    $oid = mysqli_real_escape_string($conn, $_POST["oid"]);
    $pid = mysqli_real_escape_string($conn, $_POST["pid"]);
    $rate = mysqli_real_escape_string($conn, $_POST["rate"]);
    $comment = mysqli_real_escape_string($conn, $_POST["comment"]);

    $d = array(
        new Insert_DATA("recordNO", $oid),
        new Insert_DATA("buyerID", $userID),
        new Insert_DATA("rate", $rate),
        new Insert_DATA("productID", $pid),
        new Insert_DATA("comment", $comment),

    );
    Do_Insert("productreview", $d);
    //更新商品狀態
    updateData("orderrecord", "state", $o_state_done, "recordNO", $oid);
}
//[嚮導]評價
if ($dataType == "orderDone-review") {
    $oid = mysqli_real_escape_string($conn, $_POST["oid"]);
    $pid = mysqli_real_escape_string($conn, $_POST["pid"]);
    $rate = mysqli_real_escape_string($conn, $_POST["rate"]);
    $comment = mysqli_real_escape_string($conn, $_POST["comment"]);

    //上傳回復
    $sql = "UPDATE productreview
    SET Reply='" . $comment . "',
        Reply_rate ='" . $rate . "'
    WHERE
        recordNO='" . $oid . "'
    ";
    require("../PHP/connectDB.php");
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    echo $sql;

    //更新商品狀態
    updateData("orderrecord", "state", $o_state_reply_done, "recordNO", $oid); //4=
}

//移除商品:
else if ($dataType == "removeProduct") {

    $pid = mysqli_real_escape_string($conn, $_POST['pid']);
    //[資安:]檢查本userid是否為該商品的擁有者
    echo(getData("product","sellerID","ID",$pid,""));
    if(getData("product","sellerID","ID",$pid,"")!=$userID){
        die();
    }

    removeData("productreview", "productID", $pid);
    removeData("productregion", "productID", $pid);
    removeData("productbillcontent", "productID", $pid);
    removeData("product", "ID", $pid);
    removeData("daycontent", "productID", $pid);
    removeData("collect", "productID", $pid);
    removeData("collect", "productID", $pid);
    removeData("activity", "productID", $pid);

    //刪縮圖
    $photo_name = getData("productthumbnail", "photo", "productID", $productID, "");
    if (isset($photo_name)) {
        unlink($product_thumb_FolderPath . $photo_name);
        echo "縮圖:刪除" . $product_thumb_FolderPath . $photo_name;
    }
    removeData("productthumbnail", "productID", $pid);


    //刪圖片
    $sql = "SELECT photo FROM productimage WHERE productID ='" . $pid . "'";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    while ($row = $result->fetch_assoc()) {
        unlink($product_img_FolderPath . $row["photo"]);
    }
    removeData("productimage", "productID", $pid);
}
