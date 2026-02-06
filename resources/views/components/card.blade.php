@props(['class' => ''])
<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden ' . $class]) }}>
    {{ $slot }}
</div>
