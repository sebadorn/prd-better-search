<?php

define( 'FUZZY_MIN', 75 );
define( 'NUM_PER_PAGE', 100 );
define( 'NUM_MAX_RESULTS', 2000 );
define( 'TERM_MIN_LENGTH', 2 );
define( 'TERM_MAX_LENGTH', 1000 );
define( 'VERSION', '1.8' );

if( isset( $_GET['page'] ) && $_GET['page'] >= 0 ) {
	define( 'CURRENT_PAGE', intval( $_GET['page'] ) );
}
else {
	define( 'CURRENT_PAGE', 0 );
}

include( 'search-check.php' );

$data = [
	'magic-items' => NULL,
	'magic' => NULL,
	'monsters' => NULL,
	'rules' => NULL,
	'talents' => NULL,
	'traits' => NULL,
	'words-of-power' => NULL
];

// Set to true/false if a valid JSON
// file exists in "index-data/custom/".
$has_custom_data = [
	'magic-items' => false,
	'magic' => false,
	'monsters' => false,
	'rules' => true,
	'talents' => false,
	'traits' => false,
	'words-of-power' => false
];

$translations = NULL;

$search = get_search_term();
$filter = get_search_filter();
$filter_book = get_search_book();


if(
	$search ||
	!is_null( $filter ) ||
	!is_null( $filter_book )
) {
	foreach( $data as $key => $value ) {
		$path = "index-data/{$key}.json";
		$handle = fopen( $path, 'r' );
		$content = fread( $handle, filesize( $path ) );
		fclose( $handle );

		$data[$key] = json_decode( $content );

		if( $has_custom_data[$key] ) {
			// Check for custom data.
			$path_custom = "index-data/custom/{$key}.json";

			if( file_exists( $path_custom ) ) {
				$handle_custom = fopen( $path_custom, 'r' );
				$content_custom = fread( $handle_custom, filesize( $path_custom ) );
				fclose( $handle_custom );

				$data_custom = json_decode( $content_custom );

				if( is_array( $data_custom ) ) {
					$data[$key] = array_merge( $data[$key], $data_custom );
				}
			}
		}
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

	if( isset( $_GET['b'] ) && $_GET['b'] !== '-' ) {
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

	if( isset( $_GET['f'] ) && $_GET['f'] !== '-' ) {
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

	$no_search_term = is_null( $search );

	$filter = get_search_filter();
	$filter_book = get_search_book();
	$mod = NULL;

	if(
		is_null( $filter ) &&
		is_null( $filter_book ) &&
		$no_search_term
	) {
		return $results;
	}

	if( !$no_search_term ) {
		$mod = get_search_mod( $search );
	}

	$needles = array( ',', ';', '.', ':', '-' );
	$search_cleaned = str_replace( $needles, '', $search );

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

			// No further checks aside from the previous filters.
			if( $no_search_term ) {
				array_push( $results['title_perfect'], $item );
				$num_results++;
			}
			// Do more checks according to the search term.
			else {
				$num_results += check_add_item( $search_cleaned, $mod, $item, $results );
			}

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

	$results['num_results'] = $num_results;

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
 * @param  string|NULL $s
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
