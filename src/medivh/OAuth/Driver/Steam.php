<?php
/**
 * Created by PhpStorm.
 * User: medivh
 * Date: 18-4-16
 * Time: 下午6:09
 */

namespace medivh\OAuth\Driver;


use medivh\OAuth\OAuthInterface;
use medivh\OAuth\UserInfo\SteamUserInfo;
use medivh\OAuth\UserInfo\UserInfo;

class Steam implements OAuthInterface {

    protected $config = [];

    protected $steamId = '';

    protected $CSRFToken = '';

    public function getOtherAuthorizeInfo(): string {
        return '';
    }

    public function getAuthorizeGateway(): string {
        return 'https://steamcommunity.com/openid/login';
    }

    public function getAuthorizeConfig(): array {
        $this->CSRFToken = uniqid();
        $redirectUri = $this->getConfig('redirect_uri');
        // steam对回调验证没有那么严格，所以还是决定在这里对配置的回调地址做修改添加state字段，用以回调时验证
        if ( strpos($redirectUri, '?') > 1 ){
            $redirectUri .= "&state={$this->CSRFToken}";
        }else{
            $redirectUri .= "?state={$this->CSRFToken}";
        }

        return [
            'openid_ns' =>  "http://specs.openid.net/auth/2.0",
            'openid_mode' =>  "checkid_setup",
            'openid_return_to' => $redirectUri,
            'openid_ns_sreg' => "http://openid.net/extensions/sreg/1.1",
            'openid_claimed_id' => "http://specs.openid.net/auth/2.0/identifier_select",
            'openid_identity' => "http://specs.openid.net/auth/2.0/identifier_select"
        ];
    }

    public function setConfig(array $config): OAuthInterface {
        $this->config = $config;
        return $this;
    }

    public function getConfig(string $key): string {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        throw new ConfigNotFound("配置参数{$key}未找到");
    }

    /**
     * steam 貌似用不到
     * @return string
     */
    public function getAccessTokenUri(): string {
        return 'GET';
    }

    public function getRefreshTokenUri(): string {
        return '';
    }

    public function getAuthAccessTokenUri(): string {
        return '';
    }

    public function getRefreshTokenMethod(): string {
        return 'GET';
    }

    public function getRefreshTokenParams(string $refreshToken): array {
        return [];
    }

    public function getAccessTokenMethod(): string {
        return 'GET';
    }

    public function getUserInfoMethod(): string {
        return 'GET';
    }

    public function getUserInfoUri(): string {
        return 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/';
    }

    private function getSteamId():string {
        if ( empty($this->steamId) && isset($_GET['openid_identity']) ){
            $openIdIdentity = $_GET['openid_identity'];
            $ptn = "/[^https|^http]?:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
            if (preg_match($ptn, $openIdIdentity, $matches)) {
                $this->setOpenId($matches[1]);
            }
        }

        return $this->getOpenId();
    }

    public function getUserInfoParams(): array {
        return [
            'key' => $this->getConfig('appid'),
            'steamids' => $this->getSteamId()
        ];
    }

    public function setAccessTokenInfo(string $accessTokenInfo): OAuthInterface {
        return $this;
    }

    public function getAccessTokenParams(): array {
        return [];
    }

    public function generateUserInfo(string $response): UserInfo {
        return SteamUserInfo::decode(json_decode($response, true));
    }

    public function getOpenId(): string {
        return $this->steamId;
    }

    public function setOpenId(string $openId): OAuthInterface {
        $this->steamId = $openId;
        return $this;
    }

    public function setAccessToken(string $accessToken): OAuthInterface {
        return $this;
    }

    public function getAccessToken(): string {
        return '';
    }

    public function getCSRFToken(): string {
        return $this->CSRFToken;
    }

    public function getAccessTokenInfo(string $key = ''): string {
        return '';
    }


}