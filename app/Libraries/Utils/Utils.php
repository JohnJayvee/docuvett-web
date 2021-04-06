<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 13/11/2017
 * Time: 1:28 AM
 */

namespace App\Libraries\Utils;

use Carbon\Carbon;
use Illuminate\Http\Request;
use JWTAuth;

class Utils
{
    /**
     * @return \App\Models\User\User|null
     */
    public static function getCurrentUser()
    {
        $user = null;
        /* First of all try to auth though Token */
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
        }

        /* If Token user is not available try to get authorisation through AuthFactory */
        if (!$user) {
            $user = auth()->user();
        }

        return $user;
    }

    /**
     * @return int|null
     */
    public static function getCurrentUserId()
    {
        $curUser = self::getCurrentUser();

        return $curUser ? $curUser->id : null;
    }

    /**
     * @return string
     */
    public static function getSystemEmailFooter()
    {
        return "<br><br>Please note, this is a system generated email. If you need assistance, please contact the " . config('app.name') . " team by emailing - <a href='mailto:" . config('mail.support.address') . "'>" . config('mail.support.address') . "</a>. We will respond as soon as possible.";
    }

    /**
     * Generate UUID
     *
     * @return string
     */
    public static function uuid() {
        $uuid = array(
            'time_low'  => 0,
            'time_mid'  => 0,
            'time_hi'  => 0,
            'clock_seq_hi' => 0,
            'clock_seq_low' => 0,
            'node'   => array()
        );

        $uuid['time_low'] = mt_rand(0, 0xffff) + (mt_rand(0, 0xffff) << 16);
        $uuid['time_mid'] = mt_rand(0, 0xffff);
        $uuid['time_hi'] = (4 << 12) | (mt_rand(0, 0x1000));
        $uuid['clock_seq_hi'] = (1 << 7) | (mt_rand(0, 128));
        $uuid['clock_seq_low'] = mt_rand(0, 255);

        for ($i = 0; $i < 6; $i++) {
            $uuid['node'][$i] = mt_rand(0, 255);
        }

        $guid = sprintf('%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
            $uuid['time_low'],
            $uuid['time_mid'],
            $uuid['time_hi'],
            $uuid['clock_seq_hi'],
            $uuid['clock_seq_low'],
            $uuid['node'][0],
            $uuid['node'][1],
            $uuid['node'][2],
            $uuid['node'][3],
            $uuid['node'][4],
            $uuid['node'][5]
        );

        return $guid;
    }

    /**
     * Function is little bit masking ID
     *
     * @param int|string $id
     *
     * @return string
     */
    public static function encodeId($id)
    {
        return bin2hex(base64_encode($id));
    }

    /**
     * Unmask ID number
     *
     * @param $masked_id
     *
     * @return null|string
     */
    public static function decodeId($masked_id)
    {
        $result = null;
        try {
            $base64 = hex2bin($masked_id);
            if ($base64) {
                $id = base64_decode($base64);
                if ($id) {
                    $result = $id;
                }
            }
        } catch (\Exception $e) {
        }

        return $result;
    }

    /**
     * Convert time HH:ii:ss to seconds
     *
     * @param $time
     *
     * @return int
     */
    public static function timeToSeconds($time) {
        list($h, $m, $s) = explode(':', $time);
        return ($h * 3600) + ($m * 60) + $s;
    }

    /**
     * Convert seconds to time HH:ii:ss
     *
     * @param $seconds
     *
     * @return string
     */
    public static function secondsToTime($seconds) {
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds - ($h * 3600) - ($m * 60);
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }


    /**
     * @param Request $request
     *
     * @return Request
     */
    public static function updateRequestPhone(Request $request) {
        try {
            $phone = phone($request->phone, ['AUTO', 'AU'], 'E164');
            $request->merge(['phone' => $phone]);
        } catch (\Exception $e) {
        }
        return $request;
    }

    public static function getHumanDateTime($date, $dateOnly = false)
    {
        $time = Carbon::parse($date);
        $today = Carbon::today();

        if ($time->greaterThan($today) && $dateOnly) {
            return $time->format('g:i A');
        } else {
            return $time->format(' M j');
        }
    }

    public static function getWeekPeriod(int $week = 0)
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now();

        if($week) {
            if($week > 0) {
                $startDate->addWeek($week);
                $endDate->addWeek($week);
            } elseif ($week < 0) {
                $startDate->subWeek(abs($week));
                $endDate->subWeek(abs($week));
            }
        }

        $monday = $startDate->startOfWeek();
        $sunday = $endDate->endOfWeek();

        return (object)['monday' => $monday, 'sunday' => $sunday];
    }

    /**
     * @param float $a
     * @param float $b
     *
     * @return int
     */
    public static function getPercentDifference(float $a, float $b)
    {
        // strict comparison of the value with the float type behaves unusually,
        // to exclude unforeseen situations we bring the type to int
        if (intval($a) === 0) {
            return 100;
        }
        return intval(round((($b/$a)-1)*100));
    }

    /**
     * @param array $array
     * @return array
     */
    public static function convertArrayToObject(array $array): array
    {
        return json_decode(json_encode($array));
    }
}
