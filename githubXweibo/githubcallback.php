<?php
  include_once( 'config.php' );
  require_once '../php-github-lib/Github/Autoloader.php';
  $ot=trim($_REQUEST['ot']);
  $ots=trim($_REQUEST['ots']);
  $code=trim($_REQUEST['code']);
  $f = new SaeFetchurl();
  $f->setMethod("post");
  $f->setPostData(
    array(
      "client_id"=> GH_client_id ,
      "client_secret" => GH_client_secret ,
      "code" => $code
    )
  );
  $content=$f->fetch("https://github.com/login/oauth/access_token");
  if($f->errno() == 0) {}
  else die($f->errmsg());
  $content = explode("&", $content);
  if(is_array ($content)){
	$content = explode("=", $content[0]);
	if(is_array ($content)){
		$f = new SaeFetchurl();
		$f->setMethod("get");

		$ght=$content[1];
		
		$content=$f->fetch("https://github.com/api/v2/json/user/show?access_token=".$ght);
		
		$content=json_decode ($content,true);

		$ghu=$content['user']['login'];
		if(trim($ghu)=='')die('验证帐号的时候出错！');
		$url='https://' . $_SERVER['HTTP_APPNAME'] . ".sinaapp.com/githubXweibo/weiboDone.php?ot=$ot&ots=$ots&ghu=$ghu&ght=$ght";
		echo '验证成功 <a href="'.$url.'">点击继续</a>';
	}
  }
  
?>