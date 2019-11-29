<style>
    
.progress {
  width: 100%;
  height: 20px;
}

.progress-wrap {
  background: #f80;
  margin: 0px 0;
  overflow: hidden;
  position: relative;
}
.progress-wrap .progress-bar {
  background: #ddd;
  left: 0;
  position: absolute;
  top: 0;
}
</style>
<div id="{{$moduleName}}" ng-controller="{{$moduleName}}">

    <nav class="uk-navbar">
        <span class="uk-navbar-brand">
            <a href="@route("/")">@lang('dashboard')</a> /            
            <span class="uk-text-muted">Social</span> /
            <a href="@route("/$moduleName/accountlist")">@lang('Account List')</a>
            
        </span>


        <div class="uk-navbar-flip ng-scope" >
            <div class="uk-navbar-content">                
                 @hasaccess?($moduleName, "manage.edit")
                    <a href="@route("/$moduleName/index")"> 
                    <span class="uk-button uk-form-file" data-uk-tooltip="" title="Post by Auto">                        
                        <i class="uk-icon-android"></i>
                    </span>
                    </a>


                    <a href="@route("/$moduleName/accountlist")"> 
                    <span class="uk-button uk-form-file" data-uk-tooltip="" title="List">                        
                        <i class="uk-icon-list"></i>
                    </span>
                    </a>

                    <a href="@route("/$moduleName/account")"> 
                    <span class="uk-button uk-form-file" data-uk-tooltip="" title="Add Account">                        
                        <i class="uk-icon-plus-circle"></i>
                    </span>
                    </a>

                 @end 
            </div>
        </div>
    </nav>

    <div class="app-panel">
        <table class="uk-table uk-table-striped" multiple-select="{model:items}">
                    <thead>
                        <tr>
                            
                            <th colspan="110" style="text-align:right">
                                <a href="javascript:;" data-ng-repeat="i in []|range:pagecount " ng-click="showpage(i+1)"> 
                                <span class="uk-button uk-form-file" style="@@i+1==page?'background-color:#b8b8b8':''@@">                        
                                    @@i+1@@
                                </span>
                                </a>
                            </th>
                        </tr>
                        <tr>
                            
                            <th width="9%"></th>
                            <th width="9%" ng-repeat="post in posts">
                                <a target="_blank" href="@route("/posts/post")/@@ post._id @@">@@post['title']@@</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>                        
                        <tr class="js-multiple-select ng-scope" data-ng-repeat="socacc in acc">
                            
                            <td>
                                <a target="_blank" href="@route("/socials/account")/@@ socacc['_id'] @@" class="ng-binding">@@socacc['name']@@ </a>
                            </td>
                            <td ng-repeat="post in posts">

                                <a target="_blank" data-uk-tooltip="" title="@@resultPost[socacc['_id']][post._id]['posteddate'] | fmtdate:'h:i a - d M, Y'@@" href="@@ resultPost[socacc['_id']][post._id]['content'] @@" class="ng-binding" ng-show="resultPost[socacc['_id']][post._id]['success']">
                                    <span style="font-weight:bold;color:blue">OK</span>
                                </a>
                                <span style="font-weight:bold;color:red" data-uk-tooltip="" title="@@resultPost[socacc['_id']][post._id]['content']@@" ng-show="!resultPost[socacc['_id']][post._id]['success'] && resultPost[socacc['_id']][post._id]['content']">
                                    Error
                                </span>
                                <a href="javascript:;" ng-click="repost(socacc['_id'],post._id)" ng-show="!arrPostedId[post._id]" > 
                                <span class="uk-button uk-form-file" data-uk-tooltip="" title="Repost">                        
                                    <i class="uk-icon-play"></i>@@arrPostedId[post._id]@@
                                </span>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>


        
    </div>

</div>
<div style="display:none;overflow:scroll;height:300px;" id="processinfo">
</div>
<script>
var userlistmodel,es,postaccid,postpid;
(function($){
    App.module.filter('range', function() {
      return function(input, total) {
        total = parseInt(total);
        for (var i=0; i<total; i++)
          input.push(i);
        return input;
      };
    });
    App.module.controller("{{$moduleName}}", function($scope, $rootScope, $http, $timeout) {
        $scope.moduleName='{{$moduleName}}';
        $scope.action='{{$action}}';
        $scope.controllerName=$scope.moduleName+$scope.action;
        $scope.page=1;
        $scope.pagecount=1;
        
        $http.post(App.route("/api/"+$scope.moduleName+"/find2"), {}, {responseType:"json"}).success(function(data){
            if (data && Object.keys(data).length) {
                $scope.posts=data.posts;
                $scope.acc=data.acc;
                $scope.resultPost=data.resultPost;
                $scope.pagecount=data.pagecount;
                $scope.arrPostedId=data.arrPostedId;                               
                $scope.siteurl=data.siteurl; 
            }
        }).error(App.module.callbacks.error.http);
        $scope.showpage = function(page) {
            $scope.page=page;
            $http.post(App.route("/api/"+$scope.moduleName+"/find2"), {page: $scope.page}, {responseType:"json"}).success(function(data){
                if (data && Object.keys(data).length) {
                    $scope.posts=data.posts;                   
                    $scope.resultPost=data.resultPost;
                    $scope.arrPostedId=data.arrPostedId; 

                    //$scope.$apply();
                   
                }
            }).error(App.module.callbacks.error.http);   
        }    
        $scope.repost = function(accid,pid) {
            postaccid=accid;
            postpid=pid;

            //console.log($scope.posts[pid]);
            userlistmodel=App.Ui.dialog($("#processinfo").html(),{bgclose:false});  
            userlistmodel.dialog.height(($(window).height()-200));
            userlistmodel.dialog.css('overflow-y','scroll');
            //set event

            var buttonstopHtml='<div class="uk-modal-dialog" style="margin:auto;padding:5px;">';
            
            buttonstopHtml+='<div>Username: '+$scope.acc[accid]['username']+' </div>';
            buttonstopHtml+='<div>Password: '+$scope.acc[accid]['passowrd']+' </div>';
            if($scope.acc[accid]['field1'])buttonstopHtml+='<div>Field1: '+$scope.acc[accid]['field1']+' </div>';
            if($scope.acc[accid]['field2'])buttonstopHtml+='<div>Field2: '+$scope.acc[accid]['field2']+' </div>';
            if($scope.acc[accid]['field3'])buttonstopHtml+='<div>Field3: '+$scope.acc[accid]['field3']+' </div>';
            if($scope.acc[accid]['field4'])buttonstopHtml+='<div>Field4: '+$scope.acc[accid]['field4']+' </div>';
            if($scope.acc[accid]['field5'])buttonstopHtml+='<div>Field5: '+$scope.acc[accid]['field5']+' </div>';
            buttonstopHtml+='<div>URL: '+$scope.siteurl+$scope.posts[pid]['slug']+'/ </div>';
            buttonstopHtml+='';
            buttonstopHtml+='<div style="clear:both"></div>';
            buttonstopHtml+='</div>';

            buttonstopHtml+='<div class="uk-modal-dialog" style="margin:auto;padding:5px;margin-top:5px;">';
            buttonstopHtml+='<div>Result content:<br/> <textarea style="width:99%" id="PostResult"></textarea></div>';
            buttonstopHtml+='<div style="clear:both"></div>';
            buttonstopHtml+='</div>';
            buttonstopHtml+='<div style="margin-top:5px;"><button type="button" onclick="savePostResult()"  class="uk-button uk-button-primary uk-button-medium" id="buttonSavePostResult">Save</button></div>';
            userlistmodel.dialog.html($(buttonstopHtml));
            
            
              
        }


        



      
    });
})(jQuery);
function savePostResult(){
   
    $.post(App.route("/api/socials/savePostResult2"),{accid:postaccid,pid:postpid,content:$('#PostResult').val()}).success(function(data){        
        angular.element(document.getElementById('{{$moduleName}}')).scope().resultPost[postaccid][postpid]=JSON.parse(data);
        angular.element(document.getElementById('{{$moduleName}}')).scope().$apply();
        userlistmodel.hide();
    });  
    return;
}
</script>