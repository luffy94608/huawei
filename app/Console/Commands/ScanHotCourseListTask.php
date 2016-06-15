<?php

namespace App\Console\Commands;

use App\Models\Container;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Api\Course;
use Ixudra\Curl;
use App\Helper\WeiboUtil;
use App\Models\Enums\ContainerEnum;
use App\Models\HotLink;

class ScanHotCourseListTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan_host_course_list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '抓取当季热门课程数据';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now();
        echo $now->toDateTimeString()."\r\n";
        $page = 1;
        $pageSize = 20;
        $hasMore = true;
        while ($hasMore)
        {
            $container = Container::where('page_id',WeiboUtil::getWeiboContainerId(ContainerEnum::PageStudy))->first();
            if ($container) {
                $hotCourses = Course::getHotCourseList($page,$pageSize);
                echo sprintf("正在抓取第 %s 页数据  每页 %s 条.当前抓取数据条数 %s 个\n\r",$page,$pageSize,count($hotCourses));
                $page++;
                if (!empty($hotCourses) && count($hotCourses)) {
                    foreach ($hotCourses as $hotCourse) {
                        $item['weibo_id'] = $hotCourse['_id'];
                        $item['scheme'] = $hotCourse['wbUrl'];
                        $item['title_sub'] = $hotCourse['subject'];
                        $item['container_id'] = $container->id;

                        $attributes['weibo_id'] =  $item['weibo_id'];
                        HotLink::updateOrCreate($attributes,$item);
                    }
                } else {
                    $hasMore = false;
                }
            } else {
                $hasMore = false;
            }
        }

    }
}
