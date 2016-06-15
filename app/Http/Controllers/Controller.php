<?php

namespace App\Http\Controllers;

use App\Models\Card\Card42TitleCard;
use App\Models\Container;
use GuzzleHttp\Client;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\ApiResult;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /***
     * container 的 标识
     * @var String
     */
    protected $pageName;

    /***
     * @var Container
     */
    protected $container;
    
    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        $keys = array_keys($errors);
        $error_msg = $errors[$keys[0]][0];
        return response()->json((new ApiResult(-1, $error_msg ?: '参数错误', ''))->toJson());
    }

    /**
     * 获取客户端ip
     * @return mixed
     */
    public function getRemoteIp()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');

        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    public function getCity($ip)
    {
        $client = new Client();
        $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$ip;
        $res = $client->request('GET',$url);
//        print 'End fetch weibo feed:'.$res->getStatusCode().'|'.$res->getBody();
        if ($res->getStatusCode() == 200)
        {
            $data = json_decode($res->getBody(),true);
            if ($data['ret'] == 1 && isset($data['city']))
            {
                return $data['city'];
            }

        }
        return null;
    }
}
