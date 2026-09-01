<script>
    document.addEventListener('livewire:navigated', function () {
        applyTheme();
    });

    (function () {
        applyTheme();
    })();

    function applyTheme() {
        var stored = localStorage.getItem('theme');
        var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (stored === 'dark' || (!stored && prefersDark)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
</script>