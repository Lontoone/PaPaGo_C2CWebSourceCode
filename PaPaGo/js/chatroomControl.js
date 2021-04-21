var checkChatUpdate = window.setInterval(updateChat, 2000);
//TODO:[小bug]第一次被傳訊息需要重整畫面
var chat_current_limit = 0; //載入對話limit開始 單位:(a+1)*10
var enterTime;//讀取資料時間，用來檢查有沒有新的對話
var canCheck = true;
//聊天室控制:
$(document).ready(function () {
    //設定左欄
    getUserList();

    var sid = GetURLParameter("seller");
    setupRoom(sid);

    enterTime = new Date().getTime();
    console.log("ENTER: " + enterTime);

});

//用網址裡的seller查詢對方資料
function setupRoom(sellerID) {
    //清除
    $(".productGroup").empty();
    $("#chatBoard").empty();

    //設定中欄
    ajax_getChatData(sellerID, 0).then(function (pid) {
        //補上商品資訊
        set_inquired_porduct_chatBobble_data(pid);
    });

    //設定右欄
    ajax_getSellerData(sellerID);

}

//更新對話內容
function updateChat() {
    console.log("ENTER: " + getTime_to_format(enterTime));
    //回傳: 若有訊息時間晚於enterTime，則產生訊息:
    if (canCheck) {
        ajax_checkChat().then(function (res) {
            //有無新資料?
            if (new Date(res).getTime() >= enterTime) {
                console.log("DATA");
                var sid = GetURLParameter("seller");
                console.log(res);
                ajax_getChatData(sid, 0, "", getTime_to_format(enterTime), false);
                enterTime =new Date(res).getTime();
            }
            
            else {
                if (new Date(res).getTime() > enterTime) {
                    enterTime = new Date(res).getTime();
                }
            }
            
            canCheck = true;
        });
    }

}
function ajax_checkChat() {
    canCheck = false;
    var t = getTime_to_format(enterTime);
    //console.log(t);
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/chatRoomControl.php",
            type: "post",
            data: {
                'dataType': "chatCheck",
                'enterTime': t,
                'seller': GetURLParameter("seller")
            },
            success: function (result) {
                console.log("CHECK" + result);
                //var datas = JSON.parse(result);
                resolve(result);
            },

            error: function (err) {
                reject(err);
            }
        });
    });
}

//左欄:取得有對話紀錄的用戶 or目前正在對話的用戶
function getUserList() {
    var seller = GetURLParameter("seller");
    ajax_getUser(seller).then(function () {
        if (typeof seller === "undefined") { //進入時沒指定賣家
            //找第一個
            var sid = $(".userunit").attr("id");
            var pageURL = $(location).attr("href");
            pageURL += "?seller=" + sid;
            window.history.replaceState('', '', pageURL);
            setupRoom(sid);
        } else {
            //標記目前正在對話的對象
            $("#" + seller).addClass("userunit-selected");
        }
    });

}
function ajax_getUser(sid) {
    console.log(sid);
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/chatRoomControl.php",
            type: "post",
            data: {
                'dataType': "getChatUser",
                'seller': sid
            },
            success: function (result) {
                console.log(result);
                var datas = JSON.parse(result);
                for (var i = 0; i < datas.length; i++) {
                    var src = upload_avaPic + datas[i]["photo"];
                    setUserUnit(datas[i]["ID"],
                        src,
                        datas[i]["name"]);
                }
                resolve(sid);
            },

            error: function (err) {
                reject(err);
            }
        });
    });
}
//左欄:產生每列
function setUserUnit(id, img, name) {
    var temp = $($("#userunit-template").html()).clone();
    $(temp).attr("id", id);
    $(temp).find("img").attr("src", img);
    $(temp).find(".name").html(name);
    $("#userList").append(temp);
}

//點擊左欄用戶=>打開對話與資料
$(document).on("click", ".userunit", function () {
    console.log($(this).attr("id"));
    //設定樣式
    $(".userunit-selected").removeClass("userunit-selected");
    $(this).addClass("userunit-selected");

    //更新網址上的sellerID
    var uid = $(this).attr("id");
    var pageURL = $(location).attr("href").split("?")[0];
    pageURL += "?seller=" + uid;
    window.history.replaceState('', '', pageURL);

    //刷新頁面
    setupRoom(uid);

});

//中欄:讀取聊天資料
function ajax_getChatData(sid, limit_start, time_before, time_after, isPrepend = true) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/chatRoomControl.php",
            type: "post",
            data: {
                'dataType': "getChatData",
                'seller': sid,
                "limit_start": limit_start,
                "time_before": time_before,
                "time_after": time_after
            },
            success: function (result) {
                console.log(result);
                var datas = JSON.parse(result);
                console.log(datas);
                for (var i = 0; i < datas.length; i++) {
                    var temp;
                    //判斷是一般對話還是商品泡泡
                    if (datas[i]["productID"].length) {//商品
                        temp = $($("#product-chatbobble-template").html()).clone();
                        //id格式:i+[pid]
                        $(temp).find(".product-chatbobble").attr("id", "i" + datas[i]["productID"]);
                        var h = "./productView.php?id=" + datas[i]["productID"] + "&seller=" + sid;
                        $(temp).attr("href", h);
                    }
                    else {//一般對話
                        temp = $($("#chatbobble-text-template").html()).clone();
                        $(temp).find(".content").html(datas[i]["content"]);
                        $(temp).find(".subText").html(datas[i]["date"]);
                    }

                    //判斷對方還是自己的
                    if (datas[i]["sentFrom"] == sid) {//對方
                        $(temp).addClass("f-l");
                        $(temp).addClass("btn2");
                    } else {
                        $(temp).addClass("f-r");
                        $(temp).addClass("btn1");
                    }

                    //prepend:讀取所有舊資料
                    //append:動態讀取的對話
                    if (isPrepend) {
                        $("#chatBoard").prepend(temp);
                    }
                    else {
                        console.log(datas[i]['date'] + " VS " + getTime_to_format(enterTime));
                        if (new Date(datas[i]['date']).getTime() < enterTime) {continue;}
                        $("#chatBoard").append(temp);
                    }

                    if (datas[i]["productID"].length) {
                        set_inquired_porduct_chatBobble_data(datas[i]["productID"]);
                    }
                    
                    console.log(datas[i]['date'] + " VS " + getTime_to_format(enterTime));
                    console.log(new Date(datas[i]['date']).getTime() > enterTime);
                    if (new Date(datas[i]['date']).getTime() > enterTime) {
                        enterTime = new Date(datas[i]['date']).getTime();
                        console.log(datas[i]["date"] + " VS " + getTime_to_format(enterTime));
                    }
                }
                //滑至最下面
                var d = $('#chatBoard');
                d.scrollTop(d.prop("scrollHeight"));

            },

            error: function (err) {
                reject(err);
            }
        });
    });
}

//補上對話中詢問商品的資料:
function set_inquired_porduct_chatBobble_data(pid) {
    console.log(pid);
    $.ajax({
        url: "../PHP/chatRoomControl.php",
        type: "post",
        data: {
            'dataType': "getInruiredProduct",
            'productID': pid,
        },
        success: function (result) {
            var data = JSON.parse(result);
            $("#i" + pid).find(".describe").html(data[0]["info"]);
            $("#i" + pid).find(".title-big").html(data[0]["title"]);
            $("#i" + pid).find(".price").html(data[0]["price"]);
            $("#i" + pid).find("img").attr("src", upload_thumpPhoto + data[0]["photo"]);
            //移除id避免和下面重複:
            $("#i" + pid).attr("id", "");
        },

        error: function (err) {
            reject(err);
        }
    });

}

//中欄:enter後送出對話
$(document).on("keydown", "#chatInput", function (event) {
    if (event.key == "Enter" && $(this).val() != "") {
        //送出:
        var content = $(this).val();
        $(this).val('');
        ajax_sendChatData(content).then(function () {
            //放上版面
            temp = $($("#chatbobble-text-template").html()).clone();
            $(temp).find(".content").html(content);
            $(temp).find(".subText").html(new Date().toLocaleString());
            $(temp).addClass("f-r");
            $(temp).addClass("btn1");
            $("#chatBoard").append(temp);

            //滑至最下面
            var d = $('#chatBoard');
            d.scrollTop(d.prop("scrollHeight"));

            //更新對話時間:
            //enterTime = new Date().getTime();
        });
    };
});
//送出純文字對話
function ajax_sendChatData(content) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/chatRoomControl.php",
            type: "post",
            data: {
                'dataType': "sendChatData",
                'seller': GetURLParameter("seller"),
                'content': content,
            },
            success: function (result) {
                console.log(result);
                resolve(result);
            },

            error: function (err) {
                reject(err);
            }
        });
    });
}
//送出商品詢問
$(document).on("click", ".inquire-btn", function () {
    var pid = $(this).attr("id");
    console.log(pid);
    ajax_sendProductInquireData(pid).then(function (res) {
        //copy原btn上的資料:
        var origin = $("#" + pid).parent().parent();
        console.log(origin);
        console.log($(origin).find("img").attr("src"));
        //放上版面:
        var temp = $($("#product-chatbobble-template").html()).clone();
        $(temp).find("img").attr("src", $(origin).find("img").attr("src"));
        $(temp).find(".title-big").html($(origin).find(".title-big").html());
        $(temp).find(".price").html($(origin).find(".price").html());
        ajax_single_common_request("getData", "product", "info", "ID", pid, "", "").then(function (res) {
            $(temp).find(".describe").html(res);
        });
        $(temp).addClass("f-r");
        $(temp).addClass("btn1");
        $("#chatBoard").append(temp);

        //滑至最下面
        var d = $('#chatBoard');
        d.scrollTop(d.prop("scrollHeight"));

        //更新對話時間:
        //enterTime = new Date().getTime();
    });
});
function ajax_sendProductInquireData(pid) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/chatRoomControl.php",
            type: "post",
            data: {
                'dataType': "sendChatData",
                'seller': GetURLParameter("seller"),
                'productID': pid,
            },
            success: function (result) {
                console.log(result);
                resolve(result);
            },

            error: function (err) {
                reject(err);
            }
        });
    });
}

//右欄:讀取用戶資料:
function ajax_getSellerData(sid) {
    console.log(sid);
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/chatRoomControl.php",
            type: "post",
            data: {
                'dataType': "getSellerData",
                'seller': sid
            },
            success: function (result) {
                var datas = JSON.parse(result);
                //更新用戶資訊
                $("#userava").attr("src", upload_avaPic + datas[0]["ava"]);
                $(".userPreviewUnit .nameTitle").html(datas[0]["name"]);

                //展示商品:
                for (var i = 0; i < datas.length; i++) {
                    if (typeof datas[i]["pid"] === "undefined") { return; }
                    temp = $($("#productUnit-template").html()).clone();
                    $(temp).find("img").attr("src", upload_thumpPhoto + datas[i]["pt"]);
                    $(temp).find(".title-big").html(datas[i]["title"]);
                    $(temp).find(".price").html("$" + datas[i]["price"]);
                    $(temp).find(".inquire-btn").attr("id", datas[i]["pid"]);
                    $(".productGroup").append(temp);
                }

            },

            error: function (err) {
                reject(err);
            }
        });
    });
}

//中欄:載入之前對話
$("#loadMore-m").click(function () {
    chat_current_limit = (chat_current_limit + 1) * 10;
    ajax_getChatData(GetURLParameter("seller"), chat_current_limit);
    console.log(chat_current_limit);
});


//左欄:搜尋用戶
$(document).on("input", "#search-u", function () {
    //先找上面清單有沒有
    var hasData = false;
    var input = $(this).val();
    if (input.length) {
        $(".userunit").each(function () {
            var title = $(this).find(".name").html();
            if (title.includes(input)) {
                hasData = true;
                $(this).removeClass("hidden");
            }
            else {
                $(this).addClass("hidden");
            }
        });
    }
    else {
        //全部顯示
        $(".userunit").each(function () {
            $(this).removeClass("hidden");
        });
    }

    //找不到=>資料庫找
    if (!hasData) {
        //$("#userList").empty();
        $.ajax({
            url: "../PHP/searchControl.php",
            type: "post",
            data: {
                'dataType': "like-user",
                'input': input
            },
            success: function (result) {
                console.log(result);
                if (!result.length) { return; }//防止清空時取得全部用戶
                var datas = JSON.parse(result);
                //結果:
                for (var i = 0; i < datas.length; i++) {
                    var src = upload_avaPic + datas[i]["photo"];
                    setUserUnit(datas[i]["ID"],
                        src,
                        datas[i]["name"]);
                }

            },

            error: function (err) {
                reject(err);
            }
        });
    }
});

//右欄:搜尋商品
$(document).on("input", "#search-p", function () {
    var input = $(this).val();
    if ($("#search-p").val().length) {
        $(".productUnit").each(function () {
            var title = $(this).find(".title-big").html();
            if (title.includes(input)) {
                $(this).removeClass("hidden");
            }
            else {
                $(this).addClass("hidden");
            }
        });
    }
    else {
        //全部顯示
        $(".productUnit").each(function () {
            $(this).removeClass("hidden");
        });
    }
});

//格式化毫秒時間
function getTime_to_format(milisec) {
    var date = new Date(milisec);
    var out =
        date.getFullYear() + "-" +
        (date.getMonth() + 1) + "-" +
        date.getDate() + " " +
        date.getHours() + ":" +
        date.getMinutes() + ":" +
        date.getSeconds();
    return out;

}