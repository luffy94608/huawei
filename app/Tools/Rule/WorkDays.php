<?php
/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 3/23/16
 * Time: 6:11 PM
 */

namespace App\Tools\Rule;

use Carbon\Carbon;
use App\Models\WorkDay;
use App\Tools\Message\MessageCenter;
use App\Models\Setting;

class WorkDays
{
    public static function getNextWorkDay()
    {
        $scheduleDay = Carbon::tomorrow();

        while (true)
        {
            $workDays = WorkDay::where('year', $scheduleDay->year)->where('month', $scheduleDay->month)->first();
            if (!$workDays)
            {
                $contacts = Setting::scheduleMonitor();
                //发短信报警
                MessageCenter::sendSMS($contacts, sprintf("数据库中没有%s的工作日记录!!!", $scheduleDay->toDateString()));
                return null;
            }

            if (in_array($scheduleDay->day, $workDays->days)) {
                return $scheduleDay;
            }

            $scheduleDay->addDay();
        }
    }
}