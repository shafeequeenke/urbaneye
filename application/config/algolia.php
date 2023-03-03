<?php
// CHECK ENVIRONMENT
$production 				=	(ENVIRONMENT == 'production'?true:false);
// index name 
$config['indexName'] 		=	$production?'doubthelp_prod':'doubthelp_dev';
//appid
$config['algoliaAppId'] 	= 	$production?'5ZNG43LC3O':'5ZNG43LC3O';
//admin api key
$config['algoliaAdminKey'] 	=	$production?'85c8c73870aa597dc90a3dde058b86d6':'85c8c73870aa597dc90a3dde058b86d6';
//search api key
$config['algoliaSearchKey'] =	$production?'eff3c1fc0e349d2c2f6e095ebb3b132b':'eff3c1fc0e349d2c2f6e095ebb3b132b';

?>