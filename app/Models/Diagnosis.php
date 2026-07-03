<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 診断結果（Diagnosis）モデル
 *
 * ユーザーが実行した「猫の飼育準備診断」の結果データを、
 * データベース（diagnosesテーブル）とやり取りするためのクラスです。
 */

class Diagnosis extends Model
{
    use HasFactory;

    /**
     * 複数代入（Mass Assignment）を許可する属性（カラム）のリスト
     *
     * ※セキュリティ対策：ここに指定したカラムのみ、create()やupdate()メソッドで
     * 配列を使って一括保存できるようになります。（意図しないカラムの書き換えを防ぐため）
     *
     */
    protected $fillable = [
        'user_id', // ユーザーID（未ログインのゲスト時はnull）
        'housing_type', // 住環境（例: ペット可）
        'absence_hours', // 1日の留守時間
        'planned_cat_count', // 飼育予定の猫の数
        'monthly_budget', // 月の予算
        'can_visit_hospital', // 動物病院へ連れて行けるか
        'accepts_special_care', // 介護などを受け入れられるか
        'readiness_score', // 算出された準備度スコア（0〜100）
        'readiness_level', // スコアに基づくレベル（最高、高いなど）
        'overall_comment', // 診断の総評（サマリーテキスト）
        'percentile_rank', // 全体の上位何％か
    ];

    /**
     * 属性の型変換（キャスト）設定
     *
     * データベースからデータを取り出す際、または保存する際に、
     * 自動的に指定したデータ型（数値や論理値など）に変換してくれます。
     *
     */
    protected $casts = [
        'absence_hours'        => 'integer', // 確実に数値（整数）として扱う
        'planned_cat_count'    => 'integer',
        'monthly_budget'       => 'integer',
        'can_visit_hospital'   => 'boolean', // 0/1を自動的に true/false に変換
        'accepts_special_care' => 'boolean', // 0/1を自動的に true/false に変換
        'readiness_score'      => 'integer',
    ];

    /**
     * Userモデルとのリレーション（多対1）
     *
     * 1つの診断結果は、1人のユーザー（またはゲスト）に属します。
     */
    public function user()
    {
        // 使い方: $diagnosis->user->name で紐づくユーザー名を取得できます
        return $this->belongsTo(User::class);
    }

}
