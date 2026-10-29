<?php
/** LatestiPhone logo mark: a phone outline with a location pin (our branches) inside. Inline SVG so it can animate. */
function logo_mark(int $size = 28, string $cls = ''): string {
    static $n = 0; $id = 'lg' . (++$n);
    return '<svg class="logo-mark ' . $cls . '" width="' . $size . '" height="' . $size . '" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="LatestiPhone logo">'
        . '<defs><linearGradient id="' . $id . '" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e8c766"/><stop offset="1" stop-color="#a87c1f"/></linearGradient></defs>'
        . '<rect width="64" height="64" rx="16" fill="#16140f"/>'
        . '<rect x="19.5" y="9.5" width="25" height="45" rx="6.5" fill="none" stroke="url(#' . $id . ')" stroke-width="2.4"/>'
        . '<rect x="27.5" y="13.5" width="9" height="2.8" rx="1.4" fill="url(#' . $id . ')"/>'
        . '<g class="logo-pin"><path d="M32 24c-4.3 0-7.6 3.3-7.6 7.6 0 5.6 7.6 11.6 7.6 11.6s7.6-6 7.6-11.6c0-4.3-3.3-7.6-7.6-7.6z" fill="none" stroke="url(#' . $id . ')" stroke-width="2.2" stroke-linejoin="round"/>'
        . '<circle cx="32" cy="31.6" r="2.8" fill="url(#' . $id . ')"/></g></svg>';
}
/** Mark + wordmark. $dark = true when drawn on a dark background. */
function logo_full(int $size = 28, bool $dark = false): string {
    return '<span class="logo-full inline-flex items-center gap-2.5">' . logo_mark($size)
        . '<span class="font-display font-semibold tracking-tight ' . ($dark ? 'text-white' : 'text-[#16140f] dark:text-white') . '" style="font-size:' . round($size * 0.62) . 'px">Latest<span class="' . ($dark ? 'text-[#cba135]' : 'text-[#a87c1f] dark:text-[#cba135]') . '">iPhone</span></span></span>';
}
