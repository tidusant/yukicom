@start('header')

    {{ $app->assets(['assets:vendor/fuzzysearch.js']) }}
    @trigger('cockpit.content.fields.sources')

@end('header')
<div data-ng-controller="{{$moduleName.$action}}" data-id="{{ $id }}" data-pid="{{ $proj['_id'] }}" ng-cloak>
    <nav class="uk-navbar uk-margin-large-bottom">
    <span class="uk-navbar-brand">
        <a href="@route("/")">@lang('dashboard')</a> /
        <a href="@route("/projects")">@lang('projects')</a> :
        <a href="@route("/tasks/index/".$proj['_id'])">{{$proj['name']}}</a> /
        <a href="@route("/hosts/index/".$proj['_id'])">@lang('hosts')</a> :
        <span class="uk-text-muted" ng-show="!item.name">@lang('Create host')</span>
        <span ng-show="item.name">Edit host @@ item.name @@</span>
    </span>
    </nav>
    <form class="uk-form" data-ng-submit="save()" data-ng-show="item">

        <div class="uk-grid">

            <div class="uk-width-1-1">
                <div class="app-panel">
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Name:')</label>
                        <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('Name')" data-ng-model="item.name" required autocomplete>                        
                    </div>
                     <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Hostname:')</label>
                        <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('...')" data-ng-model="item.hostname" required autocomplete>                        
                        
                    </div>

                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Username:')</label>
                        <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('...')" data-ng-model="item.username" required autocomplete>                        
                        
                    </div>

                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Password:')</label>                        
                        <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('...')" data-ng-model="item.password" required autocomplete>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Map from:')</label>                        
                        <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('...')" data-ng-model="item.mapfrom" required autocomplete>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Map to:')</label>                        
                        <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('...')" data-ng-model="item.mapto" required autocomplete>
                    </div>
                    <div class="uk-form-row">
                        <span class="uk-text-small">@lang('User can push')</span>
                        <div id="userpush" class="uk-autocomplete">
                                <div id="acList_userpush" style="float:left;margin-left:10px;"></div>                                
                                <div style="float:left;margin-left:10px;"><input class="uk-search-field" type="text" placeholder="..." data-uk-tooltip title="@lang('user join...')"></div>                                
                                <div class="uk-dropdown uk-dropdown-flip"></div>
                            </div>                    
                    </div>

                    <div class="uk-form-row" >
                        <div class="uk-button-group">
                            <button type="submit" class="uk-button uk-button-primary uk-button-large">@lang('Save Item')</button>
                            <a href="@route("/tasks/index/".$proj['_id'])" class="uk-button uk-button-large" data-ng-show="item._id"><i class="uk-icon-list"></i> @lang('Goto Task List')</a>
                        </div>
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
        $scope.id = $("[data-ng-controller='"+$scope.controllerName+"']").data("id");
        $scope.pid = $("[data-ng-controller='"+$scope.controllerName+"']").data("pid");

        

       
        //function
        $scope.save = function() {

            var item = angular.copy($scope.item);
            item.pid=$scope.pid;
            item.dltime=$('input[ng-model="item.dltime"]').val();
            //console.log(item);
            $http.post(App.route("/api/"+$scope.moduleName+"/save"), {item: item}).success(function(data){

                if (data && Object.keys(data).length) {
                    $scope.item._id = data._id;
                    App.notify(App.i18n.get("item saved!"), "success");
                }

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

       
       var acFieldCount=0;                
        $http.post(App.route("/api/projects/getUsers/1"), {userIds: JSON.parse('{{json_encode($proj["userjoin"])}}')}).success(function(items){
            $scope.users = items;   
            //console.log(items); 
           setupAutoCompleteField('userpush',items);
           //get host info
           if ($scope.id) {
                $http.post(App.route("/api/"+$scope.moduleName+"/findOne"), {filter: {"_id":$scope.id,"pid":$scope.pid}}, {responseType:"json"}).success(function(data){
                    if (data && Object.keys(data).length) {
                        $scope.item = data;
                        $scope.item.pid=$scope.pid;
                        initAutoCompleteOldData('userpush');
                    }
                }).error(App.module.callbacks.error.http);

            } else {
                $scope.item = {
                    name: "",
                    sortfield: "created",
                    sortorder: "-1"
                };
            }

        }).error(App.module.callbacks.error.http);
            
        //===========auto complete function
        function setupAutoCompleteField(divId,datas){
            //init old data
            //console.log(datas);            
            
            var autocompleteField = $.UIkit.autocomplete('#'+divId, {minLength:1,
                source: function(release) {
                    var data = FuzzySearch.filter( datas, autocompleteField.input.val(), {key:'user', maxResults: 10});
                    release(data);
                },
                renderer: function(data) {
                    if (data && data.length) {
                        var lst      = $('<ul class="uk-nav uk-nav-autocomplete uk-autocomplete-results">'),
                            usericon = '<i class="uk-icon-user uk-text-small"></i>',                            
                            li;

                        data.forEach(function(item){
                            li = $('<li><a><strong>'+usericon+' &nbsp;'+item.user+'</strong></a></li>').data(item);
                            lst.append(li);
                        });

                        this.dropdown.append(lst);
                        this.show();
                    }
                }
            });

            autocompleteField.element.on('uk.autocomplete.select', function(e, data){   
                autocompleteField.input.val('');       
                //add html new user join 
                addAutoCompleteHtml(divId,data._id);
                //hidden value to submit:
                addAutoCompleteData(divId,data._id);
            });
        }         

        function initAutoCompleteOldData(divId){          
            eval('var userids=$scope.item.'+divId+'||JSON.parse("[]")');

            for(uid in userids){                
                addAutoCompleteHtml(divId,userids[uid]);
            }
            
        }
        function addAutoCompleteHtml(divId,uid){
            
            var newdiv=$('<span style="margin-left:10px;"><a  class="uk-icon-times" style="color:red"></a>&nbsp;'+$scope.usersbyid[uid].user+'</span>');     
            $('#acList_'+divId).append(newdiv)
            //add event click to remove user
            newdiv.on('click',function(e){
                newdiv.remove();
                removeAutoCompleteData(divId,uid);
            });
            
        }
        function addAutoCompleteData(divId,uid){          
            eval('$scope.item.'+divId+'=$scope.item.'+divId+'||JSON.parse("[]")');
            eval('$scope.item.'+divId+'.push(uid)');
            
        }
        function removeAutoCompleteData(divId,uid){          
            eval('$scope.item.'+divId+'=$scope.item.'+divId+'||JSON.parse("[]")');
            eval('$scope.item.'+divId+'.splice($scope.item.'+divId+'.indexOf(uid),1)');
           
        }
        //============================================
       
    });
})(jQuery);

</script>