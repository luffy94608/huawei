<?php

namespace App\Console\Commands;

use App\Models\PageStat;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Ixudra\Curl;

class ScanPageLogStatTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan_page_log_stat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '日志分析';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $yesterday = Carbon::yesterday();
        $now = Carbon::now();
        $filePath = sprintf('logs/stat-%s.log',$yesterday->toDateString());
        $targetLog = storage_path($filePath);

        echo sprintf("当前执行时间:%s\r\n",$now->toDateTimeString());
        echo sprintf("当前log 路径:%s\r\n",$targetLog);
        if (file_exists($targetLog)) {
            $content = file_get_contents($targetLog);
            $lines = explode("\n",$content);
            echo sprintf("当前分割字符串数:%s\n\r",count($lines));
            if(count($lines) > 1){
                $data = [];

                foreach ($lines as $line) {
                    $lineArr = explode('#',$line);
                    if (isset($lineArr[1]) && isset($lineArr[3])) {
                        $uid = $lineArr[1];
                        $src = $lineArr[3];
                        $data[$src]['uv'][$uid] = 1;
                        $data[$src]['pv'][] = $uid;
                    }

                }
                echo sprintf("当前插入统计数:%s\r\n",count($data));
                if (count($data)) {
                    $insertData = [];
                    $timeStr = $yesterday->toDateString();
                    foreach ($data as $key => $value) {
                        $insertData [] = [
                            'url' => $key,
                            'time' => $timeStr,
                            'pv' => count($value['pv']),
                            'uv' => count($value['uv']),
                        ];
                    }
                    PageStat::insert($insertData);
                }
            }
        }

    }
}

