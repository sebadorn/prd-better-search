<?php

define( "NUM_MAX_RESULTS", 500 );
define( "TERM_MIN_LENGTH", 2 );
define( "TERM_MAX_LENGTH", 1000 );

$data = [
	"magic-items" => NULL,
	"magic" => NULL,
	"monsters" => NULL,
	"rules" => NULL,
	"talents" => NULL,
	"traits" => NULL,
	"words-of-power" => NULL,
];

$search = get_search_term();
$filter = get_search_filter();


if( $search ) {
	foreach( $data as $key => $value ) {
		$path = "index-data/" . $key . ".json";
		$handle = fopen( $path, "r" );
		$content = fread( $handle, filesize( $path ) );
		fclose( $handle );

		$data[$key] = json_decode( $content );
	}
}


function result_cmp( $a, $b ) {
	if( $a->book !== $b->book ) {
		if( $a->book === "Grundregelwerk" ) {
			return -1;
		}
		else if( $b->book === "Grundregelwerk" ) {
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


function get_search_filter() {
	$filter = NULL;

	if( isset( $_GET["f"] ) ) {
		$filter = $_GET["f"];

		if( get_translation( $filter ) === "" ) {
			$filter = NULL;
		}
	}

	return $filter;
}


function get_search_results() {
	global $data, $filter, $search;

	$results = [
		"title_perfect" => [],
		"title_contains" => [],
		"desc" => [],
		"keywords" => []
	];

	if( is_null( $search ) ) {
		return $results;
	}

	$num_results = 0;

	foreach( $data as $key => $contents ) {
		if( is_null( $contents ) ) {
			continue;
		}

		if( !is_null( $filter ) && $filter !== $key ) {
			continue;
		}

		if( $num_results >= NUM_MAX_RESULTS ) {
			break;
		}

		foreach( $contents as $i => $item ) {
			$item->source = $key;
			$name = strtolower( $item->name );

			if( strcmp( $name, $search ) === 0 ) {
				array_push( $results["title_perfect"], $item );
				$num_results++;
			}
			else if( strpos( $name, $search ) !== FALSE ) {
				array_push( $results["title_contains"], $item );
				$num_results++;
			}
			else if( $item->desc && stripos( $item->desc, $search ) !== FALSE ) {
				array_push( $results["desc"], $item );
				$num_results++;
			}
			else if( is_array( $item->keywords ) ) {
				foreach( $item->keywords as $j => $keyword ) {
					if( stripos( $keyword, $search ) !== FALSE ) {
						array_push( $results["keywords"], $item );
						$num_results++;

						break;
					}
				}
			}

			if( $num_results >= NUM_MAX_RESULTS ) {
				break;
			}
		}
	}

	usort( $results["title_perfect"], "result_cmp" );
	usort( $results["title_contains"], "result_cmp" );
	usort( $results["desc"], "result_cmp" );
	usort( $results["keywords"], "result_cmp" );

	return $results;
}


function get_search_term() {
	$search = NULL;

	if( isset( $_GET["s"] ) ) {
		$search = strtolower( trim( $_GET["s"] ) );
		$len = strlen( $search );

		if( $len < TERM_MIN_LENGTH || $len > TERM_MAX_LENGTH ) {
			$search = NULL;
		}
	}

	return $search;
}
