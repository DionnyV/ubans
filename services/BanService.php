<?php

namespace app\services;

use app\models\Ban;
use app\models\form\BanForm;
use DateTime;
use Exception;

/**
 * Сервис для работы с банами.
 */
class BanService
{
    /**
     * Сохраняет бан.
     *
     * @param BanForm $form
     */
    public function save(BanForm $form): void
    {
        $form->save(false);
    }

    /**
     * Устанавливает статус "Разбанен" для бана.
     *
     * @param Ban $ban
     */
    public function unban(Ban $ban): void
    {
        $ban->ban_length = -1;
        $ban->save();
    }

    /**
     * Возвращает бан по идентификатору.
     *
     * @param $id
     * @return Ban
     * @throws Exception
     */
    public function getById($id): Ban
    {
        $model = Ban::findOne($id);
        if ($model === null) {
            throw new Exception('Бан не найден.');
        }
        return $model;
    }

    /**
     * Расчитывает продолжительность бана в минутах.
     *
     * @param Ban $ban
     * @param DateTime $until
     * @return int
     */
    public function calculateBanLength(Ban $ban, $until): int
    {
        $until = strtotime($until);
        if ($until) {
            $until = abs(round(($until - $ban->ban_created) / 60));
        }
        return $until;
    }

    /**
     * Возвращает дату истечения бана.
     *
     * @param Ban $ban
     * @return string
     */
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

    /**
     * Проверяет активность бана.
     *
     * @param Ban $ban
     * @return bool
     */
    public static function isActive(Ban $ban): bool
    {
        if ($ban->ban_length === 0) {
            $isActive = true;
        } elseif ($ban->ban_length === -1) {
            $isActive = false;
        } else {
            $expireUnixTime = $ban->ban_created + $ban->ban_length * 60;
            if ($expireUnixTime < time()) {
                $isActive = false;
            } else {
                $isActive = true;
            }
        }
        return $isActive;
    }
}
