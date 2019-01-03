<?php

class rtDbCon { //this object is really just a wrapper for a PDO object
	protected $con; //pdo object
	protected $lastInsertID;
	
	public function getCon() {
		return $this->con;	
	}
	
	public function __construct($dbhost,$dbname,$dbuser,$dbpass){
		try {
			$connected = false;
			$maxtries=5;
			$try = 0;
			while($connected == false and $try <= $maxtries){
				try {
					$this->con = new PDO("mysql:host=" . $dbhost . ";dbname=" . $dbname, $dbuser, $dbpass);
					$connected = true;
					$this->con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
				} catch (PDOException $e){
					if(strpos($e->getMessage(),'Too many connections') === false){
						throw $e;
					}
				}
				if($connected == false){
					$try++;
					usleep((rand(10,30)*10000)); //connection failed. Wait to retry.
				}
			}
			if($connected == false){
				throw new Exception('Fatal Error: Unable to connect to database.');
			}
		}
		catch(Exception $e) {
			if(strpos($e->getMessage(),'Too many connections')!==false){
				die("The number of concurrent attempted database connections has exceeded our server's capacity. Please wait a few moments and try again.");
			} else {
				die($e->getMessage());
			}
		}
	}
	
	public function r($query,$params = null){
		try {
			$exceptionmessage = "Parameters need to be of the form:<br>";
			$exceptionmessage .= "&nbsp;Array<br>";
			$exceptionmessage .= "&nbsp;(<br>";
			$exceptionmessage .= "&nbsp;&nbsp;[:parameter1] => value1<br>";
			$exceptionmessage .= "&nbsp;&nbsp;[:parameter2] => value2<br>";
			$exceptionmessage .= "&nbsp;)<br>";
			if(is_array($params) && !empty($params)){
				$returnVal = $this->con->prepare($query);
				foreach($params as $param=>&$val){ // bindParam attaches via reference
					$returnVal->bindParam($param,$val);
				}
				$returnVal->execute();
				return $returnVal;
			} else {
				throw new Exception($exceptionmessage);
			}
		}
		catch(Exception $e) {
			$exceptionmessage = "Error: " . $e->getMessage();
			throw new Exception($exceptionmessage);
		}
	}
	
	public function cud($table, array $values, $args){ //create update delete query builder
		if(!isset($args['id'])) { $args['id'] = 0; }
		if(!isset($args['delete'])) { $args['delete'] = false; }
		$keystring = '';
		$preTable = '';
		$postTable = '';
		$postKeyString = '';
		if($args['id'] == 0 && $args['delete'] == false){
			$preTable = 'insert into ';
			$postTable = ' (';
			$valuestring = '';
			foreach ($values as $key=>$value){
				$keystring .= '`' . $key . '`,'; 
				$valuestring .= '?,';
			}
			$valuestring = rtrim($valuestring,',');
			$postKeyString = ') values (' . $valuestring . ');';
		} else {
			if($args['delete'] == false){
				$preTable = 'update ';
				$postTable = ' set ';
				foreach ($values as $key=>$value){
					$keystring .= '`' . $key . '` = ?,'; 	
				}
				$postKeyString = ' where `id` = ' . $args['id'] . ';';
			} else {
				$preTable = 'delete from ';
				$postTable = ' where `id` = ?;';
			}
		}
		$keystring = rtrim($keystring,',');
		$query = $preTable . $table . $postTable . $keystring . $postKeyString;
		try {
			$this->exe = $this->con->prepare($query);
			$this->exe->execute(array_values($values));
			if($args['id'] == 0){
				$this->lastInsertID = $this->con->lastInsertID();
			}
		}
		catch(PDOException $e) {
			throw new Exception ("Error: " . $e->getMessage());
		}
	}
	
	public function lastInsertID(){
		return $this->lastInsertID;
	}
}
?>