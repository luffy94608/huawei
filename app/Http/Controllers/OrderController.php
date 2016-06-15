<?php

namespace App\Http\Controllers;

use App\Http\Builders\OrderBuilder;
use App\Http\Requests;
use App\Models\Area;
use App\Models\Enums\OrderEnum;
use App\Models\Order;
use App\Models\Type;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Models\ApiResult;
use App\Models\Enums\ErrorEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendReminderEmail;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 订单列表
     * @param int $type
     * @return mixed
     */
    public function getList($type = 0)
    {
//        Auth::logout();
        if ($type == 0) {
            $filter = [OrderEnum::ORDER_STATUS_ACCEPT, OrderEnum::ORDER_STATUS_WAITING_SEND, OrderEnum::ORDER_STATUS_ACCOMPLISH];
        } else {
            $filter = [OrderEnum::ORDER_STATUS_REMARKED];
        }
        $orders = Order::whereIn('status',$filter)
                    ->orderBy('updated_at', 'desc')
                    ->get();

        $listHtml = OrderBuilder::toBuildListHtml($orders);
        $params = [
            'page'=>'page-list',
            'type'=>$type,
            'listHtml'=>$listHtml,
        ];
        return View::make('order.list',$params);
    }

    /**
     * 订单详情
     * @param $id
     * @return mixed
     */
    public function getDetail($id)
    {
        $order = Order::find($id);

        $minutes = OrderEnum::NextOrderRemindMinutes;
        $now = Carbon::now();

        $next_remind_seconds = $minutes*60 - ($now->timestamp - strtotime($order->remind_time));

        $params = [
            'page'=>'page-detail',
            'order'=>$order,
            'next_remind_seconds'=>$next_remind_seconds ? $next_remind_seconds : 0,
            'current_timestamp'=>$now->timestamp,
        ];
        return View::make('order.detail',$params);

    }

    /**
     * 创建订单
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreate()
    {
        $areas = Area::get();
        $types = Type::get();

        $areaOptionsHtml = OrderBuilder::toBuildSelectOptionsHtml($areas);
        $typeOptionsHtml = OrderBuilder::toBuildSelectOptionsHtml($types);

        $params = [
            'page'=>'page-create',
            'areaOptionsHtml'=>$areaOptionsHtml,
            'typeOptionsHtml'=>$typeOptionsHtml,
        ];
        return View::make('order.create',$params);

    }

    /**
     * 创建订单
     *
     * @param Request $request
     * @return mixed
     */
    public function postCreate(Request $request)
    {
        $patternMap = array(
            'job_id'         => 'required',
            'mobile'         => 'required',
            'area_id'        => 'required',
            'type_id'        => 'required',
            'desc'           => 'required',
        );
        $this->validate($request, $patternMap);
        $input = $request->only(array_keys($patternMap));
        $user = Auth::user();
        $input['order_no'] = Order::toCreateOrderNo();
        $input['user_id'] = $user->id;
        $result = Order::create($input);
        if ($result) {
            $data = [
                'url' => '/order/list',
            ];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            return response()->json((new ApiResult(-1, ErrorEnum::transform(ErrorEnum::Failed), ''))->toJson());
        }
    }

    /**
     * 评价
     * @param Request $request
     * @return mixed
     */
    public function postRemark(Request $request)
    {
        $patternMap = array(
            'id'            => 'required',
            'score'         => 'required',
            'remark'        => 'sometimes',
        );
        $this->validate($request, $patternMap);
        $input = $request->only(array_keys($patternMap));
        $user = Auth::user();
        $order = Order::where('user_id',$user->id)
            ->where('id',$input['id'])
            ->first();
        if ($order) {

            $order->score = $input['score'];
            $order->remark = $input['remark'];
            $order->status = OrderEnum::ORDER_STATUS_REMARKED;
            $order->save();
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), ''))->toJson());
        } else {
            return response()->json((new ApiResult(-1, ErrorEnum::transform(ErrorEnum::Failed), ''))->toJson());
        }
    }

    /**
     * 催单
     * @param Request $request
     * @return mixed
     */
    public function postRemind(Request $request)
    {
        $patternMap = array(
            'id'         => 'required',
        );
        $this->validate($request, $patternMap);
        $input = $request->only(array_keys($patternMap));
        $user = Auth::user();

        $order = Order::where('user_id',$user->id)
                    ->where('id',$input['id'])
                    ->first();
        if ($order) {
            $now = Carbon::now();
            $minutes = OrderEnum::NextOrderRemindMinutes;
            $nowTmp = Carbon::now();
            $nowTmp->subMinutes($minutes);

            $next_remind_seconds = $minutes*60 - ($now->timestamp - strtotime($order->remind_time));
            $data = [
                'next_remind_seconds' =>$next_remind_seconds>0 ? $next_remind_seconds : 0,
                'current_timestamp'=>$now->timestamp,
            ];

            if (($nowTmp->toDateTimeString() <= $order->remind_time) && $order->remind_time > 0) {
                return response()->json((new ApiResult(-1, ErrorEnum::transform(ErrorEnum::RemindMinutesNotAvailableError), $data))->toJson());
            }

            $order->remind_num += 1;
            $order->remind_time = $now;
            $order->save();

            $next_remind_seconds = $minutes*60 - ($now->timestamp - strtotime($order->remind_time));
            $data = [
                'next_remind_seconds' =>$next_remind_seconds>0 ? $next_remind_seconds : 0,
                'current_timestamp'=>$now->timestamp,
            ];
//            $this->sendReminderEmail($order);

            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            return response()->json((new ApiResult(-1, ErrorEnum::transform(ErrorEnum::Failed), ''))->toJson());
        }
    }

    /**
     * 发送提醒的 e-mail 给指定用户。
     *
     * @param Order $order
     */
    public function sendReminderEmail(Order $order)
    {
        $job = (new SendReminderEmail($order));
        $this->dispatch($job);
    }
}
