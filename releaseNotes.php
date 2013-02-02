<?php

require('core.php');
require('config.php');

$board_id = $_GET['id'];
$b = new TrelloBoard($apikey, $token, $board_id);
?>
<!doctype html>
<html>
	<head>
	    <link rel="stylesheet" type="text/css" href="components/bootstrap/docs/assets/css/bootstrap.css"/>		
	    <style type="text/css">
	    	li { margin: 1em 0; padding: 1em 0; border-bottom: 1px solid #ccc; }
	    	a { color: black; }
	    </style>
	</head>
	<body>
		<div class="container">
			<div class="content">
				<div class="row">
					<div class="span12">
					<h3>Release notes for <?php $b->getBoardName(); ?></h3>
					<?php 
					$notes = new TrelloReleaseNotes($apikey, $token, $board_id); 
					$notes->printReleaseNotes($release_note_prefix);
					?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>