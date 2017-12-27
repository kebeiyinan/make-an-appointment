<?php
namespace Org\Util;
class Sendsms
{

	public function __construct()
	{
		$this->userid = '';
		$this->account = 'AB0020301';
		$this->password = 'AB002031026';
		$this->url = 'http://dx.ipyy.net/sms.aspx';
	
	}
	public function sendsms($mobile,$content)
	{
		// $data = array
		// (
		// 		'action'=>'send',
		// 		'userid'=>'',
		// 		'account'=>$this->account,					//用户账号
		// 		'password'=>strtoupper(md5($this->password)),				//密码
		// 		'mobile'=>$mobile,					//号码
		// 		'content'=>'【乐庭整形】'.$content,	//内容
		// 		//'sendtime' =>$config['sendTime']	//定时发送时间
		// );

		$result= $this->curl_request($this->url,$data);	
		return $result;
	}
	
	public function curl_request($url,$param='',$httpMethod='GET'){
		$ch=curl_init();
		if(stripos($url, "https://") !== FALSE){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		if($httpMethod=='GET'){
			if(empty($param)){
				curl_setopt($ch, CURLOPT_URL, $url );
			}else{
				if(stripos($url, "?") !== FALSE){
					curl_setopt($ch, CURLOPT_URL, $url . "&" . http_build_query($param));
				}else{
					curl_setopt($ch, CURLOPT_URL, $url . "?" . http_build_query($param));
				}
			}
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		}else {
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }		
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1); 
		$sContent = curl_exec($ch);
        $aStatus = curl_getinfo($ch);
        curl_close($ch);
        if (intval($aStatus["http_code"]) == 200) {
                return json_decode($sContent,true);
        } else {
                return FALSE;
        }
	}	
	
	public function getsms($url,$data)
	{
		
	}
}
?>