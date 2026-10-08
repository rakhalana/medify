import './bootstrap';

import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
window.createIcons = () => createIcons({ icons });

Alpine.start();

// Initialize Lucide icons on page load and dynamically when DOM updates
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

document.addEventListener('alpine:initialized', () => {
    createIcons({ icons });
});
