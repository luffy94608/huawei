<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;

class IndexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 成功页面
     *
     * @return \Illuminate\Http\Response
     */
    public function getSuccess()
    {
        return View::make('layouts.success');
    }

    /**
     * 失败页面
     *
     * @return \Illuminate\Http\Response
     */
    public function getError()
    {
        return View::make('layouts.error');
    }
}
