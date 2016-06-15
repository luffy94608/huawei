<?php
/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 3/15/16
 * Time: 8:40 PM
 */

namespace App\Tools\Seat;

use App\Models\LineTypeEnum;
use App\Models\Schedule;
use App\Models\Line;
use App\Models\SeatStatusEnum;
use App\Models\StationScheduleSeat;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;
use PhpSpec\Exception\Exception;

class SeatTool
{
    public static function getSeatsStatus($schedule, $on, $off)
    {
        $seatsStatus = [];
        $scheduleStations = static::getStations($schedule, $on, $off);
        if ($scheduleStations === false)
        {
            return false;
        }
        foreach($scheduleStations as $scheduleId => $stationIds)
        {
            $seats = StationScheduleSeat::where('schedule_id', $scheduleId)
                ->whereIn('station_id', $stationIds)
                ->orderBy('seat_number', 'asc')
                ->orderBy('status', 'desc')
                ->get();

            foreach($seats as $seat)
            {
                if(!array_key_exists($seat->seat_number, $seatsStatus)) {
                    $seatsStatus[$seat->seat_number] = $seat;
                } elseif ($seatsStatus[$seat->seat_number]->status < $seat->status) {
                    $seatsStatus[$seat->seat_number] = $seat;
                }
            }
        }

        //TODO:返回上下车时间
        return $seatsStatus;
    }

    public static function getStations($schedule, $on, $off)
    {
        $onIdx = -1;
        $offIdx = -1;
        $stationIds = [];
        $idx = 0;

        $line = $schedule->line;

        $deptIdx = -1;

        foreach ($line->stations as $s) {
            if (!array_key_exists('_id', $s)) {
                continue;
            }
            if ($s['_id'] == $on && $onIdx < 0) {
                $onIdx = $idx;
            }
            if ($s['_id'] == $off && $offIdx < 0) {
                $offIdx = $idx;
            }

            if ($schedule->dept_at_station && $s['_id'] == $schedule->dept_at_station && $deptIdx < 0) {
                $deptIdx = $idx;
            }
            $stationIds[] = $s['_id'];
            $idx++;
        }

        if ($deptIdx < 0)
        {
            $deptIdx = 0;
        }

        //如果是环线，去掉最后一站，因为第一站和最后一站是同一站
        if ($line->type == LineTypeEnum::reverseTransform(LineTypeEnum::Circle)) {
            $stationIds = array_slice($stationIds, 0, count($stationIds)-1);
        }

        if ($onIdx < 0 || $offIdx < 0)
        {
            return false;
        }

        //每个调度设置了发车站，如果乘客选择的上车站在发车站之前，则返回false
        if ($onIdx < $deptIdx) return false;

        $result = [];
        if ($offIdx > $onIdx) {
            //一个调度内，不包含下车站
            $sliced = array_slice($stationIds, $onIdx, $offIdx-$onIdx);
            $result[$schedule->id] = $sliced;
        } elseif ($onIdx == $offIdx && $onIdx == 0) {
            $result[$schedule->id] = $stationIds;
        } else {
            $part1 = array_slice($stationIds, $onIdx, count($stationIds));
            $result[$schedule->id] = $part1;
            if ($offIdx > 0)
            {
                $part2 = array_slice($stationIds, 0, $offIdx);

                $nextSchedule = static::getNextSchedule($schedule);
                if (is_null($nextSchedule)) {
                    return false;
                }
                $result[$nextSchedule->id] = $part2;
            }
        }
        Log::info($result);

        return $result;
    }

    public static function getNextSchedule($schedule)
    {
        //TODO:如果是上午最后一班，跨调度到下午，这是不允许的，需要处理一下这种逻辑
        //用中午休息时间来切，或者可以标识是否是上午或下午的最后一班
        //下一班时间应该是发车时间大于当前班次的到站时间的第一班时间
        if (isset($schedule->has_no_next) && $schedule->has_no_next) return null;
        $line = $schedule->line;
        $destAt = $schedule->dest_at;
        $dayEnd = clone $destAt;
        $dayEnd = $dayEnd->endOfDay();
        $nextSchedule = $line->schedules()
            ->where('dept_at', '>', $destAt)
            ->where('dept_at', '<', $dayEnd)
            ->orderBy('dept_at', 'asc')
            ->first();

        return $nextSchedule;
    }

    public static function updateSeatStatus($schedule, $seatNumber, $updateType, $uid, $on, $off)
    {
        $scheduleStations = static::getStations($schedule, $on, $off);

        $connect = DB::connection('mysql');
        $connect->beginTransaction();
        try
        {
            foreach($scheduleStations as $scheduleId => $stationIds)
            {
                $count = count($stationIds);
                $query = StationScheduleSeat::where('schedule_id', $scheduleId)
                    ->where('seat_number', $seatNumber)
                    ->whereIn('station_id', $stationIds);

                $updateCount = 0;
                switch($updateType)
                {
                    case SeatStatusEnum::LOCKED:
                        $updateCount = $query->where('status', SeatStatusEnum::UNLOCKED)
                            ->update(['uid' => $uid, 'status' => $updateType]);
                        break;
                    case SeatStatusEnum::CONFIRMED:
                        $updateCount = $query->where('status', SeatStatusEnum::LOCKED)
                            ->where('uid', $uid)
                            ->update(['status' => $updateType]);
                        break;
                    case SeatStatusEnum::UNLOCKED:
                        $updateCount = $query->whereIn('status', [SeatStatusEnum::LOCKED, SeatStatusEnum::CONFIRMED])
                            ->where('uid', $uid)
                            ->update(['uid' => '', 'status' => $updateType]);
                        break;
                }
                if($updateCount != $count)
                {
                    $connect->rollback();
                    return false;
                }
            }
            $connect->commit();
        }
        catch (\Exception $e)
        {
            Log::error($e);
            $connect->rollback();
            return false;
        }
        return true;
    }

    public static function calculateStationsDeptOffset($line)
    {
        $stationsDeptOffset = [];
        $accumulatedMinutes = 0;
        $stations = $line->stations;
        if ($line->type == LineTypeEnum::reverseTransform(LineTypeEnum::Circle))
        {
            $stations = array_slice($stations, 0, count($stations)-1);
        }
        foreach($stations as $station)
        {
            if (array_key_exists('_id', $station) && array_key_exists('timespan_in_minutes', $station))
            {
                $accumulatedMinutes += $station['timespan_in_minutes'];
                $stationsDeptOffset[$station['_id']] = $accumulatedMinutes;
            }
        }

        return $stationsDeptOffset;
    }
    
    public static function isCircleLineLastStation($line, $stationId)
    {
        $stations = $line->stations;
        if ($line->type == LineTypeEnum::reverseTransform(LineTypeEnum::Circle) && end($stations)['_id'] == $stationId)
        {
            return true;
        }
        return false;
    }
    
}
