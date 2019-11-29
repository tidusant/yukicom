 @start('header')

@end('header')
<style>
        #requestLoading{
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.63);
            text-align:center;
            
            top:0;
        }
        #requestLoading img{
            margin-top:200px;
        }

        #groups-list li {
            position: relative;
            overflow: hidden;
        }
        .group-actions {
            position: absolute;
            display:none;
            min-width: 60px;
            text-align: right;
            top: 5px;
            right: 10px;
        }

        .group-actions a { font-size: 11px; }

        #groups-list li.uk-active .group-actions,
        #groups-list li:hover .group-actions { display:block; }
        #groups-list li:hover .group-actions a { color: #666; }
        #groups-list li.uk-active a,
        #groups-list li.uk-active .group-actions a { color: #fff; }

        .post-draft{
            background: #F0F0F0;            
            border: 1px solid #000000;
        }

        .post-unpublish{
            background: #FFFFE1;            
            border: 1px solid #D8D800;
        }

    </style>
 <div ng-controller="{{$moduleName.$action}}">
    <nav class="uk-navbar uk-margin-large-bottom">        
        <div >
            <div class="uk-form uk-margin-remove uk-display-inline-block ng-pristine ng-valid">
                <div class="uk-form-icon">
                    <i class="uk-icon-filter"></i>
                    <input type="text" placeholder="Filter by title..." ng-keyup="search()" data-ng-model="filter" class="ng-pristine ng-untouched ng-valid">
                </div>
                <div class="uk-form-icon">
                    <i class="uk-icon-filter"></i>
                    <input type="text" placeholder="Filter by user..." data-ng-model="filteruser" class="ng-pristine ng-untouched ng-valid">
                    <!-- <button class="uk-button" data-ng-class="isshowall? 'uk-button-primary':''" data-ng-click="showCat()" title="@lang('search')" data-uk-tooltip="{pos:'bottom'}"><b>Go</b></button> -->
                </div>

                <div class="uk-button-group">
                    <button class="uk-button" data-ng-class="mode=='list' ? 'uk-button-primary':''" data-ng-click="(mode='list')" title="@lang('List mode')" data-uk-tooltip="{pos:'bottom'}"><i class="uk-icon-th"></i></button>
                    <button class="uk-button" data-ng-class="mode=='table' ? 'uk-button-primary':''" data-ng-click="(mode='table')" title="@lang('Table mode')" data-uk-tooltip="{pos:'bottom'}"><i class="uk-icon-th-list"></i></button>
                </div>

                <div class="uk-button-group">

                    <button class="uk-button" data-ng-class="isshowpublish? 'uk-button-primary':''" data-ng-click="toggleShowpublish()" title="@lang('show publish')" data-uk-tooltip="{pos:'bottom'}"><i class="uk-icon-check-square-o"></i></button>
                    <button class="uk-button" data-ng-class="isshowunpublish? 'uk-button-primary':''" data-ng-click="toggleShowunpublish()" title="@lang('show un-publish')" data-uk-tooltip="{pos:'bottom'}"><i class="uk-icon-square-o"></i></button>
                    <button class="uk-button" data-ng-class="isshowhome? 'uk-button-primary':''" data-ng-click="toggleShowhome()" title="@lang('show home')" data-uk-tooltip="{pos:'bottom'}"><i class="uk-icon-home"></i></button>
                    <!-- <button class="uk-button" data-ng-class="isshowdraft? 'uk-button-primary':''" data-ng-click="toggleShowdraft()" title="@lang('show draft')" data-uk-tooltip="{pos:'bottom'}"><i class="uk-icon-file"></i></button>  -->
                    <!-- <button class="uk-button" data-ng-class="isshowall? 'uk-button-primary':''" data-ng-click="toggleShowall()" title="@lang('show all')" data-uk-tooltip="{pos:'bottom'}">All</button> -->
                </div>
            </div>
        </div>
    </nav>
    <div  class="uk-grid uk-grid-divider">
        <div class="uk-width-medium-1-4">
            <div class="uk-panel">
                <ul class="uk-nav uk-nav-side uk-nav-plain" ng-show="groups.length">
                    <li class="uk-nav-header">@lang("Groups")</li>
                    <li ng-class="activegroup=='0' ? 'uk-active':''" ng-click="showCat(0)"><a>@lang("All")</a></li>
                </ul>

                <ul id="groups-list" class="uk-nav uk-nav-side uk-animation-fade uk-sortable" ng-show="groups.length" data-uk-sortable>
                    <li ng-repeat="group in groups" ng-class="activegroup==group._id ? 'uk-active':''" ng-click="showCat(group._id)" draggable="true">
                        <a> @@ group.name @@</a>
                        
                    </li>
                    <li ng-click="showCat(-1)" ng-class="activegroup=='-1' ? 'uk-active':''">
                        <a></i>Chưa phân loại</a>
                    </li>
                </ul>

                <div class="uk-text-muted" ng-show="!groups.length">
                    @lang('Create groups.')
                </div>

                @hasaccess?($moduleName, 'manage.edit')
                <hr>
                <div class="uk-margin-top">
                    <button class="uk-button uk-button-success" title="@lang('Create new group')" data-uk-tooltip="{pos:'right'}" ng-click="addGroup()"><i class="uk-icon-plus-circle"></i></button>
                </div>
                @end
            </div>
        </div>
        <div class="uk-width-medium-3-4">

            <div ng-show="showlang">
                <div class="uk-float-right">
                    <span ng-repeat="lang in langs" class="uk-button uk-form-file ng-binding" ng-click="changeLang(lang)" style="margin-top:10px;@@currentlang==lang?'background-color:#b8b8b8':''@@">
                        <img ng-src="@base('/assets/images/flags/')@@listCountryFlag[lang]@@.png" />
                    </span>
                </div>
                <div style="clear:both"></div>
            </div>
            <div style="clear:both"></div>
            <div class="uk-grid uk-grid-small" data-uk-grid-match data-ng-if="items && items.length && mode=='list'">
                 <div class="app-panel">
                       <a href="javascript:;" data-ng-repeat="i in []|range:pagecount " ng-click="showpage(i+1)"> 
                        <span class="uk-button uk-form-file" style="margin-top:10px;@@ i+1==currentPage?'background-color:#b8b8b8':''@@">                        
                            @@i+1@@
                        </span>
                        </a>
                   </div>
                <div class="uk-width-1-1 uk-width-medium-1-3 uk-grid-margin" data-ng-repeat="item in items track by item._id">
                  
                        <div class="app-panel @@item.isdraft?'post-draft':(item.publish?'':'post-unpublish')@@">
                             <a class="uk-link-muted " href="@route("/$moduleName/item")/@@ item._id @@">                            
                            <img ng-src="@route('/mediamanager/thumbnail')/@@ item.featureimage?item.featureimage:'site:cockcms/uploaded/noimg.jpg'|base64 @@/250/140" title="@@ item.title?item.title:'n/a' @@"><br />
                            <strong>@@ fillString(item.title?item.title:'n/a') @@</strong> @@(users[item.uid].user?users[item.uid].user:'n/a')@@<br />
                            </a>
                            <div ng-show="item.microdata && item.microdata =='Product'">
                                SL: <input type="text" ng-model="item.noi" />
                            </div>
                            <div ng-show="item.microdata && item.microdata =='Product'">
                                price: <input type="text" ng-model="item.price" />
                            </div>
                            <div ng-show="item.microdata && item.microdata =='Product'">
                                baseprice: <input type="text" ng-model="item.baseprice" />
                            </div>
                            <div ng-show="item.microdata && item.microdata =='Product'">
                                <button  ng-click="savePrice(item)" type="submit" class="uk-button uk-button-primary uk-button-large">
                                    <div class="app-loading uk-text-center" ng-show="item.pushloading">
                                        <i class="uk-icon-spinner uk-icon-spin"></i>
                                    </div>
                                    @@item.pushloading?'':'Save'@@
                                </button>     
                            </div>
                            <div class="app-panel-box docked-bottom">
                                <div class="uk-float-left uk-link" ng-show="item.home"><i class="uk-icon-home"></i></div>
                                <div class="uk-float-left uk-link" data-uk-dropdown="{mode:'click'}">
                                    <i class="uk-icon-bars"></i>
                                    <div class="uk-dropdown">
                                        <ul class="uk-nav uk-nav-dropdown uk-nav-parent-icon">
                                            @hasaccess?($moduleName, 'manage.edit')
                                            <li><a href="@route("/$moduleName/item")/@@ item._id @@"><i class="uk-icon-pencil"></i> @lang('Edit item')</a></li>
                                            <li><a ng-click="duplicate(item._id)"><i class="uk-icon-copy"></i> @lang('Duplicate item')</a></li>
                                            <li class="uk-nav-divider"></li>
                                            <li class="uk-danger"><a data-ng-click="remove($index, item)" href="#"><i class="uk-icon-minus-circle"></i> @lang('Delete item')</a></li>
                                            @end
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                </div>
            </div>

            <div class="app-panel" data-ng-if="items && items.length && mode=='table'">
                <table class="uk-table uk-table-striped" multiple-select="{model:items}">
                    <thead>
                        <tr>
                            <th colspan="110" style="text-align:right">
                                <a href="javascript:;" data-ng-repeat="i in []|range:pagecount " ng-click="showpage(i+1)"> 
                                <span class="uk-button uk-form-file" style="margin-top:10px;@@ i+1==currentPage?'background-color:#b8b8b8':''@@">                        
                                    @@i+1@@
                                </span>
                                </a>
                            </th>
                        </tr>
                        <tr>
                            
                            <th width="60%">@lang('title')</th>
                            <th width="10%">@lang('Author')</th>
                            <th width="20%">@lang('Date post')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="js-multiple-select" data-ng-repeat="item in items track by item._id" >
                            
                            <td>
                                <a href="@route("/$moduleName/item")/@@ item._id @@">@@ item.title?item.title:'n/a' @@</a>
                            </td>
                            <td>@@(users[item.uid].user?users[item.uid].user:'n/a')@@</td>
                            <td>@@(item.startdate + ' ' + item.starttime)@@</td>
                            <td>
                                <div class="uk-float-left uk-link" ng-show="item.home"><i class="uk-icon-home"></i></div>
                                <div class="uk-float-right uk-link" data-uk-dropdown="{mode:'click'}">
                                    <i class="uk-icon-bars"></i>
                                    <div class="uk-dropdown">
                                        <ul class="uk-nav uk-nav-dropdown uk-nav-parent-icon">
                                            @hasaccess?("items", 'manage.items')
                                            <li class="uk-nav-divider"></li>
                                            <li><a href="@route("/$moduleName/item")/@@ item._id @@"><i class="uk-icon-pencil"></i> @lang('Edit item')</a></li>
                                            <li><a ng-click="duplicate(item._id)"><i class="uk-icon-copy"></i> @lang('Duplicate item')</a></li>
                                            <li class="uk-nav-divider"></li>
                                            <li class="uk-danger"><a data-ng-click="remove($index, item)" href="#"><i class="uk-icon-minus-circle"></i> @lang('Delete item')</a></li>
                                            @end
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="uk-margin-top">
                    <button class="uk-button uk-button-danger" data-ng-click="removeSelected()" data-ng-show="selected"><i class="uk-icon-trash-o"></i> @lang('Delete')</button>
                </div>
            </div>

            <!-- loader -->
            <div id="requestLoading" ng-show="loading"><img src="@base("/assets/images/ajax-loader.gif")" /></div>

        </div>
    </div>   
</div>

<script>
(function($){
    App.module.filter('range', function() {
      return function(input, total) {
        total = parseInt(total);
        for (var i=0; i<total; i++)
          input.push(i);
        return input;
      };
    });
    App.module.controller("{{$moduleName.$action}}", function($scope, $rootScope, $http, $timeout){
        
        $scope.moduleName='{{$moduleName}}';
        $scope.action='{{$action}}';
        $scope.controllerName=$scope.moduleName+$scope.action;
        $scope.showall = 0;    
        $scope.currentPage=1;
        $scope.showAssignToMe = 0;     
        $scope.mode = 'table';  
        $scope.currentlang='';
        $scope.showlang=0;
        $scope.loading = 0;
        $scope.listCountryFlag=[];
        var timeToPostSearchTerms = 0;//var to delay searchTerm keypress 

        $scope.filter = "";
        $scope.filteruser = "";
        $scope.isshowpublish=1;
        $scope.isshowunpublish=0;
        $scope.isshowdraft=0;
        $scope.isshowall=0;
        $scope.isshowhome=0;
        $scope.activegroup='0';

        
        $scope.showpage = function(page) {
            $scope.currentPage=page;
  

            data={page: $scope.currentPage,groupid:$scope.activegroup,search:$scope.filter,isshowpublish:$scope.isshowpublish,isshowunpublish:$scope.isshowunpublish,isshowhome:$scope.isshowhome,currentlang:$scope.currentlang};
            console.log(data);
            $http.post(App.route("/api/"+$scope.moduleName+"/find"), data, {responseType:"json"}).success(function(data){
                if (data && Object.keys(data).length) {
                    $scope.items=data.items; 
                    $scope.currentlang=data.defaultlang;
                    $scope.showlang=data.showlang;
                    $scope.langs = data.langs;  
                    $scope.groups = data.groups;
                    $scope.listCountryFlag=data.listCountryFlag;    
                }
            }).error(App.module.callbacks.error.http);   
        } 
       



        // get groups
        
       
        $scope.addGroup = function() {
            window.location="@route('/groups/item')";
            // var name = prompt("Group name");
            // if (name) {
            //      $http.post(App.route("/api/"+$scope.moduleName+"/addGroups"), {"name": name}, {responseType:"json"}).success(function(data){
            //         $timeout(function(){
            //             $scope.groups.push(data);
            //             App.notify(App.i18n.get("Groups created"), "success");
            //         }, 0);
            //     }).error(App.module.callbacks.error.http);
            // }
        };
       

        $scope.search=function(){
            $scope.loading = 1;
            var delay=1500;
            timeToPostSearchTerms = new Date().getTime() + delay;
            $timeout(function () {
                //after 2s if there is no other keypress (this time will be gt timeToPostSearchTerms) then do change
                if (new Date().getTime() >= timeToPostSearchTerms)
                    $scope.showCat();
            }, delay);
        }
      
        $scope.showCat = function(groupid) {  
            $scope.loading = 1;          
            var data={};    
            console.log(groupid);
                      
            if(typeof(groupid) == "undefined")
                groupid=$scope.activegroup;
            else $scope.activegroup=groupid;
            data={page:1,groupid:groupid,search:$scope.filter,isshowpublish:$scope.isshowpublish,isshowunpublish:$scope.isshowunpublish,isshowhome:$scope.isshowhome,currentlang:$scope.currentlang};
            $http.post(App.route("/api/"+$scope.moduleName+"/find"), data, {responseType:"json"}).success(function(data){
                if (data && Object.keys(data).length) {
                    $scope.items=data.items; 
                    $scope.currentlang=data.currentlang;
                    $scope.showlang=data.showlang;
                    $scope.langs = data.langs;  
                    $scope.groups = data.groups;
                    $scope.listCountryFlag=data.listCountryFlag;    

                    if(data.items.length>0){
                        $scope.pagecount=data.moduleInfo.totalpage;
                        $scope.currentPage=data.moduleInfo.currentpage;
                        $scope.users = data.users;  
                        console.log(data.items);
                    }    
                    
                    $scope.filter=data.moduleInfo.searchterms?data.moduleInfo.searchterms:'';
                   
                                        
                }
                $scope.loading = 0;
            }).error(App.module.callbacks.error.http);            
        };
        $scope.showCat(0);

        $scope.savePrice = function(item) {
            item.pushloading=1;
            $http.post(App.route("/api/"+$scope.moduleName+"/savePrice"), {item: item,currentlang:$scope.currentlang}).success(function(data){
                if (data && Object.keys(data).length) {                        
                    App.notify(data.message, data.result);                    
                }
                item.pushloading  =0;
            }).error(function(){$scope.pushloading  =0;App.module.callbacks.error.http();});            
        };

        $scope.matchName = function(name) {
            //return (name.toUpperCase().indexOf($scope.filter.toUpperCase()) !== -1);
            return true;
        };

        $scope.matchUsername = function(username) {
            return (username.toUpperCase().indexOf($scope.filteruser.toUpperCase()) !== -1);
        };

        $scope.toggleShowpublish = function(){
           if($scope.isshowpublish== 1)
            $scope.isshowpublish= 0;    
            else
            $scope.isshowpublish= 1;    
            $scope.showCat();
        };
        $scope.toggleShowunpublish = function(){
           if($scope.isshowunpublish== 1)
            $scope.isshowunpublish= 0;    
            else
            $scope.isshowunpublish= 1;  
            $scope.showCat();  
        };
        $scope.toggleShowdraft = function(){
           if($scope.isshowdraft== 1)
            $scope.isshowdraft= 0;    
            else
            $scope.isshowdraft= 1;    
        };
        $scope.toggleShowall = function(){
           if($scope.isshowall== 1)
            $scope.isshowall= 0;    
            else
            $scope.isshowall= 1;    
        };
        $scope.toggleShowhome = function(){
           if($scope.isshowhome== 1)
            $scope.isshowhome= 0;    
            else
            $scope.isshowhome= 1;    
            $scope.showCat();  
        };

        $scope.changeLang=function(lang){
            console.log('change lange to '+lang);

            if($scope.langs.indexOf(lang)>=0){
                //save old lang
                
                if($scope.currentlang!=lang){                    
                    $scope.currentlang=lang;   
                }
                $scope.showCat(); 
                
            }
            
        };

         $scope.remove = function(index, item){
            App.Ui.confirm(App.i18n.get("Are you sure to delete <b>"+item.title+"</b> ?"), function() {

                $http.post(App.route("/api/{{$moduleName}}/remove"), { "id": item._id }, {responseType:"json"}).success(function(data){

                    $timeout(function(){
                        $scope.items.splice(index, 1);
                        App.notify(App.i18n.get("Post removed"), "success");
                    }, 0);
                }).error(App.module.callbacks.error.http);
            });
        };

        $scope.duplicate = function(id){

            $http.post(App.route("/api/{{$moduleName}}/duplicate"), { "id": id }, {responseType:"json"}).success(function(item){

                $timeout(function(){
                    $scope.items.push(item);
                    App.notify(App.i18n.get("Post duplicated"), "success");
                }, 0);
            }).error(App.module.callbacks.error.http);
        };

        $scope.cutString = function(text){
            // Return early if the string is already shorter than the limit
            var limit=50;
            if(text.length < limit) {return text;}

            //regex = /(.{1,limit})\b/ig;
            matches=text.match(/(.{1,55})\b/ig);
            if(matches[0])return matches[0]+'...';
            else return text;
            
        };

        $scope.fillString = function(text){
            // Return early if the string is already shorter than the limit
            var limit=50;
            if(text.length <= limit) {
                for (var i = text.length; i <=limit; i++) {
                    text+=" ";
                    text+=" ";
                };
                return text;
            }
            else{
                return $scope.cutString(text);
            }
            
        };

        
       

    });

})(jQuery);

</script>