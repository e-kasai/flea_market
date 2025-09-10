@props([
    'items'=>[],
])

<nav class="tabs__nav" role="tablist">
    @foreach ($items as $tab)
        <a
            href="{{ $tab['href'] }}"
            class="{{ !empty($tab['active']) ? 'is-active' : '' }}"
            role="tab"
            aria-selected="{{ !empty($tab['active']) ? 'true' : 'false' }}"
        >
            {{ $tab['label'] }}
        </a>
    @endforeach
</nav>