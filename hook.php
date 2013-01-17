<?php
date_default_timezone_set('America/New_York');

$apikey = '';
$token = '';
$organization_id = '';
include('core.php');

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
		if (preg_match('/.*trello\.com\/c\/([0-9A-Za-z]+)/', $commit['message'], $matches)) {
			$a = new TrelloCard($apikey, $token, $organization_id, $matches[1], $matches[2]);
			print_r($a);
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
			$response = $a->addCommentToCard($comment);
			echo $response;
			unset($changed_text);
		}
	}
} else {
	throw new Exception("No payload provided");
}
?>