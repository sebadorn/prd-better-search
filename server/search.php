<?php

$data = [
	"magic-items" => NULL,
	"magic" => NULL,
	"monsters" => NULL,
	"rules" => NULL,
	"talents" => NULL,
	"traits" => NULL,
	"words-of-power" => NULL,
];

$search = NULL;


if( isset( $_GET["s"] ) ) {
	$search = strtolower( trim( $_GET["s"] ) );

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
			return 1;
		}
		else if( $b->book === "Grundregelwerk" ) {
			return -1;
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


function get_search_results() {
	global $data, $search;

	$results = [
		"title_perfect" => [],
		"title_contains" => [],
		"desc" => []
	];

	if( is_null( $search ) ) {
		return $results;
	}

	foreach( $data as $key => $contents ) {
		if( is_null( $contents ) ) {
			continue;
		}

		foreach( $contents as $i => $item ) {
			$item->source = $key;
			$name = strtolower( $item->name );

			if( strcmp( $name, $search ) === 0 ) {
				array_push( $results["title_perfect"], $item );
			}
			else if( strpos( $name, $search ) !== FALSE ) {
				array_push( $results["title_contains"], $item );
			}
			else if( isset( $item->desc ) ) {
				$desc = strtolower( $item->desc );

				if( strpos( $desc, $search ) !== FALSE ) {
					array_push( $results["desc"], $item );
				}
			}
		}
	}

	usort( $results["title_perfect"], "result_cmp" );
	usort( $results["title_contains"], "result_cmp" );
	usort( $results["desc"], "result_cmp" );

	return $results;
}
