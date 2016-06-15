<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 12/18/15
 * Time: 6:23 PM
 */

namespace App\Http\Transformers;

use App\Models\ApiResult;
use League\Fractal\TransformerAbstract;


class ApiResultTransformer extends TransformerAbstract
{
    /**
     * Turn this item object into a generic array.
     *
     * @param  \App\Models\ApiResult  $apiResult
     * @return array
     */
    public function transform(ApiResult $apiResult)
    {
        return array(
            'code'  => $apiResult->getCode(),
            'msg'   => $apiResult->getMsg(),
            'data'  => $apiResult->getData()
        );
    }
}