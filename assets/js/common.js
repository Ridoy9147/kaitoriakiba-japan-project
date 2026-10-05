$(function(){

	var clearTime = null;

	get_cart_num();

	setSnsShare(location.href,$("title").html());

	isTagToggle();
	$(".hotword li a").each(function(){
		$(this).click(function(){
			var words = $(this).text();
			$("input[name='q']").val(words);
			$("form[id='fullsearch']").submit();
		});
	});
	$(".quick_side").each(function(){
		$(this).find("li").slice(10).hide();
	});
	$(".btn-more").click(function(){
		$(this).siblings(".quick_side").find("li").slice(10).toggle();
		var labtext = $(this).find("img").attr("alt");
		if(labtext=='開く')
		{
		    $(this).find("img").attr('alt','閉じる');
                    $(this).find("img").attr('src','/home/default/static/images/navigation/more_close.jpg');
		}
		else
		{
		    $(this).find("img").attr('alt','開く');
                    $(this).find("img").attr('src','/home/default/static/images/navigation/more_open.jpg');
		}
	});

	var categoryItemNum = $(".sub-category ul").find("li").length;
	if(categoryItemNum>24){
		$(".sub-category ul").find("li").slice(24).hide();
		$(".sub-category .btn-fl-more").html("more").attr("onclick","categoryToggle(true)");
	}else{
		$(".sub-category .btn-fl-more").css("display","none");
	}

    $(".sub-box").on("click",".collect",function(){
        var that= $(this);
        var gid=that.data("value");
        $.ajax({
            type: "post",
            url: "/home/Goods/collect_goods",
            data: {
                gid: gid,
            },
            success: function (resp) {
                if (resp.status == 1) {
                    layer.msg(resp.msg, {time:1000,icon: 1,shade:0,area : '250px'});
                    that.data("isok") ? that.data("isok", false).removeClass("active") : that.data("isok", true).addClass("active");
                }
                else if(resp.status ==2)
                {
                    layer.msg('「会員専用機能」ログイン後ご利用いただけます', {icon: 0,shade:0,area : '390px'});
                }
            }
        });
    });

});


function categoryToggle(flag){
	$(".sub-category ul").find("li").slice(24).toggle();
	flag?$(".sub-category .btn-fl-more").html("less").attr("onclick","categoryToggle(false)"):$(".sub-category .btn-fl-more").html("more").attr("onclick","categoryToggle(true)");
}

function get_cart_num()
{
	$.ajax({
		type: "GET",
		url: "/home/api/header_cart_sum",
		success: function (data) {
			$('#cart_quantity,#mobile_cart_quantity').html(data);
		}
	});
}

function user_login_or_no()
{
	var uname = getCookie('uname');
	if (uname == '') {
		$('.islogin').remove();
		$('.nologin').show();
	} else {
		$('.nologin').remove();
		$('.islogin').show();
	}
}

function nofind(imgObject)
{
    imgObject.src = "/upload/goods/nophoto.png";
    imgObject.onerror=null;
}

function TagToggle(){
	var sub_category_xs_ul = $(".sub-category-xs ul");
	if(sub_category_xs_ul.hasClass("controls_hide")){
		sub_category_xs_ul.removeClass("controls_hide").nextAll("button.btn").html("閉じる");
	}else{
		sub_category_xs_ul.addClass("controls_hide").nextAll("button.btn").html("開く");
	}
}

function isTagToggle(){
	var sub_category_xs_ul = $(".sub-category-xs ul");
	if(sub_category_xs_ul.height()>150){
		console.log(sub_category_xs_ul.height());
		sub_category_xs_ul.addClass('controls_hide').nextAll("button.btn").html("開く");

	}else{
		sub_category_xs_ul.nextAll("button.btn").removeClass('visible-xs').css("display","none");
	}
}


function shareToggle(){
	var shareBox = $(".share-box");
	if(shareBox.hasClass("controls-show")){
		shareBox.removeClass("controls-show");
		$(".page-share").css("backgroundColor","#F08500");
	}else{
		shareBox.addClass("controls-show");
		$(".page-share").css("backgroundColor","#55B31F");
	}
}


function setSnsShare(shareUrl, title) {
    setTwitterLink(".twitter_share a", shareUrl,title);
    setFacebookLink(".facebook_share a", shareUrl, title);
    setGooglePlusLink(".google_share a", shareUrl, title);
    setHatebuLink(".hatena_share a", shareUrl, title);
    setQQLink(".QQ_share a", shareUrl, title);
    setLineLink(".LINE_share a", shareUrl, title);
    setPocketLink(".pocket_share a", shareUrl);
}


function setTwitterLink(shareSelector, shareUrl, title) {
    $(shareSelector).attr("href", "https://twitter.com/share?shareUrl=" + shareUrl + "&text=" + encodeURIComponent(title));
    setShareEvent(shareSelector, 'Twitter', shareUrl);
}

function setFacebookLink(shareSelector, shareUrl, title) {
    $(shareSelector).attr("href", "https://www.facebook.com/sharer/sharer.php?u=" + shareUrl + "&t=" + encodeURIComponent(title));    
    setShareEvent(shareSelector, 'Facebook', shareUrl);
}


function setHatebuLink(shareSelector, shareUrl, title) {
	$(shareSelector).attr("href", "https://b.hatena.ne.jp/append?"+ shareUrl);
    setShareEvent(shareSelector, 'Hatena Bookmark', shareUrl);
}

function setGooglePlusLink(shareSelector, shareUrl, title) {
    $(shareSelector).attr("href", "https://plus.google.com/share?url=" + shareUrl);
    setShareEvent(shareSelector, 'Google+', shareUrl);
}

function setQQLink(shareSelector, shareUrl, title) {
    $(shareSelector).attr("href", "http://connect.qq.com/widget/shareqq/index.html?url=" + shareUrl + "&showcount=0&desc=&summary="+encodeURIComponent($("meta[name='description']").attr("content").slice(0,20))+"...&title="+encodeURIComponent(title)+"&site=iphonekaitori&pics=");
    setShareEvent(shareSelector, 'QQ', shareUrl);
}

function setLineLink(shareSelector, shareUrl, title) {
	$(shareSelector).attr("href", "https://lineit.line.me/share/ui?url=" + shareUrl+"&text=" + encodeURIComponent(title) + " " + shareUrl);
   // $(shareSelector).attr("href", "http://line.me/R/msg/text/?" + encodeURIComponent(description + " " + shareUrl));
    setShareEvent(shareSelector, 'LINE', shareUrl);
}
function setPocketLink(shareSelector, shareUrl, title){
	$(shareSelector).attr("href","https://getpocket.com/edit?url=" + shareUrl+"&title=" + encodeURIComponent(title));
	setShareEvent(shareSelector, 'Pocket', shareUrl);
}

function setShareEvent(selector, snsName, shareUrl) {
    $(selector).on('click', function(e){

        var current = this;

		ga('send', 'social', snsName, 'share', shareUrl, {
			'nonInteraction': 1
		});

        window.open(current.href, '_blank', 'width=740, height=600, menubar=no, toolbar=no, scrollbars=yes');
        e.preventDefault();
    }); 
}

function copy(){
	var copyText=$(".copy_text");
	copyText.html($("title").html()+" 链接地址："+location.href);
	copyText.select(); // 选择对象
	document.execCommand("Copy"); // 执行浏览器复制命令


	if($(".share-info").hasClass("controls-show")){

	}else{
		$(".share-info").addClass("controls-show");
		var clearTime = setTimeout(function(){
			$(".share-info").removeClass("controls-show");
		},3000);
	}
	
}

function FontSize(that, num){
	$(that).addClass("active").siblings().removeClass("active");
	var article = document.getElementById("article");
	switch(num){
		case -1: 
			article.className="wz-article font-xs";
			break;
		case 0: 
			article.className="wz-article";
			break;
		case 1: 
			article.className="wz-article font-lg";	
			break;
	}
}

function toHalfWidth(elm) {
	return elm.value.replace(/[Ａ-Ｚａ-ｚ０-９！-～]/g, function(s){
		return String.fromCharCode(s.charCodeAt(0)-0xFEE0);
	});
}

function katakana(elm) {
	return elm.value.replace(/[０-９]/g, function (s) {
			return String.fromCharCode(s.charCodeAt(0) - 65248);
		})
		.replace(/ﾞ/g, '゛')
		.replace(/ﾟ/g, '゜')
		.replace(/　/g, ' ')
		.replace(/[‐－―]/g, '-')
		.replace(/（/g, '(')
		.replace(/）/g, ')');
		//.replace(/[^ァ-ヴーヽヾヵヶヷヸヹヺ゛゜ \d\-]/g, '');
}