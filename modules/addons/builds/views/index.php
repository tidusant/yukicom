@start('header')

@trigger('cockpit.content.fields.sources')
@end('header')
<style>
    
.progress {
  width: 100%;
  height: 20px;
}

.progress-wrap {
  background: #f80;
  margin: 0px 0;
  overflow: hidden;
  position: relative;
}
.progress-wrap .progress-bar {
  background: #ddd;
  left: 0;
  position: absolute;
  top: 0;
}
.flag-item{
    padding:10px;
    cursor: pointer;
}
.flag-item:hover{
    background-color: #dcdcdc;

}
</style>
<div data-ng-controller="{{$moduleName.$action}}" id="{{$moduleName.$action}}" ng-cloak>

  

    <form class="uk-form">
        <div class="app-panel">
            <div class="uk-form-row">
                <label class="uk-text-small">@lang('site title:')</label>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('...')" data-ng-model="host.sitetitle" required autocomplete>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">site description:</label>
                <textarea class="uk-width-1-1 uk-form-large" type="text" placeholder="" data-ng-model="host.sitedescription" required autocomplete></textarea>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">@lang('siteurl:')</label>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('...')" data-ng-model="host.siteurl" required autocomplete>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">@lang('apiurl:')</label>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('...')" data-ng-model="host.apiurl" required autocomplete>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">@lang('siteid:')</label>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('...')" data-ng-model="host.siteid" required autocomplete>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">FBId:</label>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('...')" data-ng-model="host.FBId" required autocomplete>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">Site Languages:</label>
                <span ng-repeat="lang in host.langs">
                    <img ng-src="@base('/assets/images/flags/')@@listCountryFlag[lang]@@.png" /> @@listCountry[lang]@@
                </span>
                <button type="button" ng-click="showLangs()"  class="uk-button uk-button-primary uk-button-small">
                    <div class="app-loading uk-text-center">
                        <i class="uk-icon-plus"></i>
                    </div>                    
                </button>  
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">Default language:</label>
                <select class="uk-width-1-1 uk-margin-small-top" data-ng-model="host.defaultlang" ng-select="(host.defaultlang=lang)" ng-options="listCountry[lang] for lang in host.langs track by lang"></select>
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">@lang('localurl:')</label>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="http://localhost/cockcms" data-ng-model="host.localurl" required autocomplete>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">@lang('hostname:')</label>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('...')" data-ng-model="host.hostname" required autocomplete>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">@lang('username:')</label>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('...')" data-ng-model="host.username" required autocomplete>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">@lang('password:')</label>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('...')" data-ng-model="host.password" required autocomplete>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">num of item on home page (0 is disable):</label>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('0')" data-ng-model="host.noitemhome" required autocomplete>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">num of item in category on home page (0 is disable):</label>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('0')" data-ng-model="host.noitemcathome" required autocomplete>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">num of item relate in page detail:</label>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('0')" data-ng-model="host.norelateitem" required autocomplete>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">num of item per page in cat:</label>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="@lang('0')" data-ng-model="host.noitempercat" required autocomplete>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">array of image size (json format):</label>
                <textarea id="region-template" codearea="{mode:'js', autoCloseTags: true}" class="uk-width-1-1 uk-form-large" style="height:50px !important;" placeholder="@lang('Region code')" data-ng-model="host.arrimgsize"></textarea>
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">menu cat will show on home:</label>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="" data-ng-model="host.catonhome" required autocomplete>                        
            </div>
            <div class="uk-form-row">
                <label class="uk-text-small">custome_async_script:</label>
                <textarea id="region-template" codearea="{mode:'js', autoCloseTags: true}" class="uk-width-1-1 uk-form-large" style="height:450px !important;" placeholder="@lang('Region code')" data-ng-model="host.custom_async_script"></textarea>
                
            </div>

            <div class="uk-form-row">
                <label class="uk-text-small">custom event:</label>
                <textarea id="region-template" codearea="{mode:'js', autoCloseTags: true}" class="uk-width-1-1 uk-form-large" style="height:450px !important;" placeholder="@lang('Region code')" data-ng-model="host.custom_event"></textarea>
                
            </div>

            <div class="uk-form-row">
                <label class="uk-text-small">shortcode:</label>
                <textarea id="region-template" codearea="{mode:'js', autoCloseTags: true}" class="uk-width-1-1 uk-form-large" style="height:450px !important;" placeholder="@lang('Region code')" data-ng-model="host.shortcode"></textarea>
            </div>
            
            <div class="uk-form-row">
                <button type="button" class="uk-button" data-ng-click="(host.live==1?host.live=0:host.live=1)"><i class="uk-icon-@@host.live==0?'square-o':'check-square-o'@@"></i>&nbsp;Is Live</button>         
                <button type="button" class="uk-button" data-ng-click="(host.showlang==1?host.showlang=0:host.showlang=1)"><i class="uk-icon-@@host.showlang==0?'square-o':'check-square-o'@@"></i>&nbsp;Show Lang</button>         
                <button type="button" class="uk-button" data-ng-click="(host.dev==1?host.dev=0:host.dev=1)"><i class="uk-icon-@@host.dev==0?'square-o':'check-square-o'@@"></i>&nbsp;Is Dev</button>         
                <button type="button" class="uk-button" data-ng-click="(host.disable==1?host.disable=0:host.disable=1)"><i class="uk-icon-@@host.disable==0?'square-o':'check-square-o'@@"></i>&nbsp;Is disable</button>         
                <button type="button" class="uk-button" data-ng-click="(host.builddata==1?host.builddata=0:host.builddata=1)"><i class="uk-icon-@@host.builddata==0?'square-o':'check-square-o'@@"></i>&nbsp;Is build data</button>         
                <button type="button" class="uk-button" data-ng-click="(host.debug==1?host.debug=0:host.debug=1)"><i class="uk-icon-@@host.debug==0?'square-o':'check-square-o'@@"></i>&nbsp;Is debug</button>         
                
            </div>
            <div class="uk-form-row" >
                <div class="uk-button-group">
                    <button ng-click="save()" type="button" class="uk-button uk-button-primary uk-button-large">@lang('Save')</button>
                    
                </div>
            </div>
           
        </div>

        <div class="uk-grid">
            <div class="uk-width-1-1">
                    
                    <div class="uk-form-row" >   
                        <button type="button" ng-click="build('buildscript')"  class="uk-button uk-button-primary uk-button-large">
                            <div class="app-loading uk-text-center" ng-show="pushloadingbuildscript">
                                <i class="uk-icon-spinner uk-icon-spin"></i>
                            </div>
                            @@pushloadingbuildscript?'':'Build Script'@@
                        </button>  
                        
                        <button type="button" ng-click="build('buildhome')"  class="uk-button uk-button-primary uk-button-large">
                            <div class="app-loading uk-text-center" ng-show="pushloadingbuildhome">
                                <i class="uk-icon-spinner uk-icon-spin"></i>
                            </div>
                            @@pushloadingbuildhome?'':'Build Home'@@
                        </button>  
                        
                        
                        <button type="button" ng-click="build('buildsitemap')"  class="uk-button uk-button-primary uk-button-large">
                            <div class="app-loading uk-text-center" ng-show="pushloadingbuildsitemap">
                                <i class="uk-icon-spinner uk-icon-spin"></i>
                            </div>
                            @@pushloadingbuildsitemap?'':'Build Sitemap'@@
                        </button>  

                        <button type="button" ng-click="build('createamplink')"  class="uk-button uk-button-primary uk-button-large">
                            <div class="app-loading uk-text-center" ng-show="pushloadingcreateamplink">
                                <i class="uk-icon-spinner uk-icon-spin"></i>
                            </div>
                            @@pushloadingcreateamplink?'':'Create amp link'@@
                        </button>  

                        <br />
                        <br />
                    <?php foreach ($buildModules as $key => $buildModule) {?>
                        
                        <button type="button" ng-click="buildWithStatus('Build Post')"  class="uk-button uk-button-primary uk-button-large">
                            <div class="app-loading uk-text-center" ng-show="pushloading">
                                <i class="uk-icon-spinner uk-icon-spin"></i>
                            </div>
                            @@pushloading?'':'Build {{$buildModule}}'@@
                        </button>    
                        <button type="button" ng-click="buildWithStatus('Build Post Overwrite')"  class="uk-button uk-button-primary uk-button-large">
                            <div class="app-loading uk-text-center" ng-show="pushloading">
                                <i class="uk-icon-spinner uk-icon-spin"></i>
                            </div>
                            @@pushloading?'':'Build {{$buildModule}} Overwrite'@@
                        </button>    
                        <button type="button" ng-click="resetbuild('Build Post')"  class="uk-button uk-button-primary uk-button-large">
                            <div class="app-loading uk-text-center" ng-show="pushloadingresetbuild">
                                <i class="uk-icon-spinner uk-icon-spin"></i>
                            </div>
                            Reset Build @@{{$buildModule}}@@ 
                        </button>                  
                    <?php }?>  
                    <br /></br />
                    <button type="button" ng-click="buildWithStatus('Build Group')"  class="uk-button uk-button-primary uk-button-large">
                        <div class="app-loading uk-text-center" ng-show="pushloading">
                            <i class="uk-icon-spinner uk-icon-spin"></i>
                        </div>
                        @@pushloading?'':'Build Group'@@
                    </button>  
                    <button type="button" ng-click="buildWithStatus('Build Group Overwrite')"  class="uk-button uk-button-primary uk-button-large">
                        <div class="app-loading uk-text-center" ng-show="pushloading">
                            <i class="uk-icon-spinner uk-icon-spin"></i>
                        </div>
                        @@pushloading?'':'Build Group Overwrite'@@
                    </button>      
                    <button type="button" ng-click="resetbuild('Build Group')"  class="uk-button uk-button-primary uk-button-large">
                            <div class="app-loading uk-text-center" ng-show="pushloadingresetbuild">
                                <i class="uk-icon-spinner uk-icon-spin"></i>
                            </div>
                            Reset Build Group
                    </button>   
                    <br /></br />
                    <button type="button" ng-click="buildWithStatus('Build Plugin')"  class="uk-button uk-button-primary uk-button-large">
                        <div class="app-loading uk-text-center" ng-show="pushloading">
                            <i class="uk-icon-spinner uk-icon-spin"></i>
                        </div>
                        @@pushloading?'':'Build Plugin'@@
                    </button>   
                    <button type="button" ng-click="buildWithStatus('Build Plugin Overwrite')"  class="uk-button uk-button-primary uk-button-large">
                        <div class="app-loading uk-text-center" ng-show="pushloading">
                            <i class="uk-icon-spinner uk-icon-spin"></i>
                        </div>
                        @@pushloading?'':'Build Plugin Overwrite'@@
                    </button>   
                    <button type="button" ng-click="resetbuild('Build Plugin')"  class="uk-button uk-button-primary uk-button-large">
                            <div class="app-loading uk-text-center" ng-show="pushloadingresetbuild">
                                <i class="uk-icon-spinner uk-icon-spin"></i>
                            </div>
                            Reset Build Plugin
                    </button>   
                    <br /><br />
                    <button type="button" ng-click="syncApiData()"  class="uk-button uk-button-primary uk-button-large">
                            <div class="app-loading uk-text-center" ng-show="pushloadingresetbuild">
                                <i class="uk-icon-spinner uk-icon-spin"></i>
                            </div>
                            syncApiData
                    </button>   
                </div>                
            </div>            
        </div>           
       
    </form>

    <div style="display:none;overflow:scroll;height:300px;" id="processinfo"></div>

    <div style="display:none;overflow:scroll;height:300px;" id="languages-container">
        <div ng-repeat="(key,value) in listCountry track by key" class="flag-item" id="flags-item-@@key@@">
            <img ng-src="@base('/assets/images/flags/')@@listCountryFlag[key]@@.png" data="@@key@@" onclick="addLang(this)" /> @@value@@
        </div>
    </div>

</div>



<script>
var userlistmodel,es,countrylist;
(function($){

    App.module.controller("{{$moduleName.$action}}", function($scope, $rootScope, $http, $timeout){
        $scope.moduleName='{{$moduleName}}';
        $scope.action='{{$action}}';
        $scope.controllerName=$scope.moduleName+$scope.action;
        $scope.dev=1;
        $scope.disable=1;
        $scope.isdata=0;
        $scope.isbuildscript=0;
        $scope.pushloading  =0;
        $scope.listCountry=[];
        $scope.listCountryFlag=[];
        //get host
        $http.post(App.route("/api/"+$scope.moduleName+"/getHost")).success(function(data){
                $scope.host=data.host;
                $scope.listCountry=data.listCountry;
                $scope.listCountryFlag=data.listCountryFlag;
                
            }).error(App.module.callbacks.error.http);


        //function

        $scope.buildWithStatus = function(module) {
            var loading=eval('$scope.pushloading');
            
            if(loading==undefined){
                eval('$scope.pushloading=0;');
                loading=0;
            }
            if(loading ==0){
                eval('$scope.pushloading=1;');
                
                if(loading==0){
                    //show popup 
                    //userlistmodel=$.UIkit.modal('#processinfo');
                    //userlistmodel.show();        
                    userlistmodel=App.Ui.dialog($("#processinfo").html(),{bgclose:true});                                        

                    userlistmodel.dialog.height(($(window).height()-200));
                    userlistmodel.dialog.css('overflow-y','scroll');
                    //set event

                    var buttonstopHtml='<div class="uk-modal-dialog" style="margin:-45px auto;padding:5px;">';
                    buttonstopHtml+='<div style="float:left"><button type="button" onclick="stopbuild(\''+module+'\')"  class="uk-button uk-button-primary uk-button-large" id="buttonStopBuild">Stop</button></div>';
                    buttonstopHtml+='<div class="progress-wrap progress" data-progress-percent="25">  <div class="progress-bar progress"></div></div>';
                    buttonstopHtml+='<div style="float:left;margin-left:10px" id="processMessage"></div>';
                    buttonstopHtml+='<div style="clear:both"></div>';
                    buttonstopHtml+='</div>';
                    userlistmodel.dialog.after($(buttonstopHtml));
                    //set process bar:
                    var processbar=$('.progress-wrap');
                    processbar.width(userlistmodel.dialog.width()-$('#buttonStopBuild').width()-15);
                    console.log(module);
                    var urlpost=App.route("/api/builds/buildpost?buildname="+module);
                    if(module.indexOf("Build Plugin")!=-1)urlpost=App.route("/api/plugins/build?buildname="+module);
                    es = new EventSource(urlpost);
                    es.addEventListener('message', function(e) {
                        var result = JSON.parse( e.data );
                        if(e.lastEventId == 'CLOSE') {
                            addLog('Received CLOSE closing');
                            es.close();
                        } 
                        else if(e.lastEventId=='STOP')  {
                            addLog(result.message);
                            es.close();
                            eval('$scope.pushloading=0;');
                             $scope.$apply();
                        }
                        else{
                            
                            addLog(result.message);
                            showProcess(result.processText);
                        }
                        
                    });
                    es.addEventListener('error', function(e) {
                        
                        addLog("<span style='color:#f00'>Error</span>");
                        es.close();
                    });
                }
                else{
                    userlistmodel.dialog.show();
                }
            }
        }
         
        $scope.build = function(module) {
            
            var loading=eval('$scope.pushloading'+module);
            
            if(loading==undefined){
                eval('$scope.pushloading'+module+'=0;');
                loading=0;
            }
            if(loading ==0){
                eval('$scope.pushloading'+module+'=1;');
                
                
                $http.post(App.route("/api/builds/build"), {module:module,isbuilddata:$scope.isdata}).success(function(data){
                    App.notify(App.i18n.get("item saved!"), "success");
                    eval('$scope.pushloading'+module+'=0;');
                }).error(function(){eval('$scope.pushloading'+module+'=0;');App.module.callbacks.error.http();});
                
            }

        
            
        };

       
        $scope.resetbuild = function(resetname) {
            var loading=$scope.pushloadingresetbuild;
            
            if(loading==undefined){
                eval('$scope.pushloadingresetbuild=0;');
                loading=0;
            }
            if(loading ==0){
                $http.post(App.route("/api/builds/resetbuild"), {buildname:resetname}).success(function(data){
                    App.notify(App.i18n.get("item saved!"), "success");
                    eval('$scope.pushloading=0;');
                }).error(App.module.callbacks.error.http);
            }
        };
        $scope.syncApiData = function() {
            var loading=$scope.pushloadingresetbuild;
            
            if(loading==undefined){
                eval('$scope.pushloadingresetbuild=0;');
                loading=0;
            }
            if(loading ==0){
                $http.post(App.route("/api/builds/syncApiData"), {}).success(function(data){
                    App.notify(App.i18n.get("item saved!"), "success");
                    eval('$scope.pushloading=0;');
                }).error(App.module.callbacks.error.http);
            }
        };
        $scope.save = function() {
            if($scope.host.live==undefined)$scope.host.live=0;
            
            $http.post(App.route("/api/"+$scope.moduleName+"/saveHost"), {host:$scope.host}).success(function(data){
                App.notify(App.i18n.get("item saved!"), "success");
            }).error(App.module.callbacks.error.http);
        };

        $scope.showLangs = function(){
            countrylist=App.Ui.dialog($("#languages-container").html(),{bgclose:true});   
            countrylist.dialog.height(($(window).height()-200));
            countrylist.dialog.css('overflow-y','scroll');
        };

        $scope.addLang=function(v){
            console.log($scope.host.langs);
            $scope.host.langs.push($(v).attr("data"));
            console.log($scope.host.langs);

            $('#flags-item-'+$(v).attr("data")).remove();
            countrylist.hide();
            $scope.$apply();
        };

       

    });
})(jQuery);

function addLang(v){
    
    angular.element(document.getElementById('{{$moduleName.$action}}')).scope().addLang(v);    
}
function stopbuild(module) 
{
    $('#buttonStopBuild').attr('disabled','true');
    $.post(App.route("/api/builds/stopbuild?buildname="+module)).success(function(data){});    
}
function stopTask() {
    es.close();
    addLog('Interrupted');
}
function showProcess(message) {
    if(message!=''){
        var percent=0;
        percent=eval(message);
        $('.progress-wrap').attr('data-progress-percent',percent*100);
        moveProgressBar();
        var msgs=message.split('+')[0];
        $('#processMessage').html(msgs);
    }
}
function addLog(message) {
    userlistmodel.dialog[0].children[0].innerHTML+=message;
    userlistmodel.dialog[0].scrollTop = userlistmodel.dialog[0].scrollHeight;    
}

// on page load...

  
    // SIGNATURE PROGRESS
    function moveProgressBar() {
      
        var getPercent = ($('.progress-wrap').attr('data-progress-percent') / 100);        
        var getProgressWrapWidth = $('.progress-wrap').width();
        var progressTotal = getPercent * getProgressWrapWidth;
        var animationLength = 250;
        
        // on page load, animate percentage bar to data percentage length
        // .stop() used to prevent animation queueing
        $('.progress-bar').stop().animate({
            left: progressTotal
        }, animationLength);
    }
</script>