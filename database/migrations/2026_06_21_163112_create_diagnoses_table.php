<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * diagnoses（診断履歴）テーブルを作成するマイグレーション
 *
 * ユーザーが回答した診断の入力内容と、算出したスコア・評価レベルなどを保存します。
 * 未ログイン（ゲスト）ユーザーのデータも保存できるよう、user_id は nullable に設定しています。
 */


return new class extends Migration
{
    /**
     * マイグレーション実行時の処理（テーブルの作成）
     */
    public function up(): void
    {
        Schema::create('diagnoses', function (Blueprint $table) {
            // ▼ 主キー
            $table->id(); // 診断ID（自動採番）

            /*
            |--------------------------------------------------------------------------
            | リレーション（外部キー制約）
            |--------------------------------------------------------------------------
            */
            // ユーザーID。未ログイン時はnullを許容します。
            // constrained() で users テーブルと紐づけ、
            // cascadeOnDelete() でユーザーが退会・削除された際、関連する診断履歴も自動で一括削除します。
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            /*
            |--------------------------------------------------------------------------
            | ユーザー入力項目
            |--------------------------------------------------------------------------
            */
            $table->string('housing_type');         // 住環境（例: 'ペット可', 'ペット不可'）
            $table->integer('absence_hours');       // 1日の平均留守時間（時間）
            $table->integer('planned_cat_count');   // 飼育予定頭数
            $table->integer('monthly_budget');      // 月予算
            $table->boolean('can_visit_hospital');  // 動物病院へ連れて行ける環境か（true/false）
            $table->boolean('accepts_special_care');// 将来の特別なケアや介護を受け入れられるか（true/false）


            /*
            |--------------------------------------------------------------------------
            | 診断結果（計算ロジックによる出力）
            |--------------------------------------------------------------------------
            | ※保存処理の途中でエラーが起きてもデータが欠損しないよう、念のため nullable を付与
            */
            $table->integer('readiness_score')->nullable();       // 準備度スコア（0〜100点）
            $table->string('readiness_level')->nullable();        // 準備度ランク（例: '最高', 'やや高い'）
            $table->text('overall_comment')->nullable();          // 総合コメント（改行を含む長文テキスト）
            // 全体順位（上位○%） リアルタイムで計算するためコメントアウト
            // $table->decimal('percentile_rank', 5, 2)->nullable();

            /*
            |--------------------------------------------------------------------------
            | システム日時
            |--------------------------------------------------------------------------
            */
            // タイムゾーン付きの作成日時（created_at）と更新日時（updated_at）を自動生成
            $table->timestampsTz(); // 作成日時・更新日時
        });
    }

    /**
     * マイグレーションを元に戻す時の処理（ロールバック）
     */
    public function down(): void
    {
        // diagnosesテーブルが存在すれば削除する
        Schema::dropIfExists('diagnoses');
    }
};
