<?php

namespace App\Livewire\Diagnosis;

use Livewire\Component;
use App\Models\Diagnosis;
use App\Services\DiagnosisService;

/**
 * 診断結果表示用のコンポーネント
 *
 * ユーザーの診断結果（スコア、評価、コメント）を取得し、
 * 全体統計との比較や、前回診断時とのスコア差分を計算してビューに渡します。
 */

class Result extends Component
{
    // Diagnosis 現在表示している診断データ
    public $diagnosis;
    // カテゴリ別（住環境、時間、費用）のフィードバックコメント
    public array $categoryComments = [];

    // 全ユーザーの平均スコア
    public $averageScore = 0;
    // 現在のスコアが全体の上位何％に位置するか（パーセンタイル）
    public $percentile_rank = 1;
    // 比較対象となる全ユーザー（データ）の総数
    public $totalUsersCount = 0;

    // ログインユーザーの直近（前回）の診断データ
    public $previousDiagnosis = null;
    // 前回診断時からのスコアの増減値
    public $scoreDifference = 0;

    /**
     * 診断データのID（URLパラメータから取得）
     * 診断ロジックを扱うサービス
     */
    public function mount($id, DiagnosisService $service)
    {
        // 指定されたIDの診断データを取得（存在しない場合は404エラー）
        $this->diagnosis = Diagnosis::findOrFail($id);
        $this->authorize('view', $this->diagnosis);

        $result = $service->calculate([
            'housing_type' => $this->diagnosis->housing_type,
            'absence_hours' => $this->diagnosis->absence_hours,
            'planned_cat_count' => $this->diagnosis->planned_cat_count,
            'monthly_budget' => $this->diagnosis->monthly_budget,
            'can_visit_hospital' => $this->diagnosis->can_visit_hospital,
            'accepts_special_care' => $this->diagnosis->accepts_special_care,
        ]);

        // サービスを利用して、カテゴリごとの詳細なコメントを生成
        $this->categoryComments = $result['category_comments'];

        // 統計データ（平均点や順位）の計算処理を呼び出し
        $this->calculateStatistics();

        // ログイン済みユーザーの場合のみ、前回との比較データを取得
        if (auth()->check()) {
            // 現在の診断データよりIDが小さく（古い）、かつ同一ユーザーの最新データを1件取得
            $this->previousDiagnosis = Diagnosis::where('user_id', auth()->id())
                ->where('id', '<', $this->diagnosis->id)
                ->latest('id')
                ->first();

            // 前回データが存在すれば、スコアの差分（今回 - 前回）を計算
            if ($this->previousDiagnosis) {
                $this->scoreDifference = $this->diagnosis->readiness_score - $this->previousDiagnosis->readiness_score;
            }
        }


    }

    /**
     * 全ユーザーのデータをもとに統計情報（平均点、パーセンタイル順位）を計算する
     */
    private function calculateStatistics()
    {
        $this->totalUsersCount = Diagnosis::count();

        // DBに保存されている全診断データの件数を取得
        if ($this->totalUsersCount > 1) {
            // 全ユーザーの平均スコアを計算し、小数第一位で四捨五入
            $this->averageScore = round(Diagnosis::avg('readiness_score'), 1);

            // 現在のスコアより高い点数を取っているデータの件数を取得（同点は含めない）
            $higherScoreCount = Diagnosis::where('readiness_score', '>', $this->diagnosis->readiness_score)->count();

            // 自分の「順位」を計算（高い人が0人なら1位、1人いれば2位）
            $rank = $higherScoreCount + 1;

            // 全体の中での割合（上位何％か）を計算
            $calcPercentile = ($rank / $this->totalUsersCount) * 100;

            // 少数点以下を四捨五入（最低でも1にする）
            $this->percentile_rank = max(1, round($calcPercentile));
        }
    }

    // ログイン画面へ遷移する前に現在のURLをセッションに保存
    public function redirectToLogin()
    {
        // route() ヘルパーを使って、正しい診断結果ページのURLを生成する
        $url = route('result', ['id' => $this->diagnosis->id]);
        session(['url.intended' => $url]);
        return redirect()->route('login');
    }

    // 新規登録画面へ遷移する前に現在のURLをセッションに保存
    public function redirectToRegister()
    {
        // 同様に正しいURLを生成する
        $url = route('result', ['id' => $this->diagnosis->id]);
        session(['url.intended' => $url]);
        return redirect()->route('register');
    }

    public function render()
    {
        return view('livewire.diagnosis.result')
            ->title("診断結果: {$this->diagnosis->readiness_score}点");
    }
}