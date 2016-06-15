<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\ApiResult;
use App\Models\Api\Course;
use App\Http\Builders\StudyBuilder;
use App\Models\Enums\ErrorEnum;

class UserController extends Controller
{
    /**
     *获取课程列表接口
     * @param Request $request
     */
    public function postCourseList(Request $request)
    {
        $patternMap = array(
            'page'          => 'sometimes|numeric',
            'length'        => 'sometimes|numeric',
        );
        $this->validate($request,$patternMap);
        $input = $request->only(array_keys($patternMap));
        
        $courses = Course::getCourseList($input['page']);
        $hasMore = false;
        if (count($courses) == 10) {
            $hasMore = true;
        }
        $courseHtml = StudyBuilder::toBuildCourseListHtml($courses);

        $data = [
            'html' => $courseHtml,
            'hasMore' => $hasMore,
        ];
        return response()->json((new ApiResult(0, ErrorEnum::Success, $data))->toJson());
    }

    /**
     *获取课程榜单 category 接口
     * @param Request $request
     */
    public function postCourseRandCategoryList(Request $request)
    {

        $patternMap = array(
            'category'      => 'sometimes|numeric',
            'page'          => 'sometimes|numeric',
            'length'        => 'sometimes|numeric',
        );
        $this->validate($request,$patternMap);
        $input = $request->only(array_keys($patternMap));

        $ranks = Course::getRankWithTypeList(1, $input['category'], $input['page']);
        $hasMore = false;
        if (count($ranks) == 10) {
            $hasMore = true;
        }
        $rankHtml = StudyBuilder::toBuildRankListHtml($ranks, 1);

        $data = [
            'html' => $rankHtml,
            'hasMore' => $hasMore,
        ];
        return response()->json((new ApiResult(0, ErrorEnum::Success, $data))->toJson());
    }
}
