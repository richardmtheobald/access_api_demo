<?php
	$className = 'rtUser';
	
	// because of the nature of the user object, confusion could arise whether we're supposed to send the "id" for the user
	// or the userId for the auth verification or both. Both is preferred, but this allows us to fix the situation if they
	// only present one
	if(isset($_REQUEST)){
		if(!isset($_REQUEST['id']) && isset($_REQUEST['userId'])){
			$_REQUEST['id'] = $_REQUEST['userId'];
		}
		if(!isset($_REQUEST['userId']) && isset($_REQUEST['id'])){ 
			$_REQUEST['userId'] = $_REQUEST['id'];
		}
	}
	// end block
	
	// in all of these cases, we are assuming we are generating a new user, and so we do not want to run login check
	if(!isset($_REQUEST) || 
		!isset($_REQUEST['authToken']) || 
		$_REQUEST['authToken'] == '0' ||
		!isset($_REQUEST['id']) ||
		$_REQUEST['id'] == '0'
		){
		$loginCheckRequired = false;
	} else {
		$loginCheckRequired = true;
		//$loginCheckRequired = false;
	}
	
	require('includeInAll.php');
	
	// do anything special that needs doing
	if(isset($_REQUEST['password']) && $_REQUEST['password'] != '' && isset($_REQUEST['confirmPassword']) && $_REQUEST['confirmPassword'] == $_REQUEST['password']){
		$_REQUEST['passwordHash'] = password_hash($_REQUEST['password'],PASSWORD_DEFAULT); // they have sent a password; hash it.
	}
	
	require('updateObject.php');
	
	// do anything special that needs doing
	
	require('saveObject.php');
?>