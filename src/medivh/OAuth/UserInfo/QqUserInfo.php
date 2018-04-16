<?php
/**
 * Created by PhpStorm.
 * User: medivh
 * Date: 18-4-16
 * Time: 上午11:07
 */

namespace medivh\OAuth\UserInfo;


class QqUserInfo extends UserInfo {

    public static function decode(array $data): UserInfoInterface {
        $user = new WxUserInfo;

        $user->avatar = !array_key_exists('figureurl_qq_2', $data) ? $data['figureurl_qq_2'] : $data['figureurl_qq_1'];
        $user->channel = 'qq';
        $user->gender = !array_key_exists('gender', $data) ? '' : $data['gender'];;
        $user->nickname = !array_key_exists('nickname', $data) ? '' : $data['nickname'];
        $user->openId = !array_key_exists('openid', $data) ? '' : $data['openid'];
        $user->unionId = !array_key_exists('unionid', $data) ? '' : $data['unionid'];

        return $user;
    }

}