<style>
    #requestLoading{
            position: absolute;
            width: 93%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.63);
            text-align:center;
            
            top: 90px;
        }
        #requestLoading img{
           
        }
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
        <div style="clear:both"></div>
        <div class="uk-form uk-margin-remove uk-display-inline-block ng-pristine ng-valid">
                <div class="uk-form-icon">
                    <i class="uk-icon-filter"></i>
                    <input type="text" placeholder="Filter by title..." ng-keyup="search()" data-ng-model="filter" class="ng-pristine ng-untouched ng-valid">
                </div>
              
            </div>

        <div class="uk-navbar-flip ng-scope" >
            <div class="uk-navbar-content">                
                 @hasaccess?($moduleName, "manage.edit")
                    <a href="javascript:;" ng-click="runautopost()"> 
                    <span class="uk-button uk-form-file" data-uk-tooltip="" title="Run Auto Post">                        
                        <i class="uk-icon-play"></i>
                    </span>
                    </a>

                    <a href="@route("/$moduleName/index2")"> 
                    <span class="uk-button uk-form-file" data-uk-tooltip="" title="Post by User">                        
                        <i class="uk-icon-user-md"></i>
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
                        <a target="_blank" href="@route("/socials/account")/@@ socacc['accid'] @@" class="ng-binding">@@socacc['name']@@ </a>
                    </td>
                    <td ng-repeat="post in posts">
                        <a target="_blank" data-uk-tooltip="" title="@@resultPost[socacc.accid][post._id]['posteddate'] | fmtdate:'h:i a - d M, Y'@@" href="@@ resultPost[socacc.accid][post._id]['content'] @@" class="ng-binding" ng-show="resultPost[socacc.accid][post._id]['success']">
                            <span style="font-weight:bold;color:blue">OK</span>
                        </a>
                        <span style="font-weight:bold;color:red" data-uk-tooltip="" title="@@resultPost[socacc.accid][post._id]['content']@@" ng-show="!resultPost[socacc.accid][post._id]['success'] && resultPost[socacc.accid][post._id]['content']">
                            Error
                        </span>
                        <a href="javascript:;" ng-click="repost(socacc['accid'],post._id)"> 
                        <span class="uk-button uk-form-file" data-uk-tooltip="" title="Repost">                        
                            <i class="uk-icon-play"></i>
                        </span>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="requestLoading" ng-show="loading"><img src="@base("/assets/images/ajax-loader.gif")" /></div>
    </div>
    
</div>
<div style="display:none;overflow:scroll;height:300px;" id="processinfo">
</div>
<script>
var userlistmodel,es;
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
        $scope.loading = 0;
        
        $http.post(App.route("/api/"+$scope.moduleName+"/find"), {}, {responseType:"json"}).success(function(data){
            if (data && Object.keys(data).length) {
                $scope.posts=data.posts;
                $scope.acc=data.acc;
                $scope.resultPost=data.resultPost;
                $scope.pagecount=data.pagecount;                
            }
        }).error(App.module.callbacks.error.http);

        $scope.search=function(){
            
            $scope.loading = 1;
            var delay=500;
            timeToPostSearchTerms = new Date().getTime() + delay;
            $timeout(function () {
                //after 2s if there is no other keypress (this time will be gt timeToPostSearchTerms) then do change
                if (new Date().getTime() >= timeToPostSearchTerms){
                    $scope.page=1;
                   $scope.showpage();
                }
            }, delay);
        }
        $scope.showpage = function(page) {
            $scope.page=page;
            $http.post(App.route("/api/"+$scope.moduleName+"/find"), {page: $scope.page,search:$scope.filter}, {responseType:"json"}).success(function(data){
                if (data && Object.keys(data).length) {
                    $scope.posts=data.posts;                   
                    $scope.resultPost=data.resultPost;
                    $scope.pagecount=data.pagecount;   
                    $scope.loading=0;
                    //$scope.$apply();
                    console.log($scope.pagecount);
                }
            }).error(App.module.callbacks.error.http);   
        }    
        $scope.repost = function(accid,pid) {
            //console.log("/api/socials/repost?accid="+accid+"&pid="+pid);
            $scope.openPopup("/api/socials/repost?accid="+accid+"&pid="+pid,updateRepost);    
        }

        $scope.openPopup =function(urlservice,fnData){
            userlistmodel=App.Ui.dialog($("#processinfo").html(),{bgclose:true});                                        

            userlistmodel.dialog.height(($(window).height()-200));
            userlistmodel.dialog.css('overflow-y','scroll');
            //set event

            var buttonstopHtml='<div class="uk-modal-dialog" style="margin:-45px auto;padding:5px;">';
            buttonstopHtml+='<div style="float:left"></div>';
            buttonstopHtml+='<div class="progress-wrap progress" data-progress-percent="25">  <div class="progress-bar progress"></div></div>';
            buttonstopHtml+='<div style="float:left;margin-left:10px" id="processMessage"></div>';
            buttonstopHtml+='<div style="clear:both"></div>';
            buttonstopHtml+='</div>';
            userlistmodel.dialog.after($(buttonstopHtml));
            //set process bar:
            var processbar=$('.progress-wrap');
            processbar.width(userlistmodel.dialog.width()-$('#buttonStopBuild').width()-15);
            var urlpost=App.route(urlservice);           
            es = new EventSource(urlpost);
            es.addEventListener('message', function(e) {
                var result = JSON.parse( e.data );
                if(e.lastEventId == 'CLOSE') {
                    addLog('Received CLOSE closing');
                    es.close();
                } 
                else if(e.lastEventId=='STOP')  {
                    addLog(result.message);
                    es.close();
                    eval('$scope.pushloading=0;');
                     $scope.$apply();
                }
                else if(e.lastEventId=='DATA')  {
                    fnData(result.message);
                   
                }
                else{
                    
                    addLog(result.message);
                    showProcess(result.processText);
                }
                
            });
            es.addEventListener('error', function(e) {
                var result = JSON.parse( e.data );
                addLog("<span style='color:#f00'>"+result.message+"</span>");
                es.close();
            });
        }

        $scope.runautopost = function() {
            $scope.openPopup("/api/socials/autopost");           
                
        }  

function updateRepost(strData){
   var data=JSON.parse(strData);
    //console.log(data);
    //console.log($scope.resultPost[data.accid][data.pid]);
    $scope.resultPost[data.accid][data.pid]=data;
     $scope.$apply();
}
function showProcess(message) {
    if(message!=''){
        var percent=0;
        percent=eval(message);
        $('.progress-wrap').attr('data-progress-percent',percent*100);
        moveProgressBar();
        var msgs=message.split('+')[0];
        $('#processMessage').html(msgs);
    }
}
function addLog(message) {
    userlistmodel.dialog[0].children[0].innerHTML+=message;
    userlistmodel.dialog[0].scrollTop = userlistmodel.dialog[0].scrollHeight;    
}

// on page load...

  
    // SIGNATURE PROGRESS
    function moveProgressBar() {
      
        var getPercent = ($('.progress-wrap').attr('data-progress-percent') / 100);        
        var getProgressWrapWidth = $('.progress-wrap').width();
        var progressTotal = getPercent * getProgressWrapWidth;
        var animationLength = 250;
        
        // on page load, animate percentage bar to data percentage length
        // .stop() used to prevent animation queueing
        $('.progress-bar').stop().animate({
            left: progressTotal
        }, animationLength);
    }  
      
    });
})(jQuery);

</script>