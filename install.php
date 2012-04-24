<?php
include_once( './githubXweibo/config.php' );
$kv = new SaeKV();
$kv->init();
$admincheck = $kv->get(KVDB_PERFIX.'ADMIN');
if($admincheck === false){
}else{
	die('已经安装完成，请登录！');
}

@$kv->set(KVDB_PERFIX.'WB_AKEY', $_REQUEST['AKY']);
$kv->add(KVDB_PERFIX.'WB_AKEY', $_REQUEST['AKY']);
@$kv->set(KVDB_PERFIX.'WB_SKEY', $_REQUEST['SKY']);
$kv->add(KVDB_PERFIX.'WB_SKEY', $_REQUEST['SKY']);
@$kv->set(KVDB_PERFIX.'GH_CID', $_REQUEST['CID']);
$kv->add(KVDB_PERFIX.'GH_CID', $_REQUEST['CID']);
@$kv->set(KVDB_PERFIX.'GH_CS', $_REQUEST['CS']);
$kv->add(KVDB_PERFIX.'GH_CS', $_REQUEST['CS']);

$aky = $kv->get(KVDB_PERFIX.'WB_AKEY');
$sky = $kv->get(KVDB_PERFIX.'WB_SKEY');
$cid = $kv->get(KVDB_PERFIX.'GH_CID');
$cs = $kv->get(KVDB_PERFIX.'GH_CS');


include_once( 'saet.ex.class.php' );
$o = new SaeTOAuth( $aky , $sky  );
$keys = $o->getRequestToken();
$aurl = $o->getAuthorizeURL( $keys['oauth_token'] ,false , 'https://' . $_SERVER['HTTP_APPNAME'] . '.sinaapp.com/githubXweibo/weiboDone.php?ot='.$keys['oauth_token'].'&ots='.$keys['oauth_token_secret'] );
?>
安装SAEGithub：<hr />
<ul>
	<li><h3>步骤1：</h3>
		<p>请在 <a href="http://sae.sina.com.cn/?m=taskqueue&app_id=<?php echo $_SERVER['HTTP_APPNAME'];?>&ver=1" target="blank">TaskQueue</a>中新建名为"W2G"和"G2W"两个并发队列，参数随喜好。</p>
		<p>请在 <a href="http://sae.sina.com.cn/?m=vermng&a=sdk&app_id=<?php echo $_SERVER['HTTP_APPNAME'];?>&version=1" target="blank">代码编辑器</a> 中把config.yaml第五行方括号内文字删掉并且<strong>保存</strong>，以让CRON即时生效。</p>
		<p></p>
	</li>
	<li><h3>步骤2,绑定新浪微博和Github的API：</h3><form method="get">
			<a href="http://open.weibo.com/development" target="blank">微博应用申请传送门</a><br />
			<a href="https://github.com/settings/applications/new" target="blank">Github API申请传送门</a><br />
			微博appkey:<input type="text" name="AKY" value="<?php echo $aky;?>" /><br />
			微博appkey secret:<input type="text" name="SKY" value="<?php echo $sky;?>" /><br />
			Github Client ID:<input type="text" name="CID" value="<?php echo $cid;?>" /><br />
			Github Client Secret:<input type="text" name="CS" value="<?php echo $cs;?>" /><br />
			<input type="submit" value="确定" />
	</form></li>
	<li><h3>步骤3：</h3>
		<a href="<?=$aurl?>">绑定管理员微博帐号</a>
	</li>