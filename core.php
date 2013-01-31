<?php

class Trello {
	protected $apikey;
	protected $token;
	public $apiBase = 'https://api.trello.com';
	function __construct ( $apikey , $token ) {
		$this->apikey 				= $apikey;
		$this->token 				= $token;
	}
}
class TrelloCard extends Trello {
	protected $shortlink_code;
	public $comments;
	function __construct ( $apikey, $token, $shortlink_code) {
		parent::__construct ( $apikey, $token );
		$this->shortlink_code = $shortlink_code;
	}
	public function findDuplicateComment( $text ) {
		if ($this->comments) {
			$this->getComments();
		}
		if (sizeof($this->comments)>0) {
			foreach ($this->comments as $comment) {
				if ($text == $comment['data']['text']) {
					return true;
					break;
				}
			}
			return false;			
		} else {
			return false;
		}
	}
	public function addCommentToCard( $text ) {
		$encoded = urlencode($text);
		$url = "{$this->apiBase}/1/cards/{$this->shortlink_code}/actions/comments";
		echo $url;
		$ch = curl_init( $url );
		$arguments="key={$this->apikey}&token={$this->token}&text=$encoded";
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $arguments);
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);		
		$response = curl_exec( $ch );	
		return $response;			
	}
	public function getComments ( ) {
		$response = json_decode(file_get_contents("{$this->apiBase}/1/cards/{$this->shortlink_code}?actions=commentCard&key={$this->apikey}&token={$this->token}"),true);
		$this->comments = $response['actions'];
	}
	public function findCommentContaining( $regexp ) {
		$matches = array();
		if (!$this->comments) {
			$this->getComments();
		}
		foreach ($this->comments as $comment) {
			if (preg_match($regexp, $comment['data']['text'], $m)) {
				$matches[] = $comment['data']['text'];
			} 
		}
		if (sizeof($matches)>0) {
			return $matches;
		} else {
			return false;
		}
	}
}

?>