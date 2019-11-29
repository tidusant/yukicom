<style>
    .app-panel:hover{
        background: #FFFFE1;            
            border: 1px solid #D8D800;
            
        }

        .currentSet,.currentSet:hover{
            background: #F0F0F0;            
            border: 1px solid #000000;
        }
     

</style>
<div id="{{$moduleName}}" ng-controller="{{$moduleName}}">

    <div class="uk-navbar">
        <span class="uk-navbar-brand">
            <a href="@route("/")">@lang('dashboard')</a> /            
            <span class="uk-text-muted">Themes management</span>            
        </span>

        
      
    </div>

    <div class="app-panel uk-margin uk-text-center ng-hide" data-ng-show="items && !items.length">
        <h2><i class="uk-icon-tasks"></i></h2>
        <p class="uk-text-large">
            @lang('It seems you don\'t have any item entries.')
        </p>
    </div>

    <div class="uk-grid uk-grid-small" data-uk-grid-match data-ng-if="items && items.length">
       
        <div class="uk-width-1-1 uk-width-medium-1-4 uk-grid-margin" data-ng-repeat="item in items">
            <a ng-click="setTheme(item.name)" href="javascript:;">
                <div style="text-align:center;"  class="app-panel @@item.name==currentName?'currentSet':''@@">
                                            
                    <img ng-src="@@item.screenshot@@" title="click @@ item.name @@ to set theme" ><br />
                    <strong >@@ fillString(item.name) @@</strong><br />
                    
                   
                  
                </div>
            </a>
        </div>
    </div>

</div>

<script>

    App.module.controller("{{$moduleName}}", function($scope, $rootScope, $http, $timeout) {

        fetchdata();
        $scope.currentName='';
      
        $scope.fillString = function(text){
            // Return early if the string is already shorter than the limit
            var limit=50;
            if(text.length <= limit) {
                // for (var i = text.length; i <=limit; i++) {
                //     text+=" ";
                //     text+=" ";
                // };
                return text;
            }
            else{
                return $scope.cutString(text);
            }
            
        };
      
        function fetchdata() {
            $http.post(App.route("/api/{{$moduleName}}/getThemes")).success(function(data) {
                $scope.items = data['themes'];
                $scope.currentName = data['currentTheme'];
                console.log(data);
            });
        };

        $scope.setTheme=function(name) {
            if($scope.currentName!=name){
                $http.post(App.route("/api/{{$moduleName}}/setTheme"),{name:name}).success(function(data) {                
                    $scope.currentName=name;                    
                });
            }
        };

    });

</script>