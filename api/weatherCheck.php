<?php
	$className = 'rtTemp';
	
	// we want to check first to see if we've recently called the API; weather doesn't change often, so we don't need to check it constantly
	if(!isset($_REQUEST) || !isset($_REQUEST['postalCode'])){
		header('HTTP/1.0 400 Bad error');
		exit();
	}
	require_once($_SERVER['DOCUMENT_ROOT'].'/rtDemo/conf.php');
	
	$dbQuery = $dbCon->r('Select `hash` from `rtTemps` where `postalCode` = :postalCode and `createDateTime` >= NOW() - INTERVAL 1 HOUR',array(':postalCode'=>$_REQUEST['postalCode'])); //call the read with parameters
	$dbQuery->setFetchMode(PDO::FETCH_ASSOC);
	$dbResult = $dbQuery->fetch();
	if($dbResult){
		// recent check found; don't do a new API call
		$_REQUEST['hash'] = $dbResult['hash'];
	} else {
		// no recent check found; do an API call
		$curl = curl_init(); 
    
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://samples.openweathermap.org/data/2.5/weather?zip=".$_REQUEST['postalCode']."&appid=40342083d8f8335d6ef082dec1ecf0f0",
			CURLOPT_RETURNTRANSFER => 1
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		$info = curl_getinfo($curl);
		
		curl_close($curl);
		if($err != ''){
			throw new Exception("cURL Error: ". $err);
		}
		$results = json_decode($response,true);
		
		$_REQUEST['weather'] = ucwords($results['weather'][0]['description']);
		// Temp is returned in Kelvin (0K - 273.15) × 9/5 + 32 = -459.7°F
		$_REQUEST['tempInF'] = floor((($results['main']['temp']-273.15)*9/5)+32);
	}
	
	require('includeInAll.php');
	
	// do anything special that needs doing

	require('updateObject.php');
	
	// do anything special that needs doing
	require('saveObject.php');
?>