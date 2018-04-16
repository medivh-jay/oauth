<?php
/**
 * Created by PhpStorm.
 * User: medivh
 * Date: 18-4-16
 * Time: 下午5:07
 */

namespace medivh\OAuth\UserInfo;


class BaiDuUserInfo extends UserInfo {
    public static function decode(array $data): UserInfoInterface {
        $user = new BaiDuUserInfo;

        $baseAvatarUri = 'http://himg.bdimg.com/sys/portrait/item/%s.jpg';

        $user->avatar = !array_key_exists('portrait', $data) ? '' : sprintf($baseAvatarUri, $data['portrait']);
        $user->channel = 'baidu';
        $user->gender = !array_key_exists('sex', $data) ? '' : $data['sex'];;
        $user->nickname = !array_key_exists('username', $data) ? '' : $data['username'];
        $user->openId = !array_key_exists('userid', $data) ? '' : $data['userid'];
        $user->unionId = !array_key_exists('unionid', $data) ? '' : $data['unionid'];

        return $user;
    }
}