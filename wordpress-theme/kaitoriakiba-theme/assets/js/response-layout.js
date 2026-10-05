function response_layout(el){
	var sum = null;
	var obj= {};
	var index = 0;
	el = el || ''; 
	console.log(el+".sub-box .sub-pro-box");
	var sub_pro_box = $(el+".sub-box .sub-pro-box");
	var proList = sub_pro_box.find(".pro_list");
	var col_item = sub_pro_box.children("div.col-xs-12");

	var sum = Math.round(sub_pro_box.width()/col_item.outerWidth(true));
	obj.integer = Math.floor(proList.length/sum);
	obj.remainder = proList.length%sum;
	for(var a= 0; a<proList.length; a++){
		proList.parent().eq(a).children(".pro_list").height("auto").find(".sub-pro").height("auto");
	}
	for(var x = 0; x< obj.integer; x++){
		var cacheX =null;
		for(var y = x*sum; y<(x+1)*sum;  y++){
			if(cacheX < proList.parent().eq(y).children(".pro_list").find(".sub-pro").height()){
				cacheX = proList.parent().eq(y).children(".pro_list").find(".sub-pro").height();
			}		
		}
		for(var z = x*sum; z<(x+1)*sum; z++){
			proList.parent().eq(z).find(".sub-pro").height(cacheX+"px");
		}
	}

	for(var ox = 0; ox< obj.integer; ox++){
		var cacheY = null;
		for(var oy = ox*sum; oy<(ox+1)*sum;  oy++){
			if(cacheY < proList.parent().eq(oy).children(".pro_list").height()){
				cacheY = proList.parent().eq(oy).children(".pro_list").height();
			}	
		}
		for(var oz = ox*sum; oz<(ox+1)*sum; oz++){
			proList.parent().eq(oz).children(".pro_list").height(cacheY+"px");
			console.log(cacheY);
		}
		console.log(oy,obj.integer);
		console.log('-------------');
	}


	var cacheI = null;
	var cacheM = null;
	
	for(var oi = 0; oi< obj.remainder; oi++){
		if(cacheI < proList.parent().eq(obj.integer*sum+oi).children(".pro_list").find(".sub-pro").height()){
			cacheI = proList.parent().eq(obj.integer*sum+oi).children(".pro_list").find(".sub-pro").height();
		}		
	}
	for(var oj =0 ; oj< obj.remainder; oj++){
		proList.parent().eq(obj.integer*sum+oj).find(".sub-pro").height(cacheI+"px");
	}

	for(var om = 0; om< obj.remainder; om++){
		if(cacheM < proList.parent().eq(obj.integer*sum+om).children(".pro_list").height()){
			cacheM = proList.parent().eq(obj.integer*sum+om).children(".pro_list").height();
		}		
	}
	for(var on =0 ; on< obj.remainder; on++){
		proList.parent().eq(obj.integer*sum+on).children(".pro_list").height(cacheM+"px");
	}	

}

    function js_agnHeight(){

    	var result = window.matchMedia('(max-width: 768px)');

	    var js_agn_lft = $("#js_agn_lft");
	   	var js_agn_rgt = $("#js_agn_rgt");
	   	var js_lft_tab = $("#js_lft_tab");
	   	var js_rgt_tab = $("#js_rgt_tab");

	   	js_lft_tab.css("height","auto");
	   	js_rgt_tab.css("height","auto");

	    if(!result.matches){

		   	if(Math.ceil(js_agn_lft.outerHeight())<Math.ceil(js_agn_rgt.outerHeight())){
		   		var c = Math.ceil(js_agn_rgt.outerHeight()) - Math.ceil(js_agn_lft.outerHeight());
		   	 	var cs = js_lft_tab.outerHeight()+c;
		   		js_lft_tab.outerHeight(cs);
		   	}else if(Math.ceil(js_agn_rgt.outerHeight())< Math.ceil(js_agn_lft.outerHeight())){
		   		var s = Math.ceil(js_agn_lft.outerHeight()) - Math.ceil(js_agn_rgt.outerHeight());
		        var ss = Math.ceil(js_rgt_tab.outerHeight())+s;
		   		js_rgt_tab.outerHeight(ss);
		   	}
	    }
   	}
   	js_agnHeight();


	$(window).resize(function(){
		response_layout("#layout_x");
		response_layout("#layout_y");
	   	js_agnHeight();
	});






// window.onload=response_layout;
// function response_layout(){
// 	var sum = null;
// 	var obj= {};
// 	var index = 0;
// 	var sub_pro_box = $(".sub-box .sub-pro-box");
// 	var proList = $(".sub-box .sub-pro-box .pro_list");
// 	var col_item = $(".sub-box .sub-pro-box>div.col-lg-3.col-md-3.col-sm-4.col-xs-12");
// 	var sum = Math.round(sub_pro_box.width()/col_item.outerWidth(true));
// 	obj.integer = Math.floor(proList.length/sum);
// 	obj.remainder = proList.length%sum;
// 	for(var a= 0; a<proList.length; a++){
// 		proList.parent().eq(a).children(".pro_list").height("auto");
// 	}
// 	for(var x = 0; x< obj.integer; x++){
// 		var cache =null;
// 		for(var y = x*sum; y<(x+1)*sum;  y++){
// 			if(cache < proList.parent().eq(y).children(".pro_list").height()){
// 				cache = proList.parent().eq(y).children(".pro_list").height();
// 			}
// 		}
// 		for(var z = x*sum; z<(x+1)*sum; z++){
// 			proList.parent().eq(z).children(".pro_list").height(cache+"px");
// 			console.log(cache);
// 		}
// 		console.log(x,obj.integer);
// 		console.log('-------------');
// 	}
// }
// $(window).resize(response_layout);