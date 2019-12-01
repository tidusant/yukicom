
var debug=debug||0;

function a(u,f,s,h,p){
    "use strict";
    var x;
    var host=u.indexOf('http://')>-1?u:'';
    var o=u;
    var par=window.location.href.split('?');
    s=s!=undefined?s:par[0].replace(siteurl,"");
    if(par.length>1)setCookie("cid",par[1]);
    
    if(getCookie("r").length==0){
      var ref=document.referrer;
      if(ref.length==0)setCookie("r",-1);
      else if(ref.indexOf("www.facebook.com")>0)setCookie("r",1);
      else if(ref.indexOf("www.google.com")>0)setCookie("r",2);
      else setCookie("r",0);
    }
    if (window.XMLHttpRequest)
    {
        x=new XMLHttpRequest();
    }
    else
    {
        x=new ActiveXObject("Microsoft.XMLHttpRequest");
    }
    x.onreadystatechange=function()
    {
        if (x.readyState==4 && x.status==200){ 
            
            if(o=='jquery.js'){
                f(x.responseText);
            }
            else {               
                f(b(t(x.responseText,s,h)));        
            }
        }
    };
    s=(s=="")?siteurl:s;
    
    if(debug){
        var url=''+s+u;    
        console.log('begin request '+url);
    }
    s=(s+u).replace(/[\/\.:]/g,'');
    /*s="homepageurl";*/
    /* base 64 */
    
    u=e(s.replace(/[\-]/g,'')).replace(/[=]/g,'');
    
    
    var result1 = [];
    var result2 = [];
    for (var i = u.length-1; i >=0; i--) {
        if(i%3==0)result1.push(u.charAt(i));
        else result2.push(u.charAt(i));
    }
    u=result1.join("")+result2.join("");
    u=u.substring(0,1).toLowerCase()+u.substring(1);
    var url=(host==""?siteurl+u:host)+"?v="+sitev;
    
    if(debug){        
        console.log(''+url);
    }
    var m=p!=undefined?"POST":"GET";    
    x.open(m,url,true);
    if(m=="POST")x.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    x.send(p!=undefined?p:'');
}
var sitev="";
var keyStr = "ABCDEFGHIJKLMNOP" +
               "QRSTUVWXYZabcdef" +
               "ghijklmnopqrstuv" +
               "wxyz0123456789+/" +
               "=";

  function e(input) {
     input = escape(input);
     var output = "";
     var chr1, chr2, chr3 = "";
     var enc1, enc2, enc3, enc4 = "";
     var i = 0;

     do {
        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if (isNaN(chr2)) {
           enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
           enc4 = 64;
        }

        output = output +
           keyStr.charAt(enc1) +
           keyStr.charAt(enc2) +
           keyStr.charAt(enc3) +
           keyStr.charAt(enc4);
        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";
     } while (i < input.length);

     return output;
  }

  function d(input) {
     var output = "";
     var chr1, chr2, chr3 = "";
     var enc1, enc2, enc3, enc4 = "";
     var i = 0;

     var base64test = /[^A-Za-z0-9\+\/\=]/g;
     if (base64test.exec(input)) {
        alert("There were invalid base64 characters in the input text.\n" +
              "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
              "Expect errors in decoding.");
     }
     input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
     
     do {
        enc1 = keyStr.indexOf(input.charAt(i++));
        enc2 = keyStr.indexOf(input.charAt(i++));
        enc3 = keyStr.indexOf(input.charAt(i++));
        enc4 = keyStr.indexOf(input.charAt(i++));

        chr1 = (enc1 << 2) | (enc2 >> 4);
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
        chr3 = ((enc3 & 3) << 6) | enc4;

        output = output + String.fromCharCode(chr1);

        if (enc3 != 64) {
           output = output + String.fromCharCode(chr2);
        }
        if (enc4 != 64) {
           output = output + String.fromCharCode(chr3);
        }

        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";

     } while (i < input.length);

     return unescape(output);
  }
function b(s){
    if(isNaN(s) && s!=""){
        for (var property in sitesizet) {           
            var re = new RegExp("\\[\\[sitesize."+property+"\\]\\]", "g");        
            s=s.replace(re,sitesizet[property]);
        } 

        s=s.replace(/\[\[siteurl\]\]/g, siteurl).replace(/\{\{siteurl\}\}/g, siteurl);
    }
    return s;
}
a("web.data",function(rs){
    sitev=rs.substr(10,1)+rs.substr(4,1)+rs.substr(12,1)+rs.substr(7,1)+rs.substr(15,1);

    a("style"+document.documentElement.lang+".js",function(rs){
        document.head.innerHTML=document.head.innerHTML+"<style type=\"text/css\">"+rs+"</style>";
        a("cache.js",function(rs){
            {{custom_async_script}}            
            document.body.innerHTML=rs.replace(new RegExp(localurl,"g"),siteurl); 
            a("jquery.js",function(rs){            
                eval(rs);
                a("script"+document.documentElement.lang+".js",function(rs){
                    eval(rs);
                    {{morescript}}
                },siteurl);           
            },siteurl);       
        },undefined,1);
    },siteurl);
},siteurl);

function setCookie(cname, cvalue, time) {
    
    if(time==undefined)time=24*60*60;
    var d = new Date();
    d.setTime(d.getTime() + time*1000);
    var expires = "expires="+ d.toUTCString();
    
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length,c.length);
        }
    }
    return "";
}

function addCart(code){
    var itemcode=code;
    if(itemcode==undefined){
        itemcode=$('#itemcode');
        if(itemcode.length>0)itemcode=itemcode.html();
        else return;
    }
    
    var cart=getCart();
    var isAdded=false;
    cart.cics.forEach(function(code,index){
        if(code==itemcode){
            isAdded=true;
            cart.cins[index]=cart.cins[index]*1+1;
        }
    });
    if(!isAdded){        
        cart.cics.push(itemcode);
        cart.cins.push(1);
    }
    saveCart(cart);
    
    /*window.location=siteurl+'order/';*/
}
function removeCart(code){
    var itemcode=code;
    var cart=getCart();
    var isAdded=false;
    cart.cics.forEach(function(code,index){
        if(code==itemcode){
            cart.cins.splice(index,1);
            cart.cics.splice(index,1);            
        }
    });
    saveCart(cart); 
    
    /*window.location=siteurl+'order/';*/
}
function modCart(itemcode,num){
  var cart=getCart();
    var isMod=false;
    for(i=0;i<cart.cins.length;i++){
        if(cart.cics[i]==itemcode){            
            cart.cins[i]=num;            
            isMod=true;
            break;
        }
    }
    if(isMod){        
        saveCart(cart);  
           
    }
}



function validateEmail(email) {    
    var atpos = email.indexOf("@");
    var dotpos = email.lastIndexOf(".");
    if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length) {        
        return false;
    }
    return true;
}
function validatePhone(phone) {    
    phone=phone.replace(/[^0-9]/g, "");
    if(phone=="" || !phone)return false;
    return (phone.substr(0,2)+""=="09" && phone.length==10) || (phone.substr(0,2)+""=="01" && phone.length==11);
}

function saveCart(cart){  
  var cic='';
  for(i=0;i<cart.cics.length;i++){
    cic+=cart.cics[i]+cart.cins[i];
  }
  setCookie('cic',cic);  
}
function getCart(name){
  var cart={};
  var cic=getCookie('cic');
  cart.cics=[];
  cart.cins=[];  
  if(!cic)return cart;
  do{
    var code=cic.substring(0,3);
    cic=cic.substring(3);

    var value='';
    var nextchar=cic.substring(0,1);    
    while(nextchar%1===0 && cic.length>0){
      cic=cic.substring(1);
      value+=nextchar+'';
      nextchar=cic.substring(0,1);      
    }    
    cart.cics.push(code);
    cart.cins.push(value);
  }
  while (cic.length>0);  
  return cart;
}
function myatob(data){
  return JSON.parse(decodeURIComponent(window.atob(data)));
}
function mybtoa(data){
  return window.btoa(encodeURIComponent(JSON.stringify(data))).replace(/=/g,'');
}
function makeid(num)
{
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < num; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

function numberWithCommas(x) {
    var parts = x.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return parts.join(",");
}
function shuffle(array) {
    var counter = array.length;

    // While there are elements in the array
    while (counter > 0) {
        // Pick a random index
        var index = Math.floor(Math.random() * counter);

        // Decrease counter by 1
        counter--;

        // And swap the last element with it
        var temp = array[counter];
        array[counter] = array[index];
        array[index] = temp;
    }

    return array;
}


function utf8_decode (strData) { 

  var tmpArr = [];
  var i = 0;
  var c1 = 0;
  var seqlen = 0;

  strData += '';

  while (i < strData.length) {
    c1 = strData.charCodeAt(i) & 0xFF;
    seqlen = 0;

    /* http://en.wikipedia.org/wiki/UTF-8#Codepage_layout*/
    if (c1 <= 0xBF) {
      c1 = (c1 & 0x7F);
      seqlen = 1;
    } else if (c1 <= 0xDF) {
      c1 = (c1 & 0x1F);
      seqlen = 2;
    } else if (c1 <= 0xEF) {
      c1 = (c1 & 0x0F);
      seqlen = 3;
    } else {
      c1 = (c1 & 0x07);
      seqlen = 4;
    }

    for (var ai = 1; ai < seqlen; ++ai) {
      c1 = ((c1 << 0x06) | (strData.charCodeAt(ai + i) & 0x3F));
    }

    if (seqlen === 4) {
      c1 -= 0x10000;
      tmpArr.push(String.fromCharCode(0xD800 | ((c1 >> 10) & 0x3FF)));
      tmpArr.push(String.fromCharCode(0xDC00 | (c1 & 0x3FF)));
    } else {
      tmpArr.push(String.fromCharCode(c1));
    }

    i += seqlen;
  }

  return tmpArr.join('');
}

function utf8_encode (argString) { 
  if (argString === null || typeof argString === 'undefined') {
    return '';
  }

  var string = (argString + '');
  var utftext = '';
  var start;
  var end;
  var stringl = 0;

  start = end = 0;
  stringl = string.length;
  for (var n = 0; n < stringl; n++) {
    var c1 = string.charCodeAt(n);
    var enc = null;

    if (c1 < 128) {
      end++;
    } else if (c1 > 127 && c1 < 2048) {
      enc = String.fromCharCode(
        (c1 >> 6) | 192, (c1 & 63) | 128
      );
    } else if ((c1 & 0xF800) !== 0xD800) {
      enc = String.fromCharCode(
        (c1 >> 12) | 224, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128
      );
    } else {
     
      if ((c1 & 0xFC00) !== 0xD800) {
        throw new RangeError('Unmatched trail surrogate at ' + n);
      }
      var c2 = string.charCodeAt(++n);
      if ((c2 & 0xFC00) !== 0xDC00) {
        throw new RangeError('Unmatched lead surrogate at ' + (n - 1));
      }
      c1 = ((c1 & 0x3FF) << 10) + (c2 & 0x3FF) + 0x10000;
      enc = String.fromCharCode(
        (c1 >> 18) | 240, ((c1 >> 12) & 63) | 128, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128
      );
    }
    if (enc !== null) {
      if (end > start) {
        utftext += string.slice(start, end);
      }
      utftext += enc;
      start = end = n + 1;
    }
  }

  if (end > start) {
    utftext += string.slice(start, stringl);
  }

  return utftext;
}