let today = new Date();
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();

let months = ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"];
//let monthAndYear = document.getElementById('monthAndYear');

var avalibalDate;
var selectedDate; //目前選擇的字串
checkAvaliableWeek().then(function (res) {
    res = res.split(",");
    //把星期日(7)改成0
    var i = $.inArray("7", res);
    if (i != -1) { res[i] = "0"; }

    avalibalDate = res;

    showCalendar(currentMonth, currentYear, avalibalDate);
});

function showCalendar(month, year, avaliable_week) {
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
                cell.appendChild(cellText);
                row.appendChild(cell);

                //判斷時間是否已經過了:過期就不會有選取效果
                if (year < today.getFullYear()
                    || month < today.getMonth()
                    || (month == today.getMonth() && year == today.getFullYear() && date < today.getDate())
                    || ($.inArray((j).toString(), avaliable_week) == -1)//賣家自訂義非販賣時間
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
    showCalendar(currentMonth, currentYear, avalibalDate);;
}
function next() {
    currentYear = currentMonth === 11 ? currentYear + 1 : currentYear;
    currentMonth = (currentMonth + 1) % 12;
    showCalendar(currentMonth, currentYear, avalibalDate);
}

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
}

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

        //接下來的天數內的也同樣設成選取狀態
    }

});

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