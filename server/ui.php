<?php

function ui_build_listitem( $i, $item ) {
	$out = '<li class="result">';

	$out .= $item->name;

	return $out . '</li>';
}
