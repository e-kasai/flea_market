@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/form.css") }}" />
@endpush

@section("content")
    <x-form.card title="住所の変更" action="{{ route('address.update',$item) }}" method="PATCH">
        <x-form.input
            type="text"
            name="postal_code"
            label="郵便番号"
            value="{{ $shippingAddress['postal_code'] }}"
            autocomplete="postal-code"
            required
        />
        <x-form.input
            type="text"
            name="address"
            label="住所"
            value="{{ $shippingAddress['address'] }}"
            autocomplete="street-address"
            required
        />
        <x-form.input
            type="text"
            name="building"
            label="建物名"
            value="{{ $shippingAddress['building']}}"
            autocomplete="address-line2"
        />
        <x-slot name="actions">
            <button class="btn" type="submit">更新する</button>
        </x-slot>
    </x-form.card>
@endsection
