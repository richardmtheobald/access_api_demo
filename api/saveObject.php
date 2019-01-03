<?php
	if(isset($customError) && $customError != array()){
		echo json_encode($customError);
	} else {
		if(!isset($needsSaving) || $needsSaving == true){
			$thing->saveData($dbCon);
		}
		echo json_encode($thing->export());
	}
	exit(); // regardless of what happens here, we have returned to the client. We do not want to continue any script past this point.
?>