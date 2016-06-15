<?php

namespace App\Models\Enums;


class ErrorEnum
{
    const InvalidToken              = -10001;  //Token无效
    const TokenExpired              = -10002;  //Token过期

    const Success                               = 0;
    const Failed                                = -1;
    const UserOrPswError                        = 10001;
    const UserNotExistError                     = 10002;
    const RemindMinutesNotAvailableError        = 10003;



    public static function transform($errCode)
    {
        $transformMap = array(
            ErrorEnum::InvalidToken             => "Token无效",
            ErrorEnum::TokenExpired             => "Token过期",

            ErrorEnum::Success                  => "操作成功",
            ErrorEnum::Failed                   => "操作失败",
            ErrorEnum::UserOrPswError           => "帐号或密码错误",
            ErrorEnum::UserNotExistError        => "帐号不存在",
            ErrorEnum::RemindMinutesNotAvailableError        => "未过催单时间",

        );
        return $transformMap[$errCode];
    }
}
