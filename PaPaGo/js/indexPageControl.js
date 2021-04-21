//INDEX 首頁控制:

//搜尋最新的
$("#newest").click(function () {
    setSortURL("uploadDate DESC");
});
//搜尋最熱的
$("#hotest").click(function () {
    setSortURL("hotest"); //TODO 
});
//搜尋最便宜的
$("#cheapest").click(function () {
    setSortURL("price");

});
function setSortURL(orderby) {
    current_page = 0;
    var url = new URL(location.href);
    console.log(location.href.includes("?"));

    if (!location.href.includes("?")) {
        location.href = url + "?order=" + orderby;

        console.log(url + "?order=" + orderby);
    }
    else {
        if (typeof GetURLParameter("order") === "undefined") {
            location.href = $(location).attr("href") + "&order=" + orderby;
            console.log($(location).attr("href") + "&order=" + orderby);
        }
        else {
            location.href = replaceUrlParam(location.href, "order", orderby)
        }
    }
}

//按下header搜尋:
$("#searchbar-btn").click(function () {
    var val = $("#searchbar-input").val();
    location.href = "searchResult.php?search=" + val;
    console.log(location.href);
});