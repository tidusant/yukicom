var clickcount=0;
var isShowLike=0;
function isAvailable(){
	return typeof(SiteTitle)!='undefined';
}
function clickIE() { if (document.all) {  return isAvailable(); } }
function clickNS(e) {
	if(!isAvailable()){
		if(document.layers || (document.getElementById && !document.all)) {
			if (e.which == 2 || e.which == 3) {
				//showlike();
				
				return dOClick();
			}
		}
	}
}

//disable on click
if (document.layers)
{ document.captureEvents(Event.MOUSEDOWN); document.onmousedown = clickNS; }
else { document.onmouseup = clickNS; document.oncontextmenu = clickIE; }
 //document.onmousedown = isAvailable;


//disable keydown
document.onkeydown = function (e) {
     var e = e || event;

     //if (e.keyCode != 116 && e.keyCode!=13 && !isAvailable()) {
     	if ((e.ctrlKey ||e.altKey) && !isAvailable()) {
     		//console.log(e.keyCode);
          return false;
     }
};

//for disable select option
document.onselectstart = isAvailable();
function dOClick() { 	
	clickcount++;	
	if(clickcount>=3 && !isAvailable()){
		//toggleOverlay();
		$('#likecontent').popup('show');		
		if(isShowLike!=1){
			ga('send', 'event', 'rightclick', 'show');
			isShowLike=1;
		}
	}
	return false; 
}
document.oncontextmenu =function(){return isAvailable();};


function setDisable(disable){
	if(disable){
		SiteTitle=undefined;
		$('body').attr('oncontextmenu','return false');
		$('body').attr('onselectstart','return false');
	}
	else{
		SiteTitle=sitetitle;
		$('body').removeAttr('oncontextmenu');
		$('body').removeAttr('onselectstart');
	}
}