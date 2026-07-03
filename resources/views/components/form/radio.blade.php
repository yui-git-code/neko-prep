@props([
    'label',
    'model',
    'options' => [],
])

{{--
  |--------------------------------------------------------------------------
  | ラジオボタン型 質問コンポーネント
  |--------------------------------------------------------------------------
  | 診断の各ステップで表示される「はい・いいえ」などの選択式質問フォームです。
  | Alpine.js（x-data, x-transition）を利用して、画面表示時に
  | 左からフワッとスライドインする心地よいアニメーションを実装しています。
--}}
<div
    {{-- ▼ Alpine.js によるアニメーション設定 ▼ --}}
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, 100)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 -translate-x-8"
    x-transition:enter-end="opacity-100 translate-x-0"

    {{-- カード全体のデザイン（アプリのテーマに合わせて枠線を優しく調整） --}}
    class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition duration-300 mb-6"
>

    {{-- ▼ 上部のアイコンエリア ▼ --}}
    <div class="flex flex-col items-center mb-5">
        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-brand-orange-light/50">
            <flux:icon.heart class="w-8 h-8 text-accent" stroke-width="2" />
        </div>
    </div>

    {{-- ▼ 質問テキスト ▼ --}}
    <h3 class="font-bold text-lg text-center mb-5">
        {{ $label }}
    </h3>

    {{-- ▼ 選択肢（ラジオボタン）一覧 ▼ --}}
    <div class="space-y-3">
        @foreach ($options as $option)
            {{--
              | 選択肢のラベル
              | 全体をクリック可能（cursor-pointer）にし、ホバー時にテーマカラーの薄いオレンジになるように設定
            --}}
            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-brand-orange-light/30 hover:border-accent transition duration-200">
                
                {{--
                  | ラジオボタン本体
                  | wire:model.live を使うことで、ユーザーが選択した瞬間に
                  | コンポーネント（Quiz.php）の該当変数に値が即座に同期されます。
                --}}
                <input
                    type="radio"
                    value="{{ $option }}"
                    wire:model.live="{{ $model }}"
                    class="text-accent focus:ring-accent"
                >
                
                <span class="font-medium">{{ $option }}</span>
            </label>
        @endforeach
    </div>

    {{-- ▼ バリデーションエラーメッセージ ▼ --}}
    {{-- 次へ進む際に未入力（必須エラーなど）があった場合の警告表示 --}}
    @error($model)
        <p class="text-rose-500 text-sm font-bold mt-3 text-center">
            {{ $message }}
        </p>
    @enderror

</div>