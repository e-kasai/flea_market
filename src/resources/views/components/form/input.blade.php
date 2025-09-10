@props([
    "name",
    "label",
    "type" => "text",
    "value" => null,
    "autocomplete" => null,
])

@php
    $isPassword = $type === "password";
    // 追記1
    $isTextarea = $type === "textarea";
    // パスワードは off、それ以外は指定があればその値、なければ null
    $auto = $isPassword ? "off" : $autocomplete;

    // id は渡されていればそれ、無ければ name を使う
    $id = $attributes->get("id") ?? $name;
    // エラー用ID
    $errorId = $id . "-error";
    // 追記2
    $oldValue = old($name, $value);
@endphp

<label class="form-group">
    <span class="form-group__label">{{ $label }}</span>
    {{-- 追記3:ifブロック --}}
    @if ($isTextarea)
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            class="form-group__control @error($name) is-invalid @enderror"
            @if($auto) autocomplete="{{ $auto }}" @endif
            @error($name)
                aria-invalid="true"
                aria-describedby="{{ $errorId }}"
            @enderror
            {{ $attributes }}
        >
{{ $oldValue }}</textarea
        >
    @else
        <input
            class="form-group__control @error($name) is-invalid @enderror"
            {{-- 追記４：id --}}
            id="{{ $id }}"
            type="{{ $type }}"
            name="{{ $name }}"
            {{-- パスワード以外だけoldで保持 --}}
            @unless ($isPassword)
                value="{{ old($name, $value) }}"
            @endunless
            @if ($auto)
                autocomplete="{{ $auto }}"
            @endif
            {{-- BczsGE0mW4tsTxAaBRNGWHNF1aVGZNONwNbqsXC0rB8rCDE7Vu0WSrdzs9YfkjYlB --}}
            {{ $attributes }}
        />
    @endif
    @error($name)
        <span id="{{ $errorId }}" class="form-group__error">{{ $message }}</span>
    @enderror
</label>
