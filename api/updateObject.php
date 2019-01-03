<?php
	// this cycles through an object's properties and checks to see whether or not we have sent new data to overwrite them with. If so, we update them accordingly
	if(isset($thing) &&
		is_object($thing) &&
		!is_array($thing) &&
		(!isset($className) || $className = '' || $className = get_class($thing)) // if we provided a className, we want to be sure the thing is of type className
		){
		foreach($_REQUEST as $key=>$val){
			if($thing->get($key) !== false && $thing->get($key) != $val){
				$thing->set($key,$val);
				if(isset($needsSaving) && $needsSaving == false){
					$needsSaving = true;
				}
			}
		}
	}

?>