<?php

namespace App\Console\Commands;

use App\Models\Enums\CacheKeyEnum;
use App\Models\Enums\ContainerEnum;
use App\Models\HotLink;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Cache;
use App\Models\Container;

class ScanWeiboTopic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan_weibo_topic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '扫描微博Topic接口';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $url = 'http://user.opendata.sina.com.cn/interface/school_topic/get_school_topic.jsp?uid=1438017112&type=public';
        $client = new Client();
        print 'Begin fetch weibo topic;';
        $res = $client->request('GET',$url);
        print 'End fetch weibo topic:'.$res->getStatusCode().'|'.$res->getBody();
        if ($res->getStatusCode() == 200)
        {
            $result= $res->getBody();
            $resArray = json_decode($result,true);
            $data = array();
            
            $pageName = ContainerEnum::PageHome;
            $container = Container::where('name',$pageName)->first();

            $array =  $container->hotLinks()->where('is_show',1)->get()->toArray();
            $topicMap = array();
            foreach ($array as $r)
            {
                $topicMap[$r['weibo_id']] = $r;
            }

            if ($resArray['errno'] == 1)//成功
            {
                foreach ($resArray['result']['public'] as $r)
                {
                    if (isset($topicMap[$r['object_id']]))
                    {
                        continue;
                    }
                    $temp = array();
                    $temp['weibo_id'] = $r['object_id'];
                    $temp['title_sub'] = '#'.$r['name'].'#';
                    $temp['read'] = $r['read'];
                    $temp['discuss'] = $r['discuss'];
                    $temp['container_id'] = $container->id;
                    $temp['created_at'] = \Carbon\Carbon::now();
                    $temp['updated_at'] = \Carbon\Carbon::now();
                    $data[] = $temp;
                }
            }
            HotLink::where('is_show',0)->where('container_id',$container->id)->delete();
            HotLink::insert($data);
            Cache::forget(CacheKeyEnum::WeiboTopic);

            
        }

    }
}
