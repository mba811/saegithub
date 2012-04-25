<?php
include_once( 'config.php' );
include_once( 'saet.ex.class.php' );
require_once '../php-github-lib/Github/Autoloader.php';
$kv = new SaeKV();
$kv->init();

$ot=trim($_REQUEST['ot']);
$ots=trim($_REQUEST['ots']);

if($_REQUEST['oauth_verifier']){
	$o = new SaeTOAuth( WB_AKEY , WB_SKEY , $ot , $ots  );

	$last_key = $o->getAccessToken(  $_REQUEST['oauth_verifier'] ) ;

	$ot=trim($last_key['oauth_token']);
	$ots=trim($last_key['oauth_token_secret']);
}
if($ot!='' && $ots!='')
{
	
	$c = new SaeTClient( WB_AKEY , WB_SKEY , $ot , $ots  );
	$user=$c->verify_credentials();
	if(trim($user['id'])=='')die('微博登录错误！请重试！');
	@$kv->set(KVDB_PERFIX.''.$user['id'].'_OT', $ot);
	@$kv->set(KVDB_PERFIX.''.$user['id'].'_OTS', $ots);
	$kv->add(KVDB_PERFIX.''.$user['id'].'_OT', $ot);
	$kv->add(KVDB_PERFIX.''.$user['id'].'_OTS', $ots);
	$githubcallbackurl='https://' . $_SERVER['HTTP_APPNAME'] . '.sinaapp.com/githubXweibo/githubcallback.php?ot='.$ot.'&ots='.$ots;
	$githubcallbackurl=urlencode($githubcallbackurl);
	if($_REQUEST['ghu'] && $_REQUEST['ght']){
		Github_Autoloader::register();
		$github = new Github_Client();
		$github->authenticate($_REQUEST['ghu'],$_REQUEST['ght'],Github_Client::AUTH_OAUTH_TOKEN);
		$emails = array();
		try
		{
			$emails = $github->getUserApi()->getEmails();
			@$kv->set(KVDB_PERFIX.''.$user['id'].'_EML', $emails[0]);
			$kv->add(KVDB_PERFIX.''.$user['id'].'_EML', $emails[0]);
			@$kv->set(KVDB_PERFIX.''.$user['id'].'_GHU', $_REQUEST['ghu']);
			@$kv->set(KVDB_PERFIX.''.$user['id'].'_GHT', $_REQUEST['ght']);
			$kv->add(KVDB_PERFIX.''.$user['id'].'_GHU', $_REQUEST['ghu']);
			$kv->add(KVDB_PERFIX.''.$user['id'].'_GHT', $_REQUEST['ght']);

			if($_REQUEST['lmc'])@$kv->set(KVDB_PERFIX.''.$user['id'].'_LMC', $_REQUEST['lmc']);
			$kv->add(KVDB_PERFIX.''.$user['id'].'_LMC', $_REQUEST['lmc']);
			if($_REQUEST['G2W']=='1')
				$kv->add(KVDB_PERFIX.'G2W_'.$user['id'].'', 1);
			else
				$kv->delete(KVDB_PERFIX.'G2W_'.$user['id'].'');
			if($_REQUEST['W2G']=='1')
				$kv->add(KVDB_PERFIX.'W2G_'.$user['id'].'', 1);
			else
				$kv->delete(KVDB_PERFIX.'W2G_'.$user['id'].'');
			
			echo '已经修改github设置!';
		}
		catch(Github_HttpClient_Exception $e)
		{
			echo '未能修改设置,验证Github帐号时出错!';
			
		}
		echo '<hr />';

		
	}
	$admincheck = $kv->get(KVDB_PERFIX.'ADMIN');
	if($admincheck === false){
		echo '新浪微博用户：@'.$user['name'].' 是管理员！安装完成！<hr />';
		$kv->add(KVDB_PERFIX.'ADMIN', $user['id']);
		$admincheck = $kv->get(KVDB_PERFIX.'ADMIN');
	}else{
		echo '新浪微博用户：@'.$user['name'].' 欢迎！<hr />';
	}
		$ghu = $kv->get(KVDB_PERFIX.''.$user['id'].'_GHU');
		$ght = $kv->get(KVDB_PERFIX.''.$user['id'].'_GHT');
		$lmc = $kv->get(KVDB_PERFIX.''.$user['id'].'_LMC');
		if(trim($lmc)=='')$lmc='0';
		if($ghu === false){
			echo '未绑定github帐号，可以绑定:';
		}else{
			echo '已绑定github帐号'.$ghu.'，可以更改:';
		}
		$G2W=$kv->get(KVDB_PERFIX.'G2W_'.$user['id'].'');
		$W2G=$kv->get(KVDB_PERFIX.'W2G_'.$user['id'].'');
		
		if($G2W === false){$G2W='';}else{$G2W='checked="yes"';}
		if($W2G === false){$W2G='';}else{$W2G='checked="yes"';}
		?>
			<hr />
			<form method="get">
			<table>
			<tr><td>
			Github用户名:</td><td><?php echo $ghu;?></td>
			</tr>
			<tr><td>
			Github Access Token:</td><td><?php echo $ght;?></td>
			</tr>
			<tr><td>
			</td><td>
			<a href="https://github.com/login/oauth/authorize?client_id=<?php echo GH_client_id;?>&redirect_uri=<?php echo $githubcallbackurl;?>&scope=user,public_repo">重新获取Token</a>
			</td></tr>
			<tr><td>
			LMC:</td><td><input type="text" name="lmc" value="<?php echo $lmc;?>" />
			</td></tr>
			<tr><td></td><td>
			<input type="checkbox" name="G2W" value="1" <?php echo $G2W;?> /> 同步Github事件到微博<br />
			<input type="checkbox" name="W2G" value="1" <?php echo $W2G;?> /> 同步微博@ 到Github Issue<br />
			</td></tr>
			<tr><td>
			<input type="hidden" name="ghu" value="<?php echo $ghu;?>" />
			<input type="hidden" name="ght" value="<?php echo $ght;?>" />
			<input type="hidden" name="ot" value="<?php echo $ot;?>" />
			<input type="hidden" name="ots" value="<?php echo $ots;?>" />
			<input type="submit" value="确定" />
			</td></tr>
			</table>
			</form>
		<?php
		if($admincheck == $user['id']){
			echo '您好，管理员！<hr />';
			if($_REQUEST['rmkvdb']){
				$kv->delete($_REQUEST['rmkvdb']);
				echo 'KVDB'.$_REQUEST['rmkvdb'].'已经删除！';
			}
			?>
		<form method="get">
			从KVDB删除一个:<input type="text" name="rmkvdb" value="" /><br />
			<input type="hidden" name="ot" value="<?php echo $ot;?>" />
			<input type="hidden" name="ots" value="<?php echo $ots;?>" />
			<input type="submit" value="确定" />
		</form>
		<form method="get">
			查看KVDB_prefix:<input type="text" name="listkv_prefix" value="<?php echo KVDB_PERFIX;?>" /><br />
			<input type="hidden" name="listkv" value="1" />
			<input type="hidden" name="ot" value="<?php echo $ot;?>" />
			<input type="hidden" name="ots" value="<?php echo $ots;?>" />
			<input type="submit" value="查看KVDB" />
		</form>
		<form method="get">
			<input type="hidden" name="connecttest" value="1" />
			<input type="hidden" name="ot" value="<?php echo $ot;?>" />
			<input type="hidden" name="ots" value="<?php echo $ots;?>" />
			<input type="submit" value="测试Github连接" />
		</form>

			<?php
			if($_REQUEST['listkv']){
				echo '<table><tr><td>key</td><td>value</td></tr>';
				$ret = $kv->pkrget($_REQUEST['listkv_prefix'], 100);     
				while (true) {
					foreach ($ret as $key=>$value)
					{
						echo '<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';
					}                       
					end($ret);                                
					$start_key = key($ret);
					$i = count($ret);
					if ($i < 100) break;
					$ret = $kv->pkrget($_REQUEST['listkv_prefix'], 100, $start_key);
				}
				echo '</table>';
			}
			if($_REQUEST['connecttest']){
				echo '连接测试<hr />';
				$ghu=$kv->get(KVDB_PERFIX.''.$admincheck.'_GHU');
				$ght=$kv->get(KVDB_PERFIX.''.$admincheck.'_GHT');
				Github_Autoloader::register();
				$github = new Github_Client();
				$github->authenticate($ghu,$ght,Github_Client::AUTH_OAUTH_TOKEN);
				$users = $github->getUserApi()->search($ghu);
				var_dump($users);
				echo '<hr/>';
				$emails = $github->getUserApi()->getEmails();
				var_dump($emails);
				echo '<hr/>';
				$users = $github->getUserApi()->getFollowing($ghu);
				var_dump($users);
			}
		
		}
}else{
	var_dump($last_key);
	echo '<hr />';
	die('bad!');
}
?>