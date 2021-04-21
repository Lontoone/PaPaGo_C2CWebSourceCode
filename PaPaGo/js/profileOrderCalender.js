//在Profile裡面顯示買賣家未來行程的日曆

let today = new Date();
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();

let months = ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"];
//let monthAndYear = document.getElementById('monthAndYear');

//showCalendar(currentMonth, currentYear);
//getOrder_calender_block(currentMonth);
var _dataType = "currentTrips";
function setBuyerCalender() {
    _dataType = "currentTrips";
    showCalendar(currentMonth, currentYear);
    getOrder_calender_block(currentMonth, _dataType);
}
function setSellerCalender() {
    _dataType = "currentOrders";
    showCalendar(currentMonth, currentYear);
    getOrder_calender_block(currentMonth, _dataType);
}

function showCalendar(month, year) {
    let firstDay = new Date(year, month).getDay();
    let daysInMonth = 32 - new Date(year, month, 32).getDate();
    //console.log(daysInMonth);

    let tbl = document.getElementById("calendarBody");
    tbl.innerHTML = "";
    //monthAndYear.innerHTML = year + " " + months[month];
    $("#monthAndYear").html(year + " " + months[month]);

    let date = 1;
    for (let i = 0; i < 6; i++) {
        let row = document.createElement("tr");

        for (let j = 0; j < 7; j++) {
            if (i === 0 && j < firstDay) {
                let cell = document.createElement("td");
                let cellText = document.createTextNode("");
                cell.appendChild(cellText);
                row.appendChild(cell);

            } else if (date > daysInMonth) {
                break;
            } else {
                let cell = document.createElement("td");
                let cellText = document.createTextNode(date);
                $(cell).attr("id", "c" + date);
                cell.appendChild(cellText);
                row.appendChild(cell);

                //判斷時間是否已經過了:過期就不會有選取效果
                if (year < today.getFullYear()
                    || month < today.getMonth()
                    || (month == today.getMonth() && year == today.getFullYear() && date < today.getDate())
                ) {
                    cell.classList.add("notClickableDate");
                }
                //正常可選日期
                else {
                    //檢查之前有沒有點選過日期
                    if (CheckHasSelectedDate(year, month, date)) {
                        cell.classList.add("selectedDate");
                    }
                }

                date++;
            }
        }
        tbl.appendChild(row);
    }
}

function previous() {
    currentYear = (currentMonth === 0) ? currentYear - 1 : currentYear;
    currentMonth = (currentMonth === 0) ? 11 : currentMonth - 1;
    showCalendar(currentMonth, currentYear);
    getOrder_calender_block(currentMonth, _dataType);

}
function next() {
    currentYear = currentMonth === 11 ? currentYear + 1 : currentYear;
    currentMonth = (currentMonth + 1) % 12;
    showCalendar(currentMonth, currentYear);
    getOrder_calender_block(currentMonth, _dataType);
}

//取得日歷有購買的天數
function getOrder_calender_block(now_month, _dataTpye) {
    //$("#currentTrips").find(".verticalWhiteBoard .calender-info").empty();
    $("#" + _dataType).find(".verticalWhiteBoard .calender-info").empty();
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/getProfileData.php",
            type: "post",
            data: {
                'dataType': _dataTpye,
                'month_post': (now_month + 1)
            },
            success: function (result) {
                var datas = JSON.parse(result);
                console.log(datas);

                //右欄位資訊:
                //var container = $("#currentTrips").find(".verticalWhiteBoard .calender-info");
                var container = $("#" + _dataType).find(".verticalWhiteBoard .calender-info");
                console.log(container);
                for (var i = 0; i < datas.length; i++) {

                    var unit = $($("#currentTrips-eachorder-template").html()).clone();
                    var href = "./productView.php?id=" + datas[i]["pid"] + "&seller=" + datas[i]["sellerID"];
                    //$(unit).attr("pid", data[0]);
                    $(unit).attr("id", datas[i]["recordNO"]);
                    $(unit).find(".productTitle").attr("href", href);
                    $(unit).find(".productTitle").html(datas[i]["title"]);
                    $(unit).find(".orderID").html(datas[i]["recordNO"]);
                    $(unit).find(".seller").attr("id", datas[i]["sellerID"]);
                    $(unit).find(".seller").html(datas[i]["sellerName"]);
                    $(unit).find(".people").html(datas[i]["people"]);

                    var playDate = datas[i]["startDate"] + " ~ " + datas[i]["endDate"];

                    $(unit).find(".playDate").html(playDate);
                    $(unit).find(".price").html(datas[i]["price"]);
                    var img_src = "../upload/productThumbIMG/" + datas[i]["photo"];
                    $(unit).find(".thumb").attr("src", img_src);


                    var start_day = new Date(datas[i]["startDate"]);
                    var end_day = new Date(datas[i]["endDate"]);
                    //標題:
                    var title_t = +(start_day.getMonth() + 1) + "/" + start_day.getDate() + " - " +
                        (end_day.getMonth() + 1) + "/" + end_day.getDate();
                    var title = " <p class='board-title'>" + title_t + "</p>";
                    $(container).append(title);
                    $(container).append(unit);

                    //左欄[日歷]:

                    //天數
                    const oneDay = 24 * 60 * 60 * 1000;
                    var diffDays = Math.round(Math.abs((start_day - end_day) / oneDay));
                    //從開始-結束，設定class
                    for (var j = 0; j < diffDays; j++) {
                        if (start_day.getMonth() == currentMonth) {
                            var _d = start_day.getDate() + j
                            var cell_id = "c" + _d;
                            $("#" + cell_id).addClass("selectedDate");
                        }
                        if (end_day.getMonth() == currentMonth) {
                            var _d = end_day.getDate() + j
                            var cell_id = "c" + _d;
                            $("#" + cell_id).addClass("selectedDate");
                        }
                    }

                }

                resolve(result);
                return result;
            },

            error: function (err) {
                reject(err);
            }
        });
    })
}

/*
function checkAvaliableWeek() {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/common.php",
            type: "post",
            data: {
                'func_name': 'getData',
                'tb_name_post': 'product',
                'row_name_post': 'weekSelect',
                'where_post': 'ID',
                'key_post': GetURLParameter('id'),
                'doEcho': true,
            },
            success: function (result) {
                console.log(result);
                resolve(result);
                return result;
            },

            error: function (err) {
                reject(err);
            }
        });
    })
}*/

/*
//選擇日期
$(document).on('click', '#calendarBody tr td', function () {

    console.log($(this));
    if (!$(this).hasClass("notClickableDate")) {
        //清除上一個選取的
        $(".selectedDate").each(function () {
            $(this).removeClass("selectedDate");
        });

        //設為選取的
        $(this).addClass("selectedDate");

        selectedDate = currentYear + "-" + (currentMonth + 1) + "-" + $(this).html();

    }

});
*/

//檢查有沒有選擇的日期
function CheckHasSelectedDate(year, month, date) {

    if (typeof selectedDate === "undefined") { return false; }

    var dates = selectedDate.split("-");
    if (year == dates[0] &&
        (month + 1) == dates[1] &&
        date == dates[2]
    ) {
        return true;
    }
    else {
        return false;
    }
}