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
	function __construct ( $apikey, $token, $shortlink_code) {
		parent::__construct ( $apikey, $token );
		$this->shortlink_code = $shortlink_code;
	}
	public function findDuplicateComment( $text ) {
		$url = "{$this->apiBase}/1/cards/{$this->shortlink_code}?actions=commentCard&key={$this->apikey}&token={$this->token}";
		$data = json_decode(file_get_contents($url),true);
		if (sizeof($data['actions'])>0) {
			foreach ($data['actions'] as $comment) {
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
		if (!$this->findDuplicateComment( $text )) {
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
	}
}

?>