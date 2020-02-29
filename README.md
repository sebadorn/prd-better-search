# Better Search for PRD (German)

Search results still link to their respective PRD page.


## Improvements

* Better performance on mobile devices: On the original page some auto-suggestion script blocks the whole page for a couple of seconds when starting to type.
* Sorted results: Perfect matches are listed first, and entries from the "Grundregelwerk" are listed before those which are not.
* Filter: Only search in certain categories like "talents", "monsters" etc.
* Removed some duplicate entries.


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


## LESS

    lessc --clean-css server/screen.less > server/screen.css
