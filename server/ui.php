<?php

function ui_build_listitem( $class, $i, $item ) {
	$link = "http://prd.5footstep.de/Permalink?page_id=" . $item->id;

	$out = '<li class="result ' . $class . ' ' . $item->source . '">';
	$out .= '<a class="name" href="' . $link . '">' . $item->name . '</a>';
	$out .= ui_text_source( $item );

	if( $item->desc ) {
		$out .= '<span class="desc">' . $item->desc . '</span>';
	}

	if( $item->book ) {
		$out .= '<span class="book">' . $item->book . '</span>';
	}

	return $out . '</li>';
}


function ui_text_source( $item ) {
	$text = NULL;

	switch( $item->source ) {
		case "magic-items":
			$text = "Magische Gegenstände";
			break;

		case "magic":
			$text = "Zauber";
			break;

		case "monsters":
			$text = "Monster";
			break;

		case "rules":
			$text = "Regeln";
			break;

		case "talents":
			$text = "Talente";
			break;

		case "traits":
			$text = "Wesenszüge";
			break;

		case "words-of-power":
			$text = "Worte der Macht";
			break;

		default:
			return "";
	}

	$out = '<span class="source">' . $text . '</span>';

	return $out;
}
