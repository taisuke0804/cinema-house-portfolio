<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Screening;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeletePastScreenings extends Command
{
    /**
     * コンソールコマンドの名前と使い方
     *
     * @var string
     */
    protected $signature = 'screenings:delete-past';

    /**
     * コンソールコマンドの説明
     *
     * @var string
     */
    protected $description = '過去の上映スケジュールを削除する';

    /**
     * consoleコマンドの実行
     */
    public function handle()
    {
        $oneWeekAgo = Carbon::now()->subWeek();
        $count = Screening::where('end_time', '<', $oneWeekAgo)->delete();

        // コンソールに出力
        $message = "{$count} 件の上映スケジュール（1週間以上前）を削除しました。";
        $this->info($message);
        Log::info($message);

        return Command::SUCCESS; // 終了ステータスコードを返す
    }
}
