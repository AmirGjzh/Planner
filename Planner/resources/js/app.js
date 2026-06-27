import './globals/theme.js'; /* By Sheaf.dev */

//
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import rover from "@sheaf/rover"

// now you can register
// components using Alpine.data(...) and
// plugins using Alpine.plugin(...)


Alpine.plugin(rover)

import './components/select.js';
import './globals/modals.js';
import './components/calendar/index.js';
import './components/date-picker/index.js';

// Livewire.start() — called automatically via DOMContentLoaded in livewire.esm.js
