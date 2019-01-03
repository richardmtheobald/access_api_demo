<?php

class rtLoginAttempt extends rtObject {
	const tableName = 'rtLoginAttempts';
	
	protected $id = 0;
	protected $username = '';
	protected $userId = 0;
	protected $successful = 0;
	protected $createDateTime = '0000-00-00';
	
	public function __construct($args = null){
		if($this->get('id') == 0){
			$this->set('createDateTime',date('Y-m-d H:i:s'));
		}
		return parent::__construct($args);
	}
}

?>