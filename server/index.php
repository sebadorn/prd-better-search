<?php

include( 'ui.php' );
include( 'search.php' );

$results = get_search_results();
$term = get_search_term();
$trans_sugg = get_translation_suggestions();
$en_term = get_english_term( $search );

$title = 'PRD-Suche ' . VERSION;

if( $search ) {
	$s = htmlspecialchars( $_GET['s'] );

	if( strlen( $s ) > 20 ) {
		$s = substr( $s, 0, 20 ) . '…';
	}

	$title = $s . ' | ' . $title;
}

?>
<!DOCTYPE html>

<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $title ?></title>
	<link rel="stylesheet" href="screen.css?_=3">
</head>
<body>

<div class="about">
	<a href="syntax.html">Syntax</a>
	&bull;
	<a href="https://github.com/sebadorn/prd-better-search/blob/master/CHANGELOG.md">Changelog</a>
	&bull;
	<code class="version">v<?php echo VERSION ?></code>
</div>

<form class="search" method="GET" action="index.php">
	<div class="line">
		<input name="s" placeholder="Suche …" value="<?php echo htmlspecialchars( $_GET['s'] ) ?>" />
		<button type="submit">&gt;</button>
	</div>
	<select name="f"><?php echo ui_build_filter_list() ?></select>
	<select name="b"><?php echo ui_build_book_list() ?></select>
</form>

<?php echo ui_build_en_term( $en_term ); ?>

<?php echo ui_build_translation_list( $trans_sugg ); ?>

<ol class="results">
<?php

$num_shown = 0;
$num_skip = CURRENT_PAGE * NUM_PER_PAGE;
$num_skipped = 0;

function echo_results( $key, $class ) {
	global $num_shown, $num_skip, $num_skipped, $results;

	foreach( $results[$key] as $i => $item ) {
		if( $num_skipped < $num_skip ) {
			$num_skipped++;
			continue;
		}

		echo ui_build_listitem( $class, $i, $item );
		$num_shown++;

		if( $num_shown >= NUM_PER_PAGE ) {
			break;
		}
	}
}

echo_results( 'title_perfect', 'title-perfect' );
echo_results( 'title_contains', 'title-contains' );
echo_results( 'fuzzy', 'fuzzy' );
echo_results( 'desc', 'desc-contains' );
echo_results( 'keywords', 'keywords-contains' );

?>
</ol>

<?php echo ui_build_pagination( $num_shown, $results['num_results'] ) ?>
<!-- Results: <?php echo $results['num_results'] . '/' . NUM_MAX_RESULTS ?> -->
</body>
</html>