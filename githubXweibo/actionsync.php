<?php
	$ignore=$_GET['i'];
	include_once( 'config.php' );
	include_once( 'saet.ex.class.php' );
	$wb_uid=$_GET['uid'];
	if(trim($_GET['uid'])=='')die('uid呢?');
	$kv = new SaeKV();
	$kv->init();
	$ot=$kv->get(KVDB_PERFIX.''.$wb_uid.'_OT');
	$ots=$kv->get(KVDB_PERFIX.''.$wb_uid.'_OTS');
	$ghu=$kv->get(KVDB_PERFIX.''.$wb_uid.'_GHU');
	$ght=$kv->get(KVDB_PERFIX.''.$wb_uid.'_GHT');
	if($ot && $ots && $ghu && $ght){}else{die('parm err');}

	$octocat=explode("|",OCTOCATS);
	$octocat=$octocat[rand(0,count($octocat)-1)];
	$octocat="http://octodex.github.com/images/".$octocat;
	echo $octocat;
	$c = new SaeTClient( WB_AKEY , WB_SKEY , $ot,$ots);
	$uid_get = $c->verify_credentials();
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
	
	$tempurl='https://api.github.com/users/'.$ghu.'/events/public';
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
			if($ignore!=$value['id'])continue;
		}
		$value['repo']['url']=str_replace('api.github.com/repos','github.com',$value['repo']['url']);
		if($value['type']=='IssuesEvent'){
			continue;
		}else if($value['type']=='PushEvent'){
			$url=$value['payload']['commits'][0]['url'];
			$url=str_replace('api.github.com/repos','github.com',$url);
			$message=$value['payload']['commits'][0]['message'];
			$message="pushed to ".$value['payload']['ref']." at ".$value['repo']['name']." ,commits:".$message." ,url:".$url;
			echo $message.'<br />';
			$ret=$c->upload( '#Github:event# '.$message,$octocat );
			var_dump($ret);
			die('one round over');
		}else if($value['type']=='CreateEvent'){
			$message="created ".$value['payload']['ref_type'].' '.$value['payload']["master_branch"];
			$message=$message." at ".$value['repo']['name']." ".$value['repo']['url'];
			echo $message.'<br />';
			$ret=$c->upload( '#Github:event# '.$message,$octocat );
			var_dump($ret);
			die('one round over');
		}else if($value['type']=='WatchEvent'){
			$message="started watching ".$value['repo']['name']." ".$value['repo']['url'];
			echo $message.'<br />';
			$ret=$c->upload( '#Github:event# '.$message,$octocat );
			var_dump($ret);
			die('one round over');
		}else if($value['type']=='ForkEvent'){
			$message="forked ".$value['repo']['name']." ".$value['repo']['url'];
			echo $message.'<br />';
			$ret=$c->upload( '#Github:event# '.$message,$octocat );
			var_dump($ret);
			die('one round over');
		}else if($value['type']=='FollowEvent'){
			$message="started following ".$value['payload']['target']['login'].' ,url'.$value['payload']['target']['html_url'];
			echo $message.'<br />';
			$ret=$c->upload( '#Github:event# '.$message,$octocat );
			var_dump($ret);
			die('one round over');
		}else{
			echo $value['id'].' '.$value['type'].'<br />';
		}
	}
	die();