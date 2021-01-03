<?php


include( 'short-info.php' );


$books = NULL;


/**
 *
 * @return array
 */
function get_books() {
	global $books;

	if( $books ) {
		return $books;
	}

	$path = 'index-data/books.json';
	$handle = fopen( $path, 'r' );
	$content = fread( $handle, filesize( $path ) );
	fclose( $handle );

	$books = json_decode( $content );

	return $books;
}


/**
 *
 * @param  string $key
 * @return string
 */
function get_translation( $key ) {
	$map = [
		'magic' => 'Zauber',
		'magic-items' => 'Magische Gegenstände',
		'monsters' => 'Monster',
		'rules' => 'Regeln',
		'talents' => 'Talente',
		'traits' => 'Wesenszüge',
		'words-of-power' => 'Worte der Macht'
	];

	if( isset( $map[$key] ) ) {
		return $map[$key];
	}

	return '';
}


/**
 *
 * @return string
 */
function ui_build_book_list() {
	$books = get_books();
	$out = '<option value="-">— kein Buch —</option>';

	foreach( $books as $i => $book ) {
		$check = ( $_GET['b'] === $book ) ? ' selected' : '';
		$out .= "<option value=\"{$book}\"{$check}>{$book}</option>";
	}

	return $out;
}


/**
 *
 * @param  string|NULL $term
 * @return string
 */
function ui_build_en_term( $term ) {
	if( !is_string( $term ) ) {
		return '';
	}

	$ddg = 'https://duckduckgo.com/?q=site%3Ad20pfsrd.com+';
	$ddg .= urlencode( $term );

	$link = '<a href="' . $ddg . '" rel="noreferrer">';
	$link .= htmlspecialchars( $term );
	$link .= '</a>';

	$out = '<div class="term-en">';
	$out .= '<p>Englischer Ausdruck:</p>';
	$out .= '<div class="term">' . $link . '</div>';
	$out .= '</div>';

	return $out;
}


/**
 *
 * @return string
 */
function ui_build_filter_list() {
	$options = [
		'-',
		'magic-items',
		'monsters',
		'rules',
		'talents',
		'traits',
		'words-of-power',
		'magic'
	];
	$out = '';

	foreach( $options as $key => $value ) {
		if( $value === '-' ) {
			$text = '— kein Filter —';
		}
		else {
			$text = get_translation( $value );
		}

		$check = ( $_GET['f'] === $value ) ? ' selected' : '';
		$out .= "<option value=\"{$value}\"{$check}>{$text}</option>";
	}

	return $out;
}


/**
 *
 * @param  string $class
 * @param  number $i
 * @param  object $item
 * @return string
 */
function ui_build_listitem( $class, $i, $item ) {
	$link = 'http://prd.5footstep.de/Permalink?page_id=' . $item->id;

	if( $item->url ) {
		$link = 'http://prd.5footstep.de/' . $item->url;
	}

	$out = "<li class=\"result {$class} {$item->source}\">";
	$out .= '<header>';
	$out .= '<a class="name" href="' . $link . '">' . ui_text_name( $item ) . '</a>';
	$out .= ui_text_source( $item );
	$out .= '</header>';

	if( $item->book ) {
		$out .= '<div class="book">' . $item->book . '</div>';
	}

	if( $item->type ) {
		$type = str_replace( ',', ', ', $item->type );
		$out .= '<div class="type"><span>Typ:</span> ' . $type;

		if( is_array( $item->type_sub ) ) {
			$out .= ' (' . implode( $item->type_sub, ', ' ) . ')';
		}

		$out .= '</div>';
	}

	if( is_array( $item->requirements ) ) {
		$requirements = '<em>' . implode( $item->requirements, '</em>, <em>' ) . '</em>';
		$out .= '<div class="req"><span>Voraussetzung:</span> ' . $requirements . '</div>';
	}

	if( is_array( $item->slot ) && $item->slot[0] !== '-' ) {
		$slot = implode( $item->slot, ', ' );
		$out .= '<div class="slot"><span>Platz:</span> ' . $slot . '</div>';
	}

	if( $item->school ) {
		$out .= '<div class="school"><span>Schule:</span> ' . $item->school;

		if( is_array( $item->school_sub ) ) {
			$out .= ' (' . implode( $item->school_sub, ', ' ) . ')';
		}

		if( is_array( $item->category ) ) {
			$out .= ' [' . implode( $item->category, ', ' ) . ']';
		}

		$out .= '</div>';
	}

	if( $item->source === 'rules' ) {
		if( is_array( $item->category ) ) {
			$out .= '<div class="category"><span>Kategorie:</span> ' . implode( $item->category, ', ' ) . '</div>';
		}
	}

	if( $item->desc ) {
		$desc = trim( $item->desc );

		if( strlen( $desc ) > 250 ) {
			$desc = substr( $desc, 0, 250 ) . '…';
		}

		$out .= '<div class="desc">' . $desc . '</div>';
	}

	return $out . '</li>';
}


/**
 *
 * @param  int $num_shown
 * @param  int $num_total
 * @return string
 */
function ui_build_pagination( $num_shown, $num_total ) {
	if( $num_shown >= $num_total || NUM_PER_PAGE < 1 ) {
		return '';
	}

	$num_pages = ceil( $num_total / NUM_PER_PAGE );

	if( $num_pages <= 1 ) {
		return '';
	}

	$link = '';
	$sep = '?';

	if( isset( $_GET['s'] ) ) {
		$link .= $sep . 's=' . htmlspecialchars( $_GET['s'] );
		$sep = '&';
	}

	if( isset( $_GET['f'] ) && $_GET['f'] !== '-' ) {
		$link .= $sep . 'f=' . htmlspecialchars( $_GET['f'] );
		$sep = '&';
	}

	if( isset( $_GET['b'] ) && $_GET['b'] !== '-' ) {
		$link .= $sep . 'b=' . htmlspecialchars( $_GET['b'] );
		$sep = '&';
	}

	$link .= $sep . 'page=';

	$out = '<div class="pages">';

	for( $i = 0; $i < $num_pages; $i++ ) {
		$class = '';

		if( $i === CURRENT_PAGE ) {
			$class = ' class="current"';
		}

		$out .= '<a href="' . $link . $i . '"' . $class . '>' . ( $i + 1 ) . '</a>';
	}

	$out .= '</div>';

	return $out;
}


/**
 *
 * @param  array $results
 * @return string
 */
function ui_build_short_info( $results ) {
	$first = get_first_result( $results );

	if( is_null( $first ) ) {
		return;
	}

	return get_short_info( $first );
}


/**
 *
 * @param  array $values
 * @return string
 */
function ui_build_translation_list( $values ) {
	if( is_null( $values ) || count( $values ) === 0 ) {
		return '';
	}

	$out = '<div class="translations">';
	$out .= '<p>Folgende mögliche Übersetzungen wurden gefunden:</p>';
	$out .= '<div class="links">';

	$params = '';
	$filter = get_search_filter();
	$book = get_search_book();

	if( $filter ) {
		$params .= '&f=' . $filter;
	}

	if( $book ) {
		$params .= '&b=' . $book;
	}

	foreach( $values as $i => $de ) {
		$text = htmlspecialchars( $de );
		$link = '?s=' . $text . $params;
		$out .= '<a href="' . $link . '">' . $text . '</a>, ';
	}

	$out = substr( $out, 0, -2 );

	$out .= '</div>';
	$out .= '</div>';

	return $out;
}


/**
 *
 * @param  object $item
 * @return string
 */
function ui_text_name( $item ) {
	if( isset( $item->cr ) && $item->cr !== 0 ) {
		$cr = $item->cr;

		if( $cr == 0.125 ) {
			$cr = '⅛';
		}
		else if( $cr == 0.16 ) {
			$cr = '⅙';
		}
		else if( $cr == 0.25 ) {
			$cr = '¼';
		}
		else if( $cr == 0.33 ) {
			$cr = '⅓';
		}
		else if( $cr == 0.5 ) {
			$cr = '½';
		}

		return $item->name . " (HG {$cr})";
	}

	return $item->name;
}


/**
 *
 * @param  object $item
 * @return string
 */
function ui_text_source( $item ) {
	$text = get_translation( $item->source );
	$out = '<span class="source">' . $text . '</span>';

	return $out;
}
