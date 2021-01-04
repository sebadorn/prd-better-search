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
	$matched = 0;
	$num_checks = count( $mod ) / 2;

	// Different mod checks can be combined by using " && ".
	// The even index is the mod type, and the odd index the value to check for.
	for( $i = 0; $i < $num_checks * 2; $i += 2 ) {
		$key = $mod[$i];
		$value = $mod[$i + 1];
		$result_key = NULL;

		if( $key === 'BUCH' ) {
			$result_key = check_mod_book( $value, $item );
		}
		else if( $key === 'HG' ) {
			$result_key = check_mod_cr( $value, $item );
		}
		else if( $key === 'REQ' ) {
			$result_key = check_mod_req( $value, $item );
		}
		else if( $key === 'SCHULE' ) {
			$result_key = check_mod_school( $value, $item );
		}
		else if( $key === 'TYP' ) {
			$result_key = check_mod_type( $value, $item );
		}

		if( $result_key ) {
			$matched++;
		}
		else {
			return 0;
		}

		// Passed all checks, add item to results.
		if( $matched === $num_checks ) {
			array_push( $results[$result_key], $item );
			$added++;
		}
	}

	return $added;
}


/**
 *
 * @param  string $value
 * @param  object $item
 * @return string|null
 */
function check_mod_book( $value, $item ) {
	if( $item->book ) {
		$book = strtolower( $item->book );

		if( strcmp( $book, $value ) === 0 ) {
			return 'title_perfect';
		}
		else if( strpos( $book, $value ) === 0 ) {
			return 'title_contains';
		}
	}

	return NULL;
}


/**
 *
 * @param  number $value
 * @param  object $item
 * @return string|null
 */
function check_mod_cr( $value, $item ) {
	if( $item->cr && $item->cr === $value ) {
		return 'title_perfect';
	}

	return NULL;
}


/**
 *
 * @param  string $value
 * @param  object $item
 * @return string|null
 */
function check_mod_school( $value, $item ) {
	if( $item->school ) {
		$school = strtolower( $item->school );

		if( strcmp( $school, $value ) === 0 ) {
			return 'title_perfect';
		}
		else if( strpos( $school, $value ) !== FALSE ) {
			return 'title_contains';
		}
	}

	if( is_array( $item->school_sub ) ) {
		foreach( $item->school_sub as $i => $subschool ) {
			$subschool = strtolower( $subschool );

			if( strcmp( $subschool, $value ) === 0 ) {
				return 'title_perfect';
			}
			else if( strpos( $subschool, $value ) !== FALSE ) {
				return 'title_contains';
			}
		}
	}

	return NULL;
}


/**
 *
 * @param  string $value
 * @param  object $item
 * @return string|null
 */
function check_mod_req( $value, $item ) {
	if( !is_array( $item->requirements ) ) {
		return NULL;
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
	$regex = '/^(' . implode( $options, '|' ) . ')[ ]?([<>][=]?)[ ]?[+]?([0-9]+)[+]?$/';

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
					( $comp === '>' && $reqVal > $maxVal ) ||
					( $comp === '<=' && $reqVal <= $maxVal ) ||
					( $comp === '>=' && $reqVal >= $maxVal )
				) {
					return 'title_perfect';
				}
			}
		}
	}
	else {
		foreach( $item->requirements as $i => $req ) {
			$req = strtolower( $req );

			if( strcmp( $req, $value ) === 0 ) {
				return 'title_perfect';
			}
			else if( strpos( $req, $value ) !== FALSE ) {
				return 'title_contains';
			}
		}
	}

	return NULL;
}


/**
 *
 * @param  string $value
 * @param  object $item
 * @return string|null
 */
function check_mod_type( $value, $item ) {
	if( $item->type ) {
		$type = strtolower( $item->type );

		if( strcmp( $type, $value ) === 0 ) {
			return 'title_perfect';
		}
		else if( strpos( $type, $value ) !== FALSE ) {
			return 'title_contains';
		}
	}

	if( is_array( $item->type_sub ) ) {
		foreach( $item->type_sub as $i => $subtype ) {
			$subtype = strtolower( $subtype );

			if( strcmp( $subtype, $value ) === 0 ) {
				return 'title_perfect';
			}
			else if( strpos( $subtype, $value ) !== FALSE ) {
				return 'title_contains';
			}
		}
	}

	return NULL;
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
	$needles = array( ',', ';', '.', ':', '-' );
	$name = str_replace( $needles, '', $name );

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

	// Fuzzy search.
	if( $added === 0 ) {
		similar_text( $name, $search, $percent );

		if( $percent >= FUZZY_MIN ) {
			array_push( $results['fuzzy'], $item );
			$added++;
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
	if( strpos( $search, ':' ) === FALSE ) {
		return NULL;
	}

	$and = explode( ' && ', $search );
	$result = [];

	foreach( $and as $i => $andPart ) {
		$parts = explode( ':', $andPart );

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

		array_push( $result, $mod, $value );
	}

	return $result;
}
