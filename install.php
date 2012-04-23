<?php
include_once( './githubXweibo/config.php' );
$kv = new SaeKV();
$kv->init();
$admincheck = $kv->get(KVDB_PERFIX.'ADMIN');
if($admincheck === false){
}else{
	die('已经安装完成，请登录！');
}

@$kv->set(KVDB_PERFIX.'WB_AKEY', $_GET['AKY']);
$kv->add(KVDB_PERFIX.'WB_AKEY', $_GET['AKY']);
@$kv->set(KVDB_PERFIX.'WB_SKEY', $_GET['SKY']);
$kv->add(KVDB_PERFIX.'WB_SKEY', $_GET['SKY']);
@$kv->set(KVDB_PERFIX.'GH_CID', $_GET['CID']);
$kv->add(KVDB_PERFIX.'GH_CID', $_GET['CID']);
@$kv->set(KVDB_PERFIX.'GH_CS', $_GET['CS']);
$kv->add(KVDB_PERFIX.'GH_CS', $_GET['CS']);

$aky = $kv->get(KVDB_PERFIX.'WB_AKEY');
$sky = $kv->get(KVDB_PERFIX.'WB_SKEY');
$cid = $kv->get(KVDB_PERFIX.'GH_CID');
$cs = $kv->get(KVDB_PERFIX.'GH_CS');


include_once( 'saet.ex.class.php' );
$o = new SaeTOAuth( $aky , $sky  );
$keys = $o->getRequestToken();
$aurl = $o->getAuthorizeURL( $keys['oauth_token'] ,false , 'https://' . $_SERVER['HTTP_APPNAME'] . '.sinaapp.com/githubXweibo/weiboDone.php?ot='.$keys['oauth_token'].'&ots='.$keys['oauth_token_secret'] );
?>
安装：<hr />
<ul>
	<li><h3>步骤1,绑定新浪微博和Github的API：</h3><form method="get">
			微博appkey:<input type="text" name="AKY" value="<?php echo $aky;?>" /><br />
			微博appkey secret:<input type="text" name="SKY" value="<?php echo $sky;?>" /><br />
			Github Client ID:<input type="text" name="CID" value="<?php echo $cid;?>" /><br />
			Github Client Secret:<input type="text" name="CS" value="<?php echo $cs;?>" /><br />
			<input type="submit" value="确定" />
	</form></li>
	<li><h3>步骤2：</h3>
		<a href="<?=$aurl?>">绑定管理员微博帐号</a>
	</li>