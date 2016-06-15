<?php

namespace App\Models;

use App\Http\Transformers\ApiResultTransformer;
use Spatie\Fractal\ArraySerializer;

class ApiResult
{
    protected $code;
    protected $msg;
    protected $data;

    public function __construct($code, $msg, $data)
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->data = $data;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getMsg()
    {
        return $this->msg;
    }

    public function getData()
    {
        $data = $this->data;
        if (empty($data))
        {
            $data = json_decode('{}');
        }
        return $data;
    }

    public function toJson()
    {
        $result = fractal()
            ->item($this, new ApiResultTransformer())
            ->serializeWith(new ArraySerializer())
            ->toArray();
        return $result;
    }
}
