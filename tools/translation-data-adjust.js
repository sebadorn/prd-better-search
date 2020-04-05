'use strict';

const fs = require( 'fs' );
const path = require( 'path' );

const DATA_PATH = path.join( __dirname, '..', 'translation-data' );


const getFileHTML = file => {
	const filePath = path.join( DATA_PATH, file );
	const html = fs.readFileSync( filePath );

	return String( html );
};

const writeFileJSON = ( file, content ) => {
	const filePath = path.join( DATA_PATH, file );
	const json = JSON.stringify( content );

	fs.writeFileSync( filePath, json );
};


// en -> de
const translations = {};

const DATA = [
	{ file: 'ausrüstung.html', startSel: 'id="Waffen_und_ihre_Kategorien"', cellEN: 0, cellDE: 2 },
	{ file: 'fertigkeiten.html', startSel: 'id="Fertigkeiten"', cellEN: 0, cellDE: 2 },
	{ file: 'gefahren.html', startSel: 'id="Fallen"', cellEN: 0, cellDE: 2 },
	{ file: 'klassen.html', startSel: 'id="mw-content-text"', cellEN: 0, cellDE: 1 },
	{ file: 'magische_gegenstände.html', startSel: 'id="Eigenschaften_von_magischen_Gegenst.C3.A4nden"', cellEN: 0, cellDE: 2 },
	{ file: 'merkmale.html', startSel: 'id="Allgemeine_Merkmale"', cellEN: 0, cellDE: 2 },
	{ file: 'merkmale_klassen.html', startSel: 'id="Klassenmerkmalsauspr.C3.A4gungen"', cellEN: 0, cellDE: 2 },
	{ file: 'merkmale_monster.html', startSel: 'id="Monstermerkmalsauspr.C3.A4gungen_und_anderes"', cellEN: 0, cellDE: 2 },
	{ file: 'monster.html', startSel: 'id="Monster_und_ihre_Varianten"', cellEN: 0, cellDE: 2 },
	{ file: 'talente.html', startSel: 'id="Talentkategorien"', cellEN: 0, cellDE: 2 },
	{ file: 'zauber.html', startSel: 'id="Zauber"', cellEN: 0, cellDE: 2 }
];

DATA.forEach( entry => {
	let content = getFileHTML( entry.file );

	let start = content.indexOf( entry.startSel );
	content = content.substring( start );
	start = content.indexOf( '<tr>' );
	content = content.substring( start );
	let end = content.lastIndexOf( '</table>' );
	content = content.substring( 0, end + 8 );
	content = content.trim();

	const tables = content.split( '</table>' );
	const rows = [];

	for( let i = 0; i < tables.length; i++ ) {
		let table = tables[i];
		table = table.replace( /\n/g, '' );
		table = table.replace( /<h2>.+<\/h2>/i, '' );
		table = table.replace( /<table [^>]+>/i, '' );
		table = table.replace( '</table>', '' );
		table = table.trim();
		tables[i] = table;

		rows.push( ...table.split( '</tr>' ) );
	}

	for( let i = 0; i < rows.length; i++ ) {
		let row = rows[i];

		if( row.includes( '<th>' ) ) {
			continue;
		}

		row = row.replace( /<tr>/g, '' );
		row = row.trim();

		const cells = row.split( '</td>' );

		if( cells.length < 4 ) {
			continue;
		}

		let en = cells[entry.cellEN];
		en = en.replace( '<td>', '' );
		en = en.trim();
		en = en.toLowerCase();

		let de = cells[entry.cellDE];
		de = de.replace( '<td>', '' );
		de = de.trim();

		if( en === de.toLowerCase() ) {
			continue;
		}

		// Correct entries like "Zauber, mächtiger" to "mächtiger Zauber".
		if( de.includes( ', ' ) ) {
			let parts = de.split( ', ' );

			if( parts.length === 2 && !parts[1].includes( ' ' ) ) {
				de = parts[1] + ' ' + parts[0];
			}
		}

		if( translations[en] ) {
			if( !translations[en].includes( de ) ) {
				translations[en].push( de );
			}
		}
		else {
			translations[en] = [de];
		}
	}
} );


// Sort translations by key (en).
const sorted = {};

let keys = Object.keys( translations );
keys = keys.sort( ( a, b ) => {
	return a.localeCompare( b );
} );

keys.forEach( key => {
	sorted[key] = translations[key];
} );

writeFileJSON( 'translations.json', sorted );
