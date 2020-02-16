'use strict';

const fs = require( 'fs' );
const path = require( 'path' );

const DATA_PATH = path.join( __dirname, '..', 'index-data' );
const regexNameLink = /.+['"]>(.+)<\/a>$/;


const getFileJSON = ( file ) => {
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
	}

	if( entry.Regelwerk ) {
		item.book = entry.Regelwerk;
	}

	return item;
};


const addMagicData = ( item, entry ) => {
	if( entry.Kategorie ) {
		let list = String( entry.Kategorie ).split( ',' );
		item.category = list.map( c => c.trim() );
	}

	if( entry.Schule ) {
		item.school = entry.Schule;
	}

	if( entry.Unterschule ) {
		let list = String( entry.Unterschule ).split( ',' );
		item.school_sub = list.map( s => s.trim() );
	}
};


const addMagicItemData = ( item, entry ) => {
	if( entry.Art ) {
		item.type = entry.Art;
	}

	if( typeof entry.Platz === 'string' && entry.Platz ) {
		let list = entry.Platz.split( ',' );

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
		let list = String( entry.Unterart ).split( ',' );
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
		let list = String( entry.Kategorie ).split( ',' );
		item.category = list.map( c => c.trim() );
	}

	if( entry.Schlusselworte ) {
		let list = String( entry.Schlusselworte ).split( ',' );
		item.keywords = list.map( k => k.trim() );
	}
};


const addTalentData = ( item, entry ) => {
	if( entry.Art ) {
		item.type = entry.Art;
	}

	if( entry.Voraussetzung ) {
		let list = String( entry.Voraussetzung ).split( ',' );
		item.requirements = list.map( r => r.trim() );
	}
};


const addTraitData = ( item, entry ) => {
	if( entry.Art ) {
		item.type = entry.Art;
	}
};


const addWOPData = ( item, entry ) => {
	if( entry.Kategorie ) {
		let list = String( entry.Kategorie ).split( ',' );
		item.category = list.map( c => c.trim() );
	}

	if( entry.Schule ) {
		item.school = entry.Schule;
	}

	if( entry.Unterschule ) {
		let list = String( entry.Unterschule ).split( ',' );
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

queue.forEach( a => {
	const content = getFileJSON( a.file );
	const data = [];

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

		data.push( item );
	} );

	writeFileJSON( a.file, data );
} );
