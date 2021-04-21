<!DOCTYPE html>
<html>

<head>
    <meta charset='UTF-8'>
    <title>
        搜尋結果
    </title>
    <link rel="shortcut icon" href="../img/icon/logo1.png" type="image/png">

    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+TC&display=swap&subset=chinese-traditional" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans&display=swap" rel="stylesheet">

    <link rel='stylesheet' href='../css/reset.css'>
    <link rel='stylesheet' href='../css/common.css'>
    <link rel='stylesheet' href='../css/main.css'>

    <script src='http://code.jquery.com/jquery-1.11.3.min.js'></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="../js/common.js"></script>
</head>

<body>
    <?php
    require("../PHP/autoLogin.php");
    ?>
    <div class="wrapper">
        <!--HEADER-->
        <div class="header">
            <div class="logo putCenter"><a href="../">PAPAGO</a></div>
            <!--SEARCH BAR-->
            <div class="putCenter">
                <div class="searchbar-wrapper">
                    <input class="searchBar" type="search" name="" id="searchbar-input" placeholder="明天要去哪裡玩?">
                    <input class="roundBtn" type="submit" id="searchbar-btn" value="搜尋">
                </div>
            </div>
            <!--進入聊天室-->
            <div class="putCenter">
                <a href="./chatroom.php">
                    <img class="chatroom-icon shadow-m" src="../img/icon/chatroom.png" alt="">
                </a>
            </div>
            <!--大頭貼 或 "登入|註冊"按鈕-->
            <div class="putCenter">

                <?php if (!@$_SESSION['id']) { ?>
                    <div class="sign-log-in">
                        <a href="../page/login.html">登入</a>
                        <a href="../page/singup.html">註冊</a>
                    </div>

                <?php } else { ?>
                    <a href='../page/profile.php?id=<?php GetUserData("ID") ?>'>
                        <img class='user-headShot shadow-m' src='../upload/avatars/<?php GetUserData("photo") ?>'>
                    </a>
                <?php } ?>
            </div>
        </div>

        <!--頭版區-->
        <div style="padding-top:100px;">

        </div>

        <div id="anchore-top"></div>
        <!--主要內容-->
        <div class="mainSection putCenter">
            <p class="productTitle putCenter">搜尋結果</p>
            <!--選擇排序-->
            <!--//TODO:排序功能+選擇特效-->
            <ul class="selectionBar shadow-m">
                <li id="newest">最新</li>
                <li id="hotest">最熱</li>
                <li id="cheapest">最便宜</li>
            </ul>

            <!--EXAMPLE:每個產品展示單位-->
            <div id="productDisplay">
            </div>

        </div>

        <!--下上一頁-->
        <div class="changePage-btn col2-auto-auto putCenter">
            <a href="#anchore-top">
                <p id="prevpage-btn" class="btn1 putCenter">上一頁</p>
            </a>
            <a href="#anchore-top">
                <p id="nextpage-btn" class="btn2 putCenter">下一頁</p>
            </a>
        </div>
    </div>

    <!--FOOTER-->
    <div class="footer">
        <div class="footer-info putCenter">
            <p>第13組:林慶佳、詹瀞涵</p>
            <p>-輕旅社版權所有-</p>
        </div>
    </div>

    <!--TEMPLATE:每個商品展示單位-->
    <template id="productPreviewUnit-temlplate">
        <a class="productPreviewUnit" href="#">
            <img src="../img/example/headshot.jpg" class="thumb">
            <div>
                <h1 class="productTitle">鎮瀾宮一日遊</h1>
                <div class="productPreviewInfo">
                    <!--左-->
                    <div class="ratingInfo">
                        <img src="../img/icon/star1.png" class="putCenter" alt="">
                        <p class="ordernumber">5次成交</p>
                        <p class="commentnumber">5則評論</p>
                    </div>
                    <!--右-->
                    <div class="dercribe">
                        鎮瀾宮超好玩超好玩超好玩
                    </div>
                </div>

                <!--價錢 和 聯絡人-->
                <div class="productPreviewFooter">
                    <h2 class="price putCenter">$1499</h2>
                    <!--收藏資訊-->
                    <div class="saveInfo">
                        <img src="../img/icon/heart_on.png" alt="">
                        <span>0人收藏</span>
                    </div>
                    <div class="sellerInfo putCenter">
                        <p>陳先生</p>
                        <img src="../img/icon/star1.png" alt="">

                    </div>

                </div>

            </div>
        </a>
    </template>

    <script src='../js/createProductUnit.js'></script>
    <script src='../js/indexPageControl.js'></script>

</body>

</html>