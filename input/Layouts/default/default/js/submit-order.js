
function submitorder(){
	try{
		var cart=getCart();
		var isValidCart=false;
		var isClick=false;
		var total=0;
		
		if(cart && cart.cics.length>0){
			cart.cins.forEach(function(item,i){
				total+=item*1;
			});
			isValidCart=true;
						
		}
		if(!isValidCart)window.location.href=siteurl;
		$('#thankyou').hide();
		var btnSubmit=$('#buttonSubmit');
		var phone=document.getElementById('phone-submit');
		var email=document.getElementById('email-submit');
		var name=document.getElementById('name-submit');
		var note=document.getElementById('note-submit');
		phone.value=phone.value.length>0?phone.value:getCookie('cp');
		name.value=name.value.length>0?name.value:getCookie('cna');
		btnSubmit.on('click',function(event){	
			if(isClick)return;
			
			/*validate phone*/
			
			var isValidPhone=true;
			var isValidEmail=true;


			
			if(phone.value=='' || !validatePhone(phone.value)){		
				isValidPhone=false;		
			}	
			/* if(!validateEmail(email.value) && email.value.length>0){		
			 	isValidEmail=false;
			}*/ 
			if(phone.value.length==0 && email!=null && email.value.length==0){
				alert('Bạn hãy nhập thông tin xác nhận!');	
				phone.focus();	
				return;
			}
			else if(!isValidPhone){
				alert('Số điện thoại không hợp lệ!');
				phone.focus();
				return;
			}
			else if(!isValidEmail){
				alert('Email không hợp lệ!');
				email.focus();
				return;
			}

			if(name.value.length==0){
				alert('Bạn hãy nhập tên!');
				name.focus();
				return;
			}

			if(getCookie('lr'))return;
			setCookie('lr',1,10);
			setCookie('cp',phone.value,2*366*24*3600);
			setCookie('cna',name.value,2*366*24*3600);

			var c,x = Math.floor((Math.random() * 9) + 1);
			var detailurl="submitorder";
			detailurl=c=encDat2(detailurl,9);
			/*
			detailurl=e(detailurl).replace(/=/g,"")+makeid(x);
			var result1 = [];
			var result2 = [];
			for (i = detailurl.length-1; i >=0; i--) {
			  if(i%x==0)result1.push(detailurl.charAt(i));
			  else result2.push(detailurl.charAt(i));
			}
			var ekey=result1.join("");
			detailurl=ekey+result2.join("");  
			x=e(x).replace(/[=\/]/g,"");
			var l=Math.floor(detailurl.length/2);
			detailurl=c=detailurl.substring(0,l)+x+detailurl.substring(l);
			*/

			var data='{"code":"'+getCookie("cid")+'","phone":"'+phone.value+'","name":"'+utf8_encode(name.value)+'","note":"'+utf8_encode(note.value)+'","cart":"'+getCookie("cic")+'","total":"'+total+'","cn":"'+getCookie("cn")+'","co":"'+getCookie("co")+'","r":"'+getCookie("r")+'"}';
			data=encDat2(data,5);
			/*
			x = Math.floor((Math.random() * 5) + 1);  
			data=e(data).replace(/=/g,"")+makeid(x);
			result1 = [];
			result2 = [];
			for (i = data.length-1; i >=0; i--) {
			  if(i%x==0)result1.push(data.charAt(i));
			  else result2.push(data.charAt(i));
			}
			ekey=result1.join("");
			data=ekey+result2.join("");  
			x=e(x).replace(/[=\/]/g,"");
			l=Math.floor(data.length/2);
			data=data.substring(0,l)+x+data.substring(l);
			*/

			$('#buttonSubmit').css({'background-color':'#fff','color':'#000'});
			$('#buttonSubmit').html('Xin chờ');
			isClick=true;
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
			  	data=JSON.parse(data);
			  	if(data.error){
			  		
			  			setCookie('carterror',data.error);
			  			setCookie('cid','',-1);
			  			setCookie('cc','',-1);
			  			setCookie('cn','',-1);
			  			setCookie('co','',-1);
			  			history.go(-1);
			  			setTimeout(function(){window.scrollTo(0, 0);},500);
			  			
			  		
			  	}	    
			  }
			  else{
			  	setCookie('cid','',-1);
			  	setCookie('cc','',-1);
			  	setCookie('cic','',-1);
			  	setCookie('cn','',-1);
	  			setCookie('co','',-1);
			  	$('#contactform').remove();
			  	$('#carttitle').html('Gửi giỏ hàng thành công!');
			  	$('#carttitle').css({'background-color':'white','color':'#ff789c'});
			  	$('#thankyou').show();
			  }

			}
			}); 
		});
	}catch(ex){
		setTimeout(submitorder,500);
	}
}
submitorder();