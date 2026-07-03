@php
    $score = $diagnosis->readiness_score;

    // 円グラフの計算
    $radius = 90;
    $circumference = 2 * pi() * $radius;
    $offset = $circumference - ($score / 100) * $circumference;

    // スコアに応じたバッジの色分け
    $badgeColor = match (true) {
        $score >= 80 => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        $score >= 50 => 'bg-amber-50 text-amber-700 border border-amber-200',
        default      => 'bg-rose-50 text-rose-700 border border-rose-200',
    };
@endphp

<div class="max-w-5xl mx-auto p-6 mt-10">

    {{-- ▼ ヘッダー部分 ▼ --}}
    <x-page-header
        title="診断結果"
        :date="$diagnosis->created_at->format('Y年m月d日 H:i')" />

    {{-- ▼ 未ログインユーザーへの案内 ▼ --}}
    @guest
        <div class="bg-blue-50/60 border border-blue-100 p-5 rounded-xl text-center mb-6 shadow-sm">
            <p class="text-blue-800 font-bold text-lg mb-1">この診断結果を保存しませんか？</p>
            <p class="text-sm text-blue-600/90 mb-4">アカウントを作成すると、過去の履歴としていつでも振り返ることができます。</p>
            <div class="flex justify-center gap-3">
                <button wire:click="redirectToRegister" class="px-5 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition shadow-xs">
                    新規登録して保存
                </button>
                <button wire:click="redirectToLogin" class="px-5 py-2 bg-white text-blue-600 font-medium border border-blue-200 rounded-lg hover:bg-blue-50 transition shadow-xs">
                    ログインして保存
                </button>
            </div>
        </div>
    @endguest

    {{-- ▼ 2カラムレイアウト ▼ --}}
    <div class="grid md:grid-cols-2 gap-6 mb-8">

        {{-- =====================================
             左側：準備度スコアカード
        ====================================== --}}
        <div class="bg-white rounded-2xl shadow-xs border border-gray-100 p-8 flex flex-col items-center text-center relative overflow-hidden">
            
            {{-- 前回との比較ミニバッジ --}}
            @if($previousDiagnosis)
                <div class="mb-5 flex items-center justify-center">
                    @if($scoreDifference > 0)
                        <div class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-emerald-50 text-emerald-600 font-bold text-sm rounded-full border border-emerald-100 shadow-xs">
                            <flux:icon.arrow-trending-up class="w-4 h-4 stroke-2" />
                            <span>前回より <span class="text-emerald-700 text-base">+{{ $scoreDifference }}</span> 点アップ！</span>
                        </div>
                    @elseif($scoreDifference < 0)
                        <div class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-rose-50 text-rose-600 font-bold text-sm rounded-full border border-rose-100 shadow-xs">
                            <flux:icon.arrow-trending-down class="w-4 h-4 stroke-2" />
                            <span>前回から <span class="text-rose-700 text-base">{{ $scoreDifference }}</span> 点</span>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-slate-50 text-slate-500 font-medium text-sm rounded-full border border-slate-200 shadow-xs">
                            <flux:icon.minus class="w-4 h-4 stroke-2" />
                            <span>前回と同じスコアです</span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- 円グラフ --}}
            <div class="relative w-[240px] h-[240px] mb-6">
                <svg class="w-full h-full">
                    <circle cx="120" cy="120" r="{{ $radius }}" fill="none" stroke="#f3f4f6" stroke-width="16" />
                    <circle cx="120" cy="120" r="{{ $radius }}" fill="none" class="stroke-accent" stroke-width="16" stroke-linecap="round" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}" transform="rotate(-90 120 120)" />
                </svg>

                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <p class="text-sm font-bold opacity-70 mb-1">準備度スコア</p>
                    <div class="flex items-baseline">
                        <span class="text-6xl font-black">{{ $score }}</span>
                        <span class="text-2xl font-bold opacity-60 ml-1">点</span>
                    </div>
                    <p class="text-sm text-gray-400 mt-0.5">/100</p>
                </div>
            </div>

            {{-- 評価ラベル --}}
            <div class="inline-flex items-center gap-2 px-6 py-2 rounded-full {{ $badgeColor }} font-bold mb-5 shadow-xs">
                @if($score >= 80)
                    <flux:icon.face-smile class="w-5 h-5" />
                @elseif($score >= 50)
                    <flux:icon.exclamation-circle class="w-5 h-5" />
                @else
                    <flux:icon.exclamation-triangle class="w-5 h-5" />
                @endif
                <span>{{ $diagnosis->readiness_level }}</span>
            </div>

            {{-- コメント --}}
            <p class="opacity-80 text-sm leading-relaxed max-w-xs">
                {!! nl2br(e($diagnosis->overall_comment)) !!}
            </p>
        </div>

        {{-- =====================================
             右側：全ユーザーとの比較カード
        ====================================== --}}
        <div class="bg-white rounded-2xl shadow-xs border border-gray-100 p-8 relative overflow-hidden flex flex-col justify-between">

            @if($totalUsersCount <= 1)
                <div class="flex-1 flex flex-col items-center justify-center text-gray-400">
                    <flux:icon.chart-bar class="w-12 h-12 mb-3 opacity-50" />
                    <p class="text-center text-sm leading-relaxed">
                        データが蓄積されると、<br>他のユーザーとの比較が表示されます。
                    </p>
                </div>
            @else
                @php
                    if ($percentile_rank < 10) {
                        $displayRank = $percentile_rank;
                    } else {
                        $displayRank = min(100, ceil($percentile_rank / 10) * 10);
                    }
                @endphp

                <div class="z-10">
                    <div class="flex items-center gap-2 mb-6">
                        <flux:icon.sparkles class="w-6 h-6 text-accent" />
                        <h3 class="font-bold opacity-90">全ユーザーとの比較</h3>
                    </div>

                    <div class="flex items-baseline gap-1 mb-4">
                        @if($percentile_rank <= 30)
                            {{-- 上位30%以内 --}}
                            <span class="text-xl font-bold opacity-70">上位</span>
                            <span class="text-6xl font-black text-accent leading-none">{{ $displayRank }}</span>
                            <span class="text-2xl font-bold opacity-70">%</span>
                        @elseif($score >= $averageScore)
                            {{-- 上位30%ではないが、平均以上 --}}
                            <span class="text-3xl font-black text-accent">平均以上！</span>
                        @else
                            {{-- 平均未満 --}}
                            <span class="text-3xl font-black text-accent">準備をチェック</span>
                        @endif
                    </div>

                    <p class="text-sm opacity-80 leading-relaxed max-w-[65%]">
                        @if($percentile_rank <= 10)
                            素晴らしい準備状況です！<br>全体の中でもトップクラスです。
                        @elseif($percentile_rank <= 30)
                            あなたのスコアは、全体の上位{{ $displayRank }}%に入っています。<br>安心できる準備状態です！
                        @elseif($score >= $averageScore)
                            全体の平均スコア（{{ $averageScore }}点）を上回っています。<br>この調子で準備を進めましょう！
                        @else
                            全体の平均スコアは{{ $averageScore }}点です。<br>もう一度環境を見直してみましょう。
                        @endif
                    </p>
                </div>

                {{-- 棒グラフと猫の画像 --}}
                <div class="flex items-end justify-between mt-8 relative h-32">
                    
                    {{-- 左下：順位で色が変わる棒グラフ --}}
                    <div class="flex items-end gap-2 h-full pb-2">
                        @php
                            $rankIndex = min(6, floor(($percentile_rank ?? 1) / 15));
                            $barHeights = ['h-full', 'h-4/5', 'h-3/4', 'h-2/3', 'h-1/2', 'h-1/3', 'h-1/4'];
                        @endphp

                        @foreach($barHeights as $index => $height)
                            <div class="w-4 {{ $height }} {{ $index == $rankIndex ? 'bg-accent' : 'bg-brand-orange-light' }} rounded-t-xs transition-colors duration-500"></div>
                        @endforeach
                    </div>

                    {{-- 右下：猫の画像配置スペース --}}
                    <div class="absolute bottom-0 right-0 w-48 -mr-6 -mb-6">
                        <img src="{{ asset('images/cat-placeholder.png') }}" alt="" class="w-full h-auto object-contain drop-shadow-xs">
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- カテゴリ評価 --}}
    <div class="grid gap-6 md:grid-cols-3 mb-6">

        {{-- 住環境 --}}
        <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-5">
            <div class="flex items-start gap-4 mb-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-brand-orange-light shrink-0">
                    <flux:icon.home class="w-8 h-8 text-accent" />
                </div>
                <div>
                    <h3 class="font-bold text-base">住環境</h3>
                    <p class="text-xs opacity-60">猫が快適に暮らせる環境かを評価</p>
                </div>
            </div>
            <ul class="space-y-2 text-sm opacity-80">
                @foreach($categoryComments['environment'] as $comment)
                    <li>・{{ $comment }}</li>
                @endforeach
            </ul>
        </div>

        {{-- 時間 --}}
        <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-5">
            <div class="flex items-start gap-4 mb-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-brand-orange-light shrink-0">
                    <flux:icon.clock class="w-8 h-8 text-accent" />
                </div>
                <div>
                    <h3 class="font-bold text-base">時間</h3>
                    <p class="text-xs opacity-60">日々のお世話や留守時間の状況</p>
                </div>
            </div>
            <ul class="space-y-2 text-sm opacity-80">
                @foreach($categoryComments['time'] as $comment)
                    <li>・{{ $comment }}</li>
                @endforeach
            </ul>
        </div>

        {{-- 費用 --}}
        <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-5">
            <div class="flex items-start gap-4 mb-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-brand-orange-light shrink-0">
                    <flux:icon.wallet class="w-8 h-8 text-accent" />
                </div>
                <div>
                    <h3 class="font-bold text-base">費用</h3>
                    <p class="text-xs opacity-60">初期費用・継続費用の準備状況</p>
                </div>
            </div>
            <ul class="space-y-2 text-sm opacity-80">
                @foreach($categoryComments['cost'] as $comment)
                    <li>・{{ $comment }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- 回答内容 --}}
    <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-6 mb-6">
        <h3 class="font-bold text-lg mb-4 border-b border-gray-100 pb-2">あなたの回答</h3>
        <div class="space-y-3 text-sm opacity-90">
            <div class="flex justify-between border-b border-gray-50 pb-1.5">
                <span class="opacity-70">住環境</span>
                <span class="font-medium">{{ $diagnosis->housing_type }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-50 pb-1.5">
                <span class="opacity-70">留守時間</span>
                <span class="font-medium">{{ $diagnosis->absence_hours }} 時間</span>
            </div>
            <div class="flex justify-between border-b border-gray-50 pb-1.5">
                <span class="opacity-70">飼育予定数</span>
                <span class="font-medium">{{ $diagnosis->planned_cat_count }} 匹</span>
            </div>
            <div class="flex justify-between border-b border-gray-50 pb-1.5">
                <span class="opacity-70">月予算</span>
                <span class="font-medium">{{ number_format($diagnosis->monthly_budget) }} 円</span>
            </div>
            <div class="flex justify-between border-b border-gray-50 pb-1.5">
                <span class="opacity-70">通院対応</span>
                <span class="font-medium">{{ $diagnosis->can_visit_hospital ? 'はい' : 'いいえ' }}</span>
            </div>
            <div class="flex justify-between pb-0.5">
                <span class="opacity-70">介護対応</span>
                <span class="font-medium">{{ $diagnosis->accepts_special_care ? 'はい' : 'いいえ' }}</span>
            </div>
        </div>
    </div>

    {{-- ボタン --}}
    <div class="text-center mb-10">
        <a href="{{ route('quiz') }}" class="inline-flex items-center px-8 py-3 bg-accent text-accent-foreground font-bold rounded-full hover:bg-accent-content transition shadow-sm hover:scale-[1.02] active:scale-[0.98]">
            もう一度診断する
        </a>
    </div>

</div>