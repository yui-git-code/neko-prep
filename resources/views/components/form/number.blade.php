@props([
    'label',
    'model',
    'placeholder' => '',
    'unit' => '',
    'step' => 1,
])

{{--
  |--------------------------------------------------------------------------
  | 数値入力型 質問コンポーネント (number.blade.php)
  |--------------------------------------------------------------------------
  | 留守時間や月の予算など、数値を直接入力させるためのフォーム部品です。
  | 外から「単位（時間、円など）」や「ステップ幅（1000円単位など）」を
  | 柔軟に受け取れるように設計されています。
--}}
<div
    {{-- ▼ Alpine.js によるスライドイン・アニメーション ▼ --}}
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, 100)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 -translate-x-8"
    x-transition:enter-end="opacity-100 translate-x-0"

    {{-- カード全体のデザイン（枠線を優しく、ホバーで少し浮き上がる） --}}
    class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition duration-300 mb-6"
>

    {{-- ▼ 上部のアイコンエリア ▼ --}}
    <div class="flex flex-col items-center mb-5">
        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-brand-orange-light/50">
            {{-- 変更: テーマカラーのアクセントを適用 --}}
            <flux:icon.heart class="w-8 h-8 text-accent" stroke-width="2" />
        </div>
    </div>

    {{-- ▼ 質問テキスト ▼ --}}
    <h3 class="font-bold text-lg text-center mb-5">
        {{ $label }}
    </h3>

    {{-- ▼ 入力エリア（入力欄 ＋ 単位） ▼ --}}
    <div class="flex items-center justify-center gap-3">

        {{-- 
          | 数値入力フィールド
          | wire:model.live により、キーボードで入力するたびに即座に値が同期されます。
        --}}
        <input
            type="number"
            wire:model.live="{{ $model }}"
            placeholder="{{ $placeholder }}"
            step="{{ $step }}"
            {{-- 変更: フォーカス時の枠線とシャドウをテーマカラーに変更 --}}
            class="border border-gray-200 rounded-xl px-4 py-3 w-40 text-center text-lg font-medium shadow-sm transition
                   focus:ring-2 focus:ring-brand-orange-light focus:border-accent focus:outline-none"
        >

        {{-- 単位（「時間」や「円」など）が設定されている場合のみ表示 --}}
        @if($unit)
            <span class="font-medium opacity-70">
                {{ $unit }}
            </span>
        @endif

    </div>

    {{-- ▼ バリデーションエラーメッセージ ▼ --}}
    {{-- 次へ進む際に未入力やマイナス値などのエラーがあった場合の警告表示 --}}
    @error($model)
        <p class="text-rose-500 text-sm font-bold mt-3 text-center">
            {{ $message }}
        </p>
    @enderror

</div>