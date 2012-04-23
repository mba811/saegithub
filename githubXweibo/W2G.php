<?php
	include_once( 'config.php' );
	$kv = new SaeKV();
	$kv->init();
	$queue = new SaeTaskQueue('W2G');
	$ret = $kv->pkrget(KVDB_PERFIX.'W2G_', 100); 
	while (true) {
		foreach ($ret as $key=>$value)
		{
			$queue->addTask('http://' . $_SERVER['HTTP_APPNAME'] . '.sinaapp.com/githubXweibo/weibolist.php?uid='.str_replace(KVDB_PERFIX.'W2G_','',$key));
			$queue->push();
		}
		end($ret);
		$start_key = key($ret);
		$i = count($ret);
		if ($i < 100) break;
		$ret = $kv->pkrget(KVDB_PERFIX.'W2G_', 100, $start_key);
	}
?>