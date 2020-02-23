<?php


/**
 *
 * @param  string     $search
 * @param  array|null $mod
 * @param  object     $item
 * @param  array      &$results
 * @return number
 */
function check_add_item( $search, $mod, $item, &$results ) {
	$added = 0;

	if( is_array( $mod ) ) {
		$added += check_mod( $mod, $item, $results );
	}
	else {
		$added += check_name( $search, $item, $results );
	}

	return $added;
}


/**
 *
 * @param  array  $mod
 * @param  object $item
 * @param  array  &$results
 * @return number
 */
function check_mod( $mod, $item, &$results ) {
	$added = 0;

	if( $mod[0] === 'BUCH' ) {
		$added += check_mod_book( $mod[1], $item, $results );
	}
	else if( $mod[0] === 'HG' ) {
		$added += check_mod_cr( $mod[1], $item, $results );
	}
	else if( $mod[0] === 'REQ' ) {
		$added += check_mod_req( $mod[1], $item, $results );
	}
	else if( $mod[0] === 'TYP' ) {
		$added += check_mod_type( $mod[1], $item, $results );
	}

	return $added;
}


/**
 *
 * @param  string $value
 * @param  object $item
 * @param  array  &$results
 * @return number
 */
function check_mod_book( $value, $item, &$results ) {
	$added = 0;

	if( $item->book ) {
		$book = strtolower( $item->book );

		if( strcmp( $book, $value ) === 0 ) {
			array_push( $results['title_perfect'], $item );
			$added++;
		}
		else if( strpos( $book, $value ) === 0 ) {
			array_push( $results['title_contains'], $item );
			$added++;
		}
	}

	return $added;
}


/**
 *
 * @param  number $value
 * @param  object $item
 * @param  array  &$results
 * @return number
 */
function check_mod_cr( $value, $item, &$results ) {
	$added = 0;

	if( $item->cr && $item->cr === $value ) {
		array_push( $results['title_perfect'], $item );
		$added++;
	}

	return $added;
}


/**
 *
 * @param  string $value
 * @param  object $item
 * @param  array  &$results
 * @return number
 */
function check_mod_req( $value, $item, &$results ) {
	$added = 0;

	if( !is_array( $item->requirements ) ) {
		return $added;
	}

	if( strpos( $value, 'zauberstufe' ) === 0 ) {
		if( $value === 'zauberstufe' ) {
			$value = 'zs>0';
		}
		else {
			$value = str_replace( 'zauberstufe', 'zs', $value );
		}
	}

	$options = [
		'ch',
		'gab',
		'ge',
		'in',
		'ko',
		'ref',
		'st',
		'we',
		'wil',
		'zäh',
		'zs'
	];
	$regex = '/^(' . implode( $options, '|' ) . ')[ ]?([<>])[ ]?[+]?([0-9]+)[+]?$/';

	$matches = NULL;

	if( preg_match( $regex, $value, $matches ) === 1 ) {
		$attr = $matches[1];
		$comp = $matches[2];
		$maxVal = (int) $matches[3];

		foreach( $item->requirements as $i => $req ) {
			$req = strtolower( $req );

			if( preg_match( '/^' . $attr . ' [+]?([0-9]+)[+]?$/', $req, $reqMatches ) === 1 ) {
				$reqVal = $reqMatches[1];

				if(
					( $comp === '<' && $reqVal < $maxVal ) ||
					( $comp === '>' && $reqVal > $maxVal )
				) {
					array_push( $results['title_perfect'], $item );
					$added++;

					break;
				}
			}
		}
	}
	else {
		foreach( $item->requirements as $i => $req ) {
			$req = strtolower( $req );

			if( strcmp( $req, $value ) === 0 ) {
				array_push( $results['title_perfect'], $item );
				$added++;

				break;
			}
			else if( strpos( $req, $value ) !== FALSE ) {
				array_push( $results['title_contains'], $item );
				$added++;

				break;
			}
		}
	}

	return $added;
}


/**
 *
 * @param  string $value
 * @param  object $item
 * @param  array  &$results
 * @return number
 */
function check_mod_type( $value, $item, &$results ) {
	$added = 0;

	if( $item->type ) {
		$type = strtolower( $item->type );

		if( strcmp( $type, $value ) === 0 ) {
			array_push( $results['title_perfect'], $item );
			$added++;
		}
		else if( strpos( $type, $value ) !== FALSE ) {
			array_push( $results['title_contains'], $item );
			$added++;
		}
	}

	if( $added === 0 && is_array( $item->type_sub ) ) {
		foreach( $item->type_sub as $i => $subtype ) {
			$subtype = strtolower( $subtype );

			if( strcmp( $subtype, $value ) === 0 ) {
				array_push( $results['title_perfect'], $item );
				$added++;

				break;
			}
			else if( strpos( $subtype, $value ) !== FALSE ) {
				array_push( $results['title_contains'], $item );
				$added++;

				break;
			}
		}
	}

	return $added;
}


/**
 *
 * @param  string $search
 * @param  object $item
 * @param  array  &$results
 * @return number
 */
function check_name( $search, $item, &$results ) {
	$added = 0;
	$name = strtolower( $item->name );

	if( strcmp( $name, $search ) === 0 ) {
		array_push( $results['title_perfect'], $item );
		$added++;
	}
	else if( strpos( $name, $search ) !== FALSE ) {
		array_push( $results['title_contains'], $item );
		$added++;
	}
	else if( $item->desc && stripos( $item->desc, $search ) !== FALSE ) {
		array_push( $results['desc'], $item );
		$added++;
	}
	else if( is_array( $item->keywords ) ) {
		foreach( $item->keywords as $i => $keyword ) {
			if( stripos( $keyword, $search ) !== FALSE ) {
				array_push( $results['keywords'], $item );
				$added++;

				break;
			}
		}
	}

	return $added;
}


/**
 *
 * @param  string $search
 * @return array|null
 */
function get_search_mod( $search ) {
	$parts = explode( ':', $search );

	if( count( $parts ) < 2 ) {
		return NULL;
	}

	$mod = strtoupper( trim( $parts[0] ) );
	$value = trim( implode( array_slice( $parts, 1 ), ':' ) );

	if( $mod === 'BUCH' || $mod === 'BOOK' ) {
		$mod = 'BUCH';

		if( strlen( $value ) === 0 ) {
			return NULL;
		}
	}
	else if( $mod === 'HG' || $mod === 'CR' ) {
		$mod = 'HG';
		$value = str_replace( ',', '.', $value );

		if( $value === '1/2' || $value === '½' ) {
			$value = 0.5;
		}
		else if( $value === '1/3' || $value === '⅓' ) {
			$value = 0.33;
		}
		else if( $value === '1/4' || $value === '¼' ) {
			$value = 0.25;
		}
		else if( $value === '1/6' || $value === '⅙' ) {
			$value = 0.16;
		}
		else if( $value === '1/8' || $value === '⅛' ) {
			$value = 0.125;
		}

		if( !is_numeric( $value ) ) {
			return NULL;
		}

		$value += 0; // Cast to strict numeric.
	}
	else if( $mod === 'REQ' || $mod === 'VORAUS' ) {
		$mod = 'REQ';

		if( strlen( $value ) === 0 ) {
			return NULL;
		}
	}
	else if( $mod === 'SCHULE' || $mod === 'SCHOOL' ) {
		$mod = 'SCHULE';

		if( strlen( $value ) === 0 ) {
			return NULL;
		}
	}
	else if( $mod === 'TYP' || $mod === 'TYPE' ) {
		$mod = 'TYP';

		if( strlen( $value ) === 0 ) {
			return NULL;
		}
	}
	else {
		return NULL;
	}

	return [$mod, $value];
}
