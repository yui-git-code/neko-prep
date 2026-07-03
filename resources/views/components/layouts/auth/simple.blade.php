<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <title>猫の飼育準備診断 | {{ request()->is('login') ? 'ログイン' : '新規登録' }}</title>

        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">

                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex items-center justify-center rounded-md">
                        {{-- favicon画像を表示 --}}
                        <img src="{{ asset('favicon.png') }}" alt="ロゴ" class="w-12 h-12 object-contain">
                    </span>
                    {{-- Laravelという文字の代わりに、アプリ名を表示する --}}
                    <span class="text-xl font-bold text-brand-text">猫の飼育準備診断</span>
                </a>

                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
