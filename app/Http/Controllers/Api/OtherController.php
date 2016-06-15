<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\BannerTransformer;
use App\Models\ApiResult;
use App\Models\Banner;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Spatie\Fractal\ArraySerializer;

class OtherController extends Controller
{
    /**
     * 检查更新
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUpdate(Request $request)
    {
        $versionCode = $request->headers->get("HGT-VERSION", 0);
        $clientType = strtolower($request->headers->get('HGT-OS', ''));
        $currentVersion = '1.0.0';
        $currentVersionCode = 100;

        $updateType = 0;
        if ($versionCode < $currentVersionCode)
        {
        }

        $data = array(
            'type'          => $updateType,
            'version'       => $currentVersion,
            'version_code'  => $currentVersionCode,
            'desc'          => '这里是更新提示',
            'ios_url'       => "https://itunes.apple.com/us/app/ha-luo-tong-xing-yi-zhan-shi/id808050964?mt=8",
            'android_url'   => "http://download.hollo.cn/hollogo.apk"
        );

        return response()->json((new ApiResult(0, 'success', $data))->toJson());
    }


    /**
     * 获取客户端设置，jspatch仅iOS用
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfig(Request $request)
    {
        $this->validate($request, array(
            'config_version'    => 'sometimes',
        ));

        $input = $request->only('config_version');

        $data = array(
            'config' => array(
                "config_version"    => 1,
                "qrcode_switch"     => 1,
                "patch"             => "",
                "store_url"         => "http://www.jinxiudadi.com/",
                "enable_wxpay"      => 0    //0:不开通 1:开通
            )
        );

        return response()->json((new ApiResult(0, 'success', $data))->toJson());
    }

    public function getLaunchAds()
    {
        //TODO:实现
        $data = array(
            'launch_ads' => array(
                'duration' => 0,
                'image_url' => 'http://static.hollo.cn/images/banner/ads_gee_ios_4.jpg'
            )
        );
        return response()->json((new ApiResult(0, 'success', $data))->toJson());
    }

    public function getBanners(Request $request)
    {
        $osType = $request->headers->get("HGT-OS", 'ios');
        $screenAttr = $request->headers->get('HGT-Screen', '');
        $banners = Banner::where('active', true)->orderBy('sort_num', 'asc')->get();

        $data = fractal()
            ->collection($banners)
            ->resourceName('banners')
            ->transformWith(new BannerTransformer($osType, $screenAttr))
            ->serializeWith(new ArraySerializer())
            ->toArray();

        return response()->json((new ApiResult(0, 'success', $data))->toJson());
    }

}
