<?php

class rtUser extends rtObject {
	const tableName = 'rtUsers';
	const dates = array('createDate');
	const exportFields = array('id','hash','username','firstName','lastName','emailAddress','postalCode','createDate');
	
	protected $id = 0;
	protected $hash = '';
	protected $username = '';
	protected $passwordHash = '';
	protected $firstName = '';
	protected $lastName = '';
	protected $emailAddress = '';
	protected $postalCode = '64106';
	protected $createDate = '0000-00-00';
	
	public function __construct($args = null){
		if($this->get('id') == 0){
			$this->set('createDate',date('Y-m-d'));
		} else {
			$this->set('dontSaveKeys',array('passwordHash')); // time is an issue, so we are not going to allow people to update their password
		}
		return parent::__construct($args);
	}
}

?>