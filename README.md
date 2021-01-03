# Better Search for the DPRD

Search results still link to their respective [DPRD (German Pathfinder Reference Document)](http://prd.5footstep.de/) page.

The data used for searches is downloaded from the DPRD. The contents are – to my understanding – [under the OGL (Open Game License)](http://prd.5footstep.de/FAQ/Deutsches-PRD-und-Nutzungsrechte).

The translation suggestions (en -> de) are based on the data from http://www.pathfinder-ogl.de/wiki/Regelbegriff-Glossar.


## Improvements

* Better performance on mobile devices: On the original page some auto-suggestion script blocks the whole page for a couple of seconds when starting to type.
* Sorted results: Perfect matches are listed first, and entries from the "Grundregelwerk" are listed before those which are not.
* Filter: Only search in certain categories like "talents", "monsters" etc.
* Removed some duplicate entries.
* Suggest translations for english search terms.


## Additional Syntax

Upper- or lowercase does not matter.

To return all entries with a certain **CR** you can use `HG:`. Examples:

    HG: 1/2
    HG: 0.5
    HG: 0,5
    HG: ½
    HG: 1
    HG: 24
    hg:1

To return all entries with a **type**, use `Typ:`. Examples:

    Typ: Kampf
    typ:kampf
    typ: krit
    TYP: Tier

To check talents for certain **requirements**, use `Req:`. Examples:

    REQ: GAB +6
    req:gab +6

You can also do something like this:

    req:ge >12

This will return all talents with a dexterity requirement of 13 or above. Other examples:

    req:gab >+6
    req: GAB <10
    Req: WE>9
    REQ:zs> 7
    req: wil > 0

To find all spells of a certain school or sub school, use `Schule:`. Examples:

    Schule: Illusion
    schule:zwang

To show contents of a certain book, use `Buch:`. Examples:

    Buch:Grundregelwerk
    BUCH: Ausbauregeln II: kampf
    buch:ausbauregeln



## Setup


### Look-up data

Download the data with `./tools/index-data-download.sh`. This will download a bunch of JSON files to a directory called `./index-data/export/` and will total approximately 10 MB.

In the next step call `node ./tools/index-data-adjust.js` which will create new JSON files in `./index-data/`.


### Translations

The same process for the translations:

```
./tools/translation-data-download.sh
node ./tools/translation-data-adjust.js
```

Additional translations can be added in `translation-data/custom.json` beforehand.


### CSS

If you made changes to `screen.less` recompile it with:

    lessc --clean-css server/screen.less > server/screen.css


### On the server

Then upload everything the application needs to your server. The final structure on the server should look like this:

    index-data/
      custom/
        rules.json
      books.json
      magic-items.json
      magic.json
      monsters.json
      rules.json
      talents.json
      traits.json
      words-of-power.json
    translation-data/
      custom.json
      translations.json
    index.php
    screen.css
    search-check.php
    search.php
    short-info.php
    syntax.html
    ui.php


## Requirements

* bash (or MinGW on Windows)
* LESS
* NodeJS >= 10
* PHP >= 5.4
