<?php

class Trello {
	protected $apikey;
	protected $token;
	protected $boards;
	public $apiBase = 'https://api.trello.com';
	function __construct ( $apikey , $token ) {
		$this->apikey 				= $apikey;
		$this->token 				= $token;
	}
	public function getBoards() {
		$request_url = "{$this->apiBase}/1/members/me/boards?key={$this->apikey}&token={$this->token}";
		$this->boards = json_decode(file_get_contents($request_url),true);
		return $this->boards;
	}
}
class TrelloCard extends Trello {
	private $id;
	private $comments;
	function __construct ( $apikey, $token, $id) {
		parent::__construct ( $apikey, $token );
		$this->id = $id;
	}
	public function findDuplicateComment( $text ) {
		if (!$this->comments) {
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
		$url = "{$this->apiBase}/1/cards/{$this->id}/actions/comments";
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
		if (!$this->comments) {
			$comments = json_decode(file_get_contents("{$this->apiBase}/1/cards/{$this->id}?actions=commentCard&key={$this->apikey}&token={$this->token}"),true);
			$this->comments = $comments['actions'];			
		}
		return $this->comments;

	}
	public function getLastComment() {
		if (!$this->comments) {
			$this->getComments();
		}
		return $this->comments[0];
	}
	public function findCommentContaining( $regexp ) {
		$matches = array();
		if (!$this->comments) {
			$this->getComments();
		}
		foreach ($this->comments as $comment) {
			if (preg_match("/$regexp/", $comment['data']['text'], $m)) {
				$comment['data']['text'] = substr($comment['data']['text'], strlen($regexp));
				return $comment;
			} 
		}
		return false;
	}
}
class TrelloList extends Trello {
	private $listid;
	function __construct ( $apikey, $token, $listid ) {
		parent::__construct ( $apikey, $token );
		$this->listid = $listid;
	}
	function getCards() {
		$request_url = "{$this->apiBase}/1/lists/{$this->listid}/cards?key={$this->apikey}&token={$this->token}";
		$cards = json_decode(file_get_contents($request_url),true);
		return $cards;
	}
	function getName() {
		
	}
}
class TrelloBoard extends Trello {
	private $boardid;
	function __construct ( $apikey, $token, $boardid) {
		parent::__construct ( $apikey, $token );
		$this->boardid = $boardid;
	}
	public function getLists() {
		$request_url = "{$this->apiBase}/1/boards/{$this->boardid}/lists?key={$this->apikey}&token={$this->token}";
		$lists = json_decode(file_get_contents($request_url),true);
		return $lists;
	}
	public function getCards() {
		$request_url = "{$this->apiBase}/1/boards/{$this->boardid}/lists?key={$this->apikey}&token={$this->token}";
		$lists = json_decode(file_get_contents($request_url),true);
		foreach ($lists as $list) {
				$str = "<h2>{$list['name']}</h2>";
				$str .= "<table class=\"table table-condensed table-striped table-bordered\"><thead><tr><th>Card</th><th>Last note</th></tr></thead><tbody>";
				$request_url = "{$this->apiBase}/1/lists/{$list['id']}/cards?key={$this->apikey}&token={$this->token}";
				$cards = json_decode(file_get_contents($request_url),true);
				foreach ($cards as $card) {
					$str .= "<tr><td id=\"{$card['id']}\"><a href=\"{$card['url']}\">{$card['name']}</a></td><td></td></tr>";
				}
				$str .= "</tbody></table>";	
				if (sizeof($cards)>0) {
					print $str;
				}			
		}
	}
	public function getBoardName() {
		$request_url = "{$this->apiBase}/1/boards/{$this->boardid}/actions?key={$this->apikey}&token={$this->token}";
		$board = json_decode(file_get_contents($request_url),true);
		print $board[0]['data']['board']['name'];
	}
}
class TrelloReleaseNotes extends Trello { 
	private $boardid;
	function __construct ( $apikey, $token, $boardid) {
		parent::__construct ( $apikey, $token );
		$this->boardid = $boardid;
	}
	function printReleaseNotes( $release_note_prefix ) {
		$b = new TrelloBoard($this->apikey, $this->token, $this->boardid);
		$lists = $b->getLists();
		foreach ($lists as $list) {
			$out .= "<ul class=\"media-list\">";
			$l = new TrelloList($this->apikey, $this->token, $list['id']);
			$cards = $l->getCards();			
			if (sizeof($cards)>0){
				foreach ($cards as $card) {
					$c = new TrelloCard($this->apikey, $this->token, $card['id']);
					$comment = $c->findCommentContaining( $release_note_prefix );
					if (!$comment) {
						$comment = $c->getLastComment();
					}
					$commentDate = new DateTime($comment['date']);
					$out .= "<li class=\"media\">
								<div class=\"media-body\">
									<h4 class=\"media-heading\"><a href=\"{$card['shortUrl']}\">{$card['name']}</a></h4>{$comment['data']['text']}<br/><small>Last comment by {$comment['memberCreator']['fullName']} on {$commentDate->format('m/d @ H:i')}</small> <span class=\"label\">{$list['name']}</label></li>";
				}	
			}
			$out .= "</ul>";			
		}
		echo $out;
	}
}


?>