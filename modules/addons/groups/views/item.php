@start('header')


    @trigger('cockpit.content.fields.sources')

@end('header')

<div data-ng-controller="{{$moduleName.$action}}" data-id="{{ $id }}" ng-cloak>
    <nav class="uk-navbar ">        
        <div >
            <div class="uk-form uk-margin-remove uk-display-inline-block ng-pristine ng-valid">               

                <div class="uk-button-group">
                    <button class="uk-button" data-ng-class="uk-button-primary" onclick="window.location='@route('/')';" title="@lang('Home')" data-uk-tooltip="{pos:'bottom'}"><i class="uk-icon-home"></i></button>
                    <button class="uk-button" data-ng-class="uk-button-primary" onclick="window.location='@route('/groups')';" title="@lang('Groups')" data-uk-tooltip="{pos:'bottom'}"><i class="uk-icon-book"></i></button>
                    
                </div>

               
            </div>
        </div>
    </nav>
  

    <form class="uk-form" data-ng-show="item">

        <div class="uk-grid">


            <div class="uk-width-3-4">
               

                 <div ng-show="showlang">
                    <div class="uk-float-right">
                        <span ng-repeat="lang in langs" class="uk-button uk-form-file ng-binding" ng-click="changeLang(lang)" style="margin-top:10px;@@currentlang==lang?'background-color:#b8b8b8':''@@">
                            <img ng-src="@base('/assets/images/flags/')@@listCountryFlag[lang]@@.png" />
                        </span>
                    </div>
                    <div style="clear:both"></div>
                </div>
                <div class="app-panel ">
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Title')</label>

                        <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('Name')" data-ng-model="item.name" required autocomplete>
                        <div class="uk-margin-top">                        
                            <input  class="uk-width-1-1 uk-form-blank uk-text-muted" type="text" data-ng-model="item.slug" app-slug="item.name" placeholder="@lang('Slug...')" title="slug" data-uk-tooltip="{pos:'left'}">
                        </div>
                    </div>                   
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('description')</label>
                        <textarea class="uk-width-1-1 uk-form-large" ng-model="item.description"> </textarea>
                    </div>

                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Content')</label>
                        <contentfield options='{"type":"wysiwyg"}' ng-model="item.content"></contentfield>
                        
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-text-small">Script</label>
                        <textarea class="uk-width-1-1 uk-form-large" ng-model="item.script"> </textarea>
                    </div>


                    <div class="uk-form-row" >
                        
                            <button ng-click="save()" class="uk-button uk-button-primary uk-button-large">
                                <div class="app-loading uk-text-center" ng-show="pushloading">
                                    <i class="uk-icon-spinner uk-icon-spin"></i>
                                </div>
                                @@pushloading?'':'Save'@@
                            </button>   
                            <a ng-href="<?php echo $siteurl?>@@item.slug@@/" target="_blank" class="uk-button uk-button-large" data-ng-show="item._id" > @lang('View group')</a>
                            <a href class="uk-button uk-button-large uk-button-danger" ng-click="remove()" data-ng-show="item._id" ><i class="uk-icon-trash-o"></i> @lang('Delete')</a>
                             <button ng-click="save(1)" class="uk-button uk-button-primary uk-button-large">
                                <div class="app-loading uk-text-center" ng-show="pushloading">
                                    <i class="uk-icon-spinner uk-icon-spin"></i>
                                </div>
                                @@pushloading?'':'Save overwrite'@@
                            </button> 
                            
                    </div>
                </div>

                
            </div>
            
            <div class="uk-width-1-4">

                <strong>Feature Image</strong>
                <contentfield options='{"type":"media"}' ng-model="item.featureimage"></contentfield>

               
                <div class="uk-margin-top">
                    <strong >@lang('Group')</strong>
                    <div class="uk-form-controls">
                        <div class="uk-form-select">
                            <i class="uk-icon-sitemap uk-margin-small-right"></i>
                            <a>@@ item.group.name || gname @@</a>
                            <select class="uk-width-1-1 uk-margin-small-top" data-ng-model="item.group" ng-select="(gname=value.name)" ng-options="value.name disable when value.disabled==1 for value in groups track by value._id">
                                
                            </select>
                        </div>
                    </div>
                </div>



                <div class="uk-margin-top">
                    <button type="button" class="uk-button" data-ng-class="item.home?'uk-button-primary':''" data-ng-click="(item.home==1?item.home=0:item.home=1)"><i class="uk-icon-@@item.home?'check-square-o':'square-o'@@"></i>&nbsp;Show Home</button>
                </div>

                <div class="uk-margin-top">
                    <button type="button" class="uk-button" data-ng-class="item.publish?'uk-button-primary':''" data-ng-click="(item.publish==1?item.publish=0:item.publish=1)"><i class="uk-icon-@@item.publish?'check-square-o':'square-o'@@"></i>&nbsp;Publish</button>
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
        $scope.lastautosave=0;     
        $scope.pushloading  =0;
        $scope.showlang=0;
        $scope.currentlang='{{ $currentlang }}';
        $scope.gname='- No group -';
        $scope.items=[];
        $scope.groupitems=[];
        $scope.listCountryFlag=[];
        
        $scope.newItem=function(){
            $scope.id=0;
            $scope.item = {
                publish: 0,
                home:0,
                slug:'undefined',
                description:'',
                lang:$scope.currentlang,
                gid:0
            };
            $scope.items[$scope.currentlang]=$scope.item;
            console.log('created new item');
           
        };

        $scope.changeLang=function(lang){
            console.log('change lange to '+lang);

            if($scope.langs.indexOf(lang)>=0){
                //save old lang
                
                if($scope.currentlang!=lang){
                    $scope.items[$scope.currentlang]=$scope.item;
                    $scope.currentlang=lang;   
                }
                
                if($scope.items[lang]==undefined || $scope.items[lang].name==undefined)
                    $scope.newItem();
                else
                    $scope.item=$scope.items[lang];
                //set groups
                $scope.groups=$scope.grouplangs[lang];
            }
            
        };

        
        $http.post(App.route("/api/"+$scope.moduleName+"/getdata"), {filter: {"_id":$scope.id}}, {responseType:"json"}).success(function(data){
            if (data && Object.keys(data).length) {    

                    $scope.grouplangs = data.groups;
                    $scope.items = data.items;
                    $scope.langs = data.langs;
                    $scope.showlang=data.showlang;  
                    $scope.currentlang=data.currentlang;
                    $scope.listCountryFlag=data.listCountryFlag;    
                                    
                    $scope.changeLang($scope.currentlang);
                     // get groups
            }

        }).error(App.module.callbacks.error.http);


        $scope.save=function(overwrite){
            $scope.items[$scope.currentlang]=$scope.item;
            var data=[];
            for (var key in $scope.items) {
                // skip loop if the property is from prototype
                if (!$scope.items.hasOwnProperty(key)) continue;
                $scope.items[key].lang=key;
                data.push($scope.items[key]);
                
            }
            console.log(data);
            if($scope.pushloading  ==0){
                $scope.pushloading  =1;      
                          
                $http.post(App.route("/api/"+$scope.moduleName+"/save"), {data: data,_id:'{{$id}}',overwrite:overwrite,currentlang:$scope.currentlang}).success(function(data){

                    if (data && Object.keys(data).length) {                        
                        
                        App.notify(data.message, data.result);
                        console.log(data);
                        if(data._id)
                             window.location=App.route("/"+$scope.moduleName+"/item/"+data._id);
                    }
                    $scope.pushloading  =0;

                }).error(function(){$scope.pushloading  =0;App.module.callbacks.error.http();});
            }
            
        };
       

    });
})(jQuery);
    

</script>