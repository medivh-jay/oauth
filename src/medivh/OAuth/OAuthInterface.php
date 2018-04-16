<?php
/**
 * Created by PhpStorm.
 * User: medivh
 * Date: 18-4-12
 * Time: 下午5:26
 */

namespace medivh\OAuth;

use medivh\OAuth\UserInfo\UserInfo;


/**
 * OAuth认证
 * Interface OAuthInterface
 * @package vApp\lib\OAuth
 */
interface OAuthInterface {

    /**
     * 获取认证链接的其他信息，需要是个字符串，将被直接拼接在url之后
     * @return string
     */
    public function getOtherAuthorizeInfo(): string;

    /**
     * 获取认证链接的根接口地址
     * @return string
     */
    public function getAuthorizeGateway(): string;

    /**
     * 获取拼接认证地址需要的参数
     * @return array
     */
    public function getAuthorizeConfig(): array;

    /**
     * @param array $config
     * @return OAuthInterface
     */
    public function setConfig(array $config): OAuthInterface;

    /**
     * @param string $key
     * @return string
     */
    public function getConfig(string $key): string;

    /**
     * 获取access_token的地址
     * @return string
     */
    public function getAccessTokenUri(): string;

    /**
     * 获取刷新access_token的地址
     * @return string
     */
    public function getRefreshTokenUri(): string;

    /**
     * 获取
     * @return string
     */
    public function getAuthAccessTokenUri(): string;

    /**
     * 获取刷新access_token的请求方式
     * @return string
     */
    public function getRefreshTokenMethod():string ;

    /**
     * 获取刷新access_token需要的参数
     * @param string $refreshToken
     * @return array
     */
    public function getRefreshTokenParams(string $refreshToken):array ;

    /**
     * 获取请求access_token的请求方式
     * @return string
     */
    public function getAccessTokenMethod(): string;

    /**
     * 获取请求用户信息的请求方式
     * @return string
     */
    public function getUserInfoMethod(): string;

    /**
     * 获取请求用户信息的地址
     * @return string
     */
    public function getUserInfoUri(): string;

    /**
     * 获取请求用户信息需要的参数
     * @return array
     */
    public function getUserInfoParams(): array;

    /**
     * 将得到的access_token信息写入到实例中，项目里自行保存
     * @param array $accessTokenInfo
     * @return OAuthInterface
     */
    public function setAccessTokenInfo(string $accessTokenInfo): OAuthInterface;

    /**
     * 获取请求access_token的参数
     * @return array
     */
    public function getAccessTokenParams(): array;

    /**
     * 生成用户信息
     * @param string $response
     * @return UserInfo
     */
    public function generateUserInfo(string $response): UserInfo;

    /**
     * 得到openid
     * @return string
     */
    public function getOpenId():string ;

    /**
     * 手动设置openid
     * @param string $openId
     * @return OAuthInterface
     */
    public function setOpenId(string $openId):OAuthInterface;

    /**
     * 手动设置accessToken
     * @param string $accessToken
     * @return OAuthInterface
     */
    public function setAccessToken(string $accessToken):OAuthInterface;

    /**
     * 得到accessToken
     * @return string
     */
    public function getAccessToken():string ;

    /**
     * 得到验证码
     * @return string
     */
    public function getCSRFToken():string ;

    /**
     * 子类继承的时候，第三方接口一般在获取access_token时会附带其他信息，有些信息我们可能在某些时候也是需要的，需要实现这个接口
     * 如果传入为空建议直接返回 access_token 串， 否则按第三方的接口返回的信息返回指定字段的值
     * @param string $key
     * @return string
     */
    public function getAccessTokenInfo(string $key = ''):string ;

}