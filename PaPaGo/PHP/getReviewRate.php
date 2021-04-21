<?php
//取得評價:
require("../PHP/connectDB.php");
require("../PHP/common.php");

if (!(isset($_SESSION))) {
    session_start();
   
}
//問哪種資料
$dataType = @$_POST["dataType"];
$userID = @$_SESSION['id'];

//單個商品評價星級
if ($dataType == "productReviewRate") {
    $pid = mysqli_real_escape_string($conn, $_POST["pid"]);
    $sql = "SELECT 
            avg(pv.rate) 
        From 
            productreview as pv,
            orderrecord as o
        Where 
            pv.productID='" . $pid . "' AND
            pv.productID=o.productID AND
            o.state>=3
        GROUP BY pv.productID
        ";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (!mysqli_num_rows($result) == 0) {
        //echo $result->num_rows;
        echo $result->fetch_array()[0];
    } else {
        echo 0;
    }
}

//賣家整體評價星級
elseif ($dataType == "sellerReviewRate") {
    //$sid = mysqli_real_escape_string($conn, $_POST["sid"]);
    $sql = "SELECT 
            avg(pv.rate) 
        From 
            productreview as pv,
            product as p,
            orderrecord as o
        Where 
            pv.productID=p.ID AND 
            o.productID=p.ID AND
            o.state>=3
        GROUP BY p.sellerID
        ";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (!mysqli_num_rows($result) == 0) {
        //echo $result->num_rows;
        echo $result->fetch_array()[0];
    } else {
        echo 0;
    }
}

//單個商品留言評論資訊
elseif ($dataType == "productReview") {
    $pid = mysqli_real_escape_string($conn, $_POST["pid"]);
    $sql = "SELECT
        pv.rate,
        pv.comment,
        pv.date,
        u.name,
        u.ID,
        u.photo,
        pv.Reply,
        pv.Reply_rate
    FROM
        productreview as pv,
        member as u
    WHERE
        pv.buyerID=u.ID AND
        pv.productID='".$pid."'
    ";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $out = array();
    while ($row = $result->fetch_assoc()) {
        $out[] = $row;
    }
    echo json_encode($out);
}
