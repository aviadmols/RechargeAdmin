@props(['variant' => 'default'])
@php
$classes = match($variant) {
    'success' => 'bg-green-100 text-green-800',
    'warning' => 'bg-amber-100 text-amber-800',
    'error' => 'bg-red-100 text-red-800',
    default => 'bg-slate-100 text-slate-800',
};
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ' . $classes]) }}>
    {{ $slot }}
</span>
