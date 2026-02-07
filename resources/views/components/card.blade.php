@props(['class' => ''])
<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm border border-violet-100/80 overflow-hidden ' . $class]) }}>
    {{ $slot }}
</div>
