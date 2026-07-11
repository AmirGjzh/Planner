const saved = localStorage.getItem('theme');
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

const isDark = saved === 'dark' || (saved !== 'light' && prefersDark);

document.documentElement.classList.toggle('dark', isDark);
