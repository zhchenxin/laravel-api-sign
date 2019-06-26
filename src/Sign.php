<?php

namespace Zhchenxin\ApiSign;

use Zhchenxin\ApiSign\Exception\SignException;

class Sign
{
    /**
     * 给参数添加 sign, stamp
     * @param $params
     * @param $token
     * @return mixed
     */
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
     * @throws SignException
     */
    public function checkSign($params, $token)
    {
        if (array_key_exists('token', $params)) {
            throw new SignException('签名不存在');
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
            throw new SignException('请求无效');
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

        // 如果不是线上环境, 并且有debug参数, 则打印出参数内容
        if ('production' != env('APP_ENV') && !empty($params['_debug'])) {
            unset($params['_debug']);
            echo "sign string: " . $this->_getSignStr($params) . PHP_EOL;
            echo "client sign: {$sign}" . PHP_EOL;
            echo "server sign: " . $this->_createSign($params) . PHP_EOL;
            die;
        }

        if($this->_getSignStr($params) != $sign){
            throw new SignException('签名异常');
        }
    }

    private function _createSign($params)
    {
        return md5($this->_getSignStr($params));
    }

    private function _getSignStr($params)
    {
        ksort($params);
        return collect($params)->map(function($value, $key) {
            return "$key=$value";
        })->implode('&');
    }
}