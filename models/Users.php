<?php
/*
Manage collection of user records
*/

class Users extends Base {	
	function __construct($data='') {
		$this->table = 'users';
		$this->cols = 'user_id,email,name,password,created,ended';
		$this->user_id = $this->getID();
		$this->filterKey = 'user_id';
		$this->init($data);
	}
	
	function add($data='') {
		$this->okToAdd = array('email', 'name', 'password');
		
		$this->obj->password = password_hash($this->obj->password, PASSWORD_DEFAULT);
		$this->valArr[ array_search('password', $this->keyArr) ] = $this->obj->password; 
		
		$User = $this->obj;
		$User->user_id = $this->insert();
		unset($User->password); //no need to communicate this back for privacy
		return $User;
	}
	
	function set() {
		if (Requester::isSysAdmin()) {
			array_push($this->okToSet, "ended");
			array_push($this->okToFilterBy, "user_id");		
		}		
		
		if (Requester::isUser($this->user_id)) {
			array_push($this->okToSet, "ended","email","name","password");
			array_push($this->okToFilterBy,"user_id","email");		
		}		
		
		$this->update();
	}
	
	function get() {
		if (Requester::isUser($this->user_id)) $info = $this->getToSelf();
		else $info = $this->getToAnon();
		
		return $info;
	}
	
	function getToAnon() {//limited public profile information
		$sql = "SELECT m.user_id, name, m.created, COUNT(DISTINCT(brand_id)) AS numMemberships, SUM(hours) AS totalHours
		FROM members m
		JOIN users ON m.user_id=users.user_id
		WHERE m.user_id IN (?) AND m.ended IS NULL
		GROUP BY m.user_id";
		
		return DBquery::get($sql, array($this->user_id));		
	}
	
	private function getToSelf() {
		$sql = "SELECT brand_id, b.name AS brand_name, member_id, m.created AS joined, role, hours 
			FROM members m
			JOIN brands b USING (brand_id)
			WHERE user_id=? AND m.ended IS NULL";
		
		$this->memberships = DBquery::get($sql, array($this->user_id));
		
		$sql = "SELECT a.brand_id, b.name AS brand_name, a.account_id AS account_id, a.name AS account_name, 
			sign, balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0)) AS balance, unit,
			holder_id, limkey, a.authcode, h.authcode
			FROM accounts a
			JOIN brands b ON a.brand_id = b.brand_id
			JOIN holders h ON a.account_id=h.account_id AND h.user_id=?
			LEFT JOIN (
				SELECT from_acct, SUM(amount) AS amount 
				FROM records
				WHERE entry_id > 0
				GROUP BY from_acct
			) f ON from_acct=a.account_id
			LEFT JOIN (
				SELECT to_acct, SUM(amount) AS amount 
				FROM records
				WHERE entry_id > 0
				GROUP BY to_acct
			) t ON to_acct=a.account_id
			GROUP BY a.account_id"; 
			
		$this->accounts = DBquery::get($sql, array($this->user_id));
		
		return $this;
	}
}

?>