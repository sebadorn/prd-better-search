#!/usr/bin/env bash

cd $(dirname "$0")

OUT_DIR='../index-data/export'

if [ -d "$OUT_DIR" ]; then
	rm -rf "$OUT_DIR"
fi

mkdir -p "$OUT_DIR"

wget 'http://prd.5footstep.de/cache/prd_datatable__index.txt' -O "$OUT_DIR/rules.json"
wget 'http://prd.5footstep.de/cache/prd_datatable__items.txt' -O "$OUT_DIR/magic-items.json"
wget 'http://prd.5footstep.de/cache/prd_datatable__monster.txt' -O "$OUT_DIR/monsters.json"
wget 'http://prd.5footstep.de/cache/prd_datatable__talente.txt' -O "$OUT_DIR/talents.json"
wget 'http://prd.5footstep.de/cache/prd_datatable__traits.txt' -O "$OUT_DIR/traits.json"
wget 'http://prd.5footstep.de/cache/prd_datatable__wdm.txt' -O "$OUT_DIR/words-of-power.json"
wget 'http://prd.5footstep.de/cache/prd_datatable__zauber.txt' -O "$OUT_DIR/magic.json"
