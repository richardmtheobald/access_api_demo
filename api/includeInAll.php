<?php
	// may be redundant depending on whether the file requires loginCheck.php
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
	
	if(!isset($_REQUEST) || $_REQUEST == null || $_REQUEST == array()){
		header('HTTP/1.0 400 Bad error');
		exit();
	}
	// end potential redundant block

	if(!isset($loginCheckRequired) || $loginCheckRequired == true){ // default behavior is to require the login check, but there are cases where we want to allow a non-logged in user to use some actions
		require('loginCheck.php');
	}
	
	$PDOPassArgs = array();
	$PDOPassArgs['dbCon'] = $dbCon;
	$customError = array();
	$needsSaving = false;
	if(isset($className)){ // this API will expect an object of type class
		if(isset($_REQUEST['hash']) && $_REQUEST['hash'] != '0') {
			// we have passed a hash. Let's load up the object.
			$query = 'Select * from `'.$className::tableName.'` where `hash` = :hash';
			$params = array(':hash'=>$_REQUEST['hash']);
		} elseif(isset($altIdField) && isset($_REQUEST[$altIdField])) {
			// we allow for the opportunity to custom lookup by another unique column
			$query = 'Select * from `'.$className::tableName.'` where `'.$altIdField.'` = :altIdField';
			$params = array(':altIdField'=>$_REQUEST[$altIdField]);
		}
		if(isset($query) && isset($params)){
			$dbQuery = $dbCon->r($query,$params); //call the read with parameters
			$dbQuery->setFetchMode(PDO::FETCH_CLASS, $className, array($PDOPassArgs));
			$thing = $dbQuery->fetch();
		}
		
		if (!isset($thing) || $thing == false){ // Create new object if no object is found or passed
			$thing = new $className;
		}
	}
?>