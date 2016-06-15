<?php

namespace App\Console;


use App\Console\Commands\ScanPageLogStatTask;
use App\Console\Commands\ScanWeiboFeed;
use App\Console\Commands\ScanWeiboTopic;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\ScanHotCourseListTask;
use App\Console\Commands\Inspire;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Inspire::class,
        ScanWeiboFeed::class,
        ScanHotCourseListTask::class,
        ScanWeiboTopic::class,
        ScanPageLogStatTask::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('scan_weibo_feed')->hourly()->timezone('Asia/Shanghai')->when(function(){
            return true;
        })->appendOutputTo(storage_path('/logs/scan_weibo_feed.log'));

        $schedule->command('scan_weibo_topic')->dailyAt('0:00')->timezone('Asia/Shanghai')->when(function(){
            return true;
        })->appendOutputTo(storage_path('/logs/scan_weibo_topic.log'));

        $schedule->command('scan_host_course_list')->dailyAt('0:00')->timezone('Asia/Shanghai')->when(function(){
            return true;
        })->appendOutputTo(storage_path('/logs/scan_host_course_list.log'));

        $schedule->command('scan_page_log_stat')->dailyAt('0:00')->timezone('Asia/Shanghai')->when(function(){
            return true;
        })->appendOutputTo(storage_path('/logs/scan_page_log_stat.log'));
    }
}
