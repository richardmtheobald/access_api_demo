<?php
	// may be redundant depending on whether the file is called directly
	require_once($_SERVER['DOCUMENT_ROOT'].'/rtDemo/conf.php');
	
	if(!function_exists('exceptionHandler')){
		function exceptionHandler($e){ // turn PHP errors into JSON error messages
			$customError['error']['msg'] = $e->getMessage();
			$customError['error']['code'] = $e->getCode();
			$customError['error']['page'] = basename($_SERVER['PHP_SELF']);
			echo json_encode($customError);
			exit();
		}
		set_exception_handler('exceptionHandler');
	}
	// end potential redundant block
	
	if(!isset($_REQUEST) || $_REQUEST == null || $_REQUEST == array()){
		header('HTTP/1.0 400 Bad error');
		exit();
	}
	
	if(!isset($_REQUEST['authToken']) || !isset($_REQUEST['userId'])){ // we verify the authToken and the userId to help prevent session hi-jacking
		header('HTTP/1.0 401 Unauthorized');
		exit();
	}
	
	$customError = array();
	
	$dbQuery = $dbCon->r('Select * from `rtAuthTokens` where `hash` = :hash and `userId` = :userId and `createDateTime` >= NOW() - INTERVAL 2 HOUR',array(':hash'=>$_REQUEST['authToken'], ':userId'=>$_REQUEST['userId'])); //call the read with parameters
	$dbQuery->setFetchMode(PDO::FETCH_CLASS, 'rtAuthToken', array($PDOPassArgs));
	$dbResult = $dbQuery->fetch();
	
	if($dbResult == false){ // no valid authToken
		throw new Exception('Invalid auth token.',401);
	}
?>