<?php
include_once( 'config.php' );
include_once( 'saetv2.ex.class.php' );
require_once '../php-github-lib/Github/Autoloader.php';
Github_Autoloader::register();
$github = new Github_Client();
$github->authenticate(GITHUB_USER,GITHUB_PASS,Github_Client::AUTH_HTTP_PASSWORD);

$kvdb_lastcheck=KVDB_PERFIX.'lastmentioncheck';

$kv = new SaeKV();
$kv->init();


$c = new SaeTClientV2( WB_AKEY , WB_SKEY , WB_OAUTH2);
$uid_get = $c->get_uid();
$uid = $uid_get['uid'];
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

foreach( $mentions["statuses"] as $key => $value){
	if(intval($value['id'])>$lastcheck){
		
		if(intval($value['id'])>$lastcheckupdate)$lastcheckupdate=intval($value['id']);//最大id
		if($value['retweeted_status']["id"]){
			//echo '非原创<hr />';
		}else{
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
				$ghret=$github->getIssueApi()->open(GITHUB_USER, $repostr, $titlestr, $issuebodystr);
				$commentstr=",issue已接收，请关注github页面：https://github.com/".GITHUB_USER."/$repostr/issues/";
				$c->send_comment($value['id'],'微博'.$value['id'].$commentstr);
			}
		}
	}
}

$kv->set($kvdb_lastcheck, $lastcheckupdate);//保存最后一次的最大微博id


if( isset($_REQUEST['text']) ) {
	$ret = $c->update( $_REQUEST['text'] );	//发送微博
	if ( isset($ret['error_code']) && $ret['error_code'] > 0 ) {
		echo "<p>发送失败，错误：{$ret['error_code']}:{$ret['error']}</p>";
	} else {
		echo "<p>发送成功</p>";
	}
}
?>
