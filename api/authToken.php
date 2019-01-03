<?php
	$className = 'rtAuthToken';
	
	// odds are good we are generating an authToken, so we can't have login check be required.
	if(!isset($_REQUEST) || 
		!isset($_REQUEST['authToken']) || 
		$_REQUEST['authToken'] == 0
		){
		$loginCheckRequired = false;
	}
	
	require('includeInAll.php');
	
	// do anything special that needs doing
	
	require('updateObject.php');
	
	// do anything special that needs doing
	
	require('saveObject.php');
?>