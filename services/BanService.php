<?php

namespace app\services;

use app\models\Ban;

class BanService
{
    public static function getExpireData(Ban $ban): string
    {
        switch ($ban->ban_length) {
            case 0:
                $result = Ban::FOREVER;
                break;
            case -1:
                $result = Ban::UNBANNED;
                break;
            default:
                $expireUnixTime = $ban->ban_created + $ban->ban_length * 60;
                if ($expireUnixTime < time()) {
                    $result = Ban::EXPIRED;
                } else {
                    $result = date('d.m.Y H:i', $expireUnixTime);
                }
        }

        return $result;
    }

    public static function isActive(Ban $ban): bool
    {
        if ($ban->ban_length === 0) {
            return true;
        } elseif ($ban->ban_length === -1) {
            return false;
        } else {
            $expireUnixTime = $ban->ban_created + $ban->ban_length * 60;
            if ($expireUnixTime < time()) {
                return false;
            } else {
                return true;
            }
        }
    }
}
