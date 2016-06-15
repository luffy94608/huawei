<?php

namespace App\Tools\Rule;

use App\Models\TicketStatusEnum;
use App\Models\Setting;

class RuleEngine
{

    public static function getLinePrice($line, $user)
    {
        $oriPrice = $line->price;

        if (is_null($user)) return $oriPrice;

        $validStatuses = [
            TicketStatusEnum::PAID,
            TicketStatusEnum::CHECKED,
            TicketStatusEnum::REFUND
        ];
        $boughtTicketCount = $user->tickets()->whereIn('status', $validStatuses)->count();

        return $boughtTicketCount > 0 ? $oriPrice : Setting::firstTicketPrice();
    }
}