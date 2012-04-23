<?php

include_once( 'config.php' );
include_once( 'saet.ex.class.php' );
require_once '../php-github-lib/Github/Autoloader.php';


$wb_uid=$_GET['uid'];
if(trim($_GET['uid'])=='')die('uid呢?');
$kv = new SaeKV();
$kv->init();
$ot=$kv->get(KVDB_PERFIX.''.$wb_uid.'_OT');
$ots=$kv->get(KVDB_PERFIX.''.$wb_uid.'_OTS');
$ghu=$kv->get(KVDB_PERFIX.''.$wb_uid.'_GHU');
$ght=$kv->get(KVDB_PERFIX.''.$wb_uid.'_GHT');
if($ot && $ots && $ghu && $ght){}else{die('parm err');}

Github_Autoloader::register();
$github = new Github_Client();
$github->authenticate($ghu,$ght,Github_Client::AUTH_OAUTH_TOKEN);

$kvdb_lastcheck=KVDB_PERFIX.$wb_uid.'_LMC';

$kv = new SaeKV();
$kv->init();


$c = new SaeTClient( WB_AKEY , WB_SKEY , $ot,$ots);
$uid_get =$c->verify_credentials();
$uid = $uid_get['id'];
if(false && trim($uid)==''){
        $mail = new SaeMail();
    	$mail->quickSend( 
        	"git@stigliew.com" ,
        	"[saegithub][微博连接失败]" ,
       		print_r($uid_get,true),
       		"stig.gv@gmail.com" ,
       		"q33ny5bcd3a" 
    	);

    	$mail->clean(); 
        die('微博链接失败');
}
////////////KVDB init//////////////
$lastcheck=0;
$lastcheck = $kv->get($kvdb_lastcheck);
if($lastcheck === false){
	
	$lastcheck=0;
	$kv->add($kvdb_lastcheck, 0);
}
$lastcheck=intval($lastcheck);
$lastcheckupdate=$lastcheck;
///////////////////////////////////

$mentions=$c->mentions();

foreach( $mentions as $key => $value){
	if(intval($value['id'])>$lastcheck){
		
		if(intval($value['id'])>$lastcheckupdate)$lastcheckupdate=intval($value['id']);//最大id
		if($value['retweeted_status']["id"]){
			//echo '非原创<hr />';
		}else{
			//[issue repo="" title=""][/issue]
			$issue=strpos($value['text'],'['.WB_ISSUE_TAG);
			$issueend=strpos($value['text'],'[/'.WB_ISSUE_TAG.']');
			
			if($issue===false||$issueend===false||$issueend<$issue){
			}else{
				$repo=strpos($value['text'],WB_ISSUE_TAG_REPO.'="',$issue);
				$repo+=strlen(WB_ISSUE_TAG_REPO)+2;
				$repoend=strpos($value['text'],'"',$repo);
				
				$title=strpos($value['text'],WB_ISSUE_TAG_TITLE.'="',$issue);
				$title+=strlen(WB_ISSUE_TAG_TITLE)+2;
				$titleend=strpos($value['text'],'"',$title);
				
				$issuebody=strpos($value['text'],']',$issue);
				$issuebody++;
				
				$repostr=substr($value['text'],$repo,$repoend-$repo);
				$titlestr=substr($value['text'],$title,$titleend-$title);
				$issuebodystr=substr($value['text'],$issuebody,$issueend-$issuebody);
				
				$titlestr=$titlestr.' //新浪微博@'.$value['user']['screen_name'];
				echo 'repostr:'.$repostr.'<br />titlestr:'.$titlestr.'<br />issuebodystr:'.$issuebodystr.'<br />';
				$ghret=$github->getIssueApi()->open($ghu, $repostr, $titlestr, $issuebodystr);
				$commentstr=",issue已接收，请关注github页面：https://github.com/".$ghu."/$repostr/issues/";
				$c->send_comment($value['id'],'微博'.$value['id'].$commentstr);
			}
		}
	}
}

$kv->set($kvdb_lastcheck, $lastcheckupdate);//保存最后一次的最大微博id

?>
