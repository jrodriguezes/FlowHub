@props([
    'type' => 'date',
    'name',
    'id' => null,
    'value' => '',
])

<div class="date-input-wrap relative">
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        value="{{ $value }}"
        {{ $attributes->class([
            'date-input-dark w-full bg-[#0B0F19] border border-white/10 rounded-lg py-2 px-3 text-sm text-gray-200',
        ]) }}
    />
</div>
