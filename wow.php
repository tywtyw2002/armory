<?php
include ("config.php");
$expires = $expires * 24 *3600;
if(function_exists('date_default_timezone_set')) {
	@date_default_timezone_set('UTC');
}
$selfroot= dirname(__FILE__).'/';
if($debug)
$id = "stgl";
else
$id = $_GET[id];

if ( empty($id) || $id != $char[$id][id])
	mydie ("error: unknow id");
$now = time();
//echo $cachefile;
@include($cachefile."_".$id.".php");

/*
cache file defined
$cache[id] = array (
'id' => "id",
'lastcreate' =>"unix time",
'cachename' =>" only name no / and path");
*/
if ($now - $cache[$id][lastcreate] > $expires)
 {
    //renew
    recreate($id);
 //print "renew";
    }else{
    //print "!renew";
     if ($savemethod==1){
     $file_name = $selfroot.$filepath."/".$id."/".$cache[$id][cachename];
     //print $file_name;
     showsign($file_name);
     }elseif($savemethod==2){
     $file_name = $selfroot.$filepath."/".$cache[$id][cachename];
     //print $file_name;
     showsign($file_name);
     }
 
 }
 
 function downloadpic ($url,$file_name)
 {
 //global $savemethod,$filepath,$selfroot;
 $get_file = file_get_contents($url);
 $fp = fopen($file_name,"wb") or mydie('error open signfile');
 fwrite($fp,$get_file);
  fclose($fp);

 
 
 }
 
 function showsign($file)
 {
 if (file_exists($file)) { 
    //header('Transfer-Encoding: chunked'); 
    header('Content-Type: image/jpeg'); 
    //header('Content-Disposition: attachment; filename='.basename($file)); 
    //header('Content-Transfer-Encoding: binary'); 
    header('Expires: 0'); 
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0'); 
    header('Pragma: public'); 
    header('Content-Length: ' . filesize($file)); 
    ob_clean(); 
    flush(); 
    readfile($file); 
    exit; 
}else{
mydie("read file error.");
}
 }
 
 function recreate( $id ){
 global $char,$host,$style,$savemethod,$filepath,$selfroot;
$sh = "zone=tw&server={$char[$id][relam]}&char={$char[$id][char]}&live=0&";
$style = (empty($char[$id][style])? $style: $char[$id][style]);
$signlink = updatesign($sh, $host, $style);
//echo $signlink;
 if ($savemethod==1){
     $signname = date("Ymd").".jpg";
     $file_name = $selfroot.$filepath."/".$id."/".$signname;
     if(!is_dir($selfroot.$filepath))
        mkdir($selfroot.$filepath,"0777");
      elseif(!is_dir($selfroot.$filepath."/".$id))
        mkdir($selfroot.$filepath."/".$id,"0777");
     }elseif($savemethod==2){
     $signname = $id.".jpg";
     $file_name = $selfroot.$filepath."/".$signname;
     if(!is_dir($selfroot.$filepath))
        mkdir($selfroot.$filepath,"0777");
        unlink($file_name);
       }
downloadpic($signlink , $file_name);       
gencache($id, $signname);
renewlog($id);
showsign($file_name);
}

function gencache($id, $name){
global $cachefile,$selfroot;
/*
cache file defined
$cache[id] = array (
'id' => "id",
'lastcreate' =>"unix time",
'cachename' =>" just name no / and path");
*/
$fp = fopen(($selfroot.$cachefile."_".$id.".php"),"wb") or mydie("error to create cache file.");
$contents = "\$cache[$id] = array(\r\n";
$contents .= "\"id\" => \"$id\",\r\n";
$contents .= "\"lastcreate\" => \"".time()."\",\r\n";
$contents .= "\"cachename\" => \"$name\",\r\n";
$contents .= ");";
fwrite($fp, "<?php\r\n//Sablog-X cache file\r\n//Created on ".date('Y-m-d H:i:s')."\r\n\r\n".$contents."\r\n\r\n?>");
fclose($fp);
}
function updatesign($sh, $host, $style){
//echo $sh."\n";
//print strlen($sh);
//$json = '{"error":0,"source":"Cached: 30-12-2010 05:51","chars":1,"msg":"Character <strong><\/strong> loaded successfully!"}';
//$json = json_decode($json);
//print_r ($json);
//$err = $json->{'error'}; 
//echo $err;
//setp 1. post charater data.
$result = network("POST", $host, 'ajax/ajax_wowLoad.php', $sh, '');
if(empty($result[json]))
 mydie('error: setp5, no json data!.');
$json = json_decode($result[json]);
if ($json->{'error'}!=0)
  mydie("error: setp1, get character data error.");
$cookies = trim($result[cookies]);
if (empty($cookies))
  mydie("error: setp1, no cookies.");
unset($result);
//setp 2 post sign type default is Sign2 Gs + 3 items. 

$result = network("POST", $host, 'ajax/ajax_wowType.php', 'signType=Sign2', $cookies);
if(!empty($result[cookies])){
  echo "always cookies is $cookies\nnew cookies is {$result[cookies]}";
  mydie("error: setp2, return cookies.");
}
//print_r ($result);
//setp 3   set sign styles.
$result = network("POST", $host, 'ajax/ajax_styles.php', $style, $cookies);
//in this setp no error except connect failed.

//setp 4 generat sign
//1293701409
//1293692435073
$tmpurl = 'preview.php?'.time()."222";
$result = network("GET", $host, $tmpurl, '', $cookies);
if(empty($result[jpeg]))
  mydie("error: setp4, no pic return");
unset($result);

//setp5 create sign
$result = network("POST", $host, 'ajax/ajax_save.php', 'save=1', $cookies);
if(empty($result[json]))
 mydie('error: setp5, no json data!.');
$json = json_decode($result[json]);
if ($json->{'error'}!=0)
  mydie("error: setp5, create sign error.");
if (empty($json->{'link'}))
  mydie("error: setp5, no url retuen.");

  return ($json->{'link'});
}
function network( $method, $host, $url, $content, $cookies)
{
	$timeout = 30;
	$fp = fsockopen($host , 80, $err, $errstr, $timeout);
	if (!$fp) {
		echo "$errstr ($errno)<br />\n";
		die();
    } else {
		$len = strlen($content);
		$out = "$method /$url HTTP/1.1\r\n";
		$out .= "Host: $host\r\n";
		$out .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13\r\n";
		
		if($method == "GET")
        $out .= "Accept: image/png,image/*;q=0.8,*/*;q=0.5\r\n";
    else
        $out .= "Accept: application/json, text/javascript, */*\r\n";
		$out .= "Accept-Encoding: deflate\r\n";
		//$out .= "Keep-Alive: 115\r\n";
		//$out .= "Connection: keep-alive\r\n";
		$out .= "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\n";
		$out .= "X-Requested-With: XMLHttpRequest\r\n";
		$out .= "Referer: http://www.best-signatures.com/wow-signature-generator.html\r\n";
		if (!empty($cookies))
		{
			$out .= "Cookie: PHPSESSID=$cookies\r\n";
			//Cookie: PHPSESSID=n36u2oqsrjhap4sde20islrpa2
		}
		$out .= "Content-Length: $len\r\n";
		$out .= "Connection: Close\r\n\r\n";
		$out .= $content;
		//print $out;
		fwrite($fp, $out);
    while (!feof($fp))
	{
		//echo fread($fp,4096);
		$result = fgets($fp, 4096);
		//echo $result;
		//Set-Cookie: PHPSESSID=n36u2oqsrjhap4sde20islrpa2; path=/
		if(preg_match('/^Set-Cookie:.+PHPSESSID=[a-z0-9]+;/', $result)){
			$array[cookies] = preg_replace('/^Set-Cookie:.+PHPSESSID=([a-z0-9]+);.+$/','$1',$result);
		//echo $array[cookies];
		}
		
		//json
		if(preg_match('/^{"error":.+"}$/',$result)){
			$array[json] = $result;
		}
		if(preg_match('/JFIF/',$result))
      $array[jpeg] = $result;
    }
    fclose($fp);
    
	return $array;
	}


}
function mydie($contents)
{
echo $contents;
$fp = fopen("error.log","a");
fwrite($fp, date('Y-m-d H:i:s')."\t$contents\r\n");
fclose($fp);
die();
}
function renewlog($id)
{
//echo $contents;
$fp = fopen("renew.log","a");
fwrite($fp, date('Y-m-d H:i:s')."\t$id\r\n");
fclose($fp);
}

?>