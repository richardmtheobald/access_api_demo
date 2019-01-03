<?php

class rtTemp extends rtObject {
	const tableName = 'rtTemps';
	
	protected $id = 0;
	protected $hash = '';
	protected $postalCode = 64106;
	protected $weather = '';
	protected $tempInF = 0;
	protected $createDateTime = '0000-00-00';
	
	public function __construct($args = null){
		if($this->get('id') == 0){
			$this->set('createDateTime',date('Y-m-d H:i:s'));
		}
		return parent::__construct($args);
	}
}

?>