@props(['title' => 'No data', 'description' => ''])
<div class="text-center py-12 px-4">
    <p class="text-slate-600 font-medium">{{ $title }}</p>
    @if($description)
        <p class="text-slate-500 text-sm mt-1">{{ $description }}</p>
    @endif
    {{ $slot }}
</div>
