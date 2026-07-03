<?php

namespace App\Livewire\Diagnosis;

use Livewire\Component;
use App\Models\Diagnosis;
use App\Services\DiagnosisService;

/**
 * 診断入力（質問フォーム）用のコンポーネント
 *
 * 1問ずつのステップ形式で質問を表示し、ユーザーの回答を保持します。
 * 全問回答後にスコアを計算し、データベースへ保存して結果画面へ遷移させます。
 */

class Quiz extends Component
{
    // 現在表示している質問のインデックス（0からスタート）
    public $current = 0;
    // 質問の定義データ（ラベル、入力タイプ、選択肢など）
    public $questions = [];
    /* ========================================
     * ユーザーの回答を保持するプロパティ群
     * ======================================== */
    public $housing_type = ''; // 住環境（ペット可 / ペット不可）
    public $absence_hours = ''; // 1日の留守時間（時間）
    public $planned_cat_count = ''; //飼育予定の猫の数
    public $monthly_budget = ''; //月にかけられる予算（円）
    public $can_visit_hospital = ''; //動物病院へ連れて行けるか（はい / いいえ）
    public $accepts_special_care = ''; //将来の介護などを受け入れられるか（はい / いいえ）


    /**
     *
     * 画面が最初に表示される前に1度だけ実行され、
     * 画面に表示する全6問の質問内容（配列）をセットアップします。
     *
     */
    public function mount()
    {
        $this->questions = [
            [
                'type' => 'radio',
                'label' => '1. 現在のお住まいは「猫の飼育」が正式に許可されていますか？',
                'model' => 'housing_type',
                'options' => ['ペット可', 'ペット不可'],
            ],
            [
                'type' => 'number',
                'label' => '2. 仕事やお出かけなど、誰も家にいない「1日の留守時間」はどのくらいですか？',
                'model' => 'absence_hours',
                'placeholder' => '例: 8',
                'unit' => '時間',
            ],
            [
                'type' => 'number',
                'label' => '3. お迎えを検討している猫の数は何匹ですか？',
                'model' => 'planned_cat_count',
                'placeholder' => '例: 1',
                'unit' => '匹',
            ],
            [
                'type' => 'number',
                'label' => '4. 食費や日用品、医療費の積立を含め、猫のために使える「月々の予算」はいくらですか？',
                'model' => 'monthly_budget',
                'placeholder' => '例: 10000',
                'unit' => '円',
                'step' => 1000,
            ],
            [
                'type' => 'radio',
                'label' => '5. 体調不良時や定期健診など、必要に応じて動物病院へ連れて行くことは可能ですか？',
                'model' => 'can_visit_hospital',
                'options' => ['はい', 'いいえ'],
            ],
            [
                'type' => 'radio',
                'label' => '6. 猫の寿命は15〜20年です。将来の介護や、ご自身の生活の変化があっても最後まで責任を持てますか？',
                'model' => 'accepts_special_care',
                'options' => ['はい', 'いいえ'],
            ],
        ];
    }

    /**
     * 回答の進捗率（％）を計算して取得する
     *
     * 「Computed Property（計算プロパティ）」という機能を利用しています。
     * 画面（Blade）側からは `$this->progress` として呼び出すことができます。
     */
    public function getProgressProperty()
    {
        $answered = 0;
        // 各項目が入力済み（空文字ではない）ならカウントを増やす
        if ($this->housing_type !== '') $answered++;
        if ($this->absence_hours !== '') $answered++;
        if ($this->planned_cat_count !== '') $answered++;
        if ($this->monthly_budget !== '') $answered++;
        if ($this->can_visit_hospital !== '') $answered++;
        if ($this->accepts_special_care !== '') $answered++;
        // 全6問中の回答済み割合を100分率で返す（小数点切り捨て）
        return (int)(($answered / 6) * 100);
    }

    /**
     * 現在の質問に対して「次へ」進めるかどうかを判定する
     */
    public function canGoNext(): bool
    {
        // 現在表示している質問の情報を取得
        $q = $this->questions[$this->current];
        $model = $q['model']; // 例: 'housing_type'
        $value = $this->{$model}; // 例: $this->housing_type の値を取得

        // 未入力の場合は進めない
        if ($value === '' || $value === null) {
            return false;
        }
        // 数値入力が求められる項目の場合、値が数値であるかをチェック
        if (in_array($model, ['absence_hours', 'planned_cat_count', 'monthly_budget'])) {
            return is_numeric($value);
        }

        return true;
    }

    /**
     * 次の質問へ進む
     *
     * Bladeの「次へ」ボタンが押された時に実行されます。
     */
    public function next()
    {
        // バリデーション（canGoNext）を通過しないと進めないように保護
        if (!$this->canGoNext()) {
            return;
        }
        // 最後の質問でなければ、インデックスを1進める
        if ($this->current < count($this->questions) - 1) {
            $this->current++;
        }
    }

    /**
     * 前の質問へ戻る
     *
     * Bladeの「戻る」ボタンが押された時に実行されます。
     */
    public function prev()
    {
        // 最初の質問（0）より前に戻らないように保護
        if ($this->current > 0) {
            $this->current--;
        }
    }


    /**
     * 最終的な診断スコアの計算と、結果の保存を行う
     *
     * 最後の質問で「診断結果を見る」ボタンが押された時に実行されます。
     *
     * 診断の計算ロジックを持つサービス
     * 結果画面へのリダイレクト
     */
    public function calculate(DiagnosisService $service)
    {
        // 全入力項目の最終バリデーション（不正な値が送信されるのを防ぐ）
        $validated = $this->validate([
            'housing_type' => 'required|in:ペット可,ペット不可',
            'absence_hours' => 'required|numeric|min:0',
            'planned_cat_count' => 'required|numeric|min:1',
            'monthly_budget' => 'required|numeric|min:0',
            'can_visit_hospital' => 'required',
            'accepts_special_care' => 'required',
        ]);
        // サービスに回答データを渡し、スコア・レベル・コメントを計算してもらう
        $result = $service->calculate([
            'housing_type' => $validated['housing_type'],
            'absence_hours' => (int) $validated['absence_hours'],
            'planned_cat_count' => (int) $validated['planned_cat_count'],
            'monthly_budget' => (int) $validated['monthly_budget'],
            'can_visit_hospital' => $validated['can_visit_hospital'] === 'はい', // booleanに変換
            'accepts_special_care' => $validated['accepts_special_care'] === 'はい', // booleanに変換
        ]);

        // 計算結果を含めて、データベースに新しい診断記録を作成（保存）する
        $diagnosis = Diagnosis::create([
            'user_id' => auth()->id(), // 未ログイン時は null になる
            'housing_type' => $validated['housing_type'],
            'absence_hours' => (int) $validated['absence_hours'],
            'planned_cat_count' => (int) $validated['planned_cat_count'],
            'monthly_budget' => (int) $validated['monthly_budget'],
            'can_visit_hospital' => $validated['can_visit_hospital'] === 'はい',
            'accepts_special_care' => $validated['accepts_special_care'] === 'はい',
            'readiness_score' => $result['score'],
            'readiness_level' => $result['level'],
            'overall_comment' => $result['summary'],
        ]);

        // 未ログイン（ゲスト）ユーザーの場合、作成した診断IDをセッションに記憶させる
        // 直後にログイン・会員登録した際にデータを引き継げるようになります
        if (!auth()->check()) {
            session()->put('pending_diagnosis_id', $diagnosis->id);
        }
        // 保存した診断データのIDをURLに含めて、結果画面へリダイレクト
        return redirect()->route('result', $diagnosis->id);
    }

    public function render()
    {
        return view('livewire.diagnosis.quiz');
    }
}