<?php

namespace App\Console\Commands;

use App\Models\Container;
use App\Models\Enums\CacheKeyEnum;
use App\Models\WeiboFeed;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Cache\MemcachedStore;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Cache;

class ScanWeiboFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan_weibo_feed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '扫描微博Feed接口';

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
//        $container = Cache::store('memcached')->remember('containers',60,function(){
//            print 'enter db get2\n';
//            return Container::all();
//        });
//
//        $container = Cache::remember('containers',60,function(){
//            print 'enter db get1';
//            return Container::all();
//        });
//        echo $container;

        $url = 'http://i.hot.weibo.com/3/data/get?source=646811797&category=1488&count=300&page=1';
        $client = new Client();
        print 'Begin fetch weibo feed;';
        $res = $client->request('GET',$url);
        print 'End fetch weibo feed:'.$res->getStatusCode().'|'.$res->getBody();
        if ($res->getStatusCode() == 200)
        {
            $result= $res->getBody();
            $resArray = json_decode($result,true);
            $data = array();
            foreach ($resArray as $r)
            {
                $temp = array();
                $temp['mid'] = $r['mid'];
                $temp['created_at'] = \Carbon\Carbon::now();
                $temp['updated_at'] = \Carbon\Carbon::now();
                $data[] = $temp;
            }
            WeiboFeed::truncate();
            WeiboFeed::insert($data);
//            Cache::forget(CacheKeyEnum::WeiboFeed);
//            Cache::put(CacheKeyEnum::WeiboFeed,$data,60*24);
            Cache::tags(CacheKeyEnum::WeiboFeed)->flush();
        }

    }
}
