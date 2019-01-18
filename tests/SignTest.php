<?php

namespace Zhchenxin\ApiSign\Test;

use Zhchenxin\ApiSign\Sign;

class SignTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var Sign
     */
    private $sign;

    public function setUp()
    {
        parent::setUp();
        $this->sign = new Sign();
    }

    /**
     * @test
     */
    public function create_sign()
    {
        $params = [
            '11' => '22',
            '33' => '44',
        ];

        $token = '123243435';

        $params = $this->sign->appendSign($params, $token);
        try {
            $this->sign->checkSign($params, $token);
            $this->assertTrue(true, '验证成功');
        } catch (\Exception $exception) {
            $this->assertTrue(false, '验证失败');
        }

    }
}