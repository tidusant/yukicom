
<div id="{{$moduleName}}" ng-controller="{{$moduleName}}">

    <div class="uk-grid" data-uk-grid-margin="" >
        <div class="uk-width-medium-1-1">
        <form class="uk-form" data-ng-submit="submit()" >
            <div class="app-panel">               
                <div class="uk-form-row" >
                    
                        <button type="button" ng-click="submit()" class="uk-button uk-button-primary uk-button-large">@lang('SQL2Cockpit')</button>
                        <button type="button" ng-click="submit2()" class="uk-button uk-button-primary uk-button-large">@lang('Cockpit2Mongo')</button>
                        <button type="button" ng-click="submit3()" class="uk-button uk-button-primary uk-button-large">@lang('nhatkychame.com')</button>
                    
                </div>
               
            </div>
        </form>
        </div>
    </div>

</div>

<script>

    App.module.controller("{{$moduleName}}", function($scope, $rootScope, $http, $timeout) {

       //function
        $scope.submit = function() {

           
            $http.post(App.route("/api/{{$moduleName}}/submit"), {}).success(function(data){

                if (data && Object.keys(data).length) {
                   
                    App.notify(App.i18n.get("done!"), "success");
                }

            }).error(App.module.callbacks.error.http);
        };

         $scope.submit2 = function() {

           
            $http.post(App.route("/api/{{$moduleName}}/submit2"), {}).success(function(data){

                if (data && Object.keys(data).length) {
                   
                    App.notify(App.i18n.get("done!"), "success");
                }

            }).error(App.module.callbacks.error.http);
        };

        $scope.submit3 = function() {

           
            $http.post(App.route("/api/{{$moduleName}}/submit3"), {}).success(function(data){

                if (data && Object.keys(data).length) {
                   
                    App.notify(App.i18n.get("done!"), "success");
                }

            }).error(App.module.callbacks.error.http);
        };
        
      
    });

</script>