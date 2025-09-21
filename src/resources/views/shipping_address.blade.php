@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/form.css") }}" />
@endpush

@section("content")
    <x-form.card title="住所の変更" action="{{ route('address.update',$item) }}" method="PATCH">
        {{-- 郵便番号 --}}
        <x-form.input
            type="text"
            name="postal_code"
            label="郵便番号"
            value="{{ old('postal_code', $shippingAddress['postal_code']) }}"
            required
        />
        {{-- 住所 --}}
        <x-form.input
            type="text"
            name="address"
            label="住所"
            value="{{ old('address', $shippingAddress['address']) }}"
            required
        />
        {{-- 建物名 --}}
        <x-form.input type="text" name="building" label="建物名" value="{{ old('building', $shippingAddress['building']) }}" />
        <x-slot name="actions">
            <button class="btn" type="submit">更新する</button>
        </x-slot>
    </x-form.card>
@endsection
