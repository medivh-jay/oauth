<?php
/**
 * Created by PhpStorm.
 * User: medivh
 * Date: 18-4-12
 * Time: 下午5:54
 */

namespace medivh\OAuth;
use GuzzleHttp\Client;
use medivh\OAuth\UserInfo\UserInfo;
use medivh\OAuth\UserInfo\UserInfoInterface;


/**
 * 三方认证
 * Class OAuth
 * @package vApp\lib\OAuth
 */
class OAuth {

    /**
     * @var OAuthInterface
     */
    protected static $instance;

    /**
     * 注册第三方OAuth认证操作实例
     * @param OAuthInterface $OAuthDriver 第三方OAuth认证实例
     * @param array $config 可能需要的配置参数
     * @return OAuth
     */
    public static function register(OAuthInterface $OAuthDriver, array $config = []):OAuth {
        $OAuth = new OAuth;
        static::$instance = $OAuthDriver->setConfig($config);
        return $OAuth;
    }

    /**
     * 对该实例设置配置参数
     * @param array $config
     */
    public function setConfig(array $config) {
        $this->getOAuth()->setConfig($config);
    }

    /**
     * 获取实例
     * @return OAuthInterface
     */
    public function getOAuth():OAuthInterface {
        return static::$instance;
    }

    /**
     * 获取登录地址
     * @return string
     */
    public function getAuthorizeURL():string {
        $params = http_build_query($this->getOAuth()->getAuthorizeConfig());
        return $this->getOAuth()->getAuthorizeGateway() . '?' . $params . $this->getOAuth()->getOtherAuthorizeInfo();
    }

    /**
     * 获取用户信息
     * @param string$openId 用户openid，当本地存有openid或者unionid时直接使用openid和access_token请求用户数据更快一点儿
     * @return UserInfoInterface
     */
    public function getUser(string $openId = '', string $accessToken = ''):UserInfo {
        $this->getOAuth()->setOpenId($openId)->setAccessToken($accessToken);

        if ( empty($accessToken) ){
            $this->getAccessToken();
        }

        $response = $this->request(
            $this->getOAuth()->getUserInfoMethod(),
            $this->getOAuth()->getUserInfoUri(),
            $this->getOAuth()->getUserInfoParams()
        );

        $userInfo = $this->getOAuth()->generateUserInfo($response);

        return $userInfo;
    }

    /**
     * 获取access_token信息
     * @return string
     */
    public function getAccessToken(): string {

        if ( !empty($this->getOAuth()->getAccessToken()) ){
            return $this->getOAuth()->getAccessToken();
        }

        $response = $this->request(
            $this->getOAuth()->getAccessTokenMethod(),
            $this->getOAuth()->getAccessTokenUri(),
            $this->getOAuth()->getAccessTokenParams()
        );
        $this->getOAuth()->setAccessTokenInfo($response);

        return $this->getOAuth()->getAccessToken();
    }

    /**
     * 刷新access_token得到新的
     * @param string $refreshToken
     * @return array
     */
    public function refreshToken(string $refreshToken):string {
        $response = $this->request(
            $this->getOAuth()->getRefreshTokenMethod(),
            $this->getOAuth()->getRefreshTokenUri(),
            $this->getOAuth()->getRefreshTokenParams($refreshToken)
        );

        $this->getOAuth()->setAccessTokenInfo($response);
        return $response;
    }

    public function getCSRFToken():string {
        return $this->getOAuth()->getCSRFToken();
    }

    /**
     * 请求接口
     * @param string $method
     * @param string $uri
     * @param array $params
     */
    public function request(string $method, string $uri, array $params = []):string {
        $client = new Client;
        $response = $client->request(strtoupper($method), $uri, ['query' => $params]);

        return $response->getBody()->getContents();
    }

}