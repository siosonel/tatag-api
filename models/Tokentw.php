<?php
require_once "models/Router.php";
require_once "models/Token.php";
require "../twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

class Tokentw extends Token {	
	function setTokenUserID() {
		//session variables where set in $this->prepRedirect()
		$oauth = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$info = $oauth->oauth("oauth/access_token", array("oauth_verifier" => $_GET['oauth_verifier']));
		
		if (!$info) Error::http(400, "No information was retrieved after using Twitter's oauth_verifier token.");
		if (isset($info['error'])) Error::http(500, "Error retrieving information using oauth_verifier token: [". $info['error']."]");
		
		$this->okToSet = array("otk", "user_id", "login_provider");		
		$this->user_id = $this->getByOauthID($info);
		
		if (!isset($_SESSION['TATAG_TOKEN_ID'])) Error::http(500, "Missing values for token_id and/or otk in the GET param query string."); 
		
		$this->addKeyVal('user_id',$this->user_id);		
		$this->addKeyVal('otk', mt_rand(1, 99999999));
		$this->addKeyVal('login_provider', 'tw');
		
		$this->update(array(
			"token_id" => $_SESSION['TATAG_TOKEN_ID'], 
			"otk"=> $_SESSION['TATAG_OTK'], 
			"token_val"=>'0'
		));
		
		$separator = strpos($_SESSION['TATAG_NEXT'], '?')!==false ? "&" : '?';
		
		header('Location: '. $_SESSION['TATAG_NEXT'] . $separator .'token_id='. $_SESSION['TATAG_TOKEN_ID'] .'&otk='. $this->otk);
	}
	
	function getByOauthID($info) {
		$tw_id = "".$info['user_id'];
	
		$sql = "SELECT user_id FROM users WHERE tw_id=?";
		$row = DBquery::get($sql, array($tw_id));
		if ($row) return $row[0]['user_id'];
		
		require_once "models/UserCollection.php";
		$Users = new UserCollection(json_decode('{
			"name": "'. $info['screen_name'] .'",
			"password": "'. mt_rand(5,99999999) .'",
			"tw_id": "'. $tw_id .'",
			"login_provider": "tw"
		}'));
		
		$arr = $Users->add();
		return $arr[0]->user_id;
	}
	
	function redirectToTwitter() {	
		if (isset($_GET['token_id'])) $_SESSION['TATAG_TOKEN_ID'] = $_GET['token_id'];
		if (isset($_GET['otk'])) $_SESSION['TATAG_OTK'] = $_GET['otk'];
		if (isset($_GET['next'])) $_SESSION['TATAG_NEXT'] = $_GET['next'];
		
		$oauth = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET);
		
		$token = $oauth->oauth(
			"oauth/request_token", array("oauth_callback"=>HOME.'/login_tw.php')
		);
		
		if (!$token OR ! $token['oauth_token']) Error::http(500, json_encode($requestToken));
	
		// Saving them into the session
		$_SESSION['oauth_token'] = $token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $token['oauth_token_secret'];
	 
		// Let's generate the URL and redirect
		$url = $oauth->url(
			'oauth/authorize', array('oauth_token' => $token['oauth_token'])
		); //print_r($_SESSION); exit();
		
		header('Location: '. $url);
	}
}