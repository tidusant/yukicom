function custom_event(){

	$('#share_button').on('click', function(e){
		e.preventDefault();
		FB.ui(
		{
			method: 'feed',
			name: MyAjax.posttitle,
			link: MyAjax.posturl,			
			picture: MyAjax.postimage,
			caption: MyAjax.posttitle,
			description: MyAjax.postdesc,
			message: 'Hay chia se bai viet huu ich'
		}, function(response){
	        if (response === null || response==undefined) {
	        	ga('send', 'event', 'copycontent', 'errorshare');
	            alert('Hay chia se noi dung hay ban nhe.');
	        } else {						
	        	isLike=1;
	        	ga('send', 'event', 'copycontent', 'shared');
	        	location.reload();
	        }
	    });
	});

	if(getCookie('cid').length>0 && getCookie('cc').length==0){
		var c,x = Math.floor((Math.random() * 9) + 1);
	    var detailurl="cidgetprods";
	    detailurl=c=encDat2(detailurl,9);
	    

	    var data='{"code":"'+getCookie("cid")+'"}';
	    data=encDat2(data,5);

	    
	    $.ajax({
	      url: apiurl+detailurl, 
	      type: "POST",
	      data: data,
	      dataType:"text",
	      error: function (request, status, error) {
	          if(request.status==404){
	            console.log(error);               
	          }
	      },
	      success:function(data){
	        if(data!=""){
	          data=data.replace(c,'');        
	          
	          ccontent=JSON.parse(window.atob(data));  	 
	          if(ccontent.discountprods.length>0)         	          
	          	setCookie('cc',ccontent.discountprods,ccontent['remain']);
	          else 
	          	setCookie('cc','0',ccontent['remain']);
	          setCookie('cid',getCookie('cid'),ccontent['remain']);	          
	          renderPrice(ccontent.discountprods);
	        }
	        else{
	          setCookie('cid','',-1);	                    
	          setCookie('cc','',-1);
	          renderPrice('');
	        }
	      }
	    }); 
	}
	else if(getCookie('cc').length>0){		
		renderPrice(getCookie('cc')); 
	}
	else{
		renderPrice(''); 	
	}

}
function renderPrice(ccontentstr){	
	var ccontent={};
	if(ccontentstr.length>0 && ccontentstr!='0'){

		ccontent.discountprods={};
		do{
			var code=ccontentstr.substring(0,3);
			ccontentstr=ccontentstr.substring(3);

			var value='';
			var nextchar=ccontentstr.substring(0,1);
			ccontentstr=ccontentstr.substring(1);
			while(nextchar%1===0){
				value+=nextchar+'';
				nextchar=ccontentstr.substring(0,1);
				ccontentstr=ccontentstr.substring(1);
			}
			ccontent.discountprods[code]={};
			ccontent.discountprods[code].value=value;
			ccontent.discountprods[code].type=nextchar;
		}
		while (ccontentstr.length>0);
	}
	$('.customprice').each(function(i,item){		
		var tokens=item.innerHTML.replace('[[[','').replace(']]]','');
		tokens=tokens.split(":::");		
		var code=tokens[0];
		var oldprice=price=tokens[1];
		var pricetype=tokens[2];
		
		if(ccontent && ccontent.discountprods){
			if(ccontent.discountprods[code]){
				if(ccontent.discountprods[code].type=='%'){
					price=price-Math.ceil(price*ccontent.discountprods[code].value/100);
				}
				else if(ccontent.discountprods[code].type=='k'){
					price=price-ccontent.discountprods[code].value;	
				}
			}
		}

		var htmlprice='';
		if(oldprice!=price){
			htmlprice+='<div style="height: 40px;padding: 10px;background: #ff90f3 url('+siteurl+'images/discount4.png) no-repeat;background-size: contain;">';
			htmlprice+='<div style="float: left;margin-left: 14px;margin-top: 10px;font-weight: bold;color: #fff;">'+ccontent.discountprods[code].value+ccontent.discountprods[code].type+'</div>'
			htmlprice+='<div style="float:right"><span style="text-decoration:line-through">'+numberWithCommas(oldprice*1000)+' ₫</span><br />';
			htmlprice+='<strong>'+numberWithCommas(price*1000)+' ₫</strong></div>';
			htmlprice+='</div>';
		}
		else{
			htmlprice+='<div style="text-align:right"><strong>'+numberWithCommas(price*1000)+' ₫</strong></div>';	
		}
		
		item.innerHTML=htmlprice;
	});
}