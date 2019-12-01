var isLike=0;
var isShowLike=0;
var myFBId='{{FBId}}';
		$( document ).ready(function() {
			//init FB
			if(window.FB) {
				facebookReady();
			} else {
				window.fbAsyncInit = facebookReady;
			}


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
			            //alert('Hay chia se noi dung hay ban nhe.');
			        } else {						
			        	isLike=1;
			        	ga('send', 'event', 'copycontent', 'shared');
			        	//location.reload();
			        	$('#likecontent').hide();
			        }
			    });
			});
		});

	function facebookReady(){
		FB.init({
         appId      : myFBId,
		  xfbml      : true,
		  version    : 'v2.1'
	      });      
	
		FB.Event.subscribe('edge.create', function(href, widget) {
			//do script
			isLike=1;
			if(isShowLike==1){
				ga('send', 'event', 'forceliked',MyAjax.posturl);
			}
			else{
				ga('send', 'event', 'liked',MyAjax.posturl);
			}
			$('#likecontent').hide();
		  }); 	
		//fire an event to notify that facebook js is ready to used
		$(document).trigger("facebook:ready");
	}