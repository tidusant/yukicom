app.controller('MenuController',['$scope','$http',function($scope, $http){	
	//init
	$scope.name='Menu';
	$scope.Active=1;
	$scope.data={};
	$scope.data.siteurl=siteurl;
	$scope.hide=function(mydata){
		$scope.Active=0;
	}
	$scope.curTime=new Date().getTime() / 1000;
		$.ajax({
		    url: siteurl+base64_encode('menudata').replace(/[\/=]/gi,''),
		    type: "GET",		    
		    dataType:"text",
			success:function(data){
				var cats=JSON.parse(JXG.decompress(data));
				$scope.data={};
				$scope.data.cats={};
				
				for(catid in cats)
				{
					var mycat=cats[catid];
					var showi=0;
					var cat=angular.copy(cats[catid]);
					mycat.posts={};
					for(postid in cat.posts)
					{

						var post=cat.posts[postid];
						if(showi>=4)break;
						if(post.post_date<=$scope.curTime){
							mycat.posts[postid]=post;
							showi++;
						}
					}	
					$scope.data.cats[catid]=mycat;
				}
				//console.log($scope.data.cats);
				$scope.data.siteurl=siteurl;
				$scope.data.curTime=new Date().getTime() / 1000;

				
				$scope.$apply();
				loadAllLink();
			}
		 })


	
	
	
}]);


