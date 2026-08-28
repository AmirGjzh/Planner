import { writeFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const __dirname = dirname(fileURLToPath(import.meta.url));
const root = join(__dirname, '..');

// Reicon icon names to export (PascalCase, exact filenames in reicon/icons).
// Add any new icon used in the app here, then run: npm run reicon:export
const ICONS = [
    'EyeOpen',
    'EyeClosed',
    'CheckSquare',
    'AlertTriangle',
    'ShieldAlert',
    'AlertCircle',
    'Envelope2',
    'Lock',
    'EyeClosed',
    'EyeOpen',
    'Check',
    'User4',
    'Xmark'
];

const store = {};

for (const name of ICONS) {
    const mod = await import(`reicon/icons/${name}`);
    const factory = mod[name] ?? mod.default;
    store[name] = factory.iconData;
}

const out = join(root, 'app', 'Support', 'reicon-icons.json');
writeFileSync(out, `${JSON.stringify(store, null, 2)}\n`);
console.log(`Exported ${Object.keys(store).length} icons -> app/Support/reicon-icons.json`);
