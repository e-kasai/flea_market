@props([
    "name",
    "label",
    "type" => "text",
    "value" => null,
    "autocomplete" => null,
])

@php
    $isPassword = $type === "password";
    // パスワードは off、それ以外は指定があればその値、なければ null
    $auto = $isPassword ? "off" : $autocomplete;

    // id は渡されていればそれ、無ければ name を使う
    $id = $attributes->get("id") ?? $name;
    // エラー用ID
    $errorId = $id . "-error";
@endphp

<label class="form-group">
    <span class="form-group__label">{{ $label }}</span>
    <input
        class="form-group__control @error($name) is-invalid @enderror"
        type="{{ $type }}"
        name="{{ $name }}"
        {{-- パスワード以外だけoldで保持 --}}
        @unless ($isPassword)
            value="{{ old($name, $value) }}"
        @endunless
        @if ($auto)
            autocomplete="{{ $auto }}"
        @endif
        @error($name)
            aria-invalid="true"
            aria-describedby="{{ $errorId }}"
        @enderror
        {{ $attributes }}
    />
    @error($name)
        <span id="{{ $errorId }}" class="form-group__error">{{ $message }}</span>
    @enderror
</label>
