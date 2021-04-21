<?php
//聊天室控制:
require("../PHP/connectDB.php");
require("../PHP/common.php");

if (!(isset($_SESSION))) {
    session_start();
    if (!isset($_SESSION['id'])) {
        die("die");
    }
}

$userID = @$_SESSION['id'];
//問哪種資料
$dataType = @$_POST["dataType"];

$sellerID = mysqli_real_escape_string($conn, @$_POST["seller"]);

//左欄:取得對話對象
if ($dataType == "getChatUser") {
    $sql = "SELECT
        u.ID,
        u.name,
        u.photo
    FROM
        msg as m,
        member as u
    WHERE
        (u.ID='" . $sellerID . "' AND u.ID !='" . $userID . "')
        OR
        (m.sentFrom='" . $userID . "' AND u.ID=m.sentTo AND u.ID != '" . $userID . "') 
        OR
        (m.sentTo='" . $userID . "' AND u.ID=m.sentFrom  AND u.ID != '" . $userID . "')
    Group BY u.ID
    ";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $out = array();
    while ($row = $result->fetch_assoc()) {
        $out[] = $row;
    }
    echo json_encode($out);
    /*
    $rows = ["ID", "name", "photo"];
    echo json_encode(getData_inRow_json("member", $rows, "ID", $sellerID));*/
}

//中欄:取得對話紀錄
elseif ($dataType == "getChatData") {
    $limit_start = mysqli_real_escape_string($conn, @$_POST["limit_start"]);
    $time_after = mysqli_real_escape_string($conn, @$_POST["time_after"]);
    $time_before = mysqli_real_escape_string($conn, @$_POST["time_before"]);
    $sql = "SELECT
        *
    FROM
        msg
    WHERE
        (
        (sentFrom='" . $userID . "' AND
        sentTO ='" . $sellerID . "') 
        OR
        (sentFrom='" . $sellerID . "' AND
        sentTO ='" . $userID . "') 
        )   AND
        (date<'" . $time_before . "' OR date >'" . $time_after . "')
    ORDER BY
        date DESC
    LIMIT $limit_start , 10
    ";
    $result = mysqli_query($conn, $sql);
    $out = array();
    while ($row = $result->fetch_assoc()) {
        $out[] = $row;
    }
    echo json_encode($out);
}

//中欄:送出對話
elseif ($dataType == "sendChatData") {
    $pid = mysqli_real_escape_string($conn, @$_POST["productID"]);
    $content = mysqli_real_escape_string($conn, filter_var($_POST["content"], FILTER_SANITIZE_STRING));

    $data = array(
        new Insert_DATA("sentFrom", $userID),
        new Insert_DATA("sentTO", $sellerID)
    );

    if ($pid != "") {
        //要查詢商品
        $data[] = new Insert_DATA("productID", $pid);
    } else {
        //一般對話:
        $data[] = new Insert_DATA("content", $content);
    }
    Do_Insert("msg", $data);

    date_default_timezone_set("Asia/Taipei");
    echo $date = date('Y-m-d H:i:s');
}

//右欄:取得賣家資料
elseif ($dataType == "getSellerData") {
    //有商品資料才一起印出:
    if (checkHasData("product", "ID", "sellerID", $sellerID)) {
        $sql = "SELECT 
        m.ID as uid,
        m.name as name,
        m.photo as ava,
        p.ID as pid,
        p.price,
        p.title,
        pt.photo as pt

    FROM
        member as m,
        product as p,
        productthumbnail as pt
    WHERE
        m.ID='" . $sellerID . "' AND
        p.sellerID= m.ID AND
        pt.productID = p.ID
    ";
    }
    //沒商品者
    else {
        $sql = "SELECT 
        m.ID as uid,
        m.name as name,
        m.photo as ava
    FROM
        member as m
    WHERE
        m.ID='" . $sellerID . "'";
    }
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $out = array();
    while ($row = $result->fetch_assoc()) {
        $out[] = $row;
    }
    echo json_encode($out);
}
//取得被詢問商品簡易資料
elseif ($dataType == "getInruiredProduct") {
    $pid = mysqli_real_escape_string($conn, @$_POST["productID"]);
    $sql = "SELECT * from 
        product as p,
        productthumbnail as pt
        WHERE 
        p.ID=pt.productID AND
        p.ID='" . $pid . "'
        ";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $out = array();
    while ($row = $result->fetch_assoc()) {
        $out[] = $row;
    }
    echo json_encode($out);
    //echo json_encode(getData_inRow_json("product",["*"],"ID",$pid));
}

//檢查對話內容更新
elseif ($dataType == "chatCheck") {
    $enterTime = mysqli_real_escape_string($conn, @$_POST["enterTime"]);
    $sql = "SELECT * 
        from
             msg
        where 
            date >'" . $enterTime . "' AND
            sentTo='" . $userID . "'
        ORDER BY date ASC";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    /*$out = array();
    while ($row = $result->fetch_assoc()) {
        $out[] = $row;
    }
    echo json_encode($out);*/
    //echo $sql;
    if ($result->num_rows > 0) {
        echo $result->fetch_assoc()['date'];
    }else{
        //date_default_timezone_set("Asia/Taipei");
        //echo $date = date('Y-m-d H:i:s');
    }
}
