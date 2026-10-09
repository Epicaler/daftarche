{{-- Copyright and author credit --}}
<footer class="flex flex-col items-center justify-between gap-2 px-2 py-3 text-xs text-muted sm:flex-row">
    <p>© {{ jdate_format(now(), 'Y') }} {{ config('app.name') }}. تمامی حقوق محفوظ است.</p>
    <p class="flex items-center gap-1.5">
        طراحی و توسعه: <span class="font-semibold text-ink/80">حسام</span>
        <span class="text-line">|</span>
        <a href="https://t.me/MREpicaler" target="_blank" rel="noopener" class="inline-flex items-center gap-1 font-medium text-brand-700 transition hover:text-brand-500" title="تلگرام">
            <x-icon name="send" class="size-3.5" />
            <bdi dir="ltr">@MREpicaler</bdi>
        </a>
    </p>
</footer>
