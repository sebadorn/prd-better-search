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
		$info = output_schadensreduzierung();
	}
	else if( $name === 'gelegenheitsangriffe' ) {
		$info = output_gelegenheitsangriff();
	}
	else if( $name === 'gift' || $name === 'gifte' ) {
		$info = output_gift();
	}
	else {
		return '';
	}

	$out = '<div class="short-info ' . $item->source . ' info-' . $name . '">';
	$out .= trim( $info );
	$out .= '</div>';

	return $out;
}


/**
 *
 * @return string
 */
function output_gelegenheitsangriff() {
	return '
<table>
	<tbody>
		<tr>
			<th>Standard-Aktion</th>
			<th>Gelegenheitsangriff</th>
		</tr>
		<tr>
			<td>Aktiven Zauber mittels Konzentration aufrechterhalten</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Aktivierung eines magischen Gegenstandes (außer Tränken und Ölen)</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Angriff (Fernkampf)</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Angriff (Nahkampf)</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Angriff (Waffenlos)</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Außergewöhnliche Fähigkeit einsetzen</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Energie fokussieren</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Fackel mit einem Zündholz entzünden</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Fertigkeit einsetzen, die eine Aktion erfordert</td>
			<td class="maybe">Meistens</td>
		</tr>
		<tr>
			<td>Finte</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td><a href="?s=Jemand%20anderem%20helfen&f=rules">Jemand anderem helfen</a></td>
			<td class="maybe">Vielleicht <sup>1</sup></td>
		</tr>
		<tr>
			<td>Schriftrolle lesen</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Sich vorbereiten (um eine Standard-Aktion auszulösen)</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td><a href="?s=sterbend&f=rules">Sterbenden</a> Freund <a href="?s=stabilisiert&f=rules">stabilisieren</a> (siehe die Fertigkeit <a href="?s=Heilkunde&f=rules">Heilkunde</a>)</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Trank zu sich nehmen oder ein Öl benutzen</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Übernatürliche Fähigkeit einsetzen</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td><a href="?s=Ringkampf&f=rules">Umklammerung</a> entkommen</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Verborgene Waffe ziehen (siehe <a href="?s=Fingerfertigkeit&f=rules">Fingerfertigkeit</a>)</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Volle Verteidigung</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Zauber beenden</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Zauber wirken (Zeitaufwand: 1 Standard-Aktion)</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Zauberähnliche Fähigkeit einsetzen</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Zauberresistenz senken</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<th>Bewegungsaktion</th>
			<th>Gelegenheitsangriff</th>
		</tr>
		<tr>
			<td>Aktiven Zauber lenken</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Auf ein Pferd steigen oder absteigen</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Aufstehen</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Bewegung</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Gegenstand aufheben</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Hand- oder leichte Armbrust laden</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Schild bereitmachen oder lösen <sup>2</sup></td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Schweres Objekt bewegen</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Tür öffnen oder schließen</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Verängstigtes Reittier kontrollieren</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Verstauten Gegenstand herausholen</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Waffe verstauen</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Waffe ziehen <sup>2</sup></td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<th>Volle Aktion</th>
			<th>Gelegenheitsangriff</th>
		</tr>
		<tr>
			<td>Berührungszauber auf bis zu 6 Verbündete anwenden</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td><a href="?s=Hilflose%20Verteidiger&f=rules">Coup de Grace</a></td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Fackel entzünden</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Fertigkeit mit der Dauer 1 Runde anwenden</td>
			<td class="maybe">meistens</td>
		</tr>
		<tr>
			<td>Flammen ersticken</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td><a href="?s=Rennen&f=rules">Rennen</a></td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td><a href="?s=Rückzug&f=rules">Rückzug</a> <sup>3</sup></td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Schwere oder Repetierarmbrust laden</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Sich aus einem Netz befreien</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td><a href="?s=Sturmangriff&f=rules">Sturmangriff</a> <sup>3</sup></td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Voller Angriff</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Waffe in einem beriemten Panzerhandschuh befestigen oder heraus lösen</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<td>Wurf einer Waffe mit Flächenwirkung vorbereiten</td>
			<td class="yes">Ja</td>
		</tr>
		<tr>
			<th>Freie Aktion</th>
			<th>Gelegenheitsangriff</th>
		</tr>
		<tr>
			<td>Aufhören, sich auf einen Zauber zu konzentrieren</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Gegenstand fallen lassen</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Sich zu Boden fallen lassen</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Sprechen</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>Zauberkomponenten vorbereiten, um einen Zauber zu wirken <sup>4</sup></td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<th>Schnelle Aktion</th>
			<th>Gelegenheitsangriff</th>
		</tr>
		<tr>
			<td>Einen schnellen Zauber wirken</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<th>Augenblickliche Aktion</th>
			<th>Gelegenheitsangriff</th>
		</tr>
		<tr>
			<td>Zauber <em><a href="?s=Federfall&f=magic">Federfall</a></em> wirken</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<th>Keine Aktion</th>
			<th>Gelegenheitsangriff</th>
		</tr>
		<tr>
			<td>Abwarten</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<td>1,50&nbsp;m-Schritt</td>
			<td class="no">Nein</td>
		</tr>
		<tr>
			<th>Unterschiedliche Aktionsarten</th>
			<th>Gelegenheitsangriff</th>
		</tr>
		<tr>
			<td><a href="?s=kampfmanöver&f=rules">Kampfmanöver</a> ausführen <sup>5</sup></td>
			<td class="yes">Ja</td>
		</tr>
	</tbody>
	<tfoot class="note">
		<tr>
			<td colspan="2"><sup>1</sup> Wenn du jemandem bei einer Aktion hilfst, die normalerweise einen Gelegenheits&shy;angriff provozieren würde, dann provoziert auch die unterstützende Handlung selbst einen Gelegenheits&shy;angriff.</td>
		</tr>
		<tr>
			<td colspan="2"><sup>2</sup> Ist dein GAB +1 oder höher, kannst du jeweils eine dieser Aktionen mit einer regulären Bewegung kombinieren. Hast du das Talent Kampf mit zwei Waffen, kannst du zwei Einhand- oder leichte Waffen in derselben Zeit ziehen, die du normaler&shy;weise bräuchtest, um eine Waffe zu ziehen.</td>
		</tr>
		<tr>
			<td colspan="2"><sup>3</sup> Kann als Standard-Aktion eingesetzt werden, wenn du auf eine einzelne Handlung in der Runde beschränkt bist.</td>
		</tr>
		<tr>
			<td colspan="2"><sup>4</sup> Solange die Komponente nicht ein extrem großer oder unhandlicher Gegenstand ist.</td>
		</tr>
		<tr>
			<td colspan="2"><sup>5</sup> Manche Kampf&shy;manöver ersetzen einen Nahkampf&shy;angriff, nicht eine Aktion. Als Nahkampf&shy;angriffe können sie während eines Angriffs oder Sturm&shy;angriffs einmal, einmal oder mehrmals innerhalb eines Vollen Angriffs, oder sogar als Gelegenheits&shy;angriff eingesetzt werden.</td>
		</tr>
	</tfoot>
</table>
	';
}


function output_gift() {
	return '
<p>Links:</p>
<div><a href="https://paizo.com/community/blog/v5748dyo5lc12?I-Drank-What-An-FAQ-on-Poison">I Drank What? An FAQ on Poison</a></div>
<div><a href="https://paizo.com/community/blog/v5748dyo5lc12?I-Drank-What-An-FAQ-on-Poison#34">Onset time and secondary effect – I Drank What? An FAQ on Poison</a></div>
	';
}


/**
 *
 * @return string
 */
function output_schadensreduzierung() {
	return '
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
