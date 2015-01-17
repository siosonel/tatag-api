<?php

class Brands extends Base {
	function __construct($data='') {
		$this->table = "brands";
		$this->cols = "brand_id,name,mission,description,rating_min,rating_formula,created";
		$this->brand_id = $this->getID();
		$this->idkey = 'brand_id';
		$this->okToGet = array("brand_id", "name", "mission", "description");
		
		$this->init($data);
	}
	
	function add() { 
		include_once "models/Members.php";
		include_once "models/Accounts.php";
		include_once "models/Holders.php";
		
		$this->okToAdd = array('name','mission','description','rating_min','rating_formula');
		$Brand =  $this->obj;
		$Brand->brand_id = $this->insert();		//print_r($Brand); print_r(Requester);
				
		$Brand->members[] = (new Members(json_decode('{
			"brand_id":'.$Brand->brand_id.', 
			"user_id":'. Requester::$user_id.', 
			"role":"admin",
			"hours":0
		}')))->add();
		
		$MainRev = (new Accounts(json_decode('{
			"brand_id": '. $Brand->brand_id .',
			"name": "Main Revenue",
			"authcode": "cftix",
			"unit": "hour",
			"sign": -1
		}')))->add();
		
		$MainExp = (new Accounts(json_decode('{
			"brand_id": '. $Brand->brand_id .',
			"name": "Main Expense",
			"authcode": "cftix",
			"unit": "hour",
			"sign": 1
		}')))->add();
		
		$Brand->accounts = array($MainRev, $MainExp);
		
		$Brand->holders[] =  (new Holders(json_decode('{
			"account_id": '. $MainRev->account_id .', 
			"user_id":'.Requester::$user_id.',
			"authcode": "cftix"
		}')))->add();
		
		$Brand->holders[] =  (new Holders(json_decode('{
			"account_id": '. $MainExp->account_id .', 
			"user_id":'.Requester::$user_id.',
			"authcode": "cftix"
		}')))->add();
		
		exit(json_encode($Brand));
	}
	
	function set() {
		if (Requester::isBrandAdmin($this->brand_id)) {
			array_push($this->okToSet, "ended","mission","description");		 
			array_push($this->okToFilterBy, "brand_id");			
		}
		
		$this->update("WHERE brand_id=?", array($this->brand_id));
		return array($this->obj);
	}
	
	function get() { 
		if (Requester::isBrandAdmin($this->brand_id)) return $this->getToAdmin();
		else if (Requester::isMember($this->brand_id)) return $this->getToMember();
		else return $this->getToAnon();
	}
	
	function getToAdmin() {
		$info = $this->getToAnon();
		$info[0]['accounts'] = $this->getAccounts();
		$info[0]['members'] = $this->getMembers();
		//$info['carts'] = $this->getCarts();
		return $info;
	}
	
	function getToMember() {
		$info = $this->getToAnon();
		$info[0]['accounts'] = $this->getAccounts();
		$info[0]['members'] = $this->getMembers();
		//$info['carts'] = $this->getCarts();
		return $info;
	}
	
	
	function getToAnon() {
		return DBquery::get("CALL tally(?)", array($this->brand_id)); 
	}
	
	
	private function getAccounts() {
		$sql = "SELECT accounts.account_id, name, sign*(balance+sign*(COALESCE(t.amount,0) - COALESCE(f.amount,0))) AS balance
		FROM accounts
		LEFT JOIN (
			SELECT from_acct, SUM(amount) AS amount 
			FROM records 
			JOIN accounts ON from_acct=accounts.account_id 
			WHERE brand_id=:brand_id
			GROUP BY from_acct
		) f ON from_acct=account_id
		LEFT JOIN (
			SELECT to_acct, SUM(amount) AS amount 
			FROM records
			JOIN accounts ON to_acct=accounts.account_id
			WHERE brand_id=:brand_id
			GROUP BY to_acct
		) t ON to_acct=account_id
		WHERE brand_id=:brand_id
		GROUP BY account_id";
		
		return DBquery::get($sql, array('brand_id'=>$this->brand_id));		
	}
	
	private function getMembers() {
		$sql = "SELECT members.created, member_id, users.created, users.user_id, email, role, hours
			FROM members JOIN users ON members.user_id=users.user_id
			WHERE brand_id=? AND members.ended IS NULL";
		
		return DBquery::get($sql, array($this->brand_id));
	}
	
	/*private function getCarts() {
		$sql = "SELECT cart_id,
			FROM carts JOIN users ON members.user_id=users.user_id
			WHERE brand_id=$this->brand_id AND ended=NULL";
		
		return DBquery::select($sql);
	}*/
}

?>