<?php
/*
Manage collection of user records
*/

class Users extends Base {	
	function __construct($data='') {
		$id =  $this->getID();	
		$this->{"@"} = $id ? "/users/$id" : "/users";
		$this->table = 'users';
		$this->cols = 'user_id,email,name,password,created,ended';
		$this->user_id = $id;
		$this->idkey = 'user_id';
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
		/*if (Requester::isSysAdmin()) {
			array_push($this->okToSet, "ended");
			array_push($this->okToFilterBy, "user_id");		
		}	*/	
		
		if (Requester::isUser($this->user_id)) {
			array_push($this->okToSet, "ended","email","name","password");
			array_push($this->okToFilterBy,"user_id","email");		
		}		
		
		$this->update("WHERE user_id=?", array(Requester::$user_id));
		return $this->obj;
	}
	
	function get() {
		if (!Router::$id) $info = $this->getCollectionSummary();
		else if (1 OR Requester::isUser($this->user_id)) $info = $this->getToSelf();
		else $info = $this->getToAnon();
		
		return $info;
	}
	
	function getCollectionSummary() {
		$sql = "SELECT COUNT(*) AS numUsers, MIN(created) AS earliest, MAX(created) AS latest FROM users";		
		return DBquery::get($sql);
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
		$forms = array();
	
		$sql = "SELECT brand_id, member_id, m.created AS joined, role, hours 
			FROM members m
			JOIN brands b USING (brand_id)
			WHERE user_id=? AND m.ended IS NULL";
		
		$this->memberships = DBquery::get($sql, array($this->user_id));
		$isAdminOf = array();
		
		foreach($this->memberships AS &$row) {
			$row['links']["brand"] = "/brands/".$row['brand_id'];
			$this->setForms($row, 'memberships', 'edit-by-member');
		
			if ($row['role']=='admin') {
				$isAdminOf[] = $row['brand_id'];
				$this->setForms($row, "memberships", "edit-by-admin");
			}			
		}
		
		$sql = "SELECT a.brand_id AS brand_id, b.name AS brand_name, 
			a.account_id AS account_id, a.name AS account_name, alias,
			sign, balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0)) AS balance, unit,
			holder_id, limkey, a.authcode as account_authcode, h.authcode as holder_authcode
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
		
		foreach($this->accounts AS &$row) {	
			$row['links']["brand"] = "/brands/".$row['brand_id'];
			
			if (in_array($row['brand_id'], $isAdminOf)) {
				$this->setForms($row, "accounts", "edit-by-admin");
				$this->setForms($row, "accounts", "add-holder");
			}
		}
		
		return array("@context"=>"", "@graph"=>array_merge(array($this), Requester::$graph));
	}
	
	private function getToSelfNormalized() {		
		require_once "models/Members.php";
		$Members = new Members(); 		
		list($memberships, $member_brands) = $Members->getViewable("user_id", array($this->user_id), "brand_id");
		
		require_once "models/Holders.php";
		$Holders = new Holders(); 		
		list($holdings, $holder_accounts) = $Holders->getViewable("user_id", array($this->user_id), "account_id");
		
		if ($holder_accounts) {
			require_once "models/Accounts.php";
			$Accounts = new Accounts();
			list($accounts, $account_brands) = $Accounts->getViewable("account_id", $holder_accounts, "brand_id");
		}
		
		if ($holder_accounts OR $member_brands) {
			require_once "models/Brands.php";
			$Brands = new Brands();
			list($brands) = $Brands->getViewable("brand_id", array_unique($member_brands + $account_brands));
		}  
		
		foreach($memberships AS $k=>$m) $this->links['memberships'][] = $k;
		foreach($holdings AS $k=>$m) $this->links['accountholdings'][] = $k;
		
		return array_merge(array("/users/21"=>$this), $memberships, $holdings, $accounts, $brands);
	}
}

?>