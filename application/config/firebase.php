<?php
$production 				=	(ENVIRONMENT == 'production'?true:false);
$config['firebase_app_key'] = __DIR__ . '/'.($production?'notes-zen-prod-firebase-adminsdk-1y5q8-aa139b9dc0.json':'noteszen-838af-firebase-adminsdk-kxq4c-5d8260f7da.json');

$config['apiKey'] 			=	$production?'AIzaSyD-GVmp_y2pBimcs1rRLhdYM675h5vGMX4 ':'AIzaSyCQL52wTo2kPneTXH3mVm2w2_gJ7Wfvg8A';

$config['authDomain'] 		=	$production?'notes-zen-prod.firebaseapp.com':'noteszen-838af.firebaseapp.com';

$config['databaseURL'] 		=	$production?'https://notes-zen-prod.firebaseio.com':'https://noteszen-838af.firebaseio.com';

$config['projectId'] 		=	$production?'notes-zen-prod':'noteszen-838af';
$config['storageBucket'] 	=	$production?'notes-zen-prod.appspot.com':'noteszen-838af.appspot.com';

$config['messagingSenderId']=	$production?'398320415688':'997322162244';

$config['appId'] 			=	$production?'1:398320415688:android:5cdf4820771eb9fcf546fd':'1:997322162244:web:24ece1fa098b45e5a53c11';

$config['firebaseBaseurl'] =	$production?'https://us-central1-notes-zen-prod.cloudfunctions.net':'https://us-central1-noteszen-838af.cloudfunctions.net';