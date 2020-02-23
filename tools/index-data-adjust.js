'use strict';

const fs = require( 'fs' );
const path = require( 'path' );

const DATA_PATH = path.join( __dirname, '..', 'index-data' );
const regexNameLink = /.+['"]>(.+)<\/a>$/;
const regexDescClean = /^(&lt;sup&gt;|\^\^)[A-Z]+(&lt;\/sup&gt;||\^\^)/;
const regexReqClean = /\^\^[A-Z0-9 ]+\^\^/g;
const regexReqGAB = /Grund(-A|a)ngriffsbonus/;
const regexReqBracket = /\( /g;


const getFileJSON = file => {
	const filePath = path.join( DATA_PATH, 'export', file );
	const json = fs.readFileSync( filePath );

	return JSON.parse( json );
};


const writeFileJSON = ( file, content ) => {
	const filePath = path.join( DATA_PATH, file );
	const json = JSON.stringify( content );

	fs.writeFileSync( filePath, json );
};


const getBaseData = entry => {
	const item = {
		id: entry.ID,
		name: String( entry.Name ).trim()
	};

	item.name = item.name.replace( regexNameLink, '$1' );

	if( entry.Beschreibung ) {
		item.desc = entry.Beschreibung;
		item.desc = item.desc.replace( regexDescClean, '' );
		item.desc = item.desc.trim();
	}

	if( entry.Regelwerk ) {
		item.book = entry.Regelwerk;
	}

	return item;
};


const addMagicData = ( item, entry ) => {
	if( entry.Kategorie ) {
		const list = String( entry.Kategorie ).split( ',' );
		item.category = list.map( c => c.trim() );
	}

	if( entry.Schule ) {
		item.school = entry.Schule;
	}

	if( entry.Unterschule ) {
		const list = String( entry.Unterschule ).split( ',' );
		item.school_sub = list.map( s => s.trim() );
	}
};


const addMagicItemData = ( item, entry ) => {
	if( entry.Art ) {
		item.type = entry.Art;
	}

	if( typeof entry.Platz === 'string' && entry.Platz ) {
		const list = entry.Platz.split( ',' );

		item.slot = list.map( s => {
			s = s.trim();

			if( s === 'Keiner' ) {
				return '-';
			}

			return s;
		} );
	}
};


const addMonsterData = ( item, entry ) => {
	if( entry.Art ) {
		item.type = entry.Art;
	}

	if( entry.Unterart ) {
		const list = String( entry.Unterart ).split( ',' );
		item.type_sub = list.map( t => t.trim() );
	}

	item.cr = 0;

	if( entry.HG ) {
		item.cr = Number( entry.HG );

		if( isNaN( item.cr ) ) {
			item.cr = 0;
		}
	}
};


const addRuleData = ( item, entry ) => {
	if( entry.Kategorie ) {
		const list = String( entry.Kategorie ).split( ',' );
		item.category = list.map( c => c.trim() );
	}

	if( entry.Schlusselworte ) {
		const list = String( entry.Schlusselworte ).split( ',' );
		item.keywords = list.map( k => k.trim() );
	}
};


const addTalentData = ( item, entry ) => {
	if( entry.Art ) {
		item.type = entry.Art;
	}

	if( entry.Voraussetzung ) {
		const list = String( entry.Voraussetzung ).split( ',' );

		item.requirements = list.map( r => {
			r = r.replace( regexReqClean, '' );
			r = r.replace( regexReqGAB, 'GAB' );
			r = r.replace( 'Zauberstufe ', 'ZS ' );
			r = r.replace( regexReqBracket, '(' );
			r = r.trim();

			if( r.endsWith( '.' ) ) {
				r = r.substr( 0, r.length - 1 );
			}

			return r;
		} );
	}
};


const addTraitData = ( item, entry ) => {
	if( entry.Art ) {
		item.type = entry.Art;
	}
};


const addWOPData = ( item, entry ) => {
	if( entry.Kategorie ) {
		const list = String( entry.Kategorie ).split( ',' );
		item.category = list.map( c => c.trim() );
	}

	if( entry.Schule ) {
		item.school = entry.Schule;
	}

	if( entry.Unterschule ) {
		const list = String( entry.Unterschule ).split( ',' );
		item.school_sub = list.map( s => s.trim() );
	}
};


const queue = [
	{ file: 'magic-items.json', handler: addMagicItemData },
	{ file: 'magic.json', handler: addMagicData },
	{ file: 'monsters.json', handler: addMonsterData },
	{ file: 'rules.json', handler: addRuleData },
	{ file: 'talents.json', handler: addTalentData },
	{ file: 'traits.json', handler: addTraitData },
	{ file: 'words-of-power.json', handler: addWOPData }
];

const ruleDuplicates = [
	'Magische Gegenstände',
	'Monster',
	'Talente',
	'Wesenszüge',
	'Zauber'
];

const books = [];

queue.forEach( a => {
	const content = getFileJSON( a.file );
	const data = [];
	const ids = [];

	content.data.forEach( entry => {
		const item = getBaseData( entry );
		a.handler( item, entry );

		if( a.file === 'rules.json' && Array.isArray( item.category ) ) {
			for( let i = 0; i < ruleDuplicates.length; i++ ) {
				const shouldNotInclude = ruleDuplicates[i];

				if( item.category.includes( shouldNotInclude ) ) {
					return;
				}
			}
		}

		if( ids.includes( item.id ) ) {
			return;
		}

		if( item.book && !books.includes( item.book ) ) {
			books.push( item.book );
		}

		ids.push( item.id );
		data.push( item );
	} );

	writeFileJSON( a.file, data );
} );

books.sort();
writeFileJSON( 'books.json', books );
