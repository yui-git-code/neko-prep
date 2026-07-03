<div class="max-w-4xl mx-auto p-6 mt-10">
    <x-page-header title="過去の診断履歴" cat-image="cat-sit.png">
        <p class="opacity-70 leading-relaxed">
            これまでに行った診断の履歴一覧です。<br>
            結果を見返して、準備状況の変化を確認しましょう。
        </p>
    </x-page-header>

    {{-- フィルター・ソート --}}
    {{-- スマホ時は縦並び(flex-col)、PC時は横並び(md:flex-row) --}}
    <div class="flex flex-col md:flex-row gap-3 mb-8">

        {{-- 1. 期間フィルター --}}
        {{-- w-full でスマホ時は横幅いっぱいに、md:w-auto でPC時は元の幅に戻す --}}
        <select wire:model.live="period" class="w-full md:w-auto border border-gray-200 rounded-full px-4 py-2 text-sm bg-white shadow-xs focus:ring-accent focus:border-accent transition">
            <option value="all">すべての期間</option>
            <option value="week">1週間以内</option>
            <option value="month">1ヶ月以内</option>
        </select>

        {{-- 2. 準備度フィルター --}}
        <select wire:model.live="level" class="w-full md:w-auto border border-gray-200 rounded-full px-4 py-2 text-sm bg-white shadow-xs focus:ring-accent focus:border-accent transition">
            <option value="all">すべての準備度</option>
            <option value="level_perfect">準備度: 最高</option>
            <option value="level_high">準備度: 高い</option>
            <option value="level_mid">準備度: やや高い</option>
            <option value="level_low">準備度: 低い</option>
            <option value="level_very_low">準備度: かなり低い</option>
        </select>

        {{-- 3. 並び替え --}}
        {{-- ml-auto を md:ml-auto に変更（PC画面の時だけ右寄せにする） --}}
        <select wire:model.live="sort" class="w-full md:w-auto border border-gray-200 rounded-full px-4 py-2 text-sm bg-white shadow-xs focus:ring-accent focus:border-accent transition md:ml-auto">
            <option value="latest">最新順</option>
            <option value="score_desc">スコア高い順</option>
            <option value="score_asc">スコア低い順</option>
        </select>

    </div>

    {{-- ▼ 履歴一覧 ▼ --}}
    @if($diagnoses->isEmpty())
        {{-- もしデータが1つもなかったら --}}
        <div class="bg-white border border-gray-100 p-10 rounded-2xl shadow-xs text-center flex flex-col items-center">
            <div class="w-16 h-16 bg-brand-orange-light rounded-full flex items-center justify-center mb-4">
                <flux:icon.document-text class="w-8 h-8 text-accent" />
            </div>
            <p class="mb-6 opacity-70">条件に一致する診断履歴がありません。</p>
            <flux:button href="{{ route('quiz') }}" variant="primary" class="rounded-full shadow-sm hover:scale-[1.02] active:scale-[0.98]">
                診断を受けてみる
            </flux:button>
        </div>
    @else
        {{-- データがある場合は、リスト形式で表示する --}}
        <div class="space-y-4">
            @foreach($diagnoses as $diagnosis)
                @php
                    // グラフ描画用の計算
                    $score = $diagnosis->readiness_score;
                    $radius = 30;
                    $circumference = 2 * pi() * $radius;
                    $offset = $circumference - ($score / 100) * $circumference;

                    $strokeClass = match (true) {
                        $score >= 80 => 'stroke-emerald-500',
                        $score >= 50 => 'stroke-amber-500',
                        default      => 'stroke-rose-500',
                    };
                @endphp

                <div class="bg-white p-5 rounded-2xl shadow-xs border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 hover:shadow-sm hover:border-brand-orange-light transition duration-300">
                    
                    {{-- 左側：グラフと基本情報 --}}
                    <div class="flex items-center gap-5 w-full md:w-auto">
                        
                        {{-- ミニ円グラフ --}}
                        <div class="relative w-[70px] h-[70px] shrink-0">
                            <svg class="w-full h-full">
                                {{-- 背景の円（グレー） --}}
                                <circle
                                    cx="35" cy="35" r="{{ $radius }}"
                                    fill="none" stroke="#f3f4f6" stroke-width="6"
                                />
                                {{-- スコアの円（色付き） --}}
                                <circle
                                    cx="35" cy="35" r="{{ $radius }}"
                                    fill="none" class="{{ $strokeClass }}" stroke-width="6"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ $circumference }}"
                                    stroke-dashoffset="{{ $offset }}"
                                    transform="rotate(-90 35 35)"
                                />
                            </svg>
                            {{-- 中央のスコアテキスト --}}
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-xl font-black leading-none">{{ $score }}</span>
                            </div>
                        </div>

                        {{-- 診断日時とレベル --}}
                        <div>
                            <p class="text-sm opacity-60 mb-1">
                                {{ $diagnosis->created_at->format('Y年m月d日 H:i') }}
                            </p>
                            <p class="font-bold text-lg">
                                準備度: {{ $diagnosis->readiness_level }}
                            </p>
                        </div>
                    </div>

                    {{-- 右側：アクションボタン --}}
                    <div class="flex space-x-3 w-full md:w-auto justify-end">
                        {{-- 詳細を見るボタン --}}
                        <a href="{{ route('result', ['id' => $diagnosis->id]) }}" class="px-5 py-2 bg-brand-orange-light text-accent font-bold rounded-full hover:bg-accent hover:text-white transition text-sm text-center shadow-xs">
                            詳細を見る
                        </a>

                        {{-- 削除ボタン --}}
                        <button
                            wire:click="deleteDiagnosis({{ $diagnosis->id }})"
                            wire:confirm="本当にこの診断結果を削除しますか？"
                            class="px-5 py-2 bg-rose-50 text-rose-600 font-bold rounded-full hover:bg-rose-100 transition text-sm text-center shadow-xs"
                        >
                            削除
                        </button>
                    </div>

                </div>
            @endforeach
        </div>
    @endif
</div>