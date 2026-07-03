<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Diagnosis;

/**
 * 診断結果（Diagnosis）モデルに対する操作権限を管理するポリシー
 *
 * ログインユーザーが「自分の診断結果だけを操作できるか」や、
 * 「未ログインユーザーが特定の診断結果を閲覧できるか」を判定します。
 */

class DiagnosisPolicy
{
    /**
     * 診断結果の閲覧権限を判定する
     * ログイン中のユーザー（未ログイン時は null）
     */
    public function view(?User $user, Diagnosis $diagnosis): bool
    {
        // 診断結果がどのユーザーにも紐づいていない（ゲスト診断）場合
        // 未ログインであっても閲覧を許可します。
        if ($diagnosis->user_id === null) {
            return true;
        }

        // 診断結果が誰かに紐づいている場合
        // ログイン中のユーザーIDと、診断データの所有者ID（user_id）が一致するかチェックします。
        // ※ $user?->id （Nullセーフ演算子）を使用することで、未ログイン時にエラーを回避しつつ安全に比較します。
        return $user?->id === $diagnosis->user_id;
    }

    /**
     * 診断結果の削除権限を判定する
     */
    public function delete(User $user, Diagnosis $diagnosis): bool
    {
        // 削除操作は本人確認が必須なため、引数は ?User ではなく User（ログイン済み限定）とします。
        // 診断の所有者（user_id）と現在のログインユーザーIDが一致する場合のみ許可します。
        return $user->id === $diagnosis->user_id;
    }
}