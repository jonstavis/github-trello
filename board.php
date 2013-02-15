<?php

require('core.php');
require('config.php');

$board_id = $_GET['id'];

$b = new TrelloBoard($apikey, $token, $board_id);
$lists = $b->getLists();
?>
<!doctype html>
<html>
	<head>
	    <link rel="stylesheet" type="text/css" href="bootstrap/docs/assets/css/bootstrap.css"/>		
	</head>
	<body>
		<div class="container">
			<div class="content">
				<div class="row">
					<div class="span12">
					
					<?php 
					if (sizeof($lists)>0){
						$out = "<a href=\"releaseNotes.php?id=$board_id\" class=\"btn btn-success pull-right\">Export release notes</a>";
						foreach ($lists as $list) {
						$l = new TrelloList($apikey, $token, $list['id']);
						$cards = $l->getCards();
						if (sizeof($cards)>0) {
							$out .= "<h2>{$list['name']}</h2><table class=\"table table-bordered table-condensed\">
							<thead>
								<tr><th>Name</th><th>Last comment</th></tr>
							</thead>";
							foreach ($cards as $card) {
								$c = new TrelloCard($apikey, $token, $card['id']);
								$lastComment = $c->getLastComment();	
								$out .= "<tr><td class=\"span4\"><a href=\"{$card['shortUrl']}\">{$card['name']}</a></td><td>{$lastComment}</td></tr>";
							}
							$out .= "</table>";
							
						}
					}
					}
					echo $out;
					?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>