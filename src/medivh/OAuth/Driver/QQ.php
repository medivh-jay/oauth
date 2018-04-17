<?php
/**
 * Created by PhpStorm.
 * User: medivh
 * Date: 18-4-16
 * Time: 上午10:35
 */

namespace medivh\OAuth\Driver;


use GuzzleHttp\Client;
use medivh\OAuth\ConfigNotFound;
use medivh\OAuth\OAuthInterface;
use medivh\OAuth\UserInfo\QqUserInfo;
use medivh\OAuth\UserInfo\UserInfo;

class QQ implements OAuthInterface {

    /**
     * @var array
     */
    protected $authorizeUri = [
        'default' => 'https://graph.qq.com/oauth2.0/authorize',
        'mobile' => 'https://graph.z.qq.com/moc2/authorize '
    ];

    protected $AccessTokenURL = [
        'default' => 'https://graph.qq.com/oauth2.0/token',
        'mobile' => 'https://graph.z.qq.com/moc2/token'
    ];

    protected $userOpenId = [
        'default' => 'https://graph.qq.com/oauth2.0/me',
        'mobile' => 'https://graph.z.qq.com/moc2/me'
    ];

    protected $userInfoUri = 'https://graph.qq.com/user/get_user_info';

    protected $config = [];

    protected $openId = '';

    protected $accessToken = '';

    protected $CSRFToken = '';

    /**
     * @var array
     */
    protected $accessTokenInfo = [];

    public function getOtherAuthorizeInfo(): string {
        return '';
    }

    public function getAuthorizeGateway(): string {
        return $this->authorizeUri[$this->getConfig('display')];
    }

    public function getAuthorizeConfig(): array {
        $this->CSRFToken = uniqid();

        return [
            'response_type' => $this->getConfig('response_type'),
            'client_id' => $this->getConfig('appid'),
            'redirect_uri' => $this->getConfig('redirect_uri'),
            'state' => $this->CSRFToken,
            'scope' => $this->getConfig('scope'),
            'display' => $this->getConfig('display'),
            'format' => 'json',
            'g_ut' => 2
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

    public function getAccessTokenUri(): string {
        return $this->AccessTokenURL[$this->getConfig('display')];
    }

    public function getRefreshTokenUri(): string {
        return $this->AccessTokenURL[$this->getConfig('display')];
    }

    public function getAuthAccessTokenUri(): string {
        return '';
    }

    public function getRefreshTokenMethod(): string {
        return 'GET';
    }

    public function getRefreshTokenParams(string $refreshToken): array {
        return [
            'grant_type' => 'refresh_token',
            'client_id' => $this->getConfig('appid'),
            'client_secret' => $this->getConfig('secret'),
            'refresh_token' => $this->accessTokenInfo['refresh_token']
        ];
    }

    public function getAccessTokenMethod(): string {
        return 'GET';
    }

    public function getUserInfoMethod(): string {
        return 'GET';
    }

    public function getUserInfoUri(): string {
        return $this->userInfoUri;
    }

    public function getUserInfoParams(): array {
        return [
            'access_token' => $this->accessToken,
            'oauth_consumer_key' => $this->getConfig('appid'),
            'openid' => $this->getOpenId(),
            'format' => 'json'
        ];
    }

    public function setAccessTokenInfo(string $accessTokenInfo): OAuthInterface {
        parse_str($accessTokenInfo, $this->accessTokenInfo);
        $this->setAccessToken($this->accessTokenInfo['access_token']);
        return $this;
    }

    public function getAccessTokenParams(): array {
        return [
            'grant_type' => 'authorization_code',
            'client_id' => $this->getConfig('appid'),
            'client_secret' => $this->getConfig('secret'),
            'code' => $_GET['code'],
            'redirect_uri' => $this->getConfig('redirect_uri'),
            'format' => 'json',
        ];
    }

    public function generateUserInfo(string $response): UserInfo {
        $info = json_decode($response, true);
        $info = array_merge($info, ['openid' => $this->getOpenId()]);
        return QqUserInfo::decode($info);
    }

    public function getOpenId(): string {
        if ( empty($this->openId) ){
            $params = ['access_token' => $this->accessTokenInfo['access_token']];
            $client = new Client;
            $response = $client->request('GET', $this->userOpenId[$this->getConfig('display')], ['query' => $params]);
            $resultContents = $response->getBody()->getContents();

            //--------检测错误是否发生
            if(strpos($resultContents, 'callback') !== false){
                $leftParenthesis = strpos($resultContents, '(');
                $rightParenthesis = strrpos($resultContents, ')');
                $result = substr($resultContents, $leftParenthesis + 1, $rightParenthesis - $leftParenthesis -1);

                $resultArr = json_decode($result, true);
                return empty($resultArr['openid']) ? '' : $resultArr['openid'];
            }
        }

        return $this->openId;
    }

    public function setOpenId(string $openId):OAuthInterface {
        $this->openId = $openId;
        return $this;
    }

    public function setAccessToken(string $accessToken):OAuthInterface {
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