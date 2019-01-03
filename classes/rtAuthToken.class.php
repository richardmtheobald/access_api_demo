<?php

class rtAuthToken extends rtObject {
	const tableName = 'rtAuthTokens';
	
	protected $id = 0;
	protected $hash = '';
	protected $userId = 0;
	protected $createDateTime = '0000-00-00';
	
	public function __construct($args = null){
		if($this->get('id') == 0){
			$this->set('createDateTime',date('Y-m-d H:i:s'));
		}
		return parent::__construct($args);
	}
}

?>