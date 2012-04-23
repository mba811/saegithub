<?php
$kv = new SaeKV();
$kv->init();

define( "KVDB_PERFIX" , 'SAE_GH_' );

define( "WB_AKEY" , $kv->get(KVDB_PERFIX.'WB_AKEY') );
define( "WB_SKEY" , $kv->get(KVDB_PERFIX.'WB_SKEY') );


define( "GH_client_id" , $kv->get(KVDB_PERFIX.'GH_CID') );
define( "GH_client_secret" , $kv->get(KVDB_PERFIX.'GH_CS') );

define("WB_ISSUE_TAG",'issue');
define("WB_ISSUE_TAG_REPO",'repo');
define("WB_ISSUE_TAG_TITLE",'title');

define("OCTOCATS",'snowoctocat.jpg|electrocat.png|aidorucat.png|codercat.jpg|strongbadtocat.png|adventure-cat.jpg|doctocat-brown.jpg|dojocat.jpg|defunktocat.png|herme-t-crabb.png|saint-nicktocat.jpg|orderedlistocat.jpg|thanktocat.jpg|megacat.jpg|linktocat.jpg|plumber.jpg|octotron.jpg|baracktocat.jpg|octocat-de-los-muertos.jpg|grim-repo.jpg|father_timeout.jpg|waldocat.jpg|hipster-partycat.jpg|riddlocat.jpg|visionary.jpg|oktobercat.jpg|shoptocat.jpg|nyantocat.gif|octdrey-catburn.jpg|spectrocat.png|bear-cavalry.jpg|andycat.jpg|notocat.jpg|dodgetocat.jpg|cloud.jpg|scarletteocat.jpg|poptocat.jpg|jenktocat.jpg|xtocat.jpg|chellocat.jpg|cherryontop-o-cat.jpg|supportcat.jpg|collabocats.jpg|constructocat2.jpg|total-eclipse-of-the-octocat.jpg|pacman-ghosts.jpg|okal-eltocat.jpg|octoclark-kentocat.jpg|agendacat.jpg|ironcat.jpg|inspectocat.jpg|jean-luc-picat.jpg|spocktocat.jpg|wilson.jpg|swagtocat.jpg|drunktocat.jpg|dolla-dolla-bill-yall.jpg|hubot.jpg|monroe.jpg|trekkie.jpg|octonaut.jpg|bouncer.jpg|founding-father.jpg|pythocat.jpg|drupalcat.jpg|socialite.jpg|setuptocat.jpg|repo.jpg|forktocat.jpg|benevocats.jpg|scottocat.jpg|puppeteer.jpg|octobiwan.jpg|class-act.jpg|original.jpg');