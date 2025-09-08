@props([
    "items",
])

<section class="item-grid">
    @foreach ($items as $item)
        <div class="item-card">
            <a class ="link-details" href="{{ route("details.show", $item) }}">
                <img src="{{ $item->image_url }}" alt="{{ $item->item_name }}" class="item-card__img" />
                <p class="item-card__name">
                    {{ $item->item_name }}
                    @if ($item->is_sold)
                        <span class="item-card__sold">SOLD</span>
                    @endif
                </p>
            </a>
        </div>
    @endforeach
</section>
