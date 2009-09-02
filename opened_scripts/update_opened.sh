#!/bin/sh -e
BASE="/var/www/opened.creativecommons.org/volatile/opened-frontpage-sideboxes.html"
python front_page_boxes.py http://opened.creativecommons.org/ > "$BASE.tmp"
mv "$BASE.tmp" "$BASE"
