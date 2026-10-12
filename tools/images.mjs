#!/usr/bin/env node
// WordPress.org /assets files at the exact sizes the directory reads (rendered by tools/render.mjs).
import { icon, banner, slide } from './brand.mjs';
const opts = { name: 'Bukvar Lite', tagline: 'New Bulgarian slugs and file names in Latin, by the Transliteration Act: София → sofia.', chips: ['New slugs', 'Upload file names', 'Streamlined System', 'GPL, free'], glyph: 'Бb', kicker: 'Free WordPress plugin' };
const s1 = slide(1280, 800, { kicker: 'New slugs', title: 'A Bulgarian title gets a readable Latin address', body:
`Title      Как да изберем щори за кухнята
Without    /%d0%ba%d0%b0%d0%ba-%d0%b4%d0%b0-%d0%b8%d0%b7%d0%b1%d0%b5…/
<span style="color:#2f7d5b">Bukvar Lite /kak-da-izberem-shtori-za-kuhnyata/</span>

Title      София, България
<span style="color:#2f7d5b">Bukvar Lite /sofia-bulgaria/</span>

Upload     Снимка на Пловдив.png
<span style="color:#2f7d5b">Bukvar Lite snimka-na-plovdiv.png</span>` });
const s2 = slide(1280, 800, { kicker: 'Settings, Permalinks', title: 'Three switches, on by default', body:
`<span style="font-family:system-ui;font-weight:700;font-size:24px">Latin slugs and file names (Bukvar Lite)</span>
<span style="font-family:system-ui;color:#5d6762">Bulgarian Cyrillic is written in Latin by the Streamlined System
of the Bulgarian Transliteration Act: София becomes sofia.</span>

Write new slugs in Latin              ☑
Write uploaded file names in Latin    ☑
Lower-case file names                 ☑` });
const s3 = slide(1280, 800, { kicker: 'The rules', title: 'The Streamlined System of the 2009 Act', body:
`а a   б b   в v   г g   д d   е e   ж zh  з z   и i   й y
к k   л l   м m   н n   о o   п p   р r   с s   т t   у u
ф f   х h   ц ts  ч ch  ш sh  щ sht ъ a   ь y   ю yu  я ya

"ия" at the end of a word is ia     София → Sofia
The state name stays Bulgaria       България → Bulgaria
Capitals follow the word            Щастие → Shtastie, ЩАСТИЕ → SHTASTIE` });
export default [
  { out: 'assets/icon-128x128.png', width: 128, height: 128, html: icon(128, 'Бb') },
  { out: 'assets/icon-256x256.png', width: 256, height: 256, html: icon(256, 'Бb') },
  { out: 'assets/banner-772x250.png', width: 772, height: 250, html: banner(772, 250, opts) },
  { out: 'assets/banner-1544x500.png', width: 1544, height: 500, html: banner(1544, 500, opts) },
  { out: 'assets/screenshot-1.png', width: 1280, height: 800, html: s1 },
  { out: 'assets/screenshot-2.png', width: 1280, height: 800, html: s2 },
  { out: 'assets/screenshot-3.png', width: 1280, height: 800, html: s3 }
];
