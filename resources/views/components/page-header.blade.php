@props([
    'title',
    'date' => null,
    'catImage' => null,
])

{{-- 下線 --}}
<div class="mb-6 flex flex-row justify-between items-start border-b border-gray-100 pb-4">
    
    {{-- 左側（テキストエリア） --}}
    <div class="flex-1 pt-1">
        <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-2">
            {{ $title }}
            <flux:icon.heart class="w-7 h-7 text-brand-orange-light" stroke-width="2.5" />
        </h1>
        
        @if($slot->isNotEmpty())
            <div class="mt-3 text-sm opacity-70 leading-relaxed max-w-xl">
                {{ $slot }}
            </div>
        @endif

        @if($date)
            <div class="mt-4">
                <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-orange-light text-accent text-sm font-bold rounded-full shadow-xs">
                    <flux:icon.calendar class="w-4 h-4 stroke-2" />
                    診断日：{{ $date }}
                </span>
            </div>
        @endif
    </div>

    {{-- 右側（画像エリア）: md:block でPCサイズ以上のみ表示 --}}
    @if($catImage)
        <div class="hidden md:block shrink-0 ml-4">
            <img src="{{ asset('images/' . $catImage) }}" alt="イラスト" class="h-32 w-auto object-contain drop-shadow-sm hover:scale-105 transition-transform duration-300">
        </div>
    @endif
</div>