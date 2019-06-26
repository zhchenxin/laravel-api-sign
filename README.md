## 初始化

运行命令：

```sh
composer require zhchenxin/api-sign
```

在 Kernel.php 中添加中间件

```php
<?php

protected $middlewareGroups = [
    'api' => [
        ApiSign::class,
    ],
    'web' => [
    ]
];

```

## 签名过程

(1) 在url的参数中添加几个参数：

- stamp: 当前的时间戳
- token: 前后端约定好的 token

(2) 排序

将 url 的参数按照键名排序，排序的字符串获取其 md5 哈希值，赋予 sign，并将其添加到参数中。

(3) 移除参数

将 token 参数移除，然后将所有的参数发送到服务器中。

备注:

在非线上环境, 可以添加 _debug=1 参数来调试签名, 添加了 _debug 参数之后, 会返回

```
sign string: server sign 加密的原始字符串
client sign: 客户端传进来的签名
server sign: 服务端的签名
```

## 多签名模式

一个模块如果提供给多端使用, 可以给每一个端提供不同的签名, 例如ios签名是A, Android签名是B, H5签名是C, 这样来做区分, 也方便临时关闭某一个入口.

这时, 需要将配置文件 `apisign.php` 复制到config目录下, 配置下面相关内容

```
<?php

return [
    'token' => env('URL_SIGN_TOKEN', ''),

    'api_token' => [
        'ios' => 'sm6O#^FODZqq&nG4',
        'android' => 'ncODna0!zec8gTtS',
        'h5' => 'MV1htlLROYLi^*sZ',
    ],
];
```

同时, 在客户端签名的时候, 需要增加 source 字段来表明自己的来源.

例如, ios端 source=ios, android端 source=android, H5端 source=h5
