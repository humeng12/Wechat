<?php

class Tool {
	
	const JSON = "json";
	const XML = "xml";
	
	public static function show($code,$message='',$data=array(),$type=self::JSON){
		if(!is_numeric($code)) {
			return '';
		}
		
		$type = isset($_GET['xml']) ? self::XML : self::JSON;
		
		
		
		if($type == 'json'){
			self::json($code,$message,$data);
			exit;
		}else if($type == 'array'){
			var_dump($result);
		}else if($type == 'xml'){
			self::xmlEncode($code,$message,$data);
			exit;
		} else {
			return '';
		}
	}
	
	public static function json($code,$message,$data=array()) {
		if(!is_numeric($code)) {
			return'';
		}
		
		$result = array(
			'code'=>$code,
			'message'=>$message,
			'data'=>$data
		);
		
		return json_encode($result);
		exit;
	}
	
	
	public static function xmlEncode($code,$message,$data=array()){
		if(!is_numeric($code)) {
			return '';
		}
		
		$tmpArray = "";
		if(empty($data)){
			$tmpArray = array(
				'code'=>$code,
				'message'=>$message
			);	
		} else {
			$tmpArray = array(
				'code'=>$code,
				'message'=>$message,
				'data'=>$data
			);
		}
		
		
		header("Content-Type:text/xml");
		$xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
		$xml .= "<root>";
		
		$xml .= self::xmlToEncode($tmpArray);
			
		$xml .= "</root>";

		die($xml);
		
		return $xml;
	}
	
	
	public static function xmlToEncode($data){
		$xml = $attr = "";
		foreach($data as $key => $value) {
			
			if(is_numeric($key)) {
				$attr = " id='{$key}'";
				$key = "item";
			}
			
			$xml .= "<{$key}{$attr}>";
			$xml .= is_array($value) ? self::xmlToEncode($value) : $value;
			$xml .= "</{$key}>";
		}
		
		return $xml;	
	}
}
?>