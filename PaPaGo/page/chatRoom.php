<!DOCTYPE html>
<html>

<head>
    <meta charset='UTF-8'>
    <title>聊天室</title>
    <link rel="shortcut icon" href="../img/icon/logo1.png" type="image/png">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+TC&display=swap&subset=chinese-traditional" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans&display=swap" rel="stylesheet">

    <script src='http://code.jquery.com/jquery-1.11.3.min.js'></script>
    <script src='../js/common.js'></script>

    <link rel='stylesheet' href='../css/reset.css'>
    <link rel='stylesheet' href='../css/common.css'>
    <link rel='stylesheet' href='../css/main.css'>
    <link rel='stylesheet' href='../css/profile.css'>
    <link rel='stylesheet' href='../css/forForm.css'>
    <link rel='stylesheet' href='../css/chatroom.css'>
</head>

<body>
    <?php require("../PHP/autoLogin.php");
    if (!isset($_SESSION["id"])) {
        echo "<script>alert('請先登入')</script>";
        echo "<script>location.href='../index.php'</script>";
    }
    ?>
    <div class="wrapper">
        <div class="header">
            <div class="logo putCenter"><a href="../">PAPAGO</a></div>
            <!--大頭貼 或 '登入|註冊'按鈕-->
            <div class='putCenter'>

                <?php if (!@$_SESSION['id']) { ?>
                    <div class="sign-log-in">
                        <a href="../page/login.html">登入</a>
                        <a href="../page/singup.html">註冊</a>
                    </div>

                <?php } else { ?>
                    <a href='../page/profile.php?id=<?php GetUserData("ID") ?>'>
                        <img class='user-headShot' src='../upload/avatars/<?php GetUserData("photo") ?>'>
                    </a>
                <?php } ?>

            </div>
        </div>

        <div class="chatroomWrapper">
            <!--左欄: 用戶列表-->
            <div class="Board putCenter" id="left-list">
                <!--選擇用戶-->
                <div id="userList">
                    <!--EXAMPLE
                    <div class="userunit col2-70-auto">
                        <img src="../img/example/headshot.jpg" alt="">
                        <p class="putCenter name">123</p>
                    </div>-->
                </div>
                <!--搜尋-->
                <div>

                    <div class="searchbar-wrapper putCenter">
                        <input class="searchBar shadow-m" type="search" name="" id="search-u" placeholder="搜尋">
                    </div>

                </div>


            </div>
            <!--中欄: 聊天紀錄-->
            <div class="Board" id="chatroom">
                <!--載入更多-->
                <div id="loadMore-m" class="btn-r-s btn-c">
                    載入更多
                </div>
                <!--對話區域-->
                <div id="chatBoard">
                    <!--EXAMPLE: 對話氣泡-->
                    <div class="chatbobble f-l">
                        <p class="content btn1">HIHI</p>
                        <p class="subText">19:20</p>
                    </div>

                    <!--詢問商品-->
                    <!--EXAMPLE-->
                    <div class="chatbobble btn1 f-l">
                        <p>我要詢問:</p>
                        <div class="product-chatbobble">
                            <img src="../img/example/headshot.jpg" alt="">
                            <div class="bubble-p-info">
                                <div class="title-big">大甲</div>
                                <div class="price">$1500</div>
                                <div class="describe">
                                    大甲最好玩的地方哦
                                    大甲最好玩的地方哦
                                    大甲最好玩的地方哦
                                    大甲最好玩的地方哦
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--輸入-->
                <div>
                    <div class="searchbar-wrapper">
                        <input class="searchBar shadow-m" type="search" name="" id="chatInput" placeholder="說點什麼">
                    </div>
                </div>
            </div>

            <!--右欄:賣家資料-->
            <div class="Board putCenter" id="userPreview">
                <div class="userPreviewUnit putCenter">
                    <img class="putCenter" id="userava" src="../img/example/headshot.jpg" alt="">
                    <p class="nameTitle putCenter">請選擇或搜尋用戶</p>
                    <!--搜尋商品-->
                    <div>
                        <div class="searchbar-wrapper">
                            <input class="searchBar" type="search" name="" id="search-p" placeholder="搜尋商品">
                        </div>
                    </div>

                    <!--商品列表-->
                    <div class="productGroup">
                        <!--EXAMPLE
                        <div class="productUnit putCenter">
                            <img src="../img//example/headshot.jpg" alt="">
                            <div class="p-info">
                                <div class="title-big">大甲</div>
                                <div class="price">$1500</div>
                                <div class="btn2 inquire-btn">詢問</div>
                            </div>
                        </div>
                    -->
                    </div>



                </div>
            </div>

        </div>
    </div>


    <!--TEMPLATE:對話泡泡-->
    <template id="chatbobble-text-template">
        <div class="chatbobble">
            <p class="content">HIHI</p>
            <p class="subText">19:20</p>
        </div>
    </template>

    <!--TEMPLATE:詢問商品(對話泡泡)-->
    <template id="product-chatbobble-template">
        <a class="chatbobble">
            <p>我要詢問:</p>
            <div class="product-chatbobble">
                <img src="../img/example/headshot.jpg" alt="">
                <div class="bubble-p-info">
                    <div class="title-big">大甲</div>
                    <div class="price">$1500</div>
                    <div class="describe">
                        大甲最好玩的地方哦
                        大甲最好玩的地方哦
                        大甲最好玩的地方哦
                        大甲最好玩的地方哦
                    </div>
                </div>
            </div>
        </a>
    </template>

    <!--TEMPLATE:右欄位:賣家商品瀏覽:-->
    <template id="productUnit-template">
        <div class="productUnit putCenter">
            <img src="../img//example/headshot.jpg" alt="">
            <div class="p-info">
                <div class="title-big">大甲</div>
                <div class="price">$1500</div>
                <div class="btn2 inquire-btn">詢問</div>
            </div>
        </div>
    </template>

    <!--TEMPLATE:左欄:用戶列表單位-->
    <template id="userunit-template">
        <div class="userunit col2-70-auto">
            <img src="../img/example/headshot.jpg" alt="">
            <p class="putCenter name">123</p>
        </div>
    </template>

    <script src='../js/chatroomControl.js'></script>
</body>

</html>