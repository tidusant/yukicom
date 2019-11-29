@start('header')

    @trigger('cockpit.content.fields.sources')

@end('header')

<div data-ng-controller="{{$moduleName.$action}}" data-id="{{ $id }}" ng-cloak>

   <div class="uk-navbar">
        <span class="uk-navbar-brand">
            <a href="@route("/")">@lang('dashboard')</a> /
            <a href="@route("/$moduleName")">Social</a> /
            <a href="@route("/$moduleName/accountlist")">@lang('Account List')</a> /
            <span class="uk-text-muted" ng-show="!item.name">@lang('Create')</span>
            <span ng-show="item.name">@@ item.name @@</span>
        </span>
        
        <ul class="uk-navbar-nav">
            <li><a href="@route("/$moduleName/account")" title="@lang('Add item')" data-uk-tooltip="{pos:'right'}" data-cached-title="Add item"><i class="uk-icon-plus-circle"></i></a></li>
        </ul>
    </div>

    <form class="uk-form" data-ng-submit="save()" data-ng-show="item">

        <div class="uk-grid">

            <div class="uk-width-1-1">
                <div class="app-panel">
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Name')</label>

                        <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('Name')" data-ng-model="item.name" required autocomplete>
                        
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('username')</label>

                        <input class="uk-width-1-1 uk-form-large" type="text" data-ng-model="item.username"  autocomplete>
                        
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('password')</label>

                        <input class="uk-width-1-1 uk-form-large" type="text" data-ng-model="item.password"  autocomplete>
                        
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('field1')</label>

                        <input class="uk-width-1-1 uk-form-large" type="text" data-ng-model="item.field1"  autocomplete>
                        
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('field2')</label>

                        <input class="uk-width-1-1 uk-form-large" type="text"  data-ng-model="item.field2"  autocomplete>
                        
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('field3')</label>

                        <input class="uk-width-1-1 uk-form-large" type="text"  data-ng-model="item.field3"  autocomplete>
                        
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('field4')</label>

                        <input class="uk-width-1-1 uk-form-large" type="text"  data-ng-model="item.field4"  autocomplete>
                        
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('field5')</label>

                        <input class="uk-width-1-1 uk-form-large" type="text"  data-ng-model="item.field5"  autocomplete>
                        
                    </div>
                     <div class="uk-form-row">
                        <span class="uk-text-small">@lang('Is Publish')</span>
                        <contentfield options='{"type":"boolean"}' data-ng-model="item.publish"></contentfield>
                        
                    </div>

                    <div class="uk-form-row" >
                        <div class="uk-button-group">
                            <button type="submit" class="uk-button uk-button-primary uk-button-large">@lang('Save Item')</button>                            
                        </div>
                        &nbsp;
                        <div class="uk-button-group" data-ng-show="item._id">
                            <button type="button" ng-click="verify()" class="uk-button uk-button-@@isVerified?'success':'primary'@@ uk-button-large">
                                <div class="app-loading uk-text-center" ng-show="verifyloading">
                                <i class="uk-icon-spinner uk-icon-spin"></i>
                                </div>
                                @@verifyloading?'':isVerified?'Verified':'Verify'@@
                            </button>                            
                        </div>
                        &nbsp;

                        <div class="uk-button-group" data-ng-show="item._id">
                            <button type="button" ng-click="testpost()" class="uk-button uk-button-primary uk-button-large" data-ng-disabled="!isVerified">
                                <div class="app-loading uk-text-center" ng-show="testloading">
                                <i class="uk-icon-spinner uk-icon-spin"></i>
                                </div>
                                @@testloading?'':'test'@@
                            </button>                            
                        </div>
                        &nbsp;
                        <a href="@route("/$moduleName")">@lang('Cancel')</a>
                    </div>

                </div>
            </div>
            

    </form>
</div>
<script>
(function($){

    App.module.controller("{{$moduleName.$action}}", function($scope, $rootScope, $http, $timeout, Contentfields){
        $scope.moduleName='{{$moduleName}}';
        $scope.action='{{$action}}';
        $scope.controllerName=$scope.moduleName+$scope.action;
        var id = $("[data-ng-controller='"+$scope.controllerName+"']").data("id");
        var urlparams=location.search;
        $scope.isVerified=0;
        if (id) {

            $http.post(App.route("/api/"+$scope.moduleName+"/accountfindOne"), {filter: {"_id":id}}, {responseType:"json"}).success(function(data){

                if (data && Object.keys(data).length) {
                    $scope.item = data;
                    console.log(data);
                }
                //check verify
                //check verify
                $scope.verifyloading=1;
                $http.post(App.route("/api/"+$scope.moduleName+"/checkverify"), {accname:$scope.item.name,item: $scope.item}).success(function(data){


                    if (data && data.success) {
                        //window.location=data.fblogin;
                        $scope.isVerified=1;
                    }
                    else{
                        $scope.isVerified=0
                    }
                    $scope.verifyloading=0;


                });

            }).error(App.module.callbacks.error.http);

        } else {
            $scope.item = {
                name: "",
                sortfield: "created",
                sortorder: "-1"
            };
        }
       
        //function
        $scope.save = function() {

            var item = angular.copy($scope.item);
            
            $http.post(App.route("/api/"+$scope.moduleName+"/saveaccount"), {item: item}).success(function(data){

                if (data && Object.keys(data).length) {
                    $scope.item._id = data._id;
                    App.notify(App.i18n.get("item saved!"), "success");
                }

            }).error(App.module.callbacks.error.http);
        };

       //function
        $scope.verify = function() {
            //if($scope.verifyloading || $scope.isVerified)return;
            $http.post(App.route("/api/"+$scope.moduleName+"/verifyaccount"), {accname:$scope.item.name,item: $scope.item}).success(function(data){

                if (data && data.fblogin) {
                    window.location=data.fblogin;
                }

            }).error(App.module.callbacks.error.http);
        };

        $scope.testpost = function() {
            if($scope.testloading || !$scope.isVerified)return;
            $scope.testloading=1;
            $http.post(App.route("/api/"+$scope.moduleName+"/testpost"), {accname:$scope.item.name,item: $scope.item}).success(function(data){

                if (data && data.success) {
                        //window.location=data.fblogin;
                        App.notify(App.i18n.get("test success!"), "success");
                        
                    }
                    else{
                        App.notify(App.i18n.get("test fail!"), "danger");
                    }
                    
                    $scope.testloading=0;

            }).error(App.module.callbacks.error.http);
        };




        // bind clobal command + save
        Mousetrap.bindGlobal(['command+s', 'ctrl+s'], function(e) {
            if (e.preventDefault) {
                e.preventDefault();
            } else {
                e.returnValue = false; // ie
            }
            $scope.save();
            return false;
        });

       
       
    });
})(jQuery);
    

</script>