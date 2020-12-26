<?php


/**
 *
 * @param  array $results
 * @return object|null
 */
function get_first_result( $results ) {
	if( !is_array( $results ) ) {
		return NULL;
	}

	$keys = ['title_perfect', 'title_contains', 'fuzzy', 'desc', 'keywords'];

	foreach( $keys as $i => $key ) {
		$list = $results[$key];

		if( is_array( $list ) && count( $list ) > 0 ) {
			return $list[0];
		}
	}

	return NULL;
}


/**
 *
 * @param  object $item
 * @return string
 */
function get_short_info( $item ) {
	if( is_null( $item ) ) {
		return '';
	}

	$info = '';
	$name = strtolower( $item->name );

	if( $name === 'schadensreduzierung' ) {
		$info = '
<table>
<thead>
	<tr>
		<th>Art der SR</th>
		<th>Äquivalent in Waffen&shy;verbesserungs&shy;bonus</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td><a href="?s=Kaltes+Eisen">Kaltes Eisen</a>/<a href="?s=Alchemistensilber">Silber</a></td>
		<td>+3</td>
	</tr>
	<tr>
		<td><a href="?s=Adamant">Adamant</a></td>
		<td>+4*</td>
	</tr>
	<tr>
		<td><a href="?s=Intelligente+Gegenst%C3%A4nde&f=rules">Gesinnungs&shy;basiert</a></td>
		<td>+5</td>
	</tr>
	<tr class="note">
		<td colspan="2">* Dies verleiht nicht die Fähigkeit, Härte zu ignorieren, wie es einer echten Adamant&shy;waffe möglich ist.</td>
	</tr>
</tbody>
</table>
		';
	}
	else {
		return '';
	}

	$out = '<div class="short-info ' . $item->source . '">';
	$out .= trim( $info );
	$out .= '</div>';

	return $out;
}
