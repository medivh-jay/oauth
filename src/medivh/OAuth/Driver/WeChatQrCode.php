<?php
/**
 * Created by PhpStorm.
 * User: medivh
 * Date: 18-4-13
 * Time: 下午5:06
 */

namespace medivh\OAuth\Driver;


use medivh\OAuth\ConfigNotFound;
use medivh\OAuth\OAuthInterface;
use medivh\OAuth\UserInfo;

class WeChatQrCode implements OAuthInterface {

    /**
     * @var string
     */
    protected $authorizeURL = 'https://open.weixin.qq.com/connect/qrconnect';

    /**
     * @var string
     */
    protected $accessTokenURL = 'https://api.weixin.qq.com/sns/';

    /**
     * @var array
     */
    protected $accessTokenInfo = [];

    /**
     * @var array
     */
    protected $config = [];

    protected $accessToken = '';

    protected $openId = '';

    protected $CSRFToken = '';

    /**
     * @return string
     */
    public function getOtherAuthorizeInfo(): string {
        return '#wechat_redirect';
    }

    /**
     * @return string
     */
    public function getAuthorizeGateway(): string {
        return $this->authorizeURL;
    }

    /**
     * @return array
     * @throws ConfigNotFound
     */
    public function getAuthorizeConfig(): array {
        $this->CSRFToken = uniqid();

        return [
            'appid' => $this->getConfig('appid'),
            'redirect_uri' => $this->getConfig('redirect_uri'),
            'response_type' => $this->getConfig('response_type'),
            'scope' => $this->getConfig('scope'),
            'state' => $this->CSRFToken
        ];
    }

    /**
     * @param array $config
     * @return OAuthInterface
     */
    public function setConfig(array $config): OAuthInterface {
        $this->config = $config;
        return $this;
    }

    /**
     * @param string $key
     * @return string
     * @throws ConfigNotFound
     */
    public function getConfig(string $key): string {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        throw new ConfigNotFound("配置参数{$key}未找到");
    }

    /**
     * @return string
     */
    public function getAccessTokenUri(): string {
        return $this->accessTokenURL .'oauth2/access_token';
    }

    /**
     * @return string
     */
    public function getRefreshTokenUri(): string {
        return $this->accessTokenURL. 'oauth2/refresh_token';
    }

    /**
     * @return string
     */
    public function getAuthAccessTokenUri():string {
        return $this->accessTokenURL. 'auth';
    }

    /**
     * @return string
     */
    public function getRefreshTokenMethod(): string {
        return 'GET';
    }

    /**
     * @return string
     */
    public function getAccessTokenMethod(): string {
        return 'GET';
    }

    /**
     * @return string
     */
    public function getUserInfoMethod(): string {
        return 'GET';
    }

    /**
     * @return string
     */
    public function getUserInfoUri(): string {
        return $this->accessTokenURL .'userinfo';
    }

    /**
     * @return array
     */
    public function getUserInfoParams(): array {
        return [
            'access_token' => $this->accessToken,
            'openid' => $this->getOpenId(),
        ];
    }

    /**
     * @param array $accessTokenInfo
     * @return OAuthInterface
     */
    public function setAccessTokenInfo(string $accessTokenInfo): OAuthInterface {
        $this->accessTokenInfo = json_decode($accessTokenInfo, true);
        $this->accessToken = $this->accessTokenInfo['access_token'];
        $this->openId = $this->accessTokenInfo['openid'];
        return $this;
    }

    /**
     * @param string $refreshToken
     * @return array
     * @throws ConfigNotFound
     */
    public function getRefreshTokenParams(string $refreshToken): array {
        return [
            'appid' => $this->getConfig('appid'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken
        ];
    }

    /**
     * @return array
     * @throws ConfigNotFound
     */
    public function getAccessTokenParams(): array {
        return [
            'appid' => $this->getConfig('appid'),
            'secret' => $this->getConfig('secret'),
            'code' => $_GET['code'],
            'grant_type' => 'authorization_code'
        ];
    }

    /**
     * @param array $response
     * @return UserInfo\UserInfo
     */
    public function generateUserInfo(string $response): UserInfo\UserInfo {
        return UserInfo\WxUserInfo::decode(json_decode($response, true));
    }

    public function getOpenId(): string {
        return $this->openId;
    }

    public function setOpenId(string $openId): OAuthInterface {
        $this->openId = $openId;
        return $this;
    }

    public function setAccessToken(string $accessToken): OAuthInterface {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function getAccessToken(): string {
        return $this->accessToken;
    }

    public function getCSRFToken(): string {
        return $this->CSRFToken;
    }

    public function getAccessTokenInfo(string $key = ''): string {
        if (empty($key))
            return $this->accessTokenInfo['access_token'];

        return $this->accessTokenInfo[$key];
    }

}