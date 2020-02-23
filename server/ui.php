<?php


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
		$check = ( $_GET['b'] === $book ) ? ' selected="selected"' : '';
		$out .= "<option value=\"{$book}\"{$check}>{$book}</option>";
	}

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

		$check = ( $_GET['f'] === $value ) ? ' selected="selected"' : '';
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
		$out .= '<div class="slot"><span>Slot:</span> ' . $slot . '</div>';
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
		$desc = $item->desc;

		if( strlen( $desc ) >= 250 ) {
			$desc = trim( $desc ) . '…';
		}

		$out .= '<div class="desc">' . $desc . '</div>';
	}

	return $out . '</li>';
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
