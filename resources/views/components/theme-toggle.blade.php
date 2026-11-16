{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    Light or dark. Follows the system until the visitor chooses; the choice
    is kept in localStorage and applied before the page is drawn (see the
    script in the layout's <head>).
--}}
<button type="button" class="theme-toggle" data-theme-toggle aria-pressed="false">
    <span class="when-light">{{ __('atrium-theme::theme.dark') }}</span>
    <span class="when-dark">{{ __('atrium-theme::theme.light') }}</span>
</button>
<script>
(function () {
    var root = document.documentElement;
    var button = document.currentScript.previousElementSibling;
    function current() {
        return root.dataset.theme || (window.matchMedia && matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    }
    function show() {
        root.dataset.theme = current();
        button.setAttribute('aria-pressed', current() === 'dark' ? 'true' : 'false');
    }
    button.addEventListener('click', function () {
        root.dataset.theme = current() === 'dark' ? 'light' : 'dark';
        try { localStorage.setItem('atrium-theme', root.dataset.theme); } catch (e) {}
        show();
    });
    show();
})();
</script>
