<?php

abstract class rtObject {
	// tableName: required name of the table for the object
	// dontSaveKeys: optional array of fields we don't want to be able to overwrite
	// dates: optional array of dates that need to be converted from db format to pretty format and back
	// exportFields: optional array of keys if we don't want to export the entire object to the front end
	protected $neverSaveKeys = array('neverSaveKeys','tableName','dontSaveKeys','dates','exportFields');
	
	public function __construct($args = null){
		$className = get_class($this);
		//converts dates from db format to human readable format
		if(defined($className."::dates") && is_array($className::dates)) {
			foreach($className::dates as $value){
				(($this->get($value) == '0000-00-00' || $this->get($value) == '' || $this->get($value) == '1969-12-31') ? $this->set($value,'') : $this->set($value,date('m/d/Y',strtotime($this->get($value)))));
			}
		}
	}
	
	public function set($variable,$value){ //pseudo "setter magic method"
		if(in_array($variable,get_class_vars(get_class($this)))){
			return $this->$variable = $value;
		} else {
			return false;
		}
	}
	
	public function get($variable){ //pseudo "getter magic method"
		if(isset($variable) && isset($this->$variable)){
			return $this->$variable;
		} else {
			return false;
		}
	}
	
	public function saveData(&$dbCon){
		$noerror = true;
		$className = get_class($this);
		if(array_key_exists('hash',get_class_vars(get_class($this))) && ($this->hash === "" || $this->hash === 0 || $this->hash === null || empty($this->hash))){ //has a null hash field; create hash
			$validchars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$hash = '';
			for($i = 0; $i < 20; $i++){
				$hash .= substr($validchars,rand(0,strlen($validchars)-1),1);
			}
			$this->set('hash',$hash);
		}
		//converts dates from human readable format to db format
		if(defined($className."::dates") && is_array($className::dates)) {
			foreach($className::dates as $value){
				(($this->get($value) == '00-00-0000' || $this->get($value) == '' || $this->get($value) == '1969-12-31') ? $this->set($value,'0000-00-00') : $this->set($value,date('Y-m-d',strtotime(str_replace('-', '/',$this->get($value))))));
			}
		}
		//end convert dates
		//combines dontSaveKeys and neverSaveKeys into dontSaveKeys
		if (defined($className.'::dontSaveKeys')){
			$this->dontSaveKeys = $className::dontSaveKeys;
			foreach($this->get('neverSaveKeys') as $value){
				array_push($this->dontSaveKeys,$value);
			}
		} else {
			$this->dontSaveKeys = $this->get('neverSaveKeys');
		}
		//end combining dontSaveKeys and neverSaveKeys into dontSaveKeys
		//start the insert/update
		if(defined($className.'::tableName') && isset($dbCon)){
			$values = array();
			foreach (get_class_vars(get_class($this)) as $key => $value) {
				if($noerror == true){
					if (!in_array($key,$this->get('dontSaveKeys'))){
						$val = $this->get($key);
						if(!is_object($val)){
							$val = str_replace(array("‘","’"),"'",$val);
							$val = str_replace(array("“","”"),'"',$val);
						}
						$values[$key] = $val;
					}
				}
			}
			if(defined($className.'::tableName')){
				$noerror = $dbCon->cud($className::tableName,$values,array('id'=>$this->get('id')));
				if($this->get('id') == 0){
					$this->set('id',$dbCon->lastInsertID());
				}
			}
		}
		return $noerror;
	}
	
	public function delete(&$dbCon){
		$noerror = true;
		$className = get_class($this);
		if(defined($className.'::tableName')){
			$noerror = $dbCon->cud($className::tableName,array('id'=>$this->get('id')),array('id'=>$this->get('id'),'delete'=>true));
		}
	}
	
	public function export(){
		$className = get_class($this);
		$returnArray = array();
		if(defined($className.'::exportFields')){
			foreach($className::exportFields as $key){
				$returnArray[$key] = $this->get($key);
			}
		} else {
			foreach(get_class_vars(get_class($this)) as $key => $val){
				$returnArray[$key] = $this->get($key);
			}
		}
		return $returnArray;
	}
}

?>