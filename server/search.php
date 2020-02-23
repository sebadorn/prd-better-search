<?php

define( 'NUM_MAX_RESULTS', 600 );
define( 'TERM_MIN_LENGTH', 2 );
define( 'TERM_MAX_LENGTH', 1000 );

include( 'search-check.php' );

$data = [
	'magic-items' => NULL,
	'magic' => NULL,
	'monsters' => NULL,
	'rules' => NULL,
	'talents' => NULL,
	'traits' => NULL,
	'words-of-power' => NULL,
];

$search = get_search_term();


if( $search ) {
	foreach( $data as $key => $value ) {
		$path = "index-data/{$key}.json";
		$handle = fopen( $path, 'r' );
		$content = fread( $handle, filesize( $path ) );
		fclose( $handle );

		$data[$key] = json_decode( $content );
	}
}


/**
 *
 * @param  object $a
 * @param  object $b
 * @return number
 */
function result_cmp( $a, $b ) {
	if( $a->book !== $b->book ) {
		if( $a->book === 'Grundregelwerk' ) {
			return -1;
		}
		else if( $b->book === 'Grundregelwerk' ) {
			return 1;
		}
	}

	if( $a->id > $b->id ) {
		return 1;
	}
	else if( $a->id < $b->id ) {
		return -1;
	}

	return 0;
}


/**
 *
 * @return string|null
 */
function get_search_book() {
	$book = NULL;

	if( isset( $_GET['b'] ) ) {
		$book = $_GET['b'];
		$books = get_books();

		if( !in_array( $book, $books ) ) {
			$book = NULL;
		}
	}

	return $book;
}


/**
 *
 * @return string|null
 */
function get_search_filter() {
	$filter = NULL;

	if( isset( $_GET['f'] ) ) {
		$filter = $_GET['f'];

		if( get_translation( $filter ) === '' ) {
			$filter = NULL;
		}
	}

	return $filter;
}


/**
 *
 * @return array
 */
function get_search_results() {
	global $data, $search;

	$results = [
		'title_perfect' => [],
		'title_contains' => [],
		'desc' => [],
		'keywords' => []
	];

	if( is_null( $search ) ) {
		return $results;
	}

	$filter = get_search_filter();
	$filter_book = get_search_book();
	$mod = get_search_mod( $search );

	$num_results = 0;

	foreach( $data as $source => $contents ) {
		if( is_null( $contents ) ) {
			continue;
		}

		if( $filter && $filter !== $source ) {
			continue;
		}

		if( $num_results >= NUM_MAX_RESULTS ) {
			break;
		}

		foreach( $contents as $i => $item ) {
			if( $filter_book && $item->book !== $filter_book ) {
				continue;
			}

			$item->source = $source;
			$num_results += check_add_item( $search, $mod, $item, $results );

			if( $num_results >= NUM_MAX_RESULTS ) {
				break;
			}
		}
	}

	usort( $results['title_perfect'], 'result_cmp' );
	usort( $results['title_contains'], 'result_cmp' );
	usort( $results['desc'], 'result_cmp' );
	usort( $results['keywords'], 'result_cmp' );

	return $results;
}


/**
 *
 * @return string|null
 */
function get_search_term() {
	$search = null;

	if( isset( $_GET['s'] ) ) {
		$search = strtolower( trim( $_GET['s'] ) );
		$len = strlen( $search );

		if( $len < TERM_MIN_LENGTH || $len > TERM_MAX_LENGTH ) {
			$search = null;
		}
	}

	return $search;
}
