<?php
/**
 * Created by PhpStorm.
 * User: medivh
 * Date: 18-4-13
 * Time: 上午11:22
 */

namespace medivh\OAuth\UserInfo;


abstract class UserInfo implements UserInfoInterface {

    /**
     * 第三方的snsId
     * @var string
     */
    public $openId;

    /**
     * 第三方的联合id，如果有的话
     * @var string
     */
    public $unionId = '';

    /**
     * 渠道
     * @var string
     */
    public $channel;

    /**
     * 昵称
     * @var string
     */
    public $nickname;

    /**
     * 性别
     * @var string
     */
    public $gender;

    /**
     * 性别
     * @var string
     */
    public $avatar;

}