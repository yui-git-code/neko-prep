<div class="max-w-4xl mx-auto p-6 mt-10">
    <x-page-header title="猫の飼育準備診断" cat-image="cat-play.png">
        <p class="text-brand-text/70 leading-relaxed">
            いくつかの質問に回答することで、<br>
            猫を迎える準備ができているかを診断します。
        </p>
    </x-page-header>

    <div class="max-w-2xl mx-auto p-8 bg-white shadow-xs border border-gray-100 rounded-2xl mt-10 mb-6">
        
        {{-- 進捗 --}}
        <div class="mb-8">
            <div class="flex justify-between text-sm font-bold text-brand-text/60 mb-3">
                <span>回答状況</span>
                <span>{{ $current + 1 }} / {{ count($questions) }} 問</span>
            </div>

            <div class="w-full bg-brand-orange-light rounded-full h-3 overflow-hidden">
                <div
                    class="bg-accent h-3 rounded-full transition-all duration-500 ease-out"
                    style="width: {{ (($current + 1) / count($questions)) * 100 }}%;"
                ></div>
            </div>
        </div>

        <form wire:submit.prevent="calculate" class="space-y-8">

            {{-- 質問を1つだけ表示 --}}
            @php
                $q = $questions[$current];
            @endphp

            {{-- keyを設定して、質問が変わるたびに要素を新しく作り直させる --}}
            <div wire:key="question-{{ $q['model'] }}" class="text-brand-text font-medium">
                @if($q['type'] === 'radio')
                    <x-form.radio
                        :label="$q['label']"
                        :model="$q['model']"
                        :options="$q['options']"
                    />
                @elseif($q['type'] === 'number')
                    <x-form.number
                        :label="$q['label']"
                        :model="$q['model']"
                        :placeholder="$q['placeholder']"
                        :unit="$q['unit'] ?? ''"
                        :step="$q['step'] ?? 1"
                    />
                @endif
            </div>

            <div class="flex justify-between pt-6 border-t border-gray-100">

                @if($current > 0)
                    {{-- 戻るボタン --}}
                    <button type="button"
                        wire:click="current--"
                        class="px-6 py-2.5 bg-slate-50 text-slate-600 font-bold rounded-full border border-slate-200 hover:bg-slate-100 transition">
                        戻る
                    </button>
                @else
                    {{-- 1問目の時は空のdivを置いて右寄せを維持する --}}
                    <div></div>
                @endif

                @if($current < count($questions) - 1)
                    {{-- 次へボタン --}}
                    <button
                        type="button"
                        wire:click="next"
                        @if(!$this->canGoNext()) disabled @endif
                        class="px-8 py-2.5 font-bold rounded-full transition shadow-sm
                            {{ $this->canGoNext() ? 'bg-accent text-accent-foreground hover:bg-accent-content hover:scale-[1.02] active:scale-[0.98]' : 'bg-gray-100 text-gray-400 cursor-not-allowed shadow-none' }}"
                    >
                        次へ
                    </button>
                @else
                    {{-- 回答済み（canGoNext()がtrue）の時だけ表示 --}}
                    @if($this->canGoNext())
                        {{-- 診断結果を見るボタン --}}
                        <button type="submit"
                            class="px-8 py-2.5 bg-accent text-accent-foreground font-bold rounded-full hover:bg-accent-content transition shadow-sm hover:scale-[1.02] active:scale-[0.98]">
                            診断結果を見る
                        </button>
                    @endif
                @endif

            </div>

        </form>
    </div>
</div>