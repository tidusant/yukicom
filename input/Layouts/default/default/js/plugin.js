function renderPlugin(){
	
	//setResponsive();
	//loadHome();
	//loadmenuCat();
	//loadrelatePost();
	//loadrightBanner();
	//loadMyBanner();
	 //if(isLoadGacode){
	 	
	 //}
	
}

/*============================right banner plugin=================================*/
function loadMyBanner(){	

	var bannergiay='<div class="banner_from_outside"><center><iframe style="border:none" src="http://add.deey.net/bannertutu/"></iframe></center></div>';
	//var bannergiay='<br /><!-- inline_ad --><ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8807368023281585"   data-ad-slot="1729164009" data-ad-format="auto"></ins><br />';
	//var bannergiay='<!-- nkcm_leaderboard_1 --><ins class="adsbygoogle"  style="display:block" data-ad-client="ca-pub-8807368023281585" data-ad-slot="9810675602" data-ad-format="auto"></ins>';
	//document.body.innerHTML=document.body.innerHTML.replace('[mybanner_bannergiay]',bannergiay);
	//$('.adsbygoogle').attr('style','');
	//$('.adsbygoogle').html(bannergiay);
	// console.log('location:'+MyAjax.posturl);
	// console.log('siteurl:'+siteurl);
	//disable google ad for home
	if(MyAjax.posturl!=siteurl)	{
		

		//ga ad
		// var content=document.querySelector('section #text');
		// var len=content.childNodes.length;
		// while(len<=2 && len!=0){
		// 	content=content.childNodes[0];
		// 	len=content.childNodes.length;
		// }
		// $(content.childNodes[Math.floor(len/4)]).after(bannergiay);	
		// (adsbygoogle = window.adsbygoogle || []).push({});
	 // 	isLoadGacode=0;

	 	//custom ad
	 	var content=document.querySelector('#title');
	 	$(content).after(bannergiay);
	}
	



}

/*============================right banner plugin=================================*/
function loadrightBanner(){	
	var url=siteurl+e('plugins').replace(/[=\/]/g,'')+'/'+e('rightBanner').replace(/[=\/]/g,'');
	loadPluginData(url,'rightBanner');
}
function loadrightBannerData(data){
	//gacode process disabled
	var datahtml=data['html'],gatmpl=copy(MyAjax.pred.gatmpl);
	
	if(pageData.GADisabled!=undefined){
		for(i in pageData.GADisabled){				
			if(pageData.GADisabled[i])gatmpl[i]="";
		}
	}
	//gacode replace
	if(gatmpl!=undefined){
		for(i in gatmpl){				
			datahtml=datahtml.replace("[["+i+"]]",gatmpl[i])
		}
	}
	$("#plugin-rightBanner").html(datahtml);
	if(datahtml.indexOf('class="adsbygoogle"')>-1)(adsbygoogle = window.adsbygoogle || []).push({});
		

	
	
}
/*============================relatePost plugin=================================*/

function loadrelatePost(){

	//check is detail page:
	var gid='';
	gid=$('#gid').html();
	//console.log(1);
	//console.log(gid);
	if(gid!=undefined && gid!=0 && gid!=""){
		var url=siteurl+e('plugins').replace(/[=\/]/g,'')+'/'+e('relateCat'+gid).replace(/[=\/]/g,'');
		loadPluginData(url,'relatePost');
	}
}
function loadrelatePostData(data){
	 //process data
	 //get random data

	 var items=data['content'][0].data.posts;
	 //get page slug
	 var slug=getRequestUri(MyAjax.defaulturl).replace('/','');	 
	 var renderItems=[];
	 //check total post
	 var count=data['limit'];
	 if(count>=items.length)count=items.length-1;
	 //check duplicate on show
	 var slugshow=[];
	 slugshow.push(slug);
	 for(var i=0;i<count;i++){
		 //hardcode
		 if(i==0){
			slugshow.push(items[0].slug);
			renderItems.push(items[0]);
			continue;
		 }
	 	//random item
	 	var rnditem=items[Math.floor(Math.random()*items.length)];
	 	//skip duplicate content
	 	if(slugshow.indexOf(rnditem.slug)>-1){
	 		i--;
	 		continue;
	 	}
		slugshow.push(rnditem.slug);
	 	renderItems.push(rnditem);
	 }
	 var renderData={0:{data:{posts:renderItems},name:'data'}};
	 var html=renderHtml(renderData,data['html']);
	 //console.log(data['html']);
	 $("#plugin-relateCat").html(html);
	 loadAllLink();
}
/*============================HomeData plugin=================================*/

function loadHome(){

	//check is detail page:
	
	//console.log(1);
	//console.log(gid);	
	
	if(MyAjax.posturl==siteurl)	{
		var url=siteurl+e('plugins').replace(/[=\/]/g,'')+'/'+e('homeData'+document.documentElement.lang).replace(/[=\/]/g,'');
		loadPluginData(url,'homeData');
	}
	
}
function loadhomeDataData(data){
	 //process data
	 //get random data
	 
	 var items=data['content'][0].data.posts;
	 //get page slug
	 var slug=getRequestUri(MyAjax.defaulturl).replace('/','');	 
	 
	 var renderData={0:{data:{posts:items},name:'data'}};
	 var html=renderHtml(renderData,data['html']);
	 
	 $("#plugin-homeData").html(html);
	 loadAllLink();
}
/*============================menuCat plugin=================================*/
function loadmenuCat(){
	var url=siteurl+e('plugins').replace(/[=\/]/g,'')+'/'+e('menuCat').replace(/[=\/]/g,'');
	loadPluginData(url,'menuCat');
}

function loadmenuCatData(data){
	
	var html=renderHtml(data['content'],data['html']);
	$("#plugin-menuCat").html(html);
	loadAllLink();
}	


/*============================main function to get plugin template and data=================================*/
function loadPluginData(url,pluginname){	
	
	if(hist_data[url]==undefined){
		$.ajax({
		    url: url,  
		    type: "GET",
		    data: '',
		    dataType:"text",
		    error: function (request, status, error) {
		        if(request.status==404){
		        	console.log('load '+pluginname+' fail');
		        	
		        }
		    },
			success:function(data){
				mydata=JSON.parse(JXG.decompress(data));
				 //for fb info			 		
				 hist_data[url]=mydata;
				 
				 eval('load'+pluginname+'Data(mydata);');
				
				
			}
		 });
	}
	else{
		 eval('load'+pluginname+'Data(hist_data[url]);');
	}
}

