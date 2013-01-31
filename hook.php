<?php
require('config.php');
require('core.php');
function getUnits($i) {
	if ($i == 1) {
		return "file";
	} else {
		return "files";
	}
}
$content = json_decode(urldecode($_POST['payload']),true);

if (sizeof($content['commits'])>0) {
	foreach ($content['commits'] as $commit) {
		if (preg_match('/.*trello\.com\/c\/([0-9A-Za-z]+)/', $commit['message'], $matches) && !preg_match('/Merge\s(remote-tracking\s)?branch/', $commit['message'])) {
			$a = new TrelloCard($apikey, $token, $matches[1]);
			$files['modified'] = sizeof($commit['modified']);
			$files['added'] = sizeof($commit['added']);
			$files['removed'] = sizeof($commit['removed']);
			$author = $commit['author']['name'];
			$url = substr($commit['url'], 0, -33);
					
			foreach ($files as $type => $count) {
				if ($count>0) {
					$changed_text .= strlen($changed_text)>0 ? ", " : "";
					$changed_text .= "$type $count ".getUnits($count); 
				}
			}		
	
			$comment = "$author $changed_text ($url)";
			if (!$a->findDuplicateComment( $comment )) {
				$response = $a->addCommentToCard($comment);
				print $response;
			} 
			unset($changed_text);
		}
	}
} else {
	throw new Exception("No payload provided");
}
?>