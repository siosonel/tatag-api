<?php

class ForwardVerifier {
	public $from_holder;
	public $to_holder;

	function __construct($data) {
		$this->verifyHolder($data, 'from');
		if ($this->from_holder['user_id'] != Requester::$user_id) Error::http(401, "The from-account's user_id must match the Requester's user_id in a non-reversal transaction."); 
		
		$this->verifyHolder($data, 'to');						
		if ($data->from_acct == $data->to_acct) Error::http(403, "The from_acct and to_acct must have a different account id's.");
		
		$this->setTxnType();
		if (isset($this->Relay)) $this->Relay->checkLimits($this->from_holder['brand_id'], $data->from_user);
	}
	
	function verifyHolder($data, $ft) {
		if ($ft=='to' AND strpos($data->to,"-") != false AND !is_numeric(explode("-", $data->to)[0])) $this->usePromo($data);
		
		if (strpos($data->$ft,"-")==false OR strpos($data->$ft, ".")) $data->$ft = $this->relayToHolderInfo($data, $ft);  
		list($holder_id, $limkey) = explode("-", $data->$ft);
	
		$sql = "CALL holderCheck(?)";			
		$row = DBquery::get($sql, array($holder_id));		
		if (!$row) Error::http(401, "Invalid $ft-holder_id.");
		if ($row[0]['limkey'] != $limkey) Error::http(401, "Invalid $ft-limkey.");
		
		$_holder = $ft ."_holder";
		$this->$_holder = $row[0];
		$data->{$ft ."_acct"} = $row[0]['account_id'];
		$data->{$ft ."_user"} = $row[0]['user_id']; 
		
		//delete now-unneeded holder_id from input data
		unset($data->$ft);
		
		//if account holder authcode is wildcard, override with account authcode value
		if ($this->{$_holder}['holder_auth']=='*') $this->{$_holder}['holder_auth'] = $this->{$_holder}['acct_auth'];
	}
	
	function usePromo($data) {	
		list($keyword,$promo_id) = explode("-", $data->to);
		
		$sql = "SELECT promo_id, amount, keyword, UNIX_TIMESTAMP(expires) as expires, CONCAT_WS('.',r.relay_id,r.secret) AS code 
		FROM promos p 
		JOIN relays r USING (relay_id) 
		WHERE promo_id=?";
		
		$r = DBquery::get($sql, array($promo_id))[0];
		
		if (!$r) Error::http(403, "Invalid recipient token='$data->to': no matching active promo info found.");
		if ($r['expires'] AND $r['expires'] < time()) Error::http(403, "Expired promo code.");
		if ($keyword!=$r['keyword']) Error::http(403, "Invalid keyword in recipient token='$data->to'.");
		
		$data->to = $r['code'];
		$data->amount = $r['amount'];
		$this->promo_id = $promo_id;
		if (!$data->note) $data->note = "for $keyword-$promo_id";
	}
	
	function relayToHolderInfo($data, $ft) {
		list($relay_id,$secret) = explode(".", $data->$ft);
		
		require_once "models/Relay.php";
		$this->Relay = new Relay();
		$this->Relay->setDetails($relay_id);
		
		if (!$data->amount) $data->amount = $this->Relay->getDefaultAmount();
		$this->Relay->checkAgainst($secret, $data->amount);
		
		return $this->Relay->holder_id ."-". $this->Relay->limkey;
	}
	
	function throttleCheck($amount) {
		if (!$this->to_holder['throttle_id']) return; 
		$from = $this->from_holder;
		
		require_once "models/Throttle.php";
		$Throttle = new Throttle(); 
		$Throttle->get(array(
			'brand_id' =>array($from['brand_id']), 
			'user_id' => $from["user_id"],
			'throttle_id' => $this->to_holder['throttle_id']
		)); 
		
		if ($Throttle->unusedAmt < $amount) 
			return "The transaction amount exceeds the calculated throttle limit of $Throttle->unusedAmt. Please try again at around ". round($Throttle->period/2) ." seconds.";
	}
	
	function setTxnType() {
		if ($this->from_holder['unit'] != $this->to_holder['unit']) Error::http(403, 'The accounts in a transaction must use the same unit.');
		
		$fromType = $this->from_holder['sign'] == -1 ? "n" : "p";
		$toType = $this->to_holder['sign'] == -1 ? "n" : "p";
		$this->txnType =  $fromType . $toType;
		if (isset($this->relayTxnType) AND $this->relayTxnType AND $this->txnType != $this->relayTxnType)
			Error::http(403, "The relay credential is not authorized for the detected transaction type of '$this->txntype'.");
	}

	function notifyRecipient($to_user, $subject, $message) {
		if (SITE=='dev') return;

		$sql = "SELECT email FROM users WHERE user_id=$to_user";
		$email = DBquery::get($sql)[0]['email'];
		if (!$email OR !filter_var($email, FILTER_VALIDATE_EMAIL)) return;

		$to      = $email;
		$subject = $subject;
		$message = $message;
		$headers = 'From: do-not-reply@tatag.cc' . "\r\n" .
				'Reply-To: do-not-reply@tatag.cc' . "\r\n" .
				'Return-Path: do-not-reply@tatag.cc' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();

		if (!mail($to, $subject, $message, $headers)) Error::http(500, "Unable to deliver mail to '$this->email'.");
	}
}

?>