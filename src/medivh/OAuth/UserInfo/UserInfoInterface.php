<?php
/**
 * Created by PhpStorm.
 * User: medivh
 * Date: 18-4-13
 * Time: 下午6:00
 */

namespace medivh\OAuth\UserInfo;


interface UserInfoInterface {

    public static function decode(array $data): UserInfoInterface;

}