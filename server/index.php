<?php

include( 'ui.php' );
include( 'search.php' );

$results = get_search_results();

?>
<!DOCTYPE html>

<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>PRD-Suche</title>
	<link rel="stylesheet" href="screen.css">
	<script src="search.js"></script>
</head>
<body>

<form class="search" method="GET" action="index.php">
	<div class="line">
		<input name="s" placeholder="Suche …" />
		<button type="submit">&rarr;</button>
	</div>
</form>

<ol class="results">
<?php

foreach( $results["title_perfect"] as $i => $item ) {
	echo ui_build_listitem( $i, $item );
}

foreach( $results["title_contains"] as $i => $item ) {
	echo ui_build_listitem( $i, $item );
}

foreach( $results["desc"] as $i => $item ) {
	echo ui_build_listitem( $i, $item );
}

?>
</ol>

</body>
</html>