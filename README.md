#### QQ ， 微信， 百度 三方网站登录

```
composer require medivh/oauth dev-master 直接下载
```

##### QQ 登录

```php
<?php

require 'vendor/autoload.php';

// 配置信息
$config = [
    'appid' => '申请的appid',
    'secret' => '申请的appKey',
    'redirect_uri' => '跳转地址',
    'response_type' => 'code',
    'display' => 'default', // 分 default 和 mobile
    'scope' => 'get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo,check_page_fans,add_t,add_pic_t,del_t,get_repost_list,get_info,get_other_info,get_fanslist,get_idolist,add_idol,del_idol,get_tenpay_addr' // 这里可以固定成这个
];
```

##### 得到认证对象
Driver命名空间下提供了部分认证驱动类，亦可以自己实现，只要继承了OAuthInterface接口，
都可以使用OAuth来调用

```php
$oAuth = \medivh\OAuth\OAuth::register(new \medivh\OAuth\Driver\QQ, $config);
```


##### 生成登录地址
```php
$oAuth->getAuthorizeURL();
```

##### 获取access_token

```php
$oAuth->getAccessToken();
```

##### 获取用户信息
```php
// 这个方法可以传入两个参数，openid和access_token 
// 当服务器保存了用户的openid和access_token时，可以在用户登录时直接调用这个方法获取用户信息
$oAuth->getUser();
```

//也可以调用 getOAuth() 获取其他操作，但是完全不必这样
```php
$oAuth->getOAuth();
```

// 登录的生成地址会有一个state字段,QQ 会原样返回，当做csrf_token 验证字段，需要事先记录存入用户本地cookie
// 需要自己存，这里不做cookie设置操作，提供获取方法，生成完登录地址的时候，可以同时调用该方法得到
```php
$oAuth->getCSRFToken();
```

##### 微信登录

###### 微信登录跟QQ使用方法都是一样的，唯一不同的是配置信息

```php
<?php

$config = [
    'appid' => '',
    'secret' => '',
    'redirect_uri' => '回调地址',
    'response_type' => 'code',
    'scope' => 'snsapi_login'
];

$oAuth = \medivh\OAuth\OAuth::register(new \medivh\OAuth\Driver\QQ, $config);

// 这里微信还提供刷新access_token 的方法

$refreshToken = $oAuth->getOAuth()->getAccessTokenInfo('refresh_token'); // 这个值建议在获取access_token的时候直接获取并保存
$oAuth->refreshToken($refreshToken);
```

##### 百度登录

###### 百度登录跟微信QQ差不多，不过不需要调用太多东西

```php

<?php

$config = [
    'appid' => '',
    'secret' => '',
    'redirect_uri' => '跳转地址',
    'display' => 'popop'
];

$oAuth = \medivh\OAuth\OAuth::register(new \medivh\OAuth\Driver\BaiDu, $config);

// 这个方法在回调地址使用，用户登录过后，直接回调之后使用这个方法获取用户信息就好了
var_dump($oAuth->getUser());

//登录地址
$uri = $oAuth->getAuthorizeURL();

```

##### STEAM登录

###### Steam 官方提供的第三方登录使用的LightOpenID代码，所以Steam登录略有不同

```php
<?php


require 'vendor/autoload.php';

// steam

$config = [
    'appid' => '在steam申请的key',
    'redirect_uri' => 'steam登录成功之后的回调地址'
];

$oAuth = \medivh\OAuth\OAuth::register(new \medivh\OAuth\Driver\Steam, $config);

// 不需要调用其他方法，可能会报错

// 获取用户信息

// 还是调用该方法生成登录地址
$oAuth->getAuthorizeURL();


// Steam貌似没看到什么获取access_token的东西，所以Driver里没有配置
// 这里如果调用该方法时，第二个参数一定要写点儿什么，否则会去调用获取access_token的方法
// 第一个参数需要填写steamid，不填会根据回调返回的steamid去获取该steamid用户信息
$oAuth->getUser('', 'xxx'); 
```