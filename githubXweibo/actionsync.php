<?php
	include_once( 'config.php' );
	include_once( 'saetv2.ex.class.php' );
	$octocat=explode("|",OCTOCATS);
	$octocat=$octocat[rand(0,count($octocat)-1)];
	$octocat="http://octodex.github.com/images/".$octocat;
	echo $octocat;
	$c = new SaeTClientV2( WB_AKEY , WB_SKEY , WB_OAUTH2);
	$uid_get = $c->get_uid();
	$uid = $uid_get['uid'];
	
	$tempurl='https://api.github.com/users/stigliew/events/public';
	$f = new SaeFetchurl();
	$kv = new SaeKV();
	$kv->init();
	$content = $f->fetch($tempurl);
	$content = array_reverse (json_decode($content, true));
	
	foreach( $content as $key => $value){
		$message='';
		$lastcheck = $kv->get('WB_GH_SYNC_'.$value['id']);
		if($lastcheck === false){
			$kv->add('WB_GH_SYNC_'.$value['id'], 0);
		}else{
			continue;
		}
		$value['repo']['url']=str_replace('api.github.com/repos','github.com',$value['repo']['url']);
		if($value['type']=='IssuesEvent'){
			continue;
		}else if($value['type']=='PushEvent'){
			$url=$value['payload']['commits'][0]['url'];
			$message=$value['payload']['commits'][0]['message'];
			$message="pushed to ".$value['payload']['ref']." at ".$value['repo']['name']." ,commits:".$message." ,url:".$url;
			echo $message.'<br />';
			$c->upload( '#Github:event#'.$message,$octocat );
			die('one round over');
		}else if($value['type']=='CreateEvent'){
			$message="created ".$value['payload']['ref_type'].' '.$value['payload']["master_branch"];
			$message=$message." at ".$value['repo']['name']." ".$value['repo']['url'];
			echo $message.'<br />';
			$c->upload( '#Github:event#'.$message,$octocat );
			die('one round over');
		}else if($value['type']=='WatchEvent'){
			$message="started watching ".$value['repo']['name']." ".$value['repo']['url'];
			echo $message.'<br />';
			$c->upload( '#Github:event#'.$message,$octocat );
			die('one round over');
		}else if($value['type']=='ForkEvent'){
			$message="forked ".$value['repo']['name']." ".$value['repo']['url'];
			echo $message.'<br />';
			$c->upload( '#Github:event#'.$message,$octocat );
			die('one round over');
		}else{
			echo $value['id'].' '.$value['type'].'<br />';
		}
	}
	die();