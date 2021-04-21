<!DOCTYPE html>
<html>

<head>
    <meta charset='UTF-8'>
    <?php require("../PHP/autoLogin.php"); ?>
    <?php require("../PHP/common.php"); ?>
    <title>
        <?php getData("product", "title", "ID", $_GET["id"], "") ?>
    </title>
    <link rel="shortcut icon" href="../img/icon/logo1.png" type="image/png">

    <link href='https://fonts.googleapis.com/css?family=Noto+Sans+TC&display=swap&subset=chinese-traditional' rel='stylesheet'>

    <!--價格用字體-->
    <link href='https://fonts.googleapis.com/css?family=Merriweather+Sans&display=swap' rel='stylesheet'>

    <script src='http://code.jquery.com/jquery-1.11.3.min.js'></script>
    <script src='../js/common.js'></script>

    <link rel='stylesheet' href='../css/reset.css'>
    <link rel='stylesheet' href='../css/common.css'>
    <link rel='stylesheet' href='../css/main.css'>
    <link rel='stylesheet' href='../css/productView.css'>
</head>

<body>
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
        <!--其他功能-->
        <div class="col2-auto-auto">
            <!--進入聊天室-->
            <div class="putCenter">
                <a href="./chatroom.php">
                    <img class="chatroom-icon shadow-m" src="../img/icon/chatroom.png" alt="">
                </a>

            </div>
            <!--購物車-->
            <div class="putCenter">
                <a href="./profile.php?page=cart">
                    <img class="chatroom-icon shadow-m" src="../img/icon/shoppingCart.png" alt="">
                </a>
            </div>
        </div>
        <!--大頭貼 或 "登入|註冊"按鈕-->
        <div class="putCenter">

            <?php if (!@$_SESSION['id']) { ?>
                <div class="sign-log-in">
                    <a href="../page/login.html">登入</a>
                    <a href="../page/singup.html">註冊</a>
                </div>

            <?php } else { ?>
                <a href='../page/profile.php'>
                    <img class='user-headShot shadow-m' src='../upload/avatars/<?php GetUserData("photo") ?>'>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class='mainContent'>
        <!--資訊總攬-->
        <div class='titleInfo basicWhiteBoard '>
            <!--左欄:圖片 + 購買btn-->
            <div class='titleInfo-left'>
                <img src='../upload/productThumbIMG/<?php getData("productthumbnail", "photo", "productID", $_GET['id'], "") ?>' alt=''>
                <div class='putCenter'>
                    <button class='block-btn btn1' id='contentSeller-btn' type='button' onclick=''>聯絡賣家</button>
                    <button class='block-btn btn2' id='buy-btn' type='button' onclick=''>購買</button>
                </div>
            </div>

            <!--右欄-->
            <div class='titleInfo-right'>
                <!--欄位Title-->
                <h1 class='productTitle'><?php getData("product", "title", "ID", $_GET['id'], "") ?></h1>
                <!--欄位:評價資訊-->
                <div class='title-ratingInfo'>
                    <img src='../img/icon/star1.png' class='putCenter' alt=''>
                    <p><?php countData("orderRecord", "productID", "productID", $_GET['id']) ?> 次成交</p>
                    <p><?php countData("productreview", "productID", "productID", $_GET['id']) ?>則評論</p>
                </div>

                <!--欄位:收藏+價格資訊-->
                <div class='middlePart'>
                    <h2 class='price'>$<?php getData("product", "price", "ID", $_GET['id'], "") ?></h2>
                    <div class='amount-container putCenter'>
                        <!--選擇人數-->
                        <p>人數</p>
                        <button id='amountMinus' class='btn-r-s btn1'>-</button>
                        <div id='amount'>1</div>
                        <button id='amountPlus' class='btn-r-s btn2'>+</button>
                        <div class="col-auto-auto putCenter">
                            <span class='subText'>剩餘</span>
                            <span id='amountLeft' class='subText'><?php getData("product", "people", "ID", $_GET['id'], "") ?></span>
                        </div>

                    </div>
                    <div class='saveInfo'>
                        <?php if (checkHasData("collect", "userID", "productID", $_GET['id'])) { ?>
                            <img src='../img/icon/heart_on.png' alt=''>
                        <?php } else { ?>
                            <img src='../img/icon/heart_off.png' alt=''>
                        <?php } ?>
                        <span><?php countData("collect", "userID", "productID", $_GET['id']) ?> 人收藏</span>
                    </div>
                </div>

                <!--欄位:描述-->
                <div class='describePart'>
                    <div class='basicInfo'>
                        <p>日程: <?php getData("product", "days", "ID", $_GET['id'], "") ?>天</p>
                        <p>區域:
                            <span><?php getData("productregion", "country", "productID", $_GET['id'], "") ?></span>
                            <span id="county"> <?php getData("productregion", "county", "productID", $_GET['id'], "") ?></span>
                        </p>
                        <p>上傳日期: <?php getData("product", "uploadDate", "ID", $_GET['id'], "") ?></p>
                    </div>
                    <div class='dercribe'>
                        <?php getData("product", "info", "ID", $_GET['id'], "") ?>
                    </div>
                </div>

                <!--賣家資料-->
                <div class='sellerInfo'>
                    <p> <?php getData(
                            "member",
                            "name",
                            "ID",
                            $_GET['seller'],
                            ""
                        ) ?></p>
                    <img src='../img/icon/star1.png' alt=''>
                </div>

            </div>

            <!--展示圖片-->
            <div class='showPictureGrid putCenter'>

                <!--EXAMPLE
                <img src='../img/example/headshot.jpg' alt=''>
                <img src='../img/example/headshot.jpg' alt=''>
                <img src='../img/example/headshot.jpg' alt=''>
                -->
            </div>
        </div>

        <!--選擇日期欄-->
        <div id='dateGrid' class='basicWhiteBoard putCenter'>
            <h1 class='productTitle'>選擇出遊日期</h1>
            <div class='calendarContainer putCenter'>
                <p class='calendarTitle putCenter' id='monthAndYear'>2020 4月</p>
                <table id='calendar-tabel'>
                    <thead>
                        <tr>
                            <th>日</th>
                            <th>一</th>
                            <th>二</th>
                            <th>三</th>
                            <th>四</th>
                            <th>五</th>
                            <th>六</th>
                        </tr>
                    </thead>
                    <tbody id='calendarBody'>

                    </tbody>
                </table>
                <!--切換上下月按鈕-->
                <div class='putCenter'>
                    <button class='block-btn btn1' id='preMon-btn' type='button' onclick=previous()>上個月</button>
                    <button class='block-btn btn2' id='nextMon-btn' type='button' onclick=next()>下個月</button>
                </div>
            </div>

        </div>

        <!--行程規劃-->
        <div class='basicWhiteBoard putCenter' id="">
            <h1 class='productTitle'>行程規劃</h1>
            <div class="putCenter" id="agenda-view">
                <!--EXAMPLE
                <div class='agenda putcenter'>
                    <div class='pointCircle-big putCenter'>Day 1</div>
                    <h1 class='productTitle putCenter'>鎮瀾宮一日遊</h1>
                    <p></p>
                    <div class='wrap'>
                        <div class="agenda-content">
                            <p class='pointCircle-small putCenter'></p>
                            <div class='agenda-itme'>台北車站相見歡</div>
                            <p></p>
                            <p class='subText'>6:30~7:30:林先生會在台北車站接您</p>
                        </div>
                    </div>
                </div>-->
            </div>



        </div>


    </div>

    <!--估價-->
    <div class='basicWhiteBoard'>
        <h1 class='productTitle'>估價</h1>
        <div class='price-grid putCenter'>
            <table class='price-table'>
                <thead>
                    <tr>
                        <th class='cell-l'>日程</th>
                        <th>摘要</th>
                        <th class='cell-l'>費用</th>
                        <th class='cell-l'>數量</th>
                    </tr>
                </thead>
                <tbody>
                    <!--EXAMPLE
                    <tr>
                        <td class='cell-l'>Day1</td>
                        <td class='text-indent'>服務費</td>
                        <td class='cell-l'>150</td>
                        <td class='cell-l'>1</td>
                    </tr>
                    -->
                </tbody>
            </table>

            <div class='price-total'>
                <p></p>
                <p id='total'>Total</p>
                <p id='total-number'> <?php getData("product", "price", "ID", $_GET['id'], "") ?>/人</p>
            </div>

        </div>

    </div>

    <!--其他聲明-->
    <div class='basicWhiteBoard'>
        <p class='productTitle'>其他聲明</p>
        <div id='otherState' class='putCenter'>
            <?php getData("product", "other", "ID", $_GET['id'], "") ?>
        </div>
    </div>

    <!--評價 //TODO-->
    <div class='basicWhiteBoard'>
        <p class='productTitle'>評價</p>
        <div class='commentContainer putCenter'>
            <!--EXAMPLE 買家+賣家評價-->
            <div class='commentGroup'>
                <!--EXAMPLE買家評價-->
                <div class='comment'>
                    <img src='../img/example/headshot.jpg' alt='' class='user-headShot putCenter'>
                    <p class='subText'>林小姐</p>
                    <div class='ratingInfo'>
                        <img src='../img/icon/star1.png' class='putCenter' alt=''>
                        <p>2020/4/3</p>
                    </div>
                    <p class='subText'>超級好玩</p>
                </div>
                <hr>

                <!--EXAMPLE賣家回復-->
                <div class='commentReply'>
                    <img src='../img/example/headshot.jpg' alt='' class='user-headShot putCenter'>
                    <p class='subText'>林小姐</p>
                    <div class='ratingInfo'>
                        <img src='../img/icon/star1.png' class='putCenter' alt=''>
                        <p>2020/4/3</p>
                    </div>
                    <p class='subText'>超級好玩</p>
                </div>
            </div>

        </div>


    </div>


    <!--其他推薦-->
    <div class='basicWhiteBoard'>
        <p class='productTitle'>推薦給您</p>
        <div id="productDisplay" class='recommend'>
            <!--EXAMPLE每個商品展示單位-->
        </div>
    </div>

    <script src='../js/createProductUnit.js'></script>
    <script src='../js/calendarControl.js'></script>
    <script src='../js/productViewControl.js'></script>
    <script src='../js/indexPageControl.js'></script>

    <!--TEMPLATE: 日程規劃-->
    <template id="agenda-template">
        <div class='agenda putcenter'>
            <div class='pointCircle-big putCenter'>Day 1</div>
            <h1 class='productTitle putCenter'>鎮瀾宮一日遊</h1>
            <p></p>
            <div class='wrap'>
                <!--小項目-->
            </div>
        </div>
    </template>

    <!--TEMPLATE: 活動規劃-->
    <template id="agenda-content-template">
        <div class="agenda-content">
            <p class='pointCircle-small putCenter'></p>
            <div class='agenda-itme'>台北車站相見歡</div>
            <p></p>
            <p class='subText'>6:30~7:30:林先生會在台北車站接您</p>
        </div>
    </template>


    <!--TEMPLATE: 估價 小項目-->
    <template id="price-table-tr-template">
        <tr>
            <td class='cell-l'>Day1</td>
            <td class='text-indent'>服務費</td>
            <td class='cell-l'>150</td>
            <td class='cell-l'>1</td>
        </tr>

    </template>

    <!--TEMPLATE:留言組(買家+賣家)-->
    <template id="commentGroup-template">
        <div class='commentGroup'>
            <!--EXAMPLE買家評價-->
            <div class='comment'>
                <img src='../img/example/headshot.jpg' alt='' class='user-headShot putCenter'>
                <p class='subText buyer-name'>林小姐</p>
                <div class='rv'>
                    <img src='../img/icon/star1.png' class='putCenter' alt=''>
                    <p class="subText">2020/4/3</p>
                </div>
                <p class='subText comment-text'>超級好玩</p>
            </div>
            <hr>

            <!--EXAMPLE賣家回復-->
            <div class='commentReply'>
                <img src='../img/example/headshot.jpg' alt='' class='user-headShot putCenter'>
                <p class='subText'>賣家回覆:</p>
                <div class='rv'>
                    <img src='../img/icon/star1.png' class='putCenter' alt=''>
                    <!--<p class="subText">2020/4/3</p>-->
                    <p class='subText'>超級好玩</p>
                </div>
            </div>
        </div>
    </template>

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

</body>

</html>