
<div id="{{$moduleName}}" ng-controller="{{$moduleName}}">

    <div class="uk-navbar">
        <span class="uk-navbar-brand">
            <a href="@route("/")">@lang('dashboard')</a> /            
            <span class="uk-text-muted">GA Code List</span>            
        </span>

        
        <ul class="uk-navbar-nav">
            <li><a href="@route("/$moduleName/edit")" title="@lang('Add item')" data-uk-tooltip="{pos:'right'}" data-cached-title="Add item"><i class="uk-icon-plus-circle"></i></a></li>
        </ul>
    </div>

    <div class="app-panel uk-margin uk-text-center ng-hide" data-ng-show="items && !items.length">
        <h2><i class="uk-icon-tasks"></i></h2>
        <p class="uk-text-large">
            @lang('It seems you don\'t have any item entries.')
        </p>
    </div>

    <div class="uk-grid" data-uk-grid-margin="" data-ng-show="items && items.length">
        <div class="uk-width-medium-4-4">
            <div class="app-panel">
                <table class="uk-table uk-table-striped">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>
                                @lang('Name')                            
                            </th>
                            <th width="15%">@lang('Last modified')</th>
                            <th width="15%">@lang('Created')</th>
                            <th width="10%">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-ng-repeat="item in items">
                            <td><input type="checkbox" ng-model="item.done" ng-change="itemDone(item)" /></td>
                            <td>
                                <a href="@route("/$moduleName/edit/")@@ item._id @@" title="@lang('Edit')">@@ item.name @@</a>
                            </td>
                            <td>@@ item.modified | fmtdate:'d M, Y' @@</td>
                            <td>@@ item.created | fmtdate:'d M, Y' @@</td>
                            <td class="uk-text-right">
                                <a href="@route("/$moduleName/edit/")@@ item._id @@" title="@lang('Edit')"><i class="uk-icon-pencil"></i></a>
                                <a href data-ng-click="removeitem(item)" title="@lang('Delete')"><i class="uk-icon-trash-o"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>

    App.module.controller("{{$moduleName}}", function($scope, $rootScope, $http, $timeout) {

        fetchdata();

        $scope.items;

        $scope.newitem = '';

        $scope.additem = function() {
            var name = prompt(App.i18n.get("Please enter a name:"), "");

            if (!name.length) {
                return;
            }

            saveitem({
                name: name,
                done: false
            });
        };

        
        $scope.removeitem = function(item) {
            App.Ui.confirm(App.i18n.get("Are you sure?"), function() {
                removeitem(item);
            });
        };

        $scope.itemDone = function(item) {
            saveitem(item);
        };

        function saveitem(item) {
            $http.post(App.route("/api/{{$moduleName}}/save"), {"item": item}).success(function(data) {
                if (!item._id) {
                    $scope.items.push(data);
                }
            });
        };

        function removeitem(item) {
            $http.post(App.route("/api/{{$moduleName}}/removeaccount"), {"id": item._id}).success(function(data) {
                $scope.items.splice($scope.items.indexOf(item), 1);
            });
        };

        function fetchdata() {
            $http.post(App.route("/api/{{$moduleName}}/findaccount")).success(function(data) {
                $scope.items = data;
            });
        };

    });

</script>