<?php

function xml2array($xml){
	//conver the xml to a json
	$json = json_encode($xml);

	//remove {} from creatign empty array as a value
	$json = str_replace("{}", '""', $json);

	//convert json to an array
	$array = json_decode($json, true);

	return $array;

}

function json2array($json){

	//remove {} from creatign empty array as a value
	$json = str_replace("{}", '""', $json);

	//convert json to an array
	$array = json_decode($json, true);

	foreach($array as $field => $value){
		if(!is_array($value) && !is_array($field)){
			$array[ $field ] = trim($value);
		}
	}

	return $array;
}


function get_timezone($timezone){

	$timezone = strtolower(trim($timezone));

	if(substr($timezone, 0, 3) == "gmt"){
		$timezone = trim(substr($timezone, 3));
		$timezone = timezoneNamed($timezone);
	}

	$timezone = new DateTimeZone($timezone);

	return $timezone;
}


function timezoneNamed($id = 0){
	if(empty($id)){
		$id = 0;
	}
	else{
		//use first numbers where avaliable
		$id = 0 + $id;
	}

	$timezones = array(
		- 12 => 'Kwajalein',
		- 11 => 'Pacific/Midway',
		- 10 => 'Pacific/Honolulu',
		- 9  => 'America/Anchorage',
		- 8  => 'America/Los_Angeles',
		- 7  => 'America/Denver',
		- 6  => 'America/Tegucigalpa',
		- 5  => 'America/New_York',
		- 4  => 'America/Halifax',
		- 3  => 'America/Argentina/Buenos_Aires',
		- 3  => 'America/Sao_Paulo',
		- 2  => 'Atlantic/South_Georgia',
		- 1  => 'Atlantic/Azores',
		0    => 'Europe/Dublin',
		1    => 'Europe/Belgrade',
		2    => 'Europe/Minsk',
		3    => 'Asia/Kuwait',
		4    => 'Asia/Muscat',
		5    => 'Asia/Yekaterinburg',
		6    => 'Asia/Dhaka',
		7    => 'Asia/Krasnoyarsk',
		8    => 'Asia/Brunei',
		9    => 'Asi/Seoul',
		10   => 'Australia/Canberra',
		11   => 'Asia/Magadan',
		12   => 'Pacific/Fiji',
		13   => 'Pacific/Tongatapu',
	);

	return $timezones[ $id ];
}


function curl_get_contents($location, $user = false, $password = false){
	ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2");

	//setup agents
	$agents[] = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; Media Center PC 5.0)";
	$agents[] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)";
	$agents[] = "Opera/9.63 (Windows NT 6.0; U; ru) Presto/2.1.1";
	$agents[] = "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5";
	$agents[] = "Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.8.1.18) Gecko/20081203 Firefox/2.0.0.18";
	$agents[] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.16) Gecko/20080702 Firefox/2.0.0.16";
	$agents[] = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; en-us) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1";


	ob_start();
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $location);
	#curl_setopt($ch, CURLOPT_USERAGENT, array_rand($agents));

	#curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');


	if($user && $password){
		curl_setopt($ch, CURLOPT_USERPWD, $user.":".$password);
	}

	//Create And Save Cookies
	curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=b9fb80d57c8b6157d0e527de4ed0a96f');
	curl_setopt($ch, CURLOPT_COOKIE, 'ssdef094=9717f2cbb29bfa2344be2d4d379b8f20-1452022288');


	session_write_close();

	curl_exec($ch);
	curl_close($ch);
	$file_contents = ob_get_clean();

	return $file_contents;
}

function tor_get_contents($url){
	$proxy = 'tcp://localhost:9050';

	// Create a stream
	$opts = array(
		'http'=>array(
			'method'=>"GET",
			'header'=>"Accept-language: en\r\n" .
					  "Cookie: foo=bar\r\n",
			'proxy' => $proxy,
		)
	);

	$context = stream_context_create($opts);

	return file_get_contents($url, false, $cxContext);

}