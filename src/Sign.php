<?php

namespace Zhchenxin\ApiSign;

use Zhchenxin\ApiSign\Exception\SignException;

class Sign
{
    public function appendSign($params, $token)
    {
        if (empty($params['stamp'])) {
            $params['stamp'] = time();
        }

        $params['token'] = $token;
        $params['sign'] = $this->_createSign($params);

        unset($params['token']);
        return $params;
    }

    /**
     * @param $params
     * @param $token
     * @return mixed
     * @throws SignException
     */
    public function checkSign($params, $token)
    {
        if (array_key_exists('token', $params)) {
            throw new SignException('should not have token params');
        }

        $this->_checkStamp($params['stamp']);
        $this->_checkSign($params, $token);
    }

    /**
     * 检查时间戳
     * @param $stamp
     * @throws SignException
     */
    private function _checkStamp($stamp)
    {
        // 5分钟以内有效
        if (abs(time() - $stamp) > 5*60) {
            throw new SignException('stamp error');
        }
    }

    /**
     * @param $params
     * @param $token
     * @throws SignException
     */
    private function _checkSign($params, $token)
    {
        $sign = $params['sign'];            // 加密签名
        unset($params['sign']);

        // 增加token
        $params['token'] = $token;
        if(strtoupper($this->_createSign($params)) != strtoupper($sign)){
            throw new SignException('sign error');
        }
    }

    private function _createSign($params)
    {
        ksort($params);
        $sign_str = collect($params)->map(function($value, $key) {
            return "$key=$value";
        })->implode('&');
        return md5($sign_str);
    }
}