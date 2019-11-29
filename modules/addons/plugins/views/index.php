
<div id="{{$moduleName}}" ng-controller="{{$moduleName}}">

    <div class="uk-grid" data-uk-grid-margin="" >
        <div class="uk-width-medium-1-1">
        <form class="uk-form" data-ng-submit="submit()" >
            <div class="app-panel">
                <div class="uk-form-row">
                    <label class="uk-text-small">@lang('path to file:')</label>
                    <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('Name')" data-ng-model="filepath" required autocomplete>                        
                </div>
                <div class="uk-form-row" >
                    <div class="uk-button-group">
                        <button type="submit" class="uk-button uk-button-primary uk-button-large">@lang('Submit')</button>
                        
                    </div>
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

           
            $http.post(App.route("/api/{{$moduleName}}/submit"), {item: $scope.filepath}).success(function(data){

                if (data && Object.keys(data).length) {
                   
                    App.notify(App.i18n.get("done!"), "success");
                }

            }).error(App.module.callbacks.error.http);
        };
        
      
    });

</script>