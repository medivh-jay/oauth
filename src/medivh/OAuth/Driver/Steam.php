<?php
/**
 * Created by PhpStorm.
 * User: medivh
 * Date: 18-4-16
 * Time: 下午6:09
 */

namespace medivh\OAuth\Driver;


use GuzzleHttp\Client;
use medivh\OAuth\OAuthInterface;
use medivh\OAuth\UserInfo\SteamUserInfo;
use medivh\OAuth\UserInfo\UserInfo;

class Steam implements OAuthInterface {

    protected $config = [];

    protected $steamId = '';

    protected $CSRFToken = '';

    protected $validateUri = 'https://steamcommunity.com/openid/login';

    public function getOtherAuthorizeInfo(): string {
        return '';
    }

    public function getAuthorizeGateway(): string {
        return 'https://steamcommunity.com/openid/login';
    }

    public function getAuthorizeConfig(): array {
        $this->CSRFToken = uniqid();
        $redirectUri = $this->getConfig('redirect_uri');
        // 这里会携带一个state字段，可以验证正确性，也可以不验证
        if (strpos($redirectUri, '?') > 1) {
            $redirectUri .= "&state={$this->CSRFToken}";
        } else {
            $redirectUri .= "?state={$this->CSRFToken}";
        }

        return [
            'openid_ns' => 'http://specs.openid.net/auth/2.0',
            'openid_mode' => 'checkid_setup',
            'openid_return_to' => $redirectUri,
            'openid_ns_sreg' => 'http://openid.net/extensions/sreg/1.1',
            'openid_claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid_identity' => 'http://specs.openid.net/auth/2.0/identifier_select'
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

    private function getSteamId(): string {
        if (empty($this->steamId) && $this->getRequestParam('openid_identity') !== '') {
            $openIdIdentity = $this->getRequestParam('openid_identity');
            $ptn = '/[^https|^http]?:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/';
            if (preg_match($ptn, $openIdIdentity, $matches)) {
                $this->setOpenId($matches[1]);
            }
        }

        return $this->getOpenId();
    }

    /**
     * 验证请求正确性
     * @return bool
     */
    public function validate(): bool {
        $params = [
            'openid.assoc_handle' => $this->getRequestParam('openid_assoc_handle'),
            'openid.signed' => $this->getRequestParam('openid_signed'),
            'openid.sig' => $this->getRequestParam('openid_sig'),
            'openid.ns' => $this->getRequestParam('openid_ns'),
            'openid.op_endpoint' => $this->getRequestParam('openid_op_endpoint'),
            'openid.claimed_id' => $this->getRequestParam('openid_claimed_id'),
            'openid.identity' => $this->getRequestParam('openid_identity'),
            'openid.return_to' => $this->getRequestParam('openid_return_to'),
            'openid.response_nonce' => $this->getRequestParam('openid_response_nonce'),
            'openid.mode' => 'check_authentication'
        ];

        $client = new Client;
        $response = $client->request('POST', $this->validateUri, ['form_params' => $params]);
        $contents = $response->getBody()->getContents();

        return boolval(preg_match('/is_valid\s*:\s*true/i', $contents));
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
        if ($this->getRequestParam('openid_mode') === 'id_res' && $this->validate()) {
            return SteamUserInfo::decode(json_decode($response, true));
        } else {
            if ( $this->getOpenId() !== '' )
                return SteamUserInfo::decode(json_decode($response, true));

            return new SteamUserInfo;
        }
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

    /**
     * 获取steam回调回来之后携带的参数
     * @param string $key
     * @return string
     */
    public function getRequestParam(string $key): string {
        $params = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : $_GET;
        if (array_key_exists($key, $params)) {
            return $params[$key];
        }

        return '';
    }

}