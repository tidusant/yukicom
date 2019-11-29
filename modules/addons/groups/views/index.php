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

    <nav class="uk-navbar ">        
        <div >
            <div class="uk-form uk-margin-remove uk-display-inline-block ng-pristine ng-valid">               

                <div class="uk-button-group">
                    <button class="uk-button" data-ng-class="uk-button-primary" onclick="window.location='@route('/')';" title="@lang('Home')" data-uk-tooltip="{pos:'bottom'}"><i class="uk-icon-home"></i></button>
                    <button class="uk-button" data-ng-class="uk-button-primary" onclick="window.location='@route('/groups/item')';" title="@lang('Add group')" data-uk-tooltip="{pos:'bottom'}"><i class="uk-icon-plus"></i></button>
                    
                </div>

               
            </div>
        </div>
    </nav>
   
    <div  class="uk-grid uk-grid-divider">
        
        <div class="uk-width-medium-4-4">

            <div ng-show="showlang">
                <div class="uk-float-right">
                    <span ng-repeat="lang in langs" class="uk-button uk-form-file ng-binding" ng-click="changeLang(lang)" style="margin-top:10px;@@currentlang==lang?'background-color:#b8b8b8':''@@">
                        <img ng-src="@base('/assets/images/flags/')@@listCountryFlag[lang]@@.png" />
                    </span>
                </div>
                <div style="clear:both"></div>
            </div>
            

            <div class="app-panel" data-ng-if="items && items.length">
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
                            
                            <th width="60%">@lang('name')</th>
                            <th width="10%">@lang('Author')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="js-multiple-select" data-ng-repeat="item in items track by item._id" >
                            
                            <td>
                                <a href="@route("/$moduleName/item")/@@ item._id @@">@@ item.name?item.name:'n/a' @@</a>
                            </td>
                            <td>@@(users[item.uid].user?users[item.uid].user:'n/a')@@</td>
                            <td>
                                <div class="uk-float-left uk-link" ng-show="item.home"><i class="uk-icon-home"></i></div>
                                <div class="uk-float-right uk-link" data-uk-dropdown="{mode:'click'}">
                                    <i class="uk-icon-bars"></i>
                                    <div class="uk-dropdown">
                                        <ul class="uk-nav uk-nav-dropdown uk-nav-parent-icon">
                                            @hasaccess?("items", 'manage.items')
                                            <li class="uk-nav-divider"></li>
                                            <li><a href="@route("/$moduleName/item")/@@ item._id @@"><i class="uk-icon-pencil"></i> @lang('Edit item')</a></li>
                                            
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
        $scope.currentlang='';
        $scope.itemlangs=[];
        $scope.items=[];        
        $scope.listCountryFlag=[];
        $scope.showlang=0;
        $scope.loading = 0;
        $scope.changeLang=function(lang){
            console.log('change lange to '+lang);

            if($scope.langs.indexOf(lang)>=0){
                //save old lang
                
                if($scope.currentlang!=lang){                    
                    $scope.currentlang=lang;   
                }
                
                if($scope.itemlangs[lang]!=undefined){
                    $scope.items=$scope.itemlangs[lang];
                }
                    

                console.log($scope.items);
                
            }
            
        };
           
            $http.post(App.route("/api/"+$scope.moduleName+"/find"), {}, {responseType:"json"}).success(function(data){
                if (data && Object.keys(data).length) {
                    $scope.itemlangs=data.items; 
                    $scope.currentlang=data.defaultlang;
                    $scope.showlang=data.showlang;
                    $scope.langs = data.langs;  
                    $scope.listCountryFlag=data.listCountryFlag;              
                    $scope.changeLang($scope.currentlang);
                }
            }).error(App.module.callbacks.error.http);   
       
       

            

       $scope.remove = function(index, item){
            App.Ui.confirm(App.i18n.get("Are you sure to delete <b>"+item.name+"</b> ?"), function() {

                $http.post(App.route("/api/{{$moduleName}}/remove"), { "id": item._id }, {responseType:"json"}).success(function(data){

                    $timeout(function(){
                        //window.location.reload();
                    }, 0);
                }).error(App.module.callbacks.error.http);
            });
        };

        $scope.matchName = function(name) {
            //return (name.toUpperCase().indexOf($scope.filter.toUpperCase()) !== -1);
            return true;
        };

        $scope.matchUsername = function(username) {
            return (username.toUpperCase().indexOf($scope.filteruser.toUpperCase()) !== -1);
        };

        

      
        
       

    });

})(jQuery);

</script>