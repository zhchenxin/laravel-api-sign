<?php

namespace Zhchenxin\ApiSign\Middleware;

use Closure;
use Illuminate\Http\Request;
use Zhchenxin\ApiSign\Exception\SignException;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Zhchenxin\ApiSign\Sign;

/**
 * 检查 url 签名
 */
class ApiSignMiddleware
{
    protected $token;

    protected $api_token;

    protected $sign;

    public function __construct(ConfigContract $config, Sign $sign)
    {
        // 合同配置
        if (!$config->has('api_sign')) {
            $config->set('api_sign', require __DIR__ . '/../../config/api_sign.php');
        }

        $this->token = $config->get('api_sign.token', '');
        $this->api_token = $config->get('api_sign.api_token', '');
        $this->sign = $sign;
    }

    /**
     * @param $request
     * @param Closure $next
     * @return mixed|string
     * @throws SignException
     */
    public function handle(Request $request, Closure $next)
    {
        $params = $request->all();
        $token = $this->token;
        if (!empty($params['source'])) {
            if (!in_array($params['source'], array_keys($this->api_token))) {
                throw new SignException('未知来源的请求');
            }
            $token = $this->api_token[$params['source']];
        }
        $this->sign->checkSign($params, $token);
        return $next($request);
    }
}