#!/usr/bin/env bash

cd $(dirname "$0")

OUT_DIR='../translation-data'

if [ -d "$OUT_DIR" ]; then
	rm -rf "$OUT_DIR"
fi

mkdir -p "$OUT_DIR"

wget 'http://www.pathfinder-ogl.de/wiki/Ausr%C3%BCstung_(Glossar)' -O "$OUT_DIR/ausrüstung.html"
wget 'http://www.pathfinder-ogl.de/wiki/Fertigkeiten_(Glossar)' -O "$OUT_DIR/fertigkeiten.html"
wget 'http://www.pathfinder-ogl.de/wiki/Gefahren_(Glossar)' -O "$OUT_DIR/gefahren.html"
wget 'http://www.pathfinder-ogl.de/wiki/Klassen_(Glossar)' -O "$OUT_DIR/klassen.html"
wget 'http://www.pathfinder-ogl.de/wiki/Magische_Gegenst%C3%A4nde_(Glossar)' -O "$OUT_DIR/magische_gegenstände.html"
wget 'http://www.pathfinder-ogl.de/wiki/Merkmale_(Glossar)' -O "$OUT_DIR/merkmale.html"
wget 'http://www.pathfinder-ogl.de/wiki/Klassenmerkmalsauspr%C3%A4gungen_(Glossar)' -O "$OUT_DIR/merkmale_klassen.html"
wget 'http://www.pathfinder-ogl.de/wiki/Monstermerkmalsauspr%C3%A4gungen_(Glossar)' -O "$OUT_DIR/merkmale_monster.html"
wget 'http://www.pathfinder-ogl.de/wiki/Monster_(Glossar)' -O "$OUT_DIR/monster.html"
wget 'http://www.pathfinder-ogl.de/wiki/Talente_(Glossar)' -O "$OUT_DIR/talente.html"
wget 'http://www.pathfinder-ogl.de/wiki/Zauber_(Glossar)' -O "$OUT_DIR/zauber.html"
