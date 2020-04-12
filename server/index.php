<?php

include( 'ui.php' );
include( 'search.php' );

$results = get_search_results();
$term = get_search_term();
$trans_sugg = get_translation_suggestions();
$en_term = get_english_term( $search );

?>
<!DOCTYPE html>

<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>PRD-Suche</title>
	<link rel="stylesheet" href="screen.css">
</head>
<body>

<form class="search" method="GET" action="index.php">
	<div class="line">
		<input name="s" placeholder="Suche …" value="<?php echo htmlspecialchars( $_GET["s"] ) ?>" />
		<button type="submit">&gt;</button>
	</div>
	<select name="f"><?php echo ui_build_filter_list() ?></select>
	<select name="b"><?php echo ui_build_book_list() ?></select>
</form>

<?php echo ui_build_en_term( $en_term ); ?>

<?php echo ui_build_translation_list( $trans_sugg ); ?>

<ol class="results">
<?php

foreach( $results['title_perfect'] as $i => $item ) {
	echo ui_build_listitem( 'title-perfect', $i, $item );
}

foreach( $results['title_contains'] as $i => $item ) {
	echo ui_build_listitem( 'title-contains', $i, $item );
}

foreach( $results['desc'] as $i => $item ) {
	echo ui_build_listitem( 'desc-contains', $i, $item );
}

foreach( $results['keywords'] as $i => $item ) {
	echo ui_build_listitem( 'keywords-contain', $i, $item );
}

?>
</ol>

</body>
</html>