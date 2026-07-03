<?php

namespace App\Livewire\Diagnosis;

use Livewire\Component;
use App\Models\Diagnosis;
use Livewire\Attributes\Title;

/**
 * 過去の診断履歴一覧を表示・管理するコンポーネント
 * ログインユーザー自身の診断履歴を取得し、
 * 期間や準備度レベルによる絞り込み、スコア順などの並び替え、および削除機能を提供します。
 */

#[Title('過去の診断履歴')]

class History extends Component
{
    // 画面に表示する診断履歴のデータコレクション */
    public $diagnoses;

    /* ========================================
     * フィルター・ソート条件を保持するプロパティ群
     * ======================================== */
    // 期間フィルターの選択値（all: 全期間 / week: 1週間以内 / month: 1ヶ月以内）
    public $period = 'all';
    // 準備度レベルフィルターの選択値（all: すべて / level_perfect / level_high など）
    public $level = 'all';
    // 並び替えの選択値（latest: 最新順 / score_desc: スコア高い順 / score_asc: スコア低い順）
    public $sort = 'latest'; // latest / score_desc / score_asc


    /**
     * 画面が表示される前に、初期条件（全期間、全レベル、最新順）で履歴データを読み込みます。
     */
    public function mount()
    {
        $this->loadHistory();
    }

    /**
     * プロパティが更新された直後に自動実行されるライフサイクルフック
     * ユーザーが画面上でプルダウン（期間、レベル、ソート）を変更した際、
     * ページ全体をリロードすることなく、自動的にデータを再読み込みして画面を更新します。
     *
     */
    public function updated($property)
    {
        // 変更されたプロパティがフィルターやソートに関するものだった場合のみ、再読み込みを実行
        if (in_array($property, ['period', 'level', 'sort'])) {
            $this->loadHistory();
        }
    }

    /**
     * 画面の選択条件（期間、レベル、ソート）に合わせて、データベースから診断履歴を取得する
     */

    public function loadHistory()
    {
        // 土台となるクエリを作成（現在ログインしているユーザーのデータのみに制限）
        $query = Diagnosis::where('user_id', auth()->id());

        /**
         * 絞り込み（期間）
         * 現在時刻（now()）を基準に、1週間前、または1ヶ月前以降のデータに絞り込みます。
         */
        if ($this->period === 'week') {
            $query->where('created_at', '>=', now()->subWeek());
        } elseif ($this->period === 'month') {
            $query->where('created_at', '>=', now()->subMonth());
        }

        /**
         * 絞り込み（レベル）
         * 画面から送られてくる英語のキー（level_perfect等）を、DBに保存されている日本語の判定名に変換します。
         */
        $levelMap = [
            'level_perfect'  => '最高',
            'level_high'     => '高い',
            'level_mid'      => 'やや高い',
            'level_low'      => '低い',
            'level_very_low' => 'かなり低い',
        ];

        // 画面で選択されたキーがマップの中に存在する場合、その日本語名を条件に追加
        if (array_key_exists($this->level, $levelMap)) {
            $query->where('readiness_level', $levelMap[$this->level]);
        }

        /**
         * 並び替え
         * 選択されたソート条件に応じて orderBy を切り替えます。
         */
        if ($this->sort === 'score_desc') {
            $query->orderBy('readiness_score', 'desc'); // スコアの高い順
        } elseif ($this->sort === 'score_asc') {
            $query->orderBy('readiness_score', 'asc'); // スコアの低い順
        } else {
            $query->orderBy('created_at', 'desc'); // 診断日時の新しい順（デフォルト）
        }
        // 組み立てたクエリを最後に実行（get()）し、プロパティに格納して画面へ渡す
        $this->diagnoses = $query->get();
    }

    /**
     * 指定された診断履歴を削除する
     *
     * 削除対象の診断レコードのID
     * 他人のデータを削除しようとした場合は拒否
     */
    public function deleteDiagnosis($id)
    {
        // 対象の診断データを取得（存在しない場合は404）
        $diagnosis = Diagnosis::findOrFail($id);

        // LaravelのPolicy（ポリシー）を使い、このユーザーに削除権限があるかをチェック
        // ※「他人の診断履歴のID」を悪意をもって直接送信されても削除できないように保護します（セキュリティ対策）
        $this->authorize('delete', $diagnosis);
        // データベースからレコードを削除
        $diagnosis->delete();
        // 削除後の最新の状態で、一覧データを再度読み込み直して画面を更新
        $this->loadHistory();
    }

    public function render()
    {
        return view('livewire.diagnosis.history');
    }
}