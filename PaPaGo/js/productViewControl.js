var amount = 1;//人數
var maxpeople;
var dayCount = 0;//天數


$(document).ready(function () {
    maxpeople = parseInt($("#amountLeft").html());
    maxpeople -= amount;
    $("#amountLeft").html(maxpeople);

    createAgenda();
    
    getReviewRate(GetURLParameter("id")).then(function (res) {
        console.log(res);
        var src = "../img/icon/star" + res + ".png";
        $(".title-ratingInfo img").attr("src", src);
    });
    getSellerReviewRate(GetURLParameter("seller")).then(function (res) {
        console.log(res);
        var src = "../img/icon/star" + res + ".png";
        $(".sellerInfo img").attr("src", src);
    });
    getReviewComment();

});

//人數+-
$('#amountPlus').click(function () {
    //TODO:判斷最大人數限制
    var maxpeople = $("#amountLeft").html();
    if (maxpeople > 0) {
        amount += 1;
        $("#amount").html(amount);

        $("#amountLeft").html((maxpeople - 1));
    }
});

$('#amountMinus').click(function () {
    //TODO:判斷最小人數限制
    var maxpeople = $("#amountLeft").html();
    if (amount > 1) {
        amount -= 1;
        $("#amount").html(amount);
        $("#amountLeft").html(parseInt(maxpeople) + 1);
    }
});

//收藏愛心 開關
$(".saveInfo img").click(function () {
    //紀錄至資料庫
    if ($(".saveInfo img").attr('src') == "../img/icon/heart_off.png") {
        $(".saveInfo img").attr('src', "../img/icon/heart_on.png");
    }
    else {
        $(".saveInfo img").attr('src', "../img/icon/heart_off.png");
    }
    $.ajax({
        url: "../PHP/addtoCollect.php",
        type: "post",
        data: {
            'pid': GetURLParameter('id')
        },
        success: function (result) {
            console.log(result);
        },
        error: function (err) {
        }
    });
});


//讀取頁面: 展示圖片
$.ajax({
    url: "../PHP/common.php",
    type: "post",
    data: {
        'func_name': 'getData',
        'tb_name_post': 'productimage',
        'row_name_post': 'photo',
        'where_post': 'productID',
        'key_post': GetURLParameter('id'),
        'split_by_post': '|',
    },
    success: function (result) {
        var imgSrc = "../upload/productIMG/";
        if (result != "") {
            var pics = result.split("|");
            for (var i = 0; i < pics.length - 1; i++) {
                console.log(pics[i]);
                $(".showPictureGrid").append("<img src='" + imgSrc + pics[i] + "'>");
            }
        }
    },
    error: function (err) {
        reject(err);// Reject the promise and go to catch()
    }
});

//讀取行程規劃
function createAgenda() {
    //產生日程項目then:
    createAgendaDayContent().then(createAgendaEachDaysActivity());
}

function createAgendaDayContent() {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/common.php",
            type: "post",
            data: {
                'func_name': 'getData',
                'tb_name_post': 'daycontent',
                'row_name_post': 'title',
                'where_post': 'productID',
                'key_post': GetURLParameter('id'),
                'split_by_post': '|',
                'order_by_post': 'day',
            },
            success: function (result) {
                console.log(result);
                var days = result.split('|');
                for (var i = 0; i < days.length - 1; i++) {
                    var dayContnet = $($("#agenda-template").html()).clone();
                    $(dayContnet).attr("id", "dayContent" + (i + 1));
                    $(dayContnet).find(".pointCircle-big").html("Day " + (i + 1));
                    $(dayContnet).find(".productTitle").html(days[i]);
                    $("#agenda-view").append(dayContnet);

                    dayCount++;
                }
                //resolve(result); 不用回傳值

            },
            error: function (err) {
                reject(err);
            }
        });
    });
}

//產生小項目
function createAgendaEachDaysActivity() {
    var rows = ["day", "sequence", "title", "content"];
    $.ajax({
        url: "../PHP/common.php",
        type: "post",
        data: {
            'func_name': 'getData_inRow',
            'tb_name_post': 'activity',
            'row_name_post': rows,
            'where_post': 'productID',
            'key_post': GetURLParameter('id'),
            'split_data_by_post': '|',
            'split_row_by_post': "@",
            'order_by_post': "day,sequence",
            'doEcho': true,
        },
        success: function (result) {
            var rows = result.split("@");

            for (var i = 0; i < rows.length - 1; i++) {
                var datas = rows[i].split("|");
                var dayContent = $("#dayContent" + datas[0]);
                console.log(datas);

                var wrap = $(dayContent).find(".wrap");
                var activity = $($("#agenda-content-template").html()).clone();
                $(activity).find(".agenda-itme").html(datas[2]);
                $(activity).find(".subText").html(datas[3]);
                $(activity).attr("id", ("D" + datas[0] + "-A" + datas[1]));
                wrap.append(activity);
            }

        },
        error: function (err) {
            reject(err);
        }

    });
}



//估價表:
var rows = ["day", "content", "price", "quantity"];
$.ajax({
    url: "../PHP/common.php",
    type: "post",
    data: {
        'func_name': 'getData_inRow_json',
        'tb_name_post': 'productbillcontent',
        'row_name_post': rows,
        'where_post': 'productID',
        'key_post': GetURLParameter('id'),
        'split_data_by_post': '|',
        'split_row_by_post': "@",
        'order_by_post': "day",
        'doEcho': true,
    },
    success: function (result) {
        /*
        var rows = result.split("@");

        for (var i = 0; i < rows.length - 1; i++) {
            var datas = rows[i].split("|");

            //過濾:
            datas[0] = datas[0] == 0 ? "全程" : datas[0];

            var tbody = $(".price-table tbody");
            var tr = $($("#price-table-tr-template").html()).clone();

            $(tr).children().each(function (index) {
                $(this).html(datas[index]);
            });


            tbody.append(tr);
        }*/
        var datas = JSON.parse(result);
        console.log(datas);
        for (var i = 0; i < datas.length; i++) {
            //過濾:
            datas[i]["day"] = datas[i]["day"] == 0 ? "全程" : datas[i]["day"];

            var tbody = $(".price-table tbody");
            var tr = $($("#price-table-tr-template").html()).clone();

            $(tr).children().each(function (index) {
                $(this).html(datas[i][rows[index]]);
            });


            tbody.append(tr);
        }

    },
    error: function (err) {
        reject(err);
    }

});

//TODO:日歷 讀取並顯示已經被選走的日期

//購買產品 btn:
$("#buy-btn").click(function () {

    //檢查有沒有選擇日期:
    if ($(".selectedDate").length) {
        //日期 (calender)
        var datas = selectedDate.split("-");
        var day_select = datas[2];
        var month_select = datas[1];
        var year_select = datas[0];

        //TODO:產生確定購買提示:
        console.log(datas);
        //資料:
        var startDate = new Date(year_select, month_select - 1, day_select);
        var endtDate = new Date();
        endtDate.setDate(startDate.getDate() + dayCount);
        //日期格式:
        var startDate_for = startDate.getFullYear() + "-" + (startDate.getMonth() + 1) + "-" + startDate.getDate();
        var endtDate_for = endtDate.getFullYear() + "-" + (endtDate.getMonth() + 1) + "-" + endtDate.getDate();

        var price = $(".price").html().replace("$", "");

        console.log(GetURLParameter("seller"));
        //打包資料送出:
        //TODO[安全]:檢查送出資料之格式
        var formData = new FormData();
        formData.append("productID", GetURLParameter("id"));
        formData.append("sellerID", GetURLParameter("seller"));
        formData.append("startDate", startDate_for);
        formData.append("endDate", endtDate_for);
        formData.append("price", price);
        formData.append("people", $("#amount").html());
        formData.append("payMethod", 1); //TODO:預設貨到付款=1
        console.log(price);

        $.ajax({
            url: '../PHP/makeOrder.php',
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            enctype: 'multipart/form-data',
            processData: false,
            success: function (response) {
                console.log(response);
                if (response == "die") {
                    alert("請先登入");
                }
                else {
                    alert("已經放入購物車! 請去確認");
                }
            }
        });

    }
    else {
        alert("請選擇日期");
        //TODO:自動往下滑
    }



});

//聯絡賣家 btn"
$("#contentSeller-btn").click(function () {
    var sellerID = GetURLParameter("seller");
    location.href = "chatRoom.php?seller=" + sellerID;

});

//取得評價留言資料:
function getReviewComment() {
    $.ajax({
        url: "../PHP/getReviewRate.php",
        type: "post",
        data: {
            'dataType': 'productReview',
            'pid': GetURLParameter("id")
        },
        success: function (result) {
            console.log(result);
            var datas = JSON.parse(result);
            $(".commentContainer").empty();
            for (var i = 0; i < datas.length; i++) {
                var temp = $($("#commentGroup-template").html()).clone();
                $(temp).find(".comment img").attr("src", upload_avaPic + datas[i]["photo"]);
                $(temp).find(".comment .buyer-name").html(datas[i]["name"]);
                $(temp).find(".comment .comment-text").html(datas[i]["comment"]);
                var b_star = "../img/icon/star" + Math.round(datas[i]["rate"]) + ".png"
                $(temp).find(".comment .rv img").attr("src", b_star);
                $(temp).find(".comment .rv p").html(datas[i]["date"]);

                //賣家有回復
                if (typeof datas[i]["Reply"] !== "undefined") {
                    var b_r_star = "../img/icon/star" + Math.round(datas[i]["Reply_rate"]) + ".png"
                    $(temp).find(".commentReply .rv img").attr("src", b_r_star);
                    $(temp).find(".commentReply .rv .subText").html(datas[i]["Reply"]);
                }
                else {
                    $(temp).find(".commentReply").remove();//TEMP
                }


                $(".commentContainer").append(temp);
            }
        },
        error: function (err) {
            reject(err);// Reject the promise and go to catch()
        }
    });

};

//延遲2秒再抓
setTimeout(function () { createRecommend() }, 2000);
//產生推薦(用縣市區別)
function createRecommend() {
    var c = ($("#county").html()).trim();
    console.log(c);
    getProductData(0, page_load_amount, c, "hotest").then(function () {
        //移除自己的:
        $("#" + GetURLParameter("id")).remove();

        fillFullData();
        //沒符合資料就不顯示
        if (!$(".productPreviewUnit").length) {
            console.log($(".recommend"));
            $(".recommend").parent().remove();
        }
    });
}