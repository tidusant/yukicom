@start('header')
{{ $app->assets(['assets:vendor/fuzzysearch.js']) }}
    @trigger('cockpit.content.fields.sources')
@end('header')

<div data-ng-controller="{{$moduleName.$action}}" data-id="{{ $id }}" ng-cloak>

    <form class="uk-form" data-ng-submit="save()" data-ng-show="item">

        <div class="uk-grid">


            <div class="uk-width-3-4">
                <div data-ng-show="autosaveid">
                   <a style="color:#f00" ng-click="revertAutoSave()" title="Click to revert" data-uk-tooltip="{pos:'left'}"> Auto save at @@lastautosave | fmtdate:'h:i a - d M, Y'@@
                   </a>
                </div>

                <div ng-show="showlang">
                    <div class="uk-float-right">
                        <span ng-repeat="lang in langs" ng-click="changeLang(lang)" class="uk-button uk-form-file ng-binding" style="margin-top:10px;@@currentlang==lang?'background-color:#b8b8b8':''@@">
                            <img ng-src="@base('/assets/images/flags/')@@listCountryFlag[lang]@@.png" />
                        </span>
                    </div>
                    <div style="clear:both"></div>
                </div>




                <div data-ng-show="item.source">
                   Nguồn: <a href="@@item.source@@" target="_blank"> @@item.source@@</a>
                </div>
                <div class="app-panel ">
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Title')</label>
                        <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('Name')" data-ng-model="item.title" required autocomplete>
                        <div class="uk-margin-top">                        
                            <input  class="uk-width-1-1 uk-form-blank uk-text-muted" type="text" data-ng-model="item.slug" app-slug="item.title" placeholder="@lang('Slug...')" title="slug" data-uk-tooltip="{pos:'left'}">
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
                        <textarea id="region-template" codearea="{mode:'js', autoCloseTags: true}" class="uk-width-1-1 uk-form-large" style="height:100px !important;" placeholder="@lang('Region code')" data-ng-model="item.script"></textarea>

                    </div>
                    <div class="uk-form-row">
                        <label class="uk-text-small">Relate post</label>
                        <div id="relatepost" class="uk-autocomplete">
                            <div id="acList_relatepost" style="float:left;margin-left:10px;"></div>                                
                            <div style="margin-left:10px;margin-top:10px;"><input class="uk-search-field" type="text" placeholder="..." data-uk-tooltip title="@lang('relate post...')"></div>                                
                            <div class="uk-dropdown uk-dropdown-flip"></div>
                        </div>
                    </div>    



                    <div class="uk-form-row" >
                        
                            <button type="submit" class="uk-button uk-button-primary uk-button-large">
                                <div class="app-loading uk-text-center" ng-show="pushloading">
                                    <i class="uk-icon-spinner uk-icon-spin"></i>
                                </div>
                                @@pushloading?'':'Save'@@
                            </button>   
                            <a ng-href="<?php echo $siteurl?>@@item.slug@@/" target="_blank" class="uk-button uk-button-large" data-ng-show="item._id" > @lang('View post')</a>                         
                            <!-- <button type="button" ng-click="saveDraft()" id="btnSaveDraft" class="uk-button uk-button-primary uk-button-large" ng-show="item.isdraft || !id" >@lang('Save Draft')</button> -->
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
                    <strong >Post day</strong>
                    <div class="uk-form-controls">
                        <div class="uk-form-select">
                            <contentfield options='{"type":"date","attr":"readonly=\"true\" required"}' data-ng-model="item.startdate"></contentfield>
                            <contentfield options='{"type":"time","attr":"readonly=\"true\" required"}' data-ng-model="item.starttime"></contentfield>
                        </div>
                    </div>
                </div>

                

              

                <div class="uk-margin-top">
                    <strong >@lang('MicroData')</strong>
                    <div class="uk-form-controls">
                        <div class="uk-form-select">
                            <i class="uk-icon-sitemap uk-margin-small-right"></i>
                            <a>@@ item.microdata || '- @lang("No data") -' @@</a>
                            <select class="uk-width-1-1 uk-margin-small-top" ng-change="showMicro()" data-ng-model="item.microdata">
                                <option value="Article">Article</option>
                                <option value="Video">Video</option>
                                <option value="Product">Product</option>
                                <option value="">- @lang("No data") -</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="uk-margin-top">
                    <button type="button" class="uk-button" data-ng-class="item.home?'uk-button-primary':''" data-ng-click="(item.home==1?item.home=0:item.home=1)"><i class="uk-icon-@@item.home?'check-square-o':'square-o'@@"></i>&nbsp;Show Home</button>
                </div>

                <div class="uk-margin-top">
                    <button type="button" class="uk-button" data-ng-class="item.best?'uk-button-primary':''" data-ng-click="(item.best==1?item.best=0:item.best=1)"><i class="uk-icon-@@item.best?'check-square-o':'square-o'@@"></i>&nbsp;Best</button>
                </div>

                <div class="uk-margin-top">
                    <button type="button" class="uk-button" data-ng-class="item.publish?'uk-button-primary':''" data-ng-click="(item.publish==1?item.publish=0:item.publish=1)"><i class="uk-icon-@@item.publish?'check-square-o':'square-o'@@"></i>&nbsp;Publish</button>
                </div>
                

            </div>
        </div>
        <div class="uk-grid">
            <div class="uk-width-3-4" id="microdatacontent">
            <strong >@lang('MicroData')</strong>
                <div class="app-panel" ng-show="item.microdata=='Article'">
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Author Name')</label>
                        <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('Author Name')" data-ng-model="item.authorname">
                        
                    </div>                   
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Author Link')</label>
                        <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('Author Link')" data-ng-model="item.authorlink">
                    </div>

                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Tag')</label>
                        <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('Tag')" data-ng-model="item.tag">
                        
                    </div>
                    <div class="uk-form-row" >
                        <div class="uk-button-group">
                            <button type="submit" class="uk-button uk-button-primary uk-button-large">
                                <div class="app-loading uk-text-center" ng-show="pushloading">
                                    <i class="uk-icon-spinner uk-icon-spin"></i>
                                </div>
                                @@pushloading?'':'Save'@@
                            </button>                               
                        </div>
                    </div>
                </div>

                <div class="app-panel" ng-show="item.microdata=='Video'">
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('Duration')</label>
                        <input class="uk-width-1-1 uk-form-large" type="text" data-ng-model="item.duration">
                        
                    </div>                   
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('embedUrl')</label>
                        <input class="uk-width-1-1 uk-form-large" type="text" data-ng-model="item.embedUrl">
                    </div>

                    <div class="uk-form-row" >
                        <div class="uk-button-group">
                            <button type="submit" class="uk-button uk-button-primary uk-button-large">
                                <div class="app-loading uk-text-center" ng-show="pushloading">
                                    <i class="uk-icon-spinner uk-icon-spin"></i>
                                </div>
                                @@pushloading?'':'Save'@@
                            </button>                                
                        </div>
                    </div>
                </div>

                <div class="app-panel" ng-show="item.microdata=='Product'">
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('price')</label>
                        <input class="uk-width-1-1 uk-form-large" type="text" data-ng-model="item.price">
                        
                    </div>    
                    <div class="uk-form-row">
                        <label class="uk-text-small">@lang('base price')</label>
                        <input class="uk-width-1-1 uk-form-large" type="text" data-ng-model="item.baseprice">                        
                    </div>                   
                  

                    <div class="uk-form-row" >
                        <div class="uk-button-group">
                            <button type="submit" class="uk-button uk-button-primary uk-button-large">
                                <div class="app-loading uk-text-center" ng-show="pushloading">
                                    <i class="uk-icon-spinner uk-icon-spin"></i>
                                </div>
                                @@pushloading?'':'Save'@@
                            </button>                                   
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </form>
</div>
<script>
var autosavetimer;
(function($){

    App.module.controller("{{$moduleName.$action}}", function($scope, $rootScope, $http, $timeout, Contentfields,$filter){
        $scope.moduleName='{{$moduleName}}';
        $scope.action='{{$action}}';
        $scope.controllerName=$scope.moduleName+$scope.action;
        $scope.id = $("[data-ng-controller='"+$scope.controllerName+"']").data("id");
        


        $scope.lastautosave=0;     
        $scope.pushloading  =0;
        $scope.gname='- No group -';
        $scope.items=[];
        $scope.acitems=[];
        $scope.acitemsindex=[];
        $scope.groupitems=[];
        $scope.listCountryFlag=[];

        
        

        $scope.newItem=function(){
            $scope.id=0;
            $scope.item = {
                publish: 0,
                home:0,
                slug:'undefined',
                description:'',
                content:'',
                lang:$scope.currentlang,
                gid:0
            };
            $scope.items[$scope.currentlang]=$scope.item;
            
           
        };

        $scope.changeLang=function(lang){
            

            if($scope.langs.indexOf(lang)>=0){
                //save old lang
                
                if($scope.currentlang!=lang){
                    $scope.items[$scope.currentlang]=$scope.item;
                    $scope.currentlang=lang;   
                }
                
                if($scope.items[lang]==undefined || $scope.items[lang].title==undefined)
                    $scope.newItem();
                else{
                    $scope.item=$scope.items[lang];
                    $scope.olditem = angular.copy($scope.items[lang]); 
                    $scope.id=$scope.item._id;
                }
                if(!$scope.item.startdate){
                    var date=new Date()
                    $scope.item.startdate=date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate();        
                    $scope.item.starttime="10:00";
                }
                
                 //get auto save:
                $http.post(App.route("/api/"+$scope.moduleName+"/getAutoSave"), {pid: $scope.id}).success(function(data){
                    if (data && Object.keys(data).length) {
                        $scope.lastautosave = data.modified;                    
                        $scope.autosaveid = data._id;
                        
                        //$scope.olditem = angular.copy(data.item); 
                    }
                }).error(App.module.callbacks.error.http);
                autosavetimer=setInterval(function(){$scope.olditem.content=angular.copy($scope.item.content);$scope.autoSave();},10000);
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
            
            if($scope.pushloading  ==0){
                $scope.pushloading  =1;      
                          
                $http.post(App.route("/api/"+$scope.moduleName+"/save"), {data: data,_id:'{{$id}}',overwrite:overwrite,currentlang:$scope.currentlang}).success(function(data){

                    if (data && Object.keys(data).length) {                        
                        
                        App.notify(data.message, data.result);
                        
                        if(data._id)
                             window.location=App.route("/"+$scope.moduleName+"/item/"+data._id);
                    }
                    $scope.pushloading  =0;

                }).error(function(){$scope.pushloading  =0;App.module.callbacks.error.http();});
            }
            
        };
       
       
        
      

        $scope.autoSave = function() {
            
            if(!angular.equals($scope.olditem,$scope.item)){ 
                //console.log($scope.olditem);
                //console.log($scope.item);
                //enable save draft
                $('#btnSaveDraft').removeAttr('disabled');
                $scope.olditem = angular.copy($scope.item); 
                $http.post(App.route("/api/"+$scope.moduleName+"/autosave"), {item: $scope.item}).success(function(data){
                    if (data && Object.keys(data).length) {
                        $scope.lastautosave = data.modified;                    
                        $scope.autosaveid = data._id; 
                    }
                }).error(App.module.callbacks.error.http);
            }
        };

        $scope.revertAutoSave = function() {           
            $http.post(App.route("/api/"+$scope.moduleName+"/revertAutoSave"), {id: $scope.autosaveid}).success(function(data){
                
                if (data && Object.keys(data).length) {
                    $scope.item = data.item;
                    $scope.olditem = angular.copy(data.item);  
                    $scope.autosaveid = 0;
                    App.notify("Done!!!", "success");
                    clearInterval(autosavetimer);  
                    autosavetimer=setInterval(function(){$scope.autoSave();},3000);              
                }
            }).error(App.module.callbacks.error.http);
        };


        

        $scope.showMicro = function() {
             $('html, body').animate({
                'scrollTop':   $('#microdatacontent').offset().top 
            }, 400);
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

       
       
        

        $scope.remove = function(){            

                App.Ui.confirm(App.i18n.get("Are you sure to delete "+$scope.item.title+"</b>?"), function() {

                    $http.post(App.route("/api/"+$scope.moduleName+"/remove"), { "id": $scope.item._id }, {responseType:"json"}).success(function(data){
                        if(data.error){
                            App.notify(data.error.message, "danger");
                        }
                        else{
                            location='@route("/")';
                        }
                    }).error(App.module.callbacks.error.http);
                });
            
        };



        //====================auto complete
        var acFieldCount=0;               
       var acDivId='relatepost';
       $http.post(App.route("/api/"+$scope.moduleName+"/getAllProds"), {currentlang:$scope.currentlang}).success(function(data){
            eval('$scope.ac'+acDivId+' = data.items;');
            eval('$scope.ac'+acDivId+'index = data.itemsindex;');
            
            //init autocomplete search
            eval('setupAutoCompleteField(acDivId,$scope.ac'+acDivId+');');
           //init item detail:
           initAutoCompleteOldData(acDivId);   
        }).error(App.module.callbacks.error.http);

                   
        function setupAutoCompleteField(divId,datas){
            //init old data
            //console.log(datas);
            
            
            var autocompleteField = $.UIkit.autocomplete('#'+divId, {minLength:1,
                source: function(release) {

                    var data = FuzzySearch.filter( datas, autocompleteField.input.val(), {key:'title', maxResults: 10});                    
                    release(data);
                },
                renderer: function(data) {
                    if (data && data.length) {
                        var lst      = $('<ul class="uk-nav uk-nav-autocomplete uk-autocomplete-results">'),
                            usericon = '<i class="uk-icon-user uk-text-small"></i>',                            
                            li;

                        data.forEach(function(item){
                            li = $('<li style="clear:both"><a><img style="float: left;margin-right: 2px;margin-top: -5px;" src="@route('/mediamanager/thumbnail')/'+$filter("base64")(item.featureimage)+'/50/50" /><strong> &nbsp;'+item.title+'</strong><div class="uk-text-truncate"> </div></a></li>').data(item);
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
                addAutoCompleteHtml(divId,data.slug);
                //hidden value to submit:
                addAutoCompleteData(divId,data.slug);
            });
        }         

        function initAutoCompleteOldData(divId){          
            eval('var '+divId+'=$scope.item.'+divId+'||JSON.parse("[]");for(uid in '+divId+'){addAutoCompleteHtml(divId,'+divId+'[uid]);}');
            
            
            
        }
        function addAutoCompleteHtml(divId,uid){
            eval('var index=$scope.ac'+divId+'index.indexOf(uid);var item=$scope.ac'+divId+'[index];');
            console.log(item);
            if(index==-1)return false;
            var newdiv=$('<div style="margin-left:10px;margin-top:10px;"><img style="float: left;margin-right: 2px;margin-top: -5px;" src="@route('/mediamanager/thumbnail')/'+$filter("base64")(item.featureimage)+'/50/50" />'+item.title+'&nbsp;<a  class="uk-icon-times" style="color:red"></a></div><div style="clear:both"></div>');     
            console.log(newdiv);
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

    });
})(jQuery);
    

</script>