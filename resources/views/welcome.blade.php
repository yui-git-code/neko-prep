<x-layouts.app>
    
    {{-- ▼ ヒーローセクション（角丸カードデザイン） ▼ --}}
    <div class="max-w-6xl mx-auto px-4 py-10 md:py-16">
        <div class="relative bg-[#FDFBF8] rounded-[2rem] overflow-hidden shadow-sm border border-brand-orange-light/50 min-h-[400px] flex items-center">
            
            {{-- 背景画像 --}}
            <div class="absolute inset-0 flex justify-end">
                <img src="{{ asset('images/top_img.jpg') }}" alt="可愛い猫" class="h-full w-full object-cover object-right">
                
            </div>


            {{-- テキスト＆ボタンコンテンツ --}}
            <div class="relative z-10 px-8 py-12 md:px-16 md:py-20 max-w-2xl w-full">
                <h1 class="text-3xl md:text-[2.75rem] font-black leading-snug mb-6">
                    猫を迎える前に、<br>
                    <span class="text-accent">本当に準備できているか</span><br>
                    確認しよう
                </h1>
                
                <p class="opacity-80 mb-8 leading-loose text-sm md:text-base font-medium max-w-lg">
                    この診断では、費用・時間・生活環境などをもとに、<br class="hidden md:block">
                    あなたの「猫の飼育準備度」を可視化します。
                </p>

                <div class="flex flex-col items-start gap-4">
                    {{-- 診断ボタン --}}
                    <a href="{{ route('quiz') }}" class="inline-flex items-center justify-center px-8 py-3.5 md:px-10 md:py-4 bg-accent text-accent-foreground font-bold rounded-xl shadow-md hover:scale-[1.02] active:scale-[0.98] hover:bg-accent-content transition duration-300">
                        <flux:icon.play class="w-4 h-4 mr-1.5" stroke-width="2" />
                        診断を始める
                    </a>
                    
                    {{-- 登録不要 --}}
                    <div class="flex items-center text-xs md:text-sm opacity-70 font-medium ml-1">
                        <flux:icon.shield-check class="w-4 h-4 mr-1.5" stroke-width="2" />
                        登録不要・約3〜5分で完了
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ▼ この診断でわかることセクション ▼ --}}
    <div class="bg-white py-16">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold">この診断でわかること</h2>
                <div class="w-12 h-1 bg-accent mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- カード 1 --}}
                <div class="bg-brand-orange-light/50 rounded-2xl p-8 text-center border border-brand-orange-light shadow-xs">
                    <div class="w-16 h-16 mx-auto bg-white text-accent rounded-full flex items-center justify-center mb-4 shadow-sm">
                        <flux:icon.wallet class="w-8 h-8" stroke-width="2" />
                    </div>
                    <h3 class="font-bold text-lg mb-3">費用を考慮</h3>
                    <p class="opacity-70 text-sm leading-relaxed">初期費用から毎月の維持費まで、猫を飼うために必要な費用を把握できます。</p>
                </div>
                
                {{-- カード 2 --}}
                <div class="bg-brand-orange-light/50 rounded-2xl p-8 text-center border border-brand-orange-light shadow-xs">
                    <div class="w-16 h-16 mx-auto bg-white text-accent rounded-full flex items-center justify-center mb-4 shadow-sm">
                        <flux:icon.clock class="w-8 h-8" stroke-width="2" />
                    </div>
                    <h3 class="font-bold text-lg mb-3">時間を考慮</h3>
                    <p class="opacity-70 text-sm leading-relaxed">毎日の世話や遊びの時間など、猫との生活に必要な時間を確認できます。</p>
                </div>
                
                {{-- カード 3 --}}
                <div class="bg-brand-orange-light/50 rounded-2xl p-8 text-center border border-brand-orange-light shadow-xs">
                    <div class="w-16 h-16 mx-auto bg-white text-accent rounded-full flex items-center justify-center mb-4 shadow-sm">
                        <flux:icon.home class="w-8 h-8" stroke-width="2" />
                    </div>
                    <h3 class="font-bold text-lg mb-3">将来の変化を考慮</h3>
                    <p class="opacity-70 text-sm leading-relaxed">引っ越し・結婚・出産など、将来のライフイベントも考慮します。</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>