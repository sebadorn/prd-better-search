<?php

define( 'FUZZY_MIN', 80 );
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

$translations = NULL;

$search = get_search_term();


if( $search ) {
	foreach( $data as $key => $value ) {
		$path = "index-data/{$key}.json";
		$handle = fopen( $path, 'r' );
		$content = fread( $handle, filesize( $path ) );
		fclose( $handle );

		$data[$key] = json_decode( $content );
	}

	$path = 'translation-data/translations.json';

	if( file_exists( $path ) ) {
		$handle = fopen( $path, 'r' );
		$content = fread( $handle, filesize( $path ) );
		fclose( $handle );

		$translations = json_decode( $content );
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
 * @return string|NULL
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
 * @return string|NULL
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
		'fuzzy' => [],
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
	usort( $results['fuzzy'], 'result_cmp' );
	usort( $results['desc'], 'result_cmp' );
	usort( $results['keywords'], 'result_cmp' );

	return $results;
}


/**
 *
 * @return string|NULL
 */
function get_search_term() {
	$search = NULL;

	if( isset( $_GET['s'] ) ) {
		$search = strtolower( trim( $_GET['s'] ) );

		// Remove some characters.
		$needles = array( ',', ';', '.', ':', '-' );
		$search = str_replace( $needles, '', $search );

		$len = strlen( $search );

		if( $len < TERM_MIN_LENGTH || $len > TERM_MAX_LENGTH ) {
			$search = NULL;
		}
	}

	return $search;
}


/**
 *
 * @return array|NULL
 */
function get_translation_suggestions() {
	global $search, $translations;

	if( is_null( $translations ) || !is_string( $search ) ) {
		return NULL;
	}

	$key = strtolower( $search );

	if( is_array( $translations->$key ) ) {
		return $translations->$key;
	}

	$key_len = strlen( $key );

	$matches = [];

	foreach( $translations as $en => $de_list ) {
		$pos = strpos( $en, $key );

		if(
			( $key_len >= 4 && $pos !== FALSE ) ||
			( $key_len < 4 && $pos === 0 )
		) {
			$matches = array_merge( $matches, $de_list );
		}
	}

	asort( $matches );

	$matches_len = count( $matches );

	if( $matches_len > 0 && $matches_len <= 10 ) {
		return $matches;
	}

	return NULL;
}


/**
 *
 * @param  string $s
 * @return string|NULL
 */
function get_english_term( $s ) {
	global $translations;

	if( is_null( $translations ) || !is_string( $s ) ) {
		return NULL;
	}

	$s = strtolower( $s );

	foreach( $translations as $en => $de_list ) {
		foreach( $de_list as $i => $de ) {
			if( strcmp( strtolower( $de ), $s ) === 0 ) {
				return $en;
			}
		}
	}

	return NULL;
}
