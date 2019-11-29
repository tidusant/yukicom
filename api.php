<?php 
function t($data,$exit=1) {
	echo '<pre style="text-align:left;">';
	print_r($data);
	echo '</pre>';
	if($exit)exit;
}


error_reporting(E_ALL); ini_set('display_errors', 1);

$http_origin = $_SERVER['HTTP_ORIGIN'];

if ($http_origin == "http://duyhf.noip.me" || $http_origin == "http://colisshop.com" || $http_origin == "http://colis.shop")
{  
    header("Access-Control-Allow-Origin: $http_origin");
}
define('GMT',7);

//check last request
$uri=$_SERVER['REQUEST_URI'];

$mongo = new MongoClient("mongodb://localhost",["db" => "colis","username" => "colis", "password" => "siloc@1234.P"]);
$db=$mongo->colis;
$col=$db->requests;

$lastrequest=$col->findOne(['REQUEST_URI'=>$uri,'REQUEST_TIME'=>['$gt'=>time()-10]]);
//check multi post
if($lastrequest)exit;

$col->save($_SERVER);
//parse
//t(1);

$uri=str_replace('/', '', $uri);

//print_r(explode('/',$uri));exit;
$params=explode('/',$uri);

$keyrequest='';
if(isset($params[0]))$keyrequest=$params[0];

$request=decodeUrl($keyrequest);
$rt='';


if($request=='cidinfo'){
	$rt=cidinfo();	
}
else if($request=='cidnotice'){
	$rt=cidnotice();	
}
else if($request=='submitorder'){
	$rt=submitorder();	
}
else if($request=='cidgetprods'){
	$rt=cidgetprods();	
}


echo $rt;
return 1;


function cidinfo(){
	global $keyrequest,$db;
	
	$data=parseData();
	
	if(empty($data))return '';
	//print_r($data);exit;
	if(!isset($data['code']))return '';
	//checkcode
	$code=substr($data['code'],0,strlen($data['code'])/2);
	$code=str_replace($code,'',$data['code']).$code;		
	$code=base64_decode($code);
	
	$col=$db->campaigns;		
	$curTime=time()+3600*GMT;
	
	
	$cam=$col->findOne(['code'=>$code,'start'=>['$lt'=>$curTime],'end'=>['$gt'=>$curTime]],['_id'=>0,'end'=>1,'condfields'=>1,'thenfields'=>1,'notice'=>1]);
	
	if($cam){	
		$rt=[];
		$discountprods=[];
		$orderbonus=[];
		$freeprods=[];
		//exit;
		foreach($cam['condfields'] as $index=>$conds){			
			foreach($cam['thenfields'][$index] as $theni=>$then){ 
				if($then['action']=="-"){
					$bonus['discount']=$then['value'];
					$bonus['discounttype']=$then['type'];
					$bonus['conditions']=$conds;
					
				  if($then['obj']=="order"){					  
					$orderbonus[]=$bonus;
				  }
				  else if($then['obj']=="prod")
				  { 
					$bonus['num']=$then['num'];
					if(!isset($discountprods[$then['prod']['code']]))$discountprods[$then['prod']['code']]=[];
					$discountprods[$then['prod']['code']][]=$bonus;					
				  }
				}
				else if($then['action']=="free"){					
				  if($then['obj']=="prod"){
					$freeprods[]=['name'=>$then['prod']['title'],'code'=>$then['prod']['code'],'num'=>$then['num'],'conditions'=>$conds ];
				  }
				  else if($then['obj']=='ship'){
					$freeprods[]=['name'=>'ship','conditions'=>$conds ];
				  }
				  else if($then['obj']=='service'){
					$freeprods[]=['name'=>$then['value'],'num'=>$then['num'],'conditions'=>$conds ];
				  }
				}              
			}			
		}
		$rt['discountprods']=$discountprods;
		$rt['freeprods']=$freeprods;
		$rt['orderbonus']=$orderbonus;
		$rt['remain']=$cam['end']-$curTime;		
		$rt['notice']=$cam['notice'];
		//print_r($rt);exit;
		$rt=str_replace('=','',base64_encode(json_encode($rt)));
		$rt=substr($rt,0,strlen($rt)-1).$keyrequest.substr($rt,strlen($rt)-1);
		print_r($rt);
	}
	return '';
	
	exit;
	
	
	if(!empty($script))
		$rt=$script;
	//t($rt);
	//print_r($oddstr);print_r("\r\n");exit;
		//$rt=$cockpit->module('builds')->JSencrypt($script,$key);	
	$rt=base64_encode(gzcompress($rt,9));

	$rt=substr($rt, 0,floor(strlen($rt)/2)).$ekey.substr($rt,floor(strlen($rt)/2));
	return $rt;
}

function cidnotice(){
	global $keyrequest,$db;
	
	$data=parseData();
	
	if(empty($data))return '';
	//print_r($data);exit;
	if(!isset($data['code']))return '';
	//checkcode
	$code=substr($data['code'],0,strlen($data['code'])/2);
	$code=str_replace($code,'',$data['code']).$code;		
	$code=base64_decode($code);
	
	$col=$db->campaigns;		
	$curTime=time()+3600*GMT;
	
	
	$cam=$col->findOne(['code'=>$code,'start'=>['$lt'=>$curTime],'end'=>['$gt'=>$curTime]],['_id'=>0,'end'=>1,'condfields'=>1,'thenfields'=>1,'notice'=>1]);
	
	if($cam){	
		$rt=[];			
		$rt['notice']=$cam['notice'];
		//print_r($rt);exit;
		$rt=str_replace('=','',base64_encode(json_encode($rt)));
		$rt=substr($rt,0,strlen($rt)-1).$keyrequest.substr($rt,strlen($rt)-1);
		print_r($rt);
	}
	return '';
	
	exit;
	
	
	if(!empty($script))
		$rt=$script;
	//t($rt);
	//print_r($oddstr);print_r("\r\n");exit;
		//$rt=$cockpit->module('builds')->JSencrypt($script,$key);	
	$rt=base64_encode(gzcompress($rt,9));

	$rt=substr($rt, 0,floor(strlen($rt)/2)).$ekey.substr($rt,floor(strlen($rt)/2));
	return $rt;
}

function cidgetprods(){
	global $keyrequest,$db;
	$data=parseData();
	
	if(empty($data))return '';
	//print_r($data);exit;
	if(!isset($data['code']))return '';
	//checkcode
	$code=substr($data['code'],0,strlen($data['code'])/2);
	$code=str_replace($code,'',$data['code']).$code;		
	$code=base64_decode($code);
	
	$col=$db->campaigns;		
	$curTime=time()+3600*GMT;
	
	
	$cam=$col->findOne(['code'=>$code,'start'=>['$lt'=>$curTime],'end'=>['$gt'=>$curTime]],['_id'=>0,'end'=>1,'condfields'=>1,'thenfields'=>1,'notice'=>1]);
	
	if($cam){	
		$rt=[];
		$rt['remain']=$cam['end']-$curTime;
		$discountprods='';
		
		foreach($cam['thenfields'] as $index=>$thenfields){ 
			if(substr($discountprods,0,3)=='all')break;
			foreach($thenfields as $theni=>$then){ 
				if($then['action']=="-" && $then['obj']=="prod"){
					$code=$then['prod']['code'];
					if($code=='all'){
						$discountprods='all'.$then['value'].$then['type'];
						break;
					}
					else{						
						$discountprods.=$code.$then['value'].$then['type'];
					}
					
				} 
			}
		}
		$rt['discountprods']=$discountprods;
		//print_r($rt);exit;
		$rt=str_replace('=','',base64_encode(json_encode($rt)));
		$rt=substr($rt,0,strlen($rt)-1).$keyrequest.substr($rt,strlen($rt)-1);
		print_r($rt);
	}
	return '';
	
	exit;
	
	
}

function submitorder(){
	$data=parseData();
	
	if(empty($data))return '';
	//print_r($data);exit;
	if(!isset($data['code']) || !isset($data['cart']) )return '';
	//checkcode
	$error='';
	$cus='';
	$code=$data['code'];
	$mongo = new MongoClient("mongodb://localhost",["db" => "colis","username" => "colis", "password" => "siloc@1234.P"]);
	$db=$mongo->colis;
	$colcus=$db->customers;		
	$colorder=$db->orders;
	if(!empty($code)){
		$code=substr($data['code'],0,strlen($data['code'])/2);
		$code=str_replace($code,'',$data['code']).$code;		
		$code=base64_decode($code);		
		$col=$db->campaigns;		
		$curTime=time()+3600*GMT;
		
		$cam=$col->findOne(['code'=>$code,'start'=>['$lt'=>$curTime]],['_id'=>0,'code'=>1,'end'=>1,'condfields'=>1,'thenfields'=>1,'notice'=>1]);
		if($cam){
			//get customer had use this code			
			$cusused=$colorder->findOne(['customer'=>$data['phone'],'cid'=>$cam['code']]);
			//check old cus
			$cus=$colcus->findOne(['phone'=>$data['phone']]);
			
			//check cam expired		
			if($curTime>$cam['end']){
				$error='{"error":"expired"}';
			}
			else if($data['cn'] || $data['co']){
				if($data['cn'] && $cusused)
					$error='{"error":"oldused"}';
				if($data['co'] && !$cus)
					$error='{"error":"newuser"}';
			}			
		}
		else{
			$error='{"error":"expired"}';
		}
	}
	
	if($error)echo $error;		
	else{
		
		if(!$cus){
			$cus['phone']=$data['phone'];
			$cus['name']=$data['name'];
			$cus['created']=time();				
							
		}		
		
		$order['note']=$data['note'];
		$order['r']=$data['r'];
		$order['cid']=$code;	
		$order['cic']=$data['cart'];
		$order['created']=time();
		$order['modified']=time();
		$order['customer']=$cus['phone'];
		
		$colcus->save($cus);
		
		$colorder->save($order);
		$subject='Đơn hàng từ '.$cus['phone'];
		$body='Có đơn hàng từ '.$cus['phone'].' - '.$cus['name'].'. ';
		$body.='Số lượng: '.$data['total'].'. ';
		$body.='Ghi chú: '.$order['note'];
		send_simple_message($subject,$body);
		
	}
			
	exit;
	
	
	return $rt;
}
function decodeUrl($key){
	$script='';
	$detail='';
	$oddstr='d';
	$l=floor((strlen($key)-2)/2);
	$num=substr($key, $l,2);
	$key=substr($key, 0,$l).substr($key, $l+2);
	$num=intval(base64_decode($num));
	$ekey='';
	$requesturl='';
	
	if($num>0){
		$oddstr=substr($key, 0,ceil(strlen($key)/$num));
		$ekey=$oddstr;		
		$ukey=str_replace($oddstr, '', $key);	
		$base64='';
		
		for($i=strlen($oddstr)-1;$i>=0;$i--){
			$base64.=substr($oddstr, strlen($oddstr)-1);
			$oddstr=substr($oddstr,0,strlen($oddstr)-1);
			
			if(strlen($ukey)-$num+1>0)
				$base64.=strrev(substr($ukey, strlen($ukey)-$num+1));
			else
				$base64.=strrev($ukey);
			
			$ukey=substr($ukey,0, strlen($ukey)-$num+1);
			
		}
		$base64=substr($base64,0,strlen($base64)-$num);
		//$base64='aHR0cCUzQS8vaGRvbmxpbmUudm4vZnJvbnRlbmQvZXBpc29kZS94bWxwbGF5JTNGZXAlM0QxJTI2ZmlkJTNEMjg0MSUyNmZvcm1hdCUzRGpzb24';
		$requesturl=urldecode(base64_decode($base64));			
	}
	return $requesturl;
}
function parseData(){
	$key=array_keys($_POST)[0];
	if(empty($key))return '';	
	$script='';
	$detail='';
	$oddstr='d';
	$l=floor((strlen($key)-2)/2);
	$num=substr($key, $l,2);
	$key=substr($key, 0,$l).substr($key, $l+2);
	$num=intval(base64_decode($num));
	$ekey='';
	
	if($num>0){
		//print_r($num);print_r("\r\n");	
		//get odd string

		$oddstr=substr($key, 0,ceil(strlen($key)/$num));
		$ekey=$oddstr;		
		$ukey=str_replace($oddstr, '', $key);	
		$base64='';
		
		for($i=strlen($oddstr)-1;$i>=0;$i--){
			$base64.=substr($oddstr, strlen($oddstr)-1);
			$oddstr=substr($oddstr,0,strlen($oddstr)-1);
			
			if(strlen($ukey)-$num+1>0)
				$base64.=strrev(substr($ukey, strlen($ukey)-$num+1));
			else
				$base64.=strrev($ukey);
			
			$ukey=substr($ukey,0, strlen($ukey)-$num+1);
			
		}
		$base64=substr($base64,0,strlen($base64)-$num);
		//decode cart
		$data=urldecode(base64_decode($base64));
		//print_r($data);exit;
		
		$data=json_decode($data,true);
		return $data;
	}
	return '';
	
}

function send_simple_message($subject,$body) {  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  curl_setopt($ch, CURLOPT_USERPWD, 'api:key-58c885b41d9d6512074c20addfd0af9e');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($ch, CURLOPT_URL, 
              'https://api.mailgun.net/v3/ml.duyhf.com/messages');
  curl_setopt($ch, CURLOPT_POSTFIELDS, 
                array('from' => 'Tidusant <tidusant@gmail.com>',
                      'to' => 'duyhph@gmail.com, phamsthien@gmail.com',
                      'subject' => $subject,
					  'unsubscribe_url'=>'http://colisshop.com',
                      'text' => $body));
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}

function sendMailchimp($data){
	$MailChimp = new \MailChimp('22912c0e666add7cc0d00d30b67fecb7-us14');
	
	$result = $MailChimp->post("campaigns/c207a0b08f/actions/test", [
                'test_emails' => ['cocolis2012@gmail.com'],
				'send_type' =>'html'
            ]);
	print_r($result);
}




	return 1;
?>