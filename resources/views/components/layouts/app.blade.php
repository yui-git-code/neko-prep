<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? '猫の飼育準備診断' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Googleフォント --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;500;700;900&display=swap" rel="stylesheet">
    {{-- favicon --}}
    <link rel="icon" href="{{ asset('favicon.png') }}">
    
    {{-- ▼▼ 追加：全ページでLivewire/Alpineを有効にするためのスタイル ▼▼ --}}
    @livewireStyles
    <style>
        /* x-cloak属性がついている要素は、Alpineが読み込まれるまで強制的に非表示にする */
        [x-cloak] { display: none !important; }
    </style>
    
</head>
{{-- 全体の背景と文字色をブランドカラー指定 --}}
<body class="bg-brand-bg text-brand-text font-sans antialiased flex flex-col min-h-screen">

    {{-- ヘッダー（Alpine.jsの x-data で開閉状態を管理） --}}
    <header x-data="{ mobileMenuOpen: false }" class="bg-white shadow-xs border-b border-gray-100 relative z-50">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between relative">
            <div class="font-bold text-xl text-brand-text flex items-center">
                <a href="/" class="flex items-center hover:opacity-80 transition gap-2">
                    <span class="text-2xl drop-shadow-xs">🐾</span>
                    <span>猫の飼育準備診断</span>
                </a>
            </div>
            
            {{-- ▼▼ PC用メニュー (md以上で表示) ▼▼ --}}
            <nav class="hidden md:flex items-center space-x-2">
                @auth
                    {{-- ログイン時 --}}
                    <flux:button href="{{ route('history') }}" variant="ghost" icon="document-text">
                        診断履歴
                    </flux:button>
                    
                    {{-- ユーザー名表示 --}}
                    <div class="flex items-center gap-1.5 px-3 py-1.5 mx-2 bg-brand-orange-light text-accent rounded-full text-sm font-bold shadow-xs">
                        <flux:icon.user class="w-4 h-4 stroke-2" />
                        <span>{{ auth()->user()->name }} さん</span>
                    </div>

                    {{-- ログアウトボタン --}}
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <flux:button type="submit" variant="ghost" icon="arrow-right-start-on-rectangle">
                            ログアウト
                        </flux:button>
                    </form>
                @endauth

                @guest
                    {{-- 未ログイン時 --}}
                    <flux:button href="{{ route('login') }}" variant="ghost" icon="arrow-right-end-on-rectangle">
                        ログイン
                    </flux:button>

                    <flux:button href="{{ route('register') }}" variant="outline" class="rounded-full">
                        新規登録
                    </flux:button>
                @endguest

                {{-- 診断を始めるボタン --}}
                <div class="pl-2">
                    <flux:button href="{{ route('quiz') }}" variant="primary" icon="play" class="rounded-full shadow-sm">
                        診断を始める
                    </flux:button>
                </div>
            </nav>

            {{-- ▼▼ スマホ用 ハンバーガーボタン (md未満で表示) ▼▼ --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden p-2 text-gray-500 hover:bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition" aria-controls="mobile-menu" aria-expanded="false">
                <span class="sr-only">メニューを開く</span>
                {{-- 開いていない時（三本線） --}}
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                {{-- 開いている時（バツ印） --}}
                <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- ▼▼ スマホ用 ドロップダウンメニュー ▼▼ --}}
        {{-- x-transition でアニメーションを付けて開閉 --}}
        <div x-show="mobileMenuOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden absolute top-16 left-0 w-full bg-white border-b border-gray-100 shadow-lg px-4 py-4 z-40" 
             id="mobile-menu">
            <div class="flex flex-col space-y-4">
                
                @auth
                    {{-- ユーザー名表示（スマホ） --}}
                    <div class="flex items-center gap-2 pb-3 border-b border-gray-100 text-brand-text font-bold">
                        <div class="bg-brand-orange-light p-2 rounded-full text-accent">
                            <flux:icon.user class="w-5 h-5 stroke-2" />
                        </div>
                        <span>{{ auth()->user()->name }} さん</span>
                    </div>

                    {{-- ログイン時メニュー --}}
                    <a href="{{ route('history') }}" class="flex items-center gap-3 px-3 py-2 text-brand-text hover:bg-gray-50 rounded-lg transition">
                        <flux:icon.document-text class="w-5 h-5 text-gray-500" />
                        <span class="font-medium">診断履歴</span>
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="m-0 w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-brand-text hover:bg-gray-50 rounded-lg transition text-left">
                            <flux:icon.arrow-right-start-on-rectangle class="w-5 h-5 text-gray-500" />
                            <span class="font-medium">ログアウト</span>
                        </button>
                    </form>
                @endauth

                @guest
                    {{-- 未ログイン時メニュー --}}
                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-3 py-2 text-brand-text hover:bg-gray-50 rounded-lg transition">
                        <flux:icon.arrow-right-end-on-rectangle class="w-5 h-5 text-gray-500" />
                        <span class="font-medium">ログイン</span>
                    </a>
                    <a href="{{ route('register') }}" class="flex items-center gap-3 px-3 py-2 text-brand-text hover:bg-gray-50 rounded-lg transition">
                        <flux:icon.user-plus class="w-5 h-5 text-gray-500" />
                        <span class="font-medium">新規登録</span>
                    </a>
                @endguest

                {{-- 診断を始めるボタン（スマホではフル幅で目立たせる） --}}
                <div class="pt-2">
                    <a href="{{ route('quiz') }}" class="flex items-center justify-center gap-2 w-full py-3 bg-accent text-white font-bold rounded-xl shadow-sm hover:opacity-90 transition">
                        <flux:icon.play class="w-5 h-5" />
                        診断を始める
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- メインコンテンツ --}}
    <main class="flex-grow">
        {{ $slot }}
    </main>

    {{-- フッター --}}
    <footer class="bg-white py-8 border-t border-gray-100 mt-auto">
        <div class="max-w-6xl mx-auto px-4 text-center text-brand-text/60 text-sm">
            <p>&copy; {{ date('Y') }} 猫の飼育準備診断</p>
        </div>
    </footer>

    {{-- ▼▼ 追加：閉じbodyタグの直前でスクリプトを読み込む ▼▼ --}}
    @livewireScripts
</body>
</html>