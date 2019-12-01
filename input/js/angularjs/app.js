var debug=debug || {{debug}};
var hist_data = {};
var form_data={};
var trusted = {};
var UrlRef=document.referrer;
//default url
var MyAjax={};
var isLoadedGa=0;
var pageData={};
var pageLoadedCount=1;
MyAjax.defaulturl=window.location.href.split('?')[0];
MyAjax.pred=JSON.parse(JXG.decompress(pred.replace(e64('home').replace(/[=\/]/g,''),'')));
MyAjax.posturl=MyAjax.defaulturl;
MyAjax.slug=MyAjax.posturl.replace(siteurl,'').replace('/','');
MyAjax.pagetype=$('#pagetype').html();
MyAjax.gnameslug=$('#gnameslug').html();





function e64(input) {
     input = escape(input);
     var output = "";
     var chr1, chr2, chr3 = "";
     var enc1, enc2, enc3, enc4 = "";
     var i = 0;

     do {
        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if (isNaN(chr2)) {
           enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
           enc4 = 64;
        }

        output = output +
           keyStr.charAt(enc1) +
           keyStr.charAt(enc2) +
           keyStr.charAt(enc3) +
           keyStr.charAt(enc4);
        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";
     } while (i < input.length);

     return output;
  }
  function de64 (input) { 
input=b64f(input);
var output = "";
     var chr1, chr2, chr3 = "";
     var enc1, enc2, enc3, enc4 = "";
     var i = 0;

     var base64test = /[^A-Za-z0-9\+\/\=]/g;
     if (base64test.exec(input)) {
        alert("There were invalid base64 characters in the input text.\n" +
              "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
              "Expect errors in decoding.");
     }
     input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
     
     do {
        enc1 = keyStr.indexOf(input.charAt(i++));
        enc2 = keyStr.indexOf(input.charAt(i++));
        enc3 = keyStr.indexOf(input.charAt(i++));
        enc4 = keyStr.indexOf(input.charAt(i++));

        chr1 = (enc1 << 2) | (enc2 >> 4);
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
        chr3 = ((enc3 & 3) << 6) | enc4;

        output = output + String.fromCharCode(chr1);

        if (enc3 != 64) {
           output = output + String.fromCharCode(chr2);
        }
        if (enc4 != 64) {
           output = output + String.fromCharCode(chr3);
        }

        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";

     } while (i < input.length);

     return unescape(output);
}
function b64f(s){

	n = 4 - s.length%4;
	//console.log(n);
	if(n<4){

		for (i = 0; i < n; i++) {
			s += "=";
		}
	
	}
	return s;
}

function copy(o) {
   var out, v, key;
   out = Array.isArray(o) ? [] : {};
   for (key in o) {
       v = o[key];
       out[key] = (typeof v === "object") ? copy(v) : v;
   }
   return out;
}
function randomKey(url,num){
	url=parseDataUrl(url,num);
	//console.log(url);
	num=e64(x).replace(/[=\/]/g,"");
	//console.log(num);
	var l=Math.floor(url.length/2);
	url=url.substring(0,l)+num+url.substring(l);
	return url;
}
function encDat(data,oddnumber){	
	
	var data=e64(data).replace(/[=]/g,'');		
    var result1 = [];
    var result2 = [];
    for (i = data.length-1; i >=0; i--) {
        if(i%oddnumber==0)result1.push(data.charAt(i));
        else result2.push(data.charAt(i));
    }
    data=result1.join("")+result2.join("");    
    data=data.substring(0,1).toLowerCase()+data.substring(1);
    return data;
}
function encDat2(data,oddnumber){	
	if(oddnumber==undefined)oddnumber=5;
	var x = Math.floor((Math.random() * oddnumber) + 1);
    var detailurl=data;
    detailurl=e64(detailurl).replace(/=/g,"")+makeid(x);
    var result1 = [];
    var result2 = [];
    for (i = detailurl.length-1; i >=0; i--) {
        if(i%x==0)result1.push(detailurl.charAt(i));
        else result2.push(detailurl.charAt(i));
    }
    var ekey=result1.join("");
    detailurl=ekey+result2.join("");  
    x=e64(x).replace(/[=\/]/g,"");
    var l=Math.floor(detailurl.length/2);
    detailurl=detailurl.substring(0,l)+x+detailurl.substring(l);	
    return detailurl;
}
function decDat(data){
	key=data;
	if(key=="")return '';	
	script='';
	detail='';
	oddstr='d';
	l=Math.floor((key.length-2)/2);
	num=key.substr(l,2);
	key=key.substr(0,l)+key.substr(l+2);
	//console.log("num: "+num);
	num=de64(num)*1;
	ekey='';
	//console.log("num: "+num);
	if(num>0){
		//print_r($num);print_r("\r\n");	
		//get odd string

		oddstr=key.substr(0,Math.ceil(key.length/num));
		ekey=oddstr;		
		ukey=key.replace(oddstr, '');	
		base64='';
		
		for(i=oddstr.length-1;i>=0;i--){
			base64+=oddstr.substr(oddstr.length-1);
			oddstr=oddstr.substr(0,oddstr.length-1);
			
			if(ukey.length-num+1>0)
				base64+=(ukey.substr(ukey.length-num+1)).split("").reverse().join("");
			else
				base64+=ukey.split("").reverse().join("");
			
			ukey=ukey.substr(0, ukey.length-num+1);
			
		}
		base64=base64.substr(0,base64.length-num);
		//decode cart
		data=decodeURIComponent(de64(base64));
		//print_r($data);exit;
		
		
		return data;
	}
	return '';
}

function apicall(url,data,isCheckCookie,callback){
	//console.log("apicall: "+url);
	var uid=getCookie("uid");
	if(uid=="" && isCheckCookie==1){
		createsex(apicall,url,data);
		return;
	}
	var startseconds = new Date().getTime() / 1000;
	var r1 = Math.floor((Math.random() * 9) + 1);
	var r2 = Math.floor((Math.random() * 9) + 1);
	$.ajax({

		url: apiurl+encDat2(url+"|"+uid,r1), 
		type: "POST",
		data: {data:encDat2(data,r2)},
		dataType:"text",
		error: function (request, status, error) {
			if(request.status==404){
			  console.log(error);               
			}
		},
		success:function(data){
			var tookseconds=(new Date().getTime() / 1000-startseconds)+1;
			//console.log("took: "+tookseconds+"s");
			setCookie("uid",uid,30*60-tookseconds);
		  if(data!=""){
			  if(callback!=undefined)
				callback(data); 
		  }
		  else{
			
		  }

		}
	});
}

function createsex(callback){	
//console.log("createsex");
	var detailurl="CreateSex|"+siteid+"|0";
	callbackurl=arguments[1];
	callbackdata=arguments[2];
	detailurl=encDat2(detailurl,7);
	data=new Date().getTime();
	data=encDat2(data,9);
	$.ajax({
		url: apiurl+detailurl, 
		//url: apiurl+"aMJMMYMMNYNJUYQRHlADdTzQDwADiNAFziZWkFG1kT0QmygTDdT4V2lRXlJ3", 
		type: "POST",
		data: {data:data},
		dataType:"text",
		error: function (request, status, error) {
			if(request.status==404){
			  
			}
		},
		success:function(data){
		  if(data!=""){
			//console.log(data); 
			setCookie("uid",data,30*60);
			callback(callbackurl, callbackdata);
		  }
		  else{
			
		  }

		}
	});
}

function parseDataUrl(requestUrl,oddnumber){	
	var p=requestUrl.split('?');
	return siteurl+encDat(p[0].replace(/[\/\.:\-]/g,''),oddnumber);
}
//parse routing to controller name
function parseRouting(url,isNotPushState){

	NProgress.start();


	//getdata
	var mydata;
	//if(isNotPushState != undefined)alert('isNotPushState');
	//bootstrap menu mobile:
	var navbar=$('button[data-toggle="collapse"]');

	var navbarcontent=navbar.parent().next();
	if(navbar.css("display")!="none" && navbarcontent.hasClass('in')){
		navbar.trigger('click');
	}

	
	if(hist_data[url]!=undefined){
		//process data
		NProgress.set(0.7);
		pageData=hist_data[url];	
		renderData(url,isNotPushState);
		NProgress.done(true);	
	}
	else{
		NProgress.set(0.4);			
		var data = {
			    i: sitev,
			    
			  };
		var requestUrl=getRequestUri(url);				
		if(requestUrl!=""){
			requestUrl=parseDataUrl(requestUrl,2);
			$.ajax({
			    url: requestUrl, 
			    type: "GET",
			    data: data,
			    dataType:"text",
			    error: function (request, status, error) {
			        if(request.status==404){
			        	console.log(url+' error 404');
			        	window.location=siteurl;
			        }
			    },
				success:function(data){				
				    ajaxComplete(data,url,isNotPushState)			
					
				}
			 })
		}
	}	
}
function dataParse(data,key){
	Data=JSON.parse(JXG.decompress(data.replace(key.replace(/[=\/]/g,''),'')));
	return Data;
}
function ajaxComplete(data,url,isNotPushState){

	//alert(url);
	NProgress.set(0.7);
 	if(checkResponse(data,true)){
 		var requesturl=getRequestUri(url); 		
		 pageData=JSON.parse(JXG.decompress(data.replace(e64(requesturl==''?siteurl:requesturl).replace(/[=\/]/g,''),'')));
		 //for fb info		 		
		 hist_data[url]=pageData;
		 renderData(url,isNotPushState);
	 }
	 NProgress.done(true);
}


function renderData(url,isNotPushState){
	var mydata=data=pageData;
	
	//process control	
	if(debug){
		console.log(MyAjax);
		console.log('MyAjax.pred.tmpl.'+data.controllerName+'');
	}
	hideAllController();
	//console.log('changeurl:'+url);
 	MyAjax.posturl=url;	
 	MyAjax.posttitle=data.title;	
 	MyAjax.slug=url.replace(siteurl,'').replace('/','');
 	MyAjax.postdesc=data.description;
 	MyAjax.postimage=siteurl+data.slug+'/'+data.featureimage+sitesize+data.featureimagetype;
 	MyAjax.gnameslug=data.gnameslug;
 	MyAjax.gname=data.gname;
	try{

		eval('if(MyAjax.pred.tmpl.'+data.controllerName+'!=undefined){renderController(data,MyAjax.pred.tmpl.'+data.controllerName+');}else{renderController(data,MyAjax.pred.tmpl.'+data.alternateControllerName+');}');	
		//do script

		if(data.script!=undefined && data.script!=''){
			
			eval(b(data.script));
		}
		

	}
	catch(err) {
	    console.log(err);
	}
	
	//reload link
	
	//reload canonical link:
	$('link[rel="alternate"]').attr('href',url);
	$('link[rel="canonical"]').attr('href',url);
	//reload fb
	//$('.fb-like').attr('data-href',url);
	//$('#gplus').attr('data-href',url);
	//FB.XFBML.parse();
	
	
	//reload other field

	reloadOther(mydata);
	
	
	
	//browser history 
	if(isNotPushState == undefined){
		//alert(url);
		window.history.pushState({'url': url},"", url);
		//console.log(window.history);
	}


	//init form
	
}

function reloadSocial(url){	
	if(ga!=undefined){		
		ga('send', 'pageview',url.replace(siteurl,'/'));
	}
	if(fbq!=undefined){	
		fbq('track', 'PageView');
	}
	if(url.indexOf('/contact/')>0 || url.indexOf('/about/')>0 || url==siteurl || url.indexOf('/submit-order/')>0)	{
		setDisable(false);		
	}
	else{
		if ($(".fb-like").length > 0) {
		    if (typeof (FB) != 'undefined') {
		        FB.init({ appId      : myFBId,status: true, cookie: true, xfbml: true });
		    } else {
		        $.getScript("http://connect.facebook.net/vi_VN/all.js#xfbml=1", function () {
		            FB.init({ appId      : myFBId,status: true, cookie: true, xfbml: true });
		        });
		    }
		}
		// //reload g+
		var gbuttons = $(".g-plusone");
		if (gbuttons.length > 0) {
		    if (typeof (gapi) != 'undefined') {
		        gbuttons.each(function () {
		            gapi.plusone.render($(this).get(0));
		        });
		    } else {
		        $.getScript('https://apis.google.com/js/plusone.js');
		    }
		}
		setDisable(true);
	}
}
function loadLangs(){
	var arrLangs=[];
	
	var langslink=$('link[hreflang]');
	if(langslink.length){
		langslink.each(function(){			
			arrLangs.push({'hreflang':$(this).attr('hreflang'),href:$(this).attr('href'),langiso:$(this).attr('lang-iso'),langname:$(this).attr('langname')});		
		});
		renderLangs(arrLangs);
	}
	else{
		$('#languages').hide();
	}
	
}
function renderLangs(arrLangs){
	var langElement=$('#languages');
	if(langElement.length>0 && arrLangs.length>0){		
		var langhtml=langElement.children()[0].outerHTML;		
		langElement.html('');
		for (var key in arrLangs) {
			if (arrLangs.hasOwnProperty(key)) {
				var obj = arrLangs[key];
				var newEl=$(langhtml);
				newEl.find('a').attr('href',obj.href);
				newEl.find('a').attr('title',obj.langname);
				newEl.find('img').attr('src',siteurl+'images/flags/'+obj.langiso+'.png');
				newEl.find('img').attr('alt',obj.langname);
				langElement.append(newEl);			  
			}
		}
		$('#languages').show();		
	}
	else{
		$('#languages').hide();	
	}
	
			
}

function getRequestUri(url){	
	if(url==siteurl){
		url=siteurl+"cache";
		url=url.replace(/[\/\.:\-]/g,'');
	}
	else url=url.replace('www.','').replace(siteurl,'');	
	if(url=="javascript:void(0)") url="";
	return url;
}

function hideAllController(){	
	$('[id$=Controller]').html('');	
}

var processLoadAllLink=0;
var allLink=[];
function loadAllLink(force){	
	
	if(getCookie('lal')=='true' || force){
		
		
		$('a').not('.nobinding').off('click');	
		//preload data:
		$('a').not('.nobinding').each(function(e){
			
			var url=$(this).attr('href');
			var classname=$(this).attr('class');
			//console.log(url);
			if(url!=undefined && url!="#" && url!="" && url!="javascript:;"){
				var requestUrl=url.replace(siteurl,'');			
				if(requestUrl.indexOf('http')==0 || classname=='outlink' || requestUrl.indexOf('mailto:')==0){
					;
				}
				else{			
					try{
						if(allLink.indexOf(url)==-1)
							allLink.push(url);
					}
					catch(Ex){
						allLink.push(url);
					}
					
					$(this).on('click',function(e){	
						//console.log(pageLoadedCount);
						if(pageLoadedCount<300){
							pageLoadedCount++;
							try{
								UrlRef=location.href;							
								parseRouting(url);			
							}
							catch(err) {
							    console.log(err);
							}
							window.scrollTo(0, 0);
							$('body').removeClass('with--sidebar');
							e.target.onclick;
							e.preventDefault(); 		
						}
						e.preventDefault();
					});
					
				}
			}
		});
		preloadAllLink();
		setCookie('lal','',-1);
	}
}

function preloadAllLink(){
	if(!processLoadAllLink){
		processLoadAllLink=1;
		processPreloadLink();
	}
}
function processPreloadLink(){
	if(allLink.length>0){

		var url=allLink.shift();

		var data = {
			    i: sitev,
			    
			  };
		
		

		if(hist_data[url]!=undefined){			
			processPreloadLink();
		}
		else{
			var requestUrl=getRequestUri(url);	
			if(debug){		        
		        console.log('request '+url);    		        
		    }
			//console.log(url);
			if(requestUrl!=""){
				requestUrl=parseDataUrl(requestUrl,2);	
				if(debug){        
			        console.log(''+requestUrl);
			    }			
				$.ajax({
				    url: requestUrl, 
				    type: "GET",
				    data: data,
				    dataType:"text",
				    error: function (request, status, error) {
				        if(request.status==404){
				        	console.log(url+' error 404');
				        }
				    },
					success:function(data){		
						try {
							var requesturl=getRequestUri(url); 		
						    var t=JSON.parse(JXG.decompress(data.replace(e64(requesturl==''?siteurl:requesturl).replace(/[=\/]/g,''),'')).replace(/\{\{siteurl\}\}/g, siteurl).replace(/\{\{sitesize\}\}/g, sitesize));
							if(debug){		        
						        console.log(t);    		        
						    }
							hist_data[url]=t;
						}
						catch(err) {
							console.log(err);
						}
						
						processPreloadLink();
					}
				 })
			}
		}
	}
	else{
		processLoadAllLink=0;
	}
}

function checkResponse(data,show){
	var rt=true;
	if(show!=undefined && data.indexOf('Error')>=0){
		
		rt=false;
	}

 	
 	if(data.indexOf('Session timeout')>=0){
		location.reload();
		rt=false;
	}
 	return rt;
}
function setDisable(disable){
	
}
function replaceSpecialCharCompare(str,convert){
	if(convert==undefined)convert=0;
	str=str.replace('[lte]','<=');
	str=str.replace('[lt]','<');
	return str;
}
function renderHtml(dataName,tmpl,loop){	
	
	var control=$(tmpl);
	if(loop==undefined)loop=0;
	
	
	if(dataName!=undefined){
		for(i in dataName){

			eval("var "+dataName[i].name+"=dataName[i].data;");
			
		}
	}

	//replace other token:	
	
	
	control.find("[ng-repeat]").each(function(e){
		
		var prefixs=$(this).attr("ng-repeat").split(" ");
		var loopdataname=prefixs[2];
		var loopitemname=prefixs[0];		
		var loopHtml="";
		
		var rp=$(this).attr("ng-repeat");
		$(this).removeAttr('ng-repeat');
		eval("for("+rp+"){			var dataNameForLoop=$.extend([], dataName);			dataNameForLoop.push({name:'"+loopitemname+"',data:"+loopdataname+"["+loopitemname+"]});			loopHtml+=renderHtml(dataNameForLoop,$(this)[0].outerHTML,loop+1); }");	
		$(this)[0].outerHTML=loopHtml;
		/*
		var regex = /in (.*)/g;
		var matches;
		matches = regex.exec(rp);
		var stack=matches[1];
		eval("if("+stack+".length==0)$(this).remove();else{ for("+rp+"){			var dataNameForLoop=$.extend([], dataName);			dataNameForLoop.push({name:'"+loopitemname+"',data:"+loopdataname+"["+loopitemname+"]});			loopHtml+=renderHtml(dataNameForLoop,$(this)[0].outerHTML,loop+1); } $(this)[0].outerHTML=loopHtml;}");	
		*/
		
		//loop to next		
		control[0].innerHTML=$(renderHtml($.extend([], dataName),control[0].outerHTML,loop+1)).html();
	
		return false;
	});

	control.find("[ng-show]").each(function(e){		
		var op=replaceSpecialCharCompare($(this).attr("ng-show"));
			
		eval("if(!("+op+")){$(this).remove();}else{$(this).removeAttr('ng-show');var dataNameForLoop=$.extend([], dataName);$(this).html($(renderHtml(dataNameForLoop,$(this)[0].outerHTML,10)).html());}");		
		
		// //loop next
		
		control[0].innerHTML=$(renderHtml($.extend([], dataName),control[0].outerHTML,loop+1)).html();
		
		return false;
	});

	
	
	control.find("[ng-bind]").each(function(e){		
		eval("if("+$(this).attr("ng-bind")+"!=undefined)$(this).html(b("+$(this).attr("ng-bind")+"));");	
		$(this).removeAttr("ng-bind");
	});

	control.find("[ng-href]").each(function(e){			
		eval("$(this).attr(\"href\",b(" + $(this).attr("ng-href") + "));");		
		$(this).removeAttr("ng-href");
	});
	control.find("[ng-src]").each(function(e){		
		eval("$(this).attr(\"src\",b(" + $(this).attr("ng-src") + "));");
		$(this).removeAttr("ng-src");		
			
	});
	control.find("[ng-style]").each(function(e){		
		eval("$(this).attr(\"style\",b(" + $(this).attr("ng-style") + "));");
		$(this).removeAttr("ng-style");		
			
	});
	control.find("[ng-class]").each(function(e){		
		eval("$(this).attr(\"class\",b(" + $(this).attr("ng-class") + "));");
		$(this).removeAttr("ng-class");		
			
	});

	control.find("[ng-alt]").each(function(e){		
		eval("$(this).attr(\"alt\"," + $(this).attr("ng-alt") + ");");
		$(this).removeAttr("ng-alt");		
	});


	

	var oldrthtml=rthtml=tmpl;	
	if(control[0]!=undefined)oldrthtml=rthtml= control[0].outerHTML;
	//console.log(oldrthtml);
	var regex = /\{\{(.*?)\}\}/g;
	var matches;
	while (matches = regex.exec(oldrthtml)) {
		//console.log(matches);
		//eval("rthtml=rthtml.replace(new RegExp(\"\{\{"+matches[1]+"\}\}\", 'g'),"+matches[1]+");");
		eval("rthtml=rthtml.replace(\"\{\{"+matches[1]+"\}\}\","+matches[1]+");");
	    
	    // eval("console.log("+matches[1]+")");
	    // console.log(rthtml);
	    
	}

	return rthtml;
}
function renderController(data,tmpl){
	if(debug){
		console.log(data);
		
	}
	var dataName=[];	
	//data=b(data);	
	dataName.push({name:"data",data:data})
	try{		
		//renderHtml(dataName,tmpl);
		
		var datahtml=renderHtml(dataName,tmpl);
		//gacode process disabled
		
		if($("#"+data.controllerName+"Controller").length>0)
			$("#"+data.controllerName+"Controller").html(datahtml);
		else
			$("#"+data.alternateControllerName+"Controller").html(datahtml);
		
			
		
		
		
		
		//render language link
		var arrLangs=[];
		for (var key in data.langslink) {
			if (data.langslink.hasOwnProperty(key)) {
				if(data.langslink[key].length<=7 || (data.langslink[key].substring(0,7)!="http://" && data.langslink[key].substring(0,8)!="https://"))
					data.langslink[key]=siteurl+data.langslink[key];
				arrLangs.push({href:data.langslink[key],langiso:data.langsiso[key].substring(3).toLowerCase(),langname:data.langsname[key]});		
			}
		}

		renderLangs(arrLangs);

		
	}
	catch(err){
		console.log(err);
	}
}
function reloadOther(data){	
	reloadSocial(MyAjax.posturl);
	//render plugin
	renderPlugin();

	reloadMenuTab(data.menutab);
	//reload title
	document.title=data.title;
	custom_event();	
	setCookie('lal','true',5);
}
function reloadMenuTab(tabName){
	$('.mainmenubody .active').removeClass('active');
	$('.mainmenubody .menu'+tabName).addClass('active');	
}
{{custom_event}}
//progress on go back history 
window.onpopstate = function(event) {
	var url=MyAjax.defaulturl;
	  if(event.state!=null && event.state.url!=undefined && event.state.url!=""){
		  url=event.state.url;		
	  }

	  parseRouting(url,true);
	  
	};

//fist run
setTimeout(function(){
	custom_event();
	renderPlugin();
	reloadSocial(MyAjax.defaulturl);
	loadLangs();
	
	loadAllLink(1);	
	setInterval(loadAllLink,1000);
},30);


