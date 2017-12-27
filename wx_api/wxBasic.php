<?php

namespace weixin;

class wxBasic
{

    static private $appid = 'wxc7dfb473e3b4bb78';
    static private $secret = '1b254b4e44ac3f6ce14bfa7db3b00743';
    static private $self_token = 'mytest';

    static public function getAppid()
    {
        return self::$appid;
    }

    static public function getSecret()
    {
        return self::$secret;
    }

    static public function getSelfToken()
    {
        return self::$self_token;
    }

    static public function getConfig()
    {
        return [
            'appid'=>self::$appid,
            'secret'=>self::$secret,
            'self_token'=>self::$self_token
        ];
    }

}
