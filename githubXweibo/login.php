<?php
include_once( 'config.php' );
include_once( 'saet.ex.class.php' );
$o = new SaeTOAuth( WB_AKEY , WB_SKEY  );
$keys = $o->getRequestToken();
$aurl = $o->getAuthorizeURL( $keys['oauth_token'] ,false , 'https://' . $_SERVER['HTTP_APPNAME'] . '.sinaapp.com/githubXweibo/weiboDone.php?ot='.$keys['oauth_token'].'&ots='.$keys['oauth_token_secret'] );
Header('Location:'.$aurl);
?>
<a href="<?=$aurl?>">Î¢²©ÕÊºÅµÇÂ¼</a>