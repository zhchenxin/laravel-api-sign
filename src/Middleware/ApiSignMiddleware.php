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

    protected $sign;

    public function __construct(ConfigContract $config, Sign $sign)
    {
        // 合同配置
        if (!$config->has('apisign')) {
            $config->set('apisign', require __DIR__ . '/../../config/apisign.php');
        }

        $this->token = $config->get('apisign.token', '');
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
        if ($request->isMethod('GET') || $request->isMethod('DEELTE')) {
            $params = $request->query();
        } else {
            $params = $request->query();
            if (!strpos($request->header('content-type'), 'multipart/form-data')) {
                $params['body'] = md5($request->getContent());
            }
        }
        $this->sign->checkSign($params, $this->token);
        return $next($request);
    }
}