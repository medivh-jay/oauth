<?php
/**
 * Created by PhpStorm.
 * User: medivh
 * Date: 18-4-13
 * Time: 下午6:00
 */

namespace medivh\OAuth\UserInfo;


class WxUserInfo extends UserInfo {

    public static function decode(array $data): UserInfoInterface {

        $user = new WxUserInfo;

        $user->avatar = !array_key_exists('headimgurl', $data) ? '' : $data['headimgurl'];
        $user->channel = 'wx';
        $user->gender = !array_key_exists('sex', $data) ? '' : $data['sex'];;
        $user->nickname = !array_key_exists('nickname', $data) ? '' : $data['nickname'];
        $user->openId = !array_key_exists('openid', $data) ? '' : $data['openid'];
        $user->unionId = !array_key_exists('unionid', $data) ? '' : $data['unionid'];

        return $user;
    }


}