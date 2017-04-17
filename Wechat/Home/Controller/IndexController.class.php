<?php
namespace Home\Controller;
use Think\Controller;

define("TOKEN", "weixindemo");
define("APPID", "wx01d524b6a46a3884");
define("APPSECRET", "ab6dcc21ccc1f0a965725eb7960d18a5");

class IndexController extends Controller {
    public function index(){

        // $this->valid();
        // $this->delegateMenu();
        // $this->createMenu();
      	
       	header('content-type:text');		
        $echoStr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = 'weixindemo';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $signature && $echoStr){
            exit;
        } else {
            $this->responseMsg();
        }
    }

    //取中间字符串
    function getSubstr($str, $leftStr, $rightStr)
    {
        $left = strpos($str, $leftStr);
        $right = strpos($str, $rightStr,$left);
        if($left < 0 or $right < $left) return '';
        return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
    }
    

    //验证服务器与微信服务器是否相通
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
                
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
    
    //网络请求
    private function https_request($url,$data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    private function get_access_token(){
        $appid = "wx01d524b6a46a3884";
        $appsecret = "ab6dcc21ccc1f0a965725eb7960d18a5";
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $jsoninfo = json_decode($output, true);
        return $jsoninfo['access_token'];
    }

    //获取access_token
    public function  getWxAccessToken(){
        if( isset($_SESSION['access_token']) && $_SESSION['expire_time']>time()){
          //如果access_token在session没有过期
            return $_SESSION['access_token'];
        }
        else{
            //如果access_token比存在或者已经过期，重新取access_token
            $access_token = $this->get_access_token();
            //将重新获取到的aceess_token存到session
            $_SESSION['access_token']=$access_token;
            $_SESSION['expire_time']=time()+7000;
            return $access_token;
        }
    }


    //获取微信服务器IP地址
    function  getWxServerIp(){
        $accessToken ="weixindemo";
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$accessToken;
        $ch  =curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $res =curl_exec($ch);
        //5.关闭curl
        curl_close($ch);
        if(curl_error($ch)){
            var_dump(curl_error($ch));
        }
        $arr=json_decode($res,true);
        
        var_dump($arr);
        
    }

    //创建菜单
    private function createMenu(){
        $jsonmenu = '{
              "button":[
              {
                   "name":"点击进入女子学院",
                   "type":"view",
                   "url":"http://mlyh.ngrok.cc/Wechat/Wechat/Home/View/index.html"
               }
            ]
         }';

        $access_token = $this->getWxAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        $result = $this->https_request($url, $jsonmenu);
        echo $result;
    }
    
    //删除菜单
    private function delegateMenu(){
    	$access_token = $this->getWxAccessToken();
    	$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$access_token;
    	$result = $this->https_request($url);
    	echo $result;
    }


    //获取到用户的信息
    //1.用户关注以及回复消息的时候，均可以获得用户的OpenID FromUserName就是OpenID 
    //2. 然后使用access_token接口，请求获得全局Access Token https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET
    //3. 再使用全局ACCESS_TOKEN获取OpenID的详细信息 https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID
    public function getUserInfo($FromUserName){
        
        $access_token = $this->getWxAccessToken();

        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$FromUserName;

        $res = $this->https_request($url);

        $nickname = $this->getSubstr($res,'"nickname":"','",');
    }


    public function responseMsg(){

        //获取微信推送来的消息(xml)
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){

            //处理消息类型，并设置回复类型和内容
             $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
             $RX_TYPE = trim($postObj->MsgType);


            //$this->getUserInfo($postObj->FromUserName);

			
             //消息类型分离
            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    if (strstr($postObj->Content, "第三方")){
                        $result = $this->relayPart3("http://www.fangbei.org/test.php".'?'.$_SERVER['QUERY_STRING'], $postStr);
                    }else{
                        $result = $this->receiveText($postObj);
                    }
                    break;
                case "image":
                    $result = $this->receiveImage($postObj);
                    break;
                case "location":
                    $result = $this->receiveLocation($postObj);
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":
                    $result = $this->receiveVideo($postObj);
                    break;
                case "link":
                    $result = $this->receiveLink($postObj);
                    break;
                default:
                    $result = "unknown msg type: ".$RX_TYPE;
                    break;
            }

            echo $result;
        } else {
            echo "";
            exit;
        }
    }

    //接收事件消息
    private function receiveEvent($object){
        $content = "";
        switch ($object->Event){
            case "subscribe":
                $content = "欢迎关注女子学院";
                break;
            case "unsubscribe":
                 $content = "取消关注";
                 break;
            case "CLICK":
                switch ($object->EventKey)
                {
                    case "COMPANY":
                         $content = array();
                         $content[] = array("Title"=>"方倍工作室", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                         break;
                     default:
                         $content = "点击菜单：".$object->EventKey;
                         break;
                 }
                 break;
            case "VIEW":
                 $content = "跳转链接 ".$object->EventKey;
                 break;
            case "SCAN":
                 $content = "扫描场景 ".$object->EventKey;
                 break;
            case "LOCATION":
                 //$content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
                 break;
            case "scancode_waitmsg":
                 if ($object->ScanCodeInfo->ScanType == "qrcode"){
                     $content = "扫码带提示：类型 二维码 结果：".$object->ScanCodeInfo->ScanResult;
                 }else if ($object->ScanCodeInfo->ScanType == "barcode"){
                     $codeinfo = explode(",",strval($object->ScanCodeInfo->ScanResult));
                     $codeValue = $codeinfo[1];
                     $content = "扫码带提示：类型 条形码 结果：".$codeValue;
                 }else{
                     $content = "扫码带提示：类型 ".$object->ScanCodeInfo->ScanType." 结果：".$object->ScanCodeInfo->ScanResult;
                 }
                 break;
            case "scancode_push":
                 $content = "扫码推事件";
                 break;
            case "pic_sysphoto":
                 $content = "系统拍照";
                 break;
            case "pic_weixin":
                 $content = "相册发图：数量 ".$object->SendPicsInfo->Count;
                 break;
            case "pic_photo_or_album":
                 $content = "拍照或者相册：数量 ".$object->SendPicsInfo->Count;
                 break;
            case "location_select":
                 $content = "发送位置：标签 ".$object->SendLocationInfo->Label;
                 break;
            default:
                $content = "receive a new event: ".$object->Event;
                break;
        }

        if(is_array($content)){
             if (isset($content[0]['PicUrl'])){
                 $result = $this->transmitNews($object, $content);
             }else if (isset($content['MusicUrl'])){
                 $result = $this->transmitMusic($object, $content);
             }
         }else{
             $result = $this->transmitText($object, $content);
         }
         return $result;
    }



    //接收文本消息
     private function receiveText($object)
     {
         $keyword = trim($object->Content);
         //多客服人工回复模式
         if (strstr($keyword, "请问在吗") || strstr($keyword, "在线客服")){
             $result = $this->transmitService($object);
             return $result;
         }
          //自动回复模式
         if (strstr($keyword, "文本")){
             $content = "这是个文本消息";
         }else if (strstr($keyword, "表情")){
             $content = "中国：".$this->bytes_to_emoji(0x1F1E8).$this->bytes_to_emoji(0x1F1F3)."\n仙人掌：".$this->bytes_to_emoji(0x1F335);
         }else if (strstr($keyword, "单图文")){
             $content = array();
             $content[] = array("Title"=>"单图文标题",  "Description"=>"单图文内容", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
         }else if (strstr($keyword, "图文") || strstr($keyword, "多图文")){
             $content = array();
             $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
             $content[] = array("Title"=>"多图文2标题", "Description"=>"", "PicUrl"=>"http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
             $content[] = array("Title"=>"多图文3标题", "Description"=>"", "PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
         }else if (strstr($keyword, "音乐")){
             $content = array();
             $content = array("Title"=>"最炫民族风", "Description"=>"歌手：凤凰传奇", "MusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3", "HQMusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3"); 
         }else{

            //$content = date("Y-m-d H:i:s",time())."\nOpenID：".$object->FromUserName."\n技术支持 方倍工作室";
            $content[] = array("Title"=>"女子学院",  "Description"=>"点击进入详情页", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://mlyh.ngrok.cc/Wechat/Wechat/Home/View/introduce/index.html");
        
        }
 
         if(is_array($content)){
             if (isset($content[0])){
                 $result = $this->transmitNews($object, $content);
             }else if (isset($content['MusicUrl'])){
                 $result = $this->transmitMusic($object, $content);
             }
         }else{
             $result = $this->transmitText($object, $content);
         }
         return $result;
     }




     //接收图片消息
     private function receiveImage($object)
     {
         $content = array("MediaId"=>$object->MediaId);
         $result = $this->transmitImage($object, $content);
         return $result;
     }
 
     //接收位置消息
     private function receiveLocation($object)
     {
         $content = "你发送的是位置，经度为：".$object->Location_Y."；纬度为：".$object->Location_X."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
         $result = $this->transmitText($object, $content);
         return $result;
     }
 
     //接收语音消息
     private function receiveVoice($object)
     {
         if (isset($object->Recognition) && !empty($object->Recognition)){
             $content = "你刚才说的是：".$object->Recognition;
             $result = $this->transmitText($object, $content);
         }else{
             $content = array("MediaId"=>$object->MediaId);
             $result = $this->transmitVoice($object, $content);
         }
         return $result;
     }
 
     //接收视频消息
     private function receiveVideo($object)
     {
         $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
         $result = $this->transmitVideo($object, $content);
         return $result;
     }
 
     //接收链接消息
     private function receiveLink($object)
     {
         $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
         $result = $this->transmitText($object, $content);
         return $result;
     }
 
     //回复文本消息
     private function transmitText($object, $content)
     {
         if (!isset($content) || empty($content)){
             return "";
         }
 
         $xmlTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";
         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
 
         return $result;
     }
 
     //回复图文消息
     private function transmitNews($object, $newsArray)
     {
         if(!is_array($newsArray)){
             return "";
         }
         $itemTpl = "        <item>
             <Title><![CDATA[%s]]></Title>
             <Description><![CDATA[%s]]></Description>
             <PicUrl><![CDATA[%s]]></PicUrl>
             <Url><![CDATA[%s]]></Url>
         </item>
 ";
         $item_str = "";
         foreach ($newsArray as $item){
             $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
         }
         $xmlTpl = "<xml>
     <ToUserName><![CDATA[%s]]></ToUserName>
     <FromUserName><![CDATA[%s]]></FromUserName>
     <CreateTime>%s</CreateTime>
     <MsgType><![CDATA[news]]></MsgType>
     <ArticleCount>%s</ArticleCount>
     <Articles>
 $item_str    </Articles>
 </xml>";
 
         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
         return $result;
     }
 
     //回复音乐消息
     private function transmitMusic($object, $musicArray)
     {
         if(!is_array($musicArray)){
             return "";
         }
         $itemTpl = "<Music>
         <Title><![CDATA[%s]]></Title>
         <Description><![CDATA[%s]]></Description>
         <MusicUrl><![CDATA[%s]]></MusicUrl>
         <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
     </Music>";
 
         $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);
 
         $xmlTpl = "<xml>
     <ToUserName><![CDATA[%s]]></ToUserName>
     <FromUserName><![CDATA[%s]]></FromUserName>
     <CreateTime>%s</CreateTime>
     <MsgType><![CDATA[music]]></MsgType>
     $item_str
 </xml>";
 
         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
         return $result;
     }
 
     //回复图片消息
     private function transmitImage($object, $imageArray)
     {
         $itemTpl = "<Image>
         <MediaId><![CDATA[%s]]></MediaId>
     </Image>";
 
         $item_str = sprintf($itemTpl, $imageArray['MediaId']);
 
         $xmlTpl = "<xml>
     <ToUserName><![CDATA[%s]]></ToUserName>
     <FromUserName><![CDATA[%s]]></FromUserName>
     <CreateTime>%s</CreateTime>
     <MsgType><![CDATA[image]]></MsgType>
     $item_str
 </xml>";
 
         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
         return $result;
     }
 
     //回复语音消息
     private function transmitVoice($object, $voiceArray)
     {
         $itemTpl = "<Voice>
         <MediaId><![CDATA[%s]]></MediaId>
     </Voice>";
 
        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);
         $xmlTpl = "<xml>
     <ToUserName><![CDATA[%s]]></ToUserName>
     <FromUserName><![CDATA[%s]]></FromUserName>
     <CreateTime>%s</CreateTime>
     <MsgType><![CDATA[voice]]></MsgType>
     $item_str
 </xml>";
 
         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
         return $result;
     }
 
     //回复视频消息
     private function transmitVideo($object, $videoArray)
     {
         $itemTpl = "<Video>
         <MediaId><![CDATA[%s]]></MediaId>
         <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
         <Title><![CDATA[%s]]></Title>
         <Description><![CDATA[%s]]></Description>
     </Video>";
 
         $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);
 
         $xmlTpl = "<xml>
     <ToUserName><![CDATA[%s]]></ToUserName>
     <FromUserName><![CDATA[%s]]></FromUserName>
     <CreateTime>%s</CreateTime>
     <MsgType><![CDATA[video]]></MsgType>
     $item_str
 </xml>";
 
         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
         return $result;
     }
 
     //回复多客服消息
     private function transmitService($object)
     {
         $xmlTpl = "<xml>
     <ToUserName><![CDATA[%s]]></ToUserName>
     <FromUserName><![CDATA[%s]]></FromUserName>
     <CreateTime>%s</CreateTime>
     <MsgType><![CDATA[transfer_customer_service]]></MsgType>
 </xml>";
         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
         return $result;
     }
 
     //回复第三方接口消息
     private function relayPart3($url, $rawData)
     {
         $headers = array("Content-Type: text/xml; charset=utf-8");
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $rawData);
         $output = curl_exec($ch);
         curl_close($ch);
         return $output;
     }
 
     //字节转Emoji表情
     function bytes_to_emoji($cp)
     {
         if ($cp > 0x10000){       # 4 bytes
             return chr(0xF0 | (($cp & 0x1C0000) >> 18)).chr(0x80 | (($cp & 0x3F000) >> 12)).chr(0x80 | (($cp & 0xFC0) >> 6)).chr(0x80 | ($cp & 0x3F));
         }else if ($cp > 0x800){   # 3 bytes
             return chr(0xE0 | (($cp & 0xF000) >> 12)).chr(0x80 | (($cp & 0xFC0) >> 6)).chr(0x80 | ($cp & 0x3F));
         }else if ($cp > 0x80){    # 2 bytes
             return chr(0xC0 | (($cp & 0x7C0) >> 6)).chr(0x80 | ($cp & 0x3F));
         }else{                    # 1 byte
             return chr($cp);
         }
     }
 
     //日志记录
     private function logger($log_content)
     {
         if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
             sae_set_display_errors(false);
             sae_debug($log_content);
             sae_set_display_errors(true);
         }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
             $max_size = 1000000;
             $log_filename = "log.xml";
             if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
             file_put_contents($log_filename, date('Y-m-d H:i:s')." ".$log_content."\r\n", FILE_APPEND);
         }
     }
     
    public function http_curl($url,$type='get',$res='json',$arr=''){
    	$ch = curl_init();
    	curl_setopt($ch,CURLOPT_URL,$url);
    	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    	if($type == 'post'){
    		curl_setopt($ch,CURLOPT_POST,1);
    		curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
    	}
    	$output = curl_exec($ch);
    	echo $output;
    	curl_close($ch);
    	if($type == 'json'){
    		return json_decode($output,true);
    	}
    	return $output;
    }
 
 	public function getQrCode(){
 		$access_token = $this->getWxAccessToken();
 		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
 		$postArr = array(
 			'expire_seconds'=>604800,
 			'action_name'=>"QR_SCENE",
 			'action_info'=>array(
 				'scene'=>array('scene_id'=>2000),
 			),
 		);
 		
 		$postJson = json_encode($postArr);
 		$res = $this->https_request($url,$postJson);
 		echo $res['ticket'];
 		
 		$ticket = "gQFh8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyM0RrSE5pYk04elUxU0VseWhvMUQAAgQoG9lYAwSAOgkA";
 		$url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
 		//$res = $this->https_request($url);
 		
 		echo "<img src='".$url."'/>";
 	}
}