<?php
/**
 * Created by PhpStorm.
 * User: medivh
 * Date: 18-4-16
 * Time: 下午6:26
 */

namespace medivh\OAuth\UserInfo;


class SteamUserInfo extends UserInfo {

    /**
     * Steam 支持同时获取多个用户信息, 这里不支持
     * @param array $data
     * @return UserInfoInterface
     */
    public static function decode(array $data): UserInfoInterface {
        $players = array_key_exists('response', $data) ? $data['response'] : [];
        $player = array_pop($players['players']);

        $data = is_array($player) ? $player : [];

        $user = new SteamUserInfo;
        $user->openId = array_key_exists('steamid', $data) ? $data['steamid'] : '';
        $user->unionId = '';
        $user->nickname = array_key_exists('personaname', $data) ? $data['personaname'] : '';
        $user->gender = '';
        $user->channel = 'steam';
        $user->avatar = array_key_exists('avatarfull', $data) ? $data['avatarfull'] : '';

        return $user;
    }
}