<div id="cookie-consent" class="fixed bottom-0 left-0 right-0 z-50 bg-fjord-ink text-white px-4 py-3 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-lg" style="display:none">
    <p class="text-sm text-[#d6e6f7]">{{ trans('cookies.text') }}</p>
    <button id="cookie-accept" class="px-4 py-1.5 bg-glacier text-fjord-ink font-semibold text-sm rounded whitespace-nowrap hover:bg-white transition">{{ trans('cookies.accept') }}</button>
</div>
<script>
    (function () {
        var panel = document.getElementById('cookie-consent');
        var btn = document.getElementById('cookie-accept');
        if (localStorage.getItem('cookie-consent') === '1') { panel.style.display = 'none'; }
        if (btn) { btn.addEventListener('click', function () { localStorage.setItem('cookie-consent', '1'); panel.style.display = 'none'; }); }
    })();
</script>