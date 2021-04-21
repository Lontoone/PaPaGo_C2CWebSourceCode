
//新增產品:提交時
//$(document).$('#newProductForm').submit(function () {
    $(document).on('submit', '#newProductForm', function () {
        var canSubmit = false;
    
        //preventDefault();
        console.log("up");
        //return false;
    
    
        //表單輸入資料
        var title = $('#input-producttitle').val();
        var country = $('#country-select').val();
        var county = $('#county-select').val();
        var duration = $('#input-duration').val();
        var productInfo = $('#input-productinfo').val();
        var bill_total = $('#total-number').html().split("/")[0]; //總價格
        var declear = $('#input-declear').val();
        var thumbnail = $("#product_thumb_pic_upload").val();
    
        var agenda_dayTitle = new Array(duration);
        var agenda_dayActivity = new Array(); //天數,標題,內容
    
        //合併資料:
        var agenda_dayTitle_wrap = ""; //格式: 1|內容@2|內容
        var agenda_dayActivity_wrap = ""; //格式: 1|標題|內容@2|標題|內容
        var bill_wrap = ""; //格式: 天數|內容|價格|數量|@
        var week_select = ""; //開放的星期 格式:1,2,3...
    
        //TODO:之後要檢查圖片
    
        //TODO:檢查安全字元 : |@
        //檢查基本完整性
        if (title.length && country.length && county.length
            && duration.length && productInfo.length) {//&& thumbnail) {
    
            canSubmit = true;
            //檢查每日行程大標題
            $('.agenda .productTitle').each(function (index) {
    
                if (!$(this).val().length) {
                    alert("請填寫日程標題");
                    canSubmit = false;
                    return false;
                }
                agenda_dayTitle[index] = $(this).val();
                agenda_dayTitle_wrap += (index + 1) + "|" + $(this).val() + "@";
    
                //每日的小項目:
                $(this).parent().find('.wrap .agenda-content').each(function (i) {
                    var _parent = $(this);
                    console.log(_parent.find('.agenda-itme').val());
                    var act = {
                        day: index + 1,
                        title: _parent.find('.agenda-itme').val(),
                        content: _parent.find('.subText').val()
                    }
                    //沒資料就跳過
                    if (act.title == "") {
                        return; //continue
                    }
                    agenda_dayActivity_wrap += (i + 1) + "|" + act.day + "|" + act.title + "|" + act.content + "@"
                    agenda_dayActivity.push(act);
                });
            });
    
            //收費項目
            console.log($('.price-table tbody tr'));
            $('.price-table tbody tr').each(function () {
                var day = $(this).find('.day-select').val();
                var content = $(this).find('.billcontent').val();
                var price = $(this).find('.price-input').val();
                var unit = $(this).find('.unit-input').val();
                if (content != "" && price != 0 && unit != 0) {
                    bill_wrap += day + "|" + content + "|" + price + "|" + unit + "@";
                }
            });
    
            //開放星期
            $("#week-select span").each(function (index, element) {
                if ($(element).hasClass("week-selected")) {
                    week_select += (index + 1) + ",";
                }
            });
            week_select = week_select.slice(0, -1); //移除最後一個多的','
            console.log("開放: " + week_select);
        }
    
        if (canSubmit) {
            //Ajax上傳
            alert("上傳成功");
            var formdata = new FormData(document.querySelector('#newProductForm'));
            //確定有縮圖上傳
            if(hasChange_thumbnail){
                formdata.append("hasChange_thumbnail",true);
            }
            //合併小物件資料:
            //包裝資料
            formdata.append("bill_total", bill_total);
            formdata.append("agenda_dayTitle_wrap", agenda_dayTitle_wrap);
            formdata.append("agenda_dayActivity_wrap", agenda_dayActivity_wrap);
            formdata.append("bill_content", bill_wrap);
            formdata.append("week_select", week_select);
            formdata.append("deleted_picID",deleted_picID_list); //要刪除的圖片ID
    
            //圖片: 新增刪除圖片功能，所以要跑回圈抓取
            let data = [];
            let fileArray = Array.from(document.getElementById('product_pics_upload').files);//TEST
            fileArray.forEach(item => {
                $(".gallery-photobox img").each(function (i) {
    
                    if ($(this).parent().attr("id") == item.name) {
                        data.push(item);
                    }
                    console.log($(this).parent().attr("id") + "vs" + item);
                });
            });
            console.log(data);
            /* 原本圖片上傳方法:
            var p_c = document.getElementById('product_pics_upload').files.length;
            for (var x = 0; x < p_c; x++) {
                formdata.append("product_pics_form[]", document.getElementById('product_pics_upload').files[x]);
            }
            */
            for (var x = 0; x < data.length; x++) {
                formdata.append("product_pics_form[]", data[x]);
            }
    
            console.log(agenda_dayTitle_wrap);
    
            //DEBUG用vv
            for (var pair of formdata.entries()) {
                console.log(pair[0] + ', ' + pair[1]);
            }
            //return false; //DEBUG開啟:不會提交 
            //DEBUG^^
    
    
            //判斷是新建產品還是編輯產品
            var pid = GetURLParameter("pid");
            console.log(pid);
            if (typeof pid !== "undefined") {
                //呼叫編輯方法
                //do_edit_submit(pid, formdata);
                //停止新建
                //return false;
                formdata.append("pid",pid);
                formdata.append("action","edit");
            }
    
            //新建:
            $.ajax({
                url: '../PHP/createNewProduct.php',
                type: 'POST',
                data: formdata,
                async: false,
                cache: false,
                contentType: false,
                enctype: 'multipart/form-data',
                processData: false,
                success: function (response) {
                    console.log(response);
                    //alert(response);
                }
            });
            
            window.history.back();
            return false; //不會提交*/
        }
        else {
            alert("上傳失敗：資料不完整");
            return false; //不會提交
        }
    
    });
    
    
    //禁止form一按enter就submit
    $(document).on("keydown", ":input:not(textarea)", function (event) {
        return event.key != "Enter";
    });
    
    
    /******************************************* */
    var countrise = ["台灣", "日本"];
    
    var county_tw = ["台北","基隆","新竹","苗栗","彰化","南投","雲林","宜蘭","台東","台中", "高雄", "屏東", "花蓮","新北","桃園","台南","嘉義","澎湖","綠島"];
    var county_jp = ["東京", "大阪", "北海道"];
    
    var hasChange_thumbnail=false;//是否有上傳主縮圖
    
    //國家選項更變->選項也變:
    //$("#country-select").change(function () {
    $(document).on("change", "#country-select", function () {
        checkCountry(this.value);
    });
    function checkCountry(country) {
        switch (country) {
            case "tw":
                refreshSelect("#county-select", county_tw);
                break;
            case "jp":
                refreshSelect("#county-select", county_jp);
                break;
        }
    
    }
    
    function refreshSelect(select, newData) {
        $(select + " option").remove();
        newData.forEach(element => {
            $(select).append($("<option></option>").attr("value", element).text(element));
        });
    }
    /******************************************* */
    
    /*vvvvvvvvvvvvvvvvvvvvv行程規劃vvvvvvvvvvvvvvvvvvvvv*/
    //日程數量改變=>自動生成對應數量的行程規劃物件
    //$("#input-duration").change(function () {
    $(document).on("change", "#input-duration", function () {
        var days = $("#input-duration").val();
        var agendaNumber = $(".agenda").length;
    
        if (days >= 1) {
            console.log(days + " " + agendaNumber);
            console.log(agendaNumber > days);
            if (agendaNumber > days) {
                for (var i = days; i <= agendaNumber; i++) {
                    if (i != days) {
                        $("#agenda-d" + i).remove();
                        console.log("remove" + i);
                    }
                }
            }
            else {
                for (var i = agendaNumber; i < days; i++) {
                    console.log(i);
                    //複製模板
                    var temp = document.getElementById("agenda-template");
                    var content = temp.content.cloneNode(true);
                    //改模板資料
                    $(content).children().attr('id', 'agenda-d' + (i + 1));
                    $(content).children().find(".day").html("Day " + (i + 1));
                    $("#agenda-wrapper").append(content);
                }
            }
        }
        updateDaySelect_opt_num(days);
    });
    
    //縮圖圖片上傳時候，紀錄更變
    $(document).on("change","#product_thumb_pic_upload",function(){
        hasChange_thumbnail=true;
        console.log(hasChange_thumbnail);
    });
    
    
    //天數改變=>自動產生對應行程
    function duration_input_change(days) {
        var agendaNumber = $(".agenda").length;
    
        if (days >= 1) {
            console.log(days + " " + agendaNumber);
            console.log(agendaNumber > days);
            if (agendaNumber > days) {
                for (var i = days; i <= agendaNumber; i++) {
                    if (i != days) {
                        $("#agenda-d" + i).remove();
                        console.log("remove" + i);
                    }
                }
            }
            else {
                for (var i = agendaNumber; i < days; i++) {
                    console.log(i);
                    //複製模板
                    var temp = document.getElementById("agenda-template");
                    var content = temp.content.cloneNode(true);
                    //改模板資料
                    $(content).children().attr('id', 'agenda-d' + (i + 1));
                    $(content).children().find(".day").html("Day " + (i + 1));
                    $("#agenda-wrapper").append(content);
                }
            }
        }
    }
    
    //新增小項目
    $(document).on('click', '.addevent-btn', function () {
        var agenda = $(this).prev();
        /*
        var temp = document.getElementById("agenda-content-template");
        var content = temp.content.cloneNode(true);
        agenda.children(".wrap").append(content);*/
        addDayActivity(agenda);
        console.log($(this).prev().attr('id'));
    });
    //添加每日行程
    function addDayActivity(prev_agenda) {
        /*
        var temp = document.getElementById("agenda-content-template");
        var content = temp.content.cloneNode(true);*/
        var content = $($("#agenda-content-template").html()).clone();
        prev_agenda.find(".wrap").append(content);
        return content;
    }
    
    
    /*^^^^^^^^^^^^^^^^^^^^^行程規劃^^^^^^^^^^^^^^^^^^^^^*/
    
    /*vvvvvvvvvvvvvvvvvvvvv估價單vvvvvvvvvvvvvvvvvvvvv*/
    //估價單: (第一格)輸入時自動產生下一格
    $(document).on('change', '#priceing .input-none', function () {
    
        pricingTable_addRow($(this).parent().parent());
    
        //計算總金額
        var sum = 0;
        $('.price-table tbody tr').each(function () {
            var price = $('.price-input', this).val();
            var unit = $('.unit-input', this).val();
            if (price > 0 && unit > 0) {
                sum += price * unit;
            }
            $(".price-total").children('#total-number').html(sum + '/人');
        });
    
    });
    
    //檢查這一行是否欄位都有內容
    function hasContent(row, classname) {
        var hasContent = false;
        row.find(classname).each(function (e) {
            if ($(this).val().length) {
                hasContent = true;
                return false; //break
            }
            else { hasContent = false; }
        });
        return hasContent;
    }
    
    function pricingTable_addRow(currentRow) {
    
        //自動增加一格
        var _hasContent = hasContent(currentRow, '.input-none');
        if (_hasContent && $(currentRow).is(':first-child')) {
            var table = $('.price-table tbody');
            /*
            var temp = document.getElementById("pricing-tr-input-template");
            var content = temp.content.cloneNode(true);*/
            var content = $($("#pricing-tr-input-template").html()).clone();
    
            //設定天數選項數量
            var _select = $(content).find('.day-select');
            updateDaySelect_opt_num_s(_select);
    
            //table.append(content);
            table.prepend(content);
    
        }
        /*
        //刪除欄位全空的
        if (!_hasContent) {
          currentRow.remove();
        }*/
    }
    
    
    //照日程自動排序 //0=全程
    $(document).on('change', '.day-select', function () {
        //取的目前輸入的列
        var rowCount = $('.price-table tbody tr').length;
        //排序
        for (var i = 0; i < rowCount - 1; i++) {
    
            //刪掉空白欄位
            if (i != 0 && !hasContent($('.price-table tbody tr').eq(i), '.input-none')) {
                $('.price-table tbody tr').eq(i).remove();
                continue;
            }
    
            for (var j = 0; j < rowCount - i - 1; j++) {
    
                var current_s = $('.price-table tbody tr').eq(j).find('.day-select');
                var current_s_row = current_s.parent().parent();
    
                var _s = $('.price-table tbody tr').eq(j + 1).find('.day-select');
                var _s_row = _s.parent().parent();
    
                //console.log(j + " " + $(_s).find(':selected').val() + " vs " + $(current_s).find(':selected').val());
    
                if ($(_s).find(':selected').val() > $(current_s).find(':selected').val()) {
                    $(_s_row).insertAfter(current_s_row);
                }
                else {
                    $(current_s_row).insertAfter(_s_row);
                }
            }
    
    
        }
        //補上待輸入欄位
        pricingTable_addRow($('.price-table tbody tr').eq(0));
    
    });
    
    //更新日程天數選項數量
    function updateDaySelect_opt_num(maxNum) {
        var selects = $(document).find('.day-select');
        console.log(selects);
        $(selects).each(function (index, element) {
    
            var selected_opts = $(element).find('option');
            $(selected_opts).each(function (_index, _element) {
                //把多的選項刪除
                if (_index > maxNum) {
                    console.log("remove OPT" + _element);
                    _element.remove();
                }
                //增加不足的
                else if (_index >= selected_opts.length - 1 && _index < maxNum) {
                    var new_opt = "<option value=" + (_index + 1) + ">" + (_index + 1) + "</option>";
                    /*$(new_opt).attr('value',_index);
                    $(new_opt).html(_index);*/
                    $(_element).parent().append(new_opt);
                }
            });
        });
    }
    
    //更改特定的select
    function updateDaySelect_opt_num_s(select) {
        var maxNum = $("#input-duration").val();
    
        var selected_opts = $(select).find('option');
        $(selected_opts).each(function (_index, _element) {
            //把多的選項刪除
            if (_index > maxNum) {
                console.log("remove OPT" + _element);
                _element.remove();
            }
            //增加不足的
            else if (_index >= selected_opts.length - 1 && _index < maxNum) {
                var new_opt = "<option value=" + (_index + 1) + ">" + (_index + 1) + "</option>";
                /*$(new_opt).attr('value',_index);
                $(new_opt).html(_index);*/
                $(_element).parent().append(new_opt);
            }
        });
    }
    
    /*^^^^^^^^^^^^^^^^^^^^^估價單^^^^^^^^^^^^^^^^^^^^^*/
    
    //開放星期:
    $(document).on("click", ".week-selected", function () {
        console.log("so");
        $(this).removeClass("week-selected");
        $(this).addClass("week-unselected");
    });
    $(document).on("click", ".week-unselected", function () {
        console.log("hi");
        $(this).removeClass("week-unselected");
        $(this).addClass("week-selected");
    });
    
    //vvvvvvvvvvvvvvvvvvvvvvvvv圖片廊:vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
    var deleted_picID_list = []; //刪除的圖片id
    //刪除圖片的叉叉控制
    $(document).on("mouseover", ".gallery-photobox", function () {
        if ($(this).find("span").hasClass("hidden")) {
            $(this).find("span").removeClass("hidden");
        }
    });
    $(document).on("mouseleave", ".gallery-photobox", function () {
        if (!$(this).find("span").hasClass("hidden")) {
            $(this).find("span").addClass("hidden");
        }
    });
    
    //按下叉叉時不會以為是要上傳圖片  //用event.preventDefault();取代
    /*
    $(document).on("mouseover", ".gallery-photobox-remove", function () {
        $("#gallery_upload_lable").attr("for", "");
    });
    $(document).on("mouseleave", ".gallery-photobox-remove", function () {
        $("#gallery_upload_lable").attr("for", "product_pics_upload");
    });*/
    
    //將上傳分開成2個步驟: (取代readGalleryPhoto方法)
    $(function () {
        //$('#product_pics_upload').change(function () {
        $(document).on("change", "#product_pics_upload", function () {
            console.log('change start');
    
            $.each(this.files, function (a, b) {
                readGalleryPhoto_single(a);
            })
        });
    
    });
    
    function readGalleryPhoto_single(a) {
        console.log('show ' + a)
        var f = document.getElementById('product_pics_upload').files[a];
    
        var freader = new FileReader();
        freader.onload = function (e) {
    
            console.log('set img' + a);
    
            var picbox = $($("#gallery-photobox-template").html()).clone();
    
            $(picbox).find("img").attr("src", e.target.result);
            $(picbox).attr("id", f.name);
    
            $('.gallery').append(picbox);
    
        }
        freader.readAsDataURL(f);
        $(".gallery").find(".defult").addClass("hidden");
    }
    
    //一次產生上傳的圖片預覽(//TODO:報廢)
    function readGalleryPhoto(input, insertElement) {
        console.log($(input.files));
        if (input.files) {
    
            //TODO:限制上傳數量 和 大小?
            //隱藏預設圖片
            $(insertElement).find(".defult").addClass("hidden");
            var filename = [];
            var box = [];
    
            var fileAmout = input.files.length;
            for (var i = 0; i < fileAmout; i++) {
    
                filename.push(input.files[i].name);
                console.log(filename);
    
                var reader = new FileReader();
                reader.onload = function (event) {
    
                    //創建圖片物件
                    var picbox = $($("#gallery-photobox-template").html()).clone();
                    box.push(picbox);
                    console.log(box);
                    //console.log(i+" "+input.files[0].name);
                    //console.log(i+" "+event.target.value);
                    $(picbox).attr("id", "pic-" + input.files[0].name);
    
                    $(picbox).find("img").attr("src", event.target.result);
                    $(insertElement).append(picbox);
                    /*
                    var pic = document.createElement("img");
                    pic.src = event.target.result;
                    pic.classList.add("max-200px");
                    $(insertElement).append(pic);
                   */
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    }
    
    //點擊叉叉=>刪除照片
    $(document).on("click", ".gallery-photobox-remove", function (event) {
        event.preventDefault();
        var p_id = $(this).parent().attr("id");
        //若開頭=pim 代表要刪除舊圖
        if (p_id.substr(0, 3) == "pim") {
            deleted_picID_list.push(p_id);
            console.log(deleted_picID_list);
        }
    
        console.log("正在刪除:" + $(this).parent().attr("id"));
    
        $(this).parent().remove();
    
        //若目前沒圖片=>打開預設的:
        if ($(".gallery").children().length <= 1) {
            $(".gallery").find(".defult").removeClass("hidden");
        }
    });
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^圖片廊:^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    
    //可拉動圖片調整順序:
    /*
    $(".gallery").sortable({
        axis: "x",
        placeholder: 'sort-placeholder',
        forcePlaceholderSize: true,
        revert: true,
        scroll: false,
        cursor: "move"
    });
    $(".gallery").disableSelection();
    
    $(function () {
        $('.gallery').sortable({
            start: function (event, ui) {
                var start_pos = ui.item.index();
                ui.item.data('start_pos', start_pos);
            },
            change: function (event, ui) {
                var start_pos = ui.item.data('start_pos');
                var index = ui.placeholder.index();
                $(ui.item).attr("id","pic"+index);
                console.log(ui.item);
            },
            update: function (event, ui) {
                //$('#sortable li').removeClass('highlights');
            }
        });
    });
    */