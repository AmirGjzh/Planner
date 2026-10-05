/**
 * Regenerates the mail-embedded font subsets in resources/fonts/mail/.
 *
 * Run from the repo root (inside the dev container):
 *   npm install --prefix /tmp/fontsubset subset-font
 *   NODE_PATH=/tmp/fontsubset/node_modules node resources/fonts/mail/subset.cjs
 *
 * Three weights are embedded — the same trio the web app ships: 400 (kept for
 * parity; today's markup renders only 500 body/button, matching the app's
 * font-medium usage, and 600 titles, matching font-semibold). Bold was dropped
 * app-wide in favour of the dominant semibold usage. The trio must stay under
 * Gmail's ~102KB message-clip budget (pinned by the size assertion in
 * resources/views/mail/reset-password.test.php); add a weight here (and a face +
 * font-weight in the template) if the mail ever needs one.
 *
 * Glyph pool: Latin ASCII + Persian alphabet/digits + ZWNJ + punctuation + every
 * Persian copy string in lang/fa.json — so translated mail copy always renders.
 */
const fs = require('fs');
const path = require('path');
const subsetFont = require('subset-font');

const FONTS_DIR = path.resolve(__dirname, '..'); // resources/fonts
const OUT_DIR = __dirname; // resources/fonts/mail
const FA_LANG = path.resolve(__dirname, '../../../lang/fa.json');

const WEIGHTS = ['Regular', 'Medium', 'SemiBold'];

const fa = JSON.parse(fs.readFileSync(FA_LANG, 'utf8'));

let text = '';
for (let c = 32; c <= 126; c++) text += String.fromCharCode(c); // Latin ASCII
text += 'ابپتثجچحخدذرزژسشصضطظعغفقکگلمنوهی'; // Persian alphabet
text += 'ءأؤئآىة'; // common variants
text += '۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩'; // Persian + Arabic-Indic digits
text += '\u200C'; // ZWNJ — essential for Persian joining
text += '«»،؟؛…—–‘’“”';
for (const value of Object.values(fa)) {
    if (typeof value === 'string') text += value; // full Persian copy pool
}
text = [...new Set([...text])].join('');

(async () => {
    fs.mkdirSync(OUT_DIR, { recursive: true });
    let total = 0;

    for (const name of WEIGHTS) {
        const source = path.join(FONTS_DIR, `Vazirmatn-${name}.woff2`);
        const output = path.join(OUT_DIR, `Vazirmatn-${name}.woff2`);
        const subset = await subsetFont(fs.readFileSync(source), text);
        fs.writeFileSync(output, subset);
        total += subset.length;
        console.log(`${name}: ${fs.statSync(source).size} -> ${subset.length} bytes`);
    }

    // Drop stale weights from earlier runs so the template and directory agree.
    for (const file of fs.readdirSync(OUT_DIR)) {
        if (file.startsWith('Vazirmatn-') && !WEIGHTS.some((name) => file === `Vazirmatn-${name}.woff2`)) {
            fs.unlinkSync(path.join(OUT_DIR, file));
            console.log(`removed stale ${file}`);
        }
    }

    const base64 = Math.ceil((total * 4) / 3);
    console.log(`total raw ${total} bytes | embedded ~${base64} bytes | unique glyphs ${[...new Set([...text])].length}`);
})().catch((error) => {
    console.error(error);
    process.exit(1);
});
