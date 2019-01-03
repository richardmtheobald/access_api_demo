<?php
	$className = 'rtLoginAttempt';
	$timeOutInMinutes = 5;
	$numberOfAttemptsBeforeLockOut = 3;
	
	// login check can't be required on a login attempt
	$loginCheckRequired = false;
	
	require('includeInAll.php');
	
	// do anything special that needs doing
	// verify username and password
	if(!isset($_REQUEST['username']) || $_REQUEST['username'] == '' || !isset($_REQUEST['password']) || $_REQUEST['password'] == ''){
		throw new Exception('Missing credentials',401);
	} else {
		$dbQuery = $dbCon->r('SELECT `rtUsers`.`id`,`hash`,`username`, `firstName`, `postalCode`, `passwordHash`, ifnull(a.`attempts`,0) as "attempts" FROM `rtUsers` left join (select `userId`, count(`id`) as "attempts" from `rtLoginAttempts` where `successful` = 0 and `createDateTime` >= NOW() - INTERVAL '.(int) $timeOutInMinutes.' MINUTE group by `userId`) a on `rtUsers`.`id` = a.`userId` where `username` = :username',array(':username'=>$_REQUEST['username']));
		$dbQuery->setFetchMode(PDO::FETCH_ASSOC);
		$dbResult = $dbQuery->fetch();
		if($dbResult == false || $dbResult['attempts'] > $numberOfAttemptsBeforeLockOut || password_verify($_REQUEST['password'],$dbResult['passwordHash']) == false){
			$_REQUEST['successful'] = 0;
		} else {
			$_REQUEST['successful'] = 1;
			$authToken = new rtAuthToken();
			$authToken->set('userId',$dbResult['id']);
			$authToken->saveData($dbCon);
			$userInfo = array(
				'hash'=>$dbResult['hash'],
				'userId'=>$dbResult['id'],
				'authToken'=>$authToken->get('hash')
			);
		}
		if($dbResult != false){
			$_REQUEST['userId'] = $dbResult['id'];
		}
	}
	
	require('updateObject.php');
	
	// do anything special that needs doing
	// always save this object regardless if the attempt is successful or not
	$thing->saveData($dbCon);
	if(isset($customError) && $customError != array()){
		echo json_encode($customError);
	} else {
		if(isset($userInfo)){
			echo json_encode($userInfo);
		} else {
			throw new Exception('Either your credentials were invalid, or you have exceeded the maximum number of attempts. Please wait '.$timeOutInMinutes.' minutes and try again.',401);
		}
	}
?>