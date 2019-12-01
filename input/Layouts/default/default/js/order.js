
if(document.getElementById('likecontent'))
  document.getElementById('likecontent').style.display='none';
checkcart();

function checkcart(){

  try{
    /*check code*/
    var cid=getCookie('cid');
    var ccontent='';
    var c,x = Math.floor((Math.random() * 9) + 1);
    var detailurl="cidinfo";
    detailurl=c=encDat2(detailurl,9);
    /*
    detailurl=e(detailurl).replace(/=/g,"")+makeid(x);
    var result1 = [];
    var result2 = [];
    for (i = detailurl.length-1; i >=0; i--) {
        if(i%x==0)result1.push(detailurl.charAt(i));
        else result2.push(detailurl.charAt(i));
    }
    var ekey=result1.join("");
    detailurl=ekey+result2.join("");  
    x=e(x).replace(/[=\/]/g,"");
    var l=Math.floor(detailurl.length/2);
    detailurl=c=detailurl.substring(0,l)+x+detailurl.substring(l);
*/
    var data='{"code":"'+getCookie("cid")+'"}';
    data=encDat2(data,5);
    /*
    x = Math.floor((Math.random() * 5) + 1);  
    data=e(data).replace(/=/g,"")+makeid(x);
    result1 = [];
    result2 = [];
    for (i = data.length-1; i >=0; i--) {
        if(i%x==0)result1.push(data.charAt(i));
        else result2.push(data.charAt(i));
    }
    ekey=result1.join("");
    data=ekey+result2.join("");  
    x=e(x).replace(/[=\/]/g,"");
    l=Math.floor(data.length/2);
    data=data.substring(0,l)+x+data.substring(l);
    */

    $.ajax({
      url: apiurl+detailurl, 
      type: "POST",
      data: data,
      dataType:"text",
      error: function (request, status, error) {
          if(request.status==404){
            console.log(error);               
          }
      },
      success:function(data){
        if(data!=""){
          data=data.replace(c,'');

          ccontent=JSON.parse(window.atob(data));
          
          setCookie('cid',cid,ccontent['remain']);
          renderCart(ccontent);
        }
        else{
          setCookie('cid','',-1);          
          renderCart();
        }
      }
    }); 
   
  }
  catch(ex){
    
    setTimeout(checkcart,500);
  }
}

function renderCart(ccontent){  
    
    var cartsContainer=$('#carts');
    var emptycart=cartsContainer.html();
    var cart=getCart();
    var isValidCart=false;
    if(cart.cics.length==0){
      $('#cartempty').show();
      cartsContainer.hide();
      return;
    }    
    $('#cartempty').hide();
    var carthtml='<div id="carttitle">Giỏ hàng của bạn</div>';
    var carterror=getCookie('carterror');
    if(carterror){
      setCookie('carterror','',-1);
      if(carterror=='expired'){      
        carthtml+='<div id="carterror">Thành thật xin lỗi bạn '+getCookie('cna')+', chương trình khuyến mãi của shop đã hết hạn.<br/> Mời bạn kiểm tra lại giỏ hàng một lần nữa.<br />Xin cảm ơn bạn đã ủng hộ shop.</div>';  
      }
      else if(carterror=='oldused'){      
        carthtml+='<div id="carterror">Thành thật xin lỗi bạn '+getCookie('cna')+', chương trình khuyến mãi chỉ áp dụng 1 lần cho mỗi khách hàng.<br/> Mời bạn kiểm tra lại giỏ hàng một lần nữa.<br />Xin cảm ơn bạn đã ủng hộ shop.</div>';  
      }
      else if(carterror=='newuser'){      
        carthtml+='<div id="carterror">Thành thật xin lỗi bạn '+getCookie('cna')+', chương trình khuyến mãi chỉ áp dụng cho khách thân thiết.<br/> Mời bạn kiểm tra lại giỏ hàng một lần nữa.<br />Xin cảm ơn bạn đã ủng hộ shop.</div>';  
      }
    }
    

    var total=0;
    carthtml+='<div style="padding: 20px;">';
    var discount=0;
    var discounttype='%';
    var shipfee=25;
    var checknew=0;
    var checkold=0;
    var totalprice=0;
    var totalItem=0;

    cart.cins.forEach(function(item,i){
      totalItem+=item*1;
    });

    for(i=0;i<cart.cics.length;i++){   
      totalprice+=prods[cart.cics[i]].price*cart.cins[i];
    }
    
    cart.cics.forEach(function(code,index){
      var item=$.extend(true, {}, prods[code]);
      item.num=cart.cins[index];
      item.code=code;
      if(item.num>=0){   
          var newitem={};     
          var discountprods=[];
          var discountprodindex=[];
          var orderbonus=[];
          var needchecknew=0;
          var needcheckold=0;
          /*
          if(ccontent){        
            ccontent.condfields.forEach(function(conds,condsi){
              var condsatis=1;          
              conds.forEach(function(cond,condi){
                
                if(cond.obj=="customer"){
                  if(cond.action=="use")                  
                    needchecknew=1;                
                  else if(cond.action=="old")                                
                    needcheckold=1;                  
                }
                else if(cond.obj=="order"){
                  if(cond.action=="totalprice"){                    
                    if(totalprice<cond.value)
                      condsatis=0;
                  }
                }            
              });

              if(condsatis){
                ccontent.thenfields[condsi].forEach(function(then,theni){

                  if(then.action=="-"){
                    if(then.obj=="order"){
                      discount=then.value;
                      discounttype=then.type;
                    }
                    else if(then.obj=="prod")
                    {                  
                      var code=then.prod.code=='all'?item.code:then.prod.code;
                      discountprods.push({"code":code,"value":then.value,"type":then.type,"num":then.num});
                      discountprodindex.push(code);
                    }
                  }
                  else if(then.action=="free"){
                    if(then.obj=="prod"){

                      newitem=$.extend(true, {}, prods[then.prod.code]);
                      newitem.price=0;
                      newitem.name=newitem.name+' (Quà tặng)';
                      newitem.num=then.value;                      
                      newitem.isnew=true;
                      
                    }
                  }              
                });
              }
            });
          }*/
          

          
          if(ccontent && ccontent.discountprods[item.code]){
            var giam=0;
            var disprods=ccontent.discountprods[item.code];
            for(i=0;i<disprods.length;i++){
              condrs=checkCond(newitem.conditions,totalprice,totalItem);
              condsatis=condrs[0];
              needchecknew=condrs[1];
              needcheckold=condrs[2];

              if(condsatis){
                disprod=disprods[i];
                if(disprod.discounttype=='%'){
                  giam=Math.ceil(item.price*disprod.discount/100);                  
                }
                else if(disprod.discounttype=='k'){
                  giam=disprod.discount;                  
                }
                
                if(disprod.num<item.num){
                  newitem=$.extend(true, {}, item);
                  newitem.num=disprod.num;
                  newitem.max=disprod.num;
                  newitem.price-=giam;
                  newitem.isnew=true;

                  item.num=item.num-disprod.num;
                  item.max=item.max-disprod.num;
                  newitem.name+=" (giảm "+disprod.discount+disprod.discounttype+")";
                }
                else{
                  item.name+=" (giảm "+disprod.discount+disprod.discounttype+")";
                  item.price-=giam;
                }
                totalprice-=giam*item.num;
                checknew=needchecknew;
                checkold=needcheckold;
                break;
              }  
            }
          }
          

          
          if(newitem.isnew){
            carthtml+=renderItem(newitem,index);            
          }
          carthtml+=renderItem(item,index);

          

          isValidCart=true;

      }
    });

    if(ccontent && ccontent.freeprods){
      
      for(i=0;i<ccontent.freeprods.length;i++){
        newitem=ccontent.freeprods[i];
        
        condrs=checkCond(newitem.conditions,totalprice,totalItem);
        condsatis=condrs[0];
        checknew=condrs[1];
        checkold=condrs[2];       
        
        if(condsatis){
          
          if(newitem.name=='ship'){
            shipfee=0;
          }
          else if(prods[newitem.code]){            
            newitem.slug=prods[newitem.code].slug;
            newitem.name=prods[newitem.code].name+' (quà tặng)';            
            carthtml+=renderItem(newitem,-1);
            
          }
          else{
            carthtml+=renderItem(newitem,-1);
          }
          
        }
      }
    }
    carthtml+='</div>';
    if(ccontent && ccontent.notice){
      carthtml+='<div id="carterror" style="text-align:left">'+ccontent.notice+'</div>';    
    }    
    carthtml+='<div class="clear"> </div>';


    carthtml+='<div id="totalcontent">';
    carthtml+='<div class="total">Tạm tính: <div class="carttotalprice">'+numberWithCommas(totalprice)+'k</div></div>';
    if(ccontent && ccontent.orderbonus){
      for(i=0;i<ccontent.orderbonus.length;i++){
        bonus=ccontent.orderbonus[i];
        
        condrs=checkCond(newitem.conditions,totalprice,totalItem);
        condsatis=condrs[0];
        checknew=condrs[1];
        checkold=condrs[2];
        
        if(condsatis){
          carthtml+='<div class="total">Khuyến mãi: <div class="carttotalprice">-'+numberWithCommas(bonus.discount)+bonus.discounttype+'</div></div>';
          if(bonus.discounttype=='%')totalprice-=totalprice*bonus.discount/100;
          else if(bonus.discounttype=='k')totalprice-=bonus.discount;
        }
      }      
    }
    if(totalprice>=1000){
      shipfee=0;
    }
    carthtml+='<div class="total">Phí ship: <div class="carttotalprice">'+shipfee+'k</div></div>';
    carthtml+='<div class="total">Thành tiền: <div class="carttotalprice" id="total">'+numberWithCommas((totalprice+shipfee)*1000)+'₫</div>  </div>';
    carthtml+='</div>';
    carthtml+='<div class="clear" style="margin-top:20px"> </div>';
    carthtml+='<div id="cart_buttons"><a href="'+siteurl+'"><button class="button buttonR" >Xem thêm</button></a>';
    carthtml+='<a href="'+siteurl+'submit-order/"><button class="button buttonG" >Thanh Toán</button></a></div>';
      
    
    if(isValidCart){
      cartsContainer.html(carthtml);
      setCookie('lal','true',3);
      setCookie('cn',checknew);
      setCookie('co',checkold);
      cart.cics.forEach(function(moditem,modindex){
        $('#btndecrease'+modindex).click(function(){
            modCart(modindex,-1);
            renderCart(ccontent);        
        });
        $('#btnincrease'+modindex).click(function(){
          modCart(modindex,1);
          renderCart(ccontent);
        });
      });    
      
      $('#carts img').css({'width':'40px','position':'relative'});
    }
  }

  function checkCond(conds,totalprice,totalItem){
    
    condsatis=1;
    checknew=0;
    checkold=0;
    conds.forEach(function(cond,condi){
      
      if(cond.obj=="customer"){
        if(cond.action=="use")                  
          checknew=1;                
        else if(cond.action=="old")                                
          checkold=1;                  
      }
      else if(cond.obj=="order"){
        if(cond.action=="totalprice"){   
          if(totalprice<cond.value)
            condsatis=0;
        }
        else if(cond.action=="numOfProd"){                    
          if(totalItem<cond.value)
            condsatis=0;
        }
      }  
    });
    
    return [condsatis,checknew,checkold];


  }

  function renderItem(item,index){
    var carthtml='';
    carthtml+='<div class="clear" style="margin-top: 10px;height: 20px;"><hr /> </div>';
    carthtml+='<div class="itemname">';
    if(item.slug!='' && item.slug!=undefined)
      carthtml+='<a href="'+siteurl+item.slug+'/"> <span> <img class="noresize" width="40" style="float:left;margin-right:5px;margin-bottom:5px;width:40px !important;" src="'+siteurl+item.slug+'/'+item.slug+'300.jpg" /></span> <strong>'+item.name+'</strong></a>';
    else
      carthtml+='<strong>'+item.name+'</strong>';
    carthtml+='</div>';
    carthtml+='<div class="clear" style="margin-top:20px"> </div>';
    if(index>=0){
      carthtml+='<div class="itemprice">';
      carthtml+='<button class="button '+((item.num<=0||item.isnew)?'buttonGr':'buttonR')+'"  id="'+(item.isnew?'':('btndecrease'+index))+'" type="image" alt="descrease" >-</button>';
      carthtml+='<button class="button buttonW" id="item'+index+'num" class="itemnum">'+item.num+'</button>';
      carthtml+='<button class="button '+(item.num>=item.max?'buttonGr':'buttonG')+'"   id="'+(item.isnew?'':('btnincrease'+index))+'" type="image" alt="increase" >+</button>';
      carthtml+='<button class="button buttonW"> x '+item.price+'k</button>';
      carthtml+='<button class="button buttonT" id="item'+index+'total" style="width:53px">='+(item.price*item.num)+'k</button>';
      carthtml+='</div>';
    }
    return carthtml;
  }