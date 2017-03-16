<?php
/**
 * @api
 */

require_once 'autoload.php';

class api{
	function __construct(){
	}

	function __destruct(){
	}

	// talk robot
	static function talk($hesaid){
		$url = "http://www.tuling123.com/openapi/api";
		$key = key::ass('tuling123');
		$opts = array(
			"url" => $url,
			"header" => 0,
			"post" => 1,
			"postfields" => "key=$key&info=$hesaid",
		);
		$data = curl::go($opts);

		$a = json_decode($data, true);
		return $a["text"];

	}

	// microsoft cognitive speech recognize
	function sr(){
		$token = key::ass('ms');

		$url = "https://speech.platform.bing.com/recognize";
		$appid = "D4D52672-91D7-4C74-8AD8-42B1D98141A5";
		$instanceid = $requestid = "b2c95ede-97eb-4c88-81e4-80f32d6aee54";
		$urll = "$url?scenarios=catsearch&appid=$appid&locale=zh-TW&device.os=wp7&version=3.0&format=json&requestid=$requestid&instanceid=$instanceid";
		$opts = array(
			"url" => $urll,
			"returntransfer" => 0,
			"post" => 1,
			"header" => 1,
			//"infile" => '@isay.mp3',
			"httpheader" => array(
				'Content-Type: audio/wav; samplerate=16000',
				'Authorization: ' . 'Bearer ' . $token,
			),
			"postfields" => "@isay.mp3",

		);
		curl::go($opts);


	}
	function ss($isay, $lang = 'zh-TW'){
		$token = key::ass('ms');
		$url = "https://speech.platform.bing.com/synthesize";
		$v = array(
			"zh-TW" => "Yating, Apollo",
			"zh-CN" => "HuihuiRUS",
			//"zh-CN" => "Yaoyao, Apollo",
			"en-US" => "ZiraRUS",
			"en-CA" => "Linda",
			"en-GB" => "Susan, Apollo",
			"en-AU" => "Catherine",
		);

		// assemble xml
		$doc = new DOMDocument();
		$root = $doc->createElement( "speak" );
		$root->setAttribute( "version" , "1.0" );
		$root->setAttribute( "xml:lang" , $lang );
		$voice = $doc->createElement( "voice" );
		$voice->setAttribute( "xml:lang" , $lang );
		//$voice->setAttribute( "xml:gender" , "Female" );
		$voice->setAttribute( "name" , "Microsoft Server Speech Text to Speech Voice ($lang, ${v[$lang]})" );
		$text = $doc->createTextNode( $isay );
		$voice->appendChild( $text );
		$root->appendChild( $voice );
		$doc->appendChild( $root );
		$data = $doc->saveXML();

		$opts = array(
			"url" => $url,
			"post" => 1,
			"header" => 1, // fuck
			"httpheader" => array(
				'Content-Type: application/ssml+xml',
				"X-Microsoft-OutputFormat: riff-16khz-16bit-mono-pcm",
				"X-Search-AppId: 07D3234E49CE426DAA29772419F436CA",
				"X-Search-ClientID: 1ECFAE91408841A480F00935DC390960",
				"User-Agent: TTSPHP",
				'Authorization: ' . 'Bearer ' . $token,
				"content-length: ".strlen($data),
			),
			"postfields" => $data,
		);
		file_put_contents("isay.mp3", curl::go($opts));
		
	}

	static function vv($num=""){
		if (empty($num)) exit("give me a number\n");

		// $num = ltrim($num, 86);
		if(! preg_match('/^[0-9]{11,12}$/', $num)) die("Wrong number.\n");

		date_default_timezone_set("Asia/Shanghai");
		$code = rand(999, 9999);
		$url = "https://api.miaodiyun.com/20150822/call/voiceCode";
		$arr = key::ass('miaodiyun');
		$sid = $arr[1];
		$key = $arr[0];
		$time = date("YmdHis");
		$sig = md5($sid.$key.$time);
		$opts = array(
			"url" => $url,
			"post" => 1,
			"postfields" => "accountSid=$sid&verifyCode=$code&called=$num&playTimes=3&timestamp=$time&sig=$sig",
		);
		return $data = curl::go($opts);
	}
}

(new api)->ss('可以群聊，仅耗少量流量，适合大部分智能手机', 'zh-TW');