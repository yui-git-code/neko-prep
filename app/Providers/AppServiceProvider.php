<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use App\Models\Diagnosis;

/**
 * アプリケーション全体のサービスプロバイダー
 *
 * アプリ起動時のブート処理や、イベントリスナーの登録などを行います。
 */

class AppServiceProvider extends ServiceProvider
{
    /**
     * サービスコンテナへのバインディング登録
     */
    public function register(): void
    {
        //
    }

    /**
     * アプリケーションの全サービスの初期化（ブート）処理
     */
    public function boot(): void
    {
        // ゲスト（未ログイン）状態で診断したデータを、ログイン/登録後のユーザーIDと紐付ける処理
        $linkDiagnosis = function ($event) {
            // セッションに「紐付け待ちの診断ID」が保存されているか確認
            $pendingId = session('pending_diagnosis_id');

            if ($pendingId) {
                $diagnosis = Diagnosis::find($pendingId);

                // 診断データが存在し、かつ現在まだ誰のアカウントにも紐づいていない（user_idがnull）場合
                if ($diagnosis && $diagnosis->user_id === null) {
                    // ログインしたユーザーのIDを診断データにセットして保存
                    $diagnosis->user_id = $event->user->id;
                    $diagnosis->save();
                }

                // 紐付け処理が完了したので、セッションからIDを削除
                session()->forget('pending_diagnosis_id');
            }
        };

        // 「新規登録時」または「ログイン成功時」に、上記の紐付け処理を実行するようにイベントを登録
        Event::listen(Registered::class, $linkDiagnosis);
        Event::listen(Login::class, $linkDiagnosis);
    }
}