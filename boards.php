<?php
require('core.php');
require('config.php');
$b = new Trello($apikey, $token );
?>
<!doctype html>
<html>
	<head>
	    <link rel="stylesheet" type="text/css" href="components/bootstrap/docs/assets/css/bootstrap.css"/>			
	</head>
	<body>
		<div class="container">
			<div class="content">
				<div class="row">
					<div class="span12">
					<h2>Boards</h2>
					<?php 
						$boards = $b->getBoards(); 
						if (sizeof($boards)>0) { 
							$str = "<table class=\"table table-condensed table-striped table-bordered\"><thead><tr><th>Board</th><th>Actions</th></tr></thead><tbody>";
							foreach($boards as $board) {
								$str.= "<tr><td><a href=\"board.php?id={$board['id']}\">{$board['name']}</a></td><td><a href=\"releaseNotes.php?id={$board['id']}\">Get release notes</a></td></tr>";
							}
							$str .= "</table>";
							echo $str;
						} else {
							throw new Exception("No boards found");
						}
					?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>