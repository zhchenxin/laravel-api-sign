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
- body: 如果是 POST 请求，则需要添加此参数，数值为请求的 body 的 md5 哈希值

> 如果请求的 content-type=form-data ，则不需要添加 body 参数

(2) 排序

将 url 的参数按照键名排序，排序的字符串获取其 md5 哈希值，赋予 sign，并将其添加到参数中。

(3) 移除参数

将 token 参数移除，然后将所有的参数发送到服务器中。

下面使用 node.js

## 例子

通用的方法：

```js
function sortDict(dict)
{
    var dict2 = {},
        keys = Object.keys(dict).sort();

    for (var i = 0, n = keys.length, key; i < n; ++i) {
        key = keys[i];
        dict2[key] = dict[key];
    }

    return dict2;
}

function md5(str)
{
    return crypto.createHash('md5').update(str).digest('hex');
}
```

### GET 请求

```js
var params = {
    page: 1,
    count: 10
};
params.stamp = parseInt(Date.now() / 1000);
params.nonce = Math.random().toString(36).substr(2);;
params.token = 'D8PMQ1BHYCGbvVxcScLrjRi3fbq7OkOP';
params = sortDict(params);
params['sign'] = md5(querystring.stringify(params));
delete params.token;

console.log('请求参数：');
console.log(params);
axios.get('/finance/transaction?' + querystring.stringify(params))
    .then(function (response) {
        console.log('返回数据');
        console.log(response.data);
    })
    .catch(function (error) {
        console.log(error);
    });
```

### POST 请求

```js
var body = {
    mobile: '18515220153',
    password: '123456'
};
var params = {
    stamp: parseInt(Date.now() / 1000),
    nonce: Math.random().toString(36).substr(2),
    token: 'D8PMQ1BHYCGbvVxcScLrjRi3fbq7OkOP',
    body: md5(JSON.stringify(body)),
};
params = sortDict(params);
params['sign'] = md5(querystring.stringify(params));
delete params.token;

console.log('请求参数：');
console.log(params);
axios({
    method: 'POST',
    data: body,
    url: '/auth/login?' + querystring.stringify(params),
}).then(function (response) {
    console.log('返回数据');
    console.log(response.data);
}).catch(function (error) {
    console.log(error);
});
```

### form-data

```js
var params = {
    stamp: parseInt(Date.now() / 1000),
    nonce: Math.random().toString(36).substr(2),
    token: 'D8PMQ1BHYCGbvVxcScLrjRi3fbq7OkOP',
};

params = sortDict(params);
params['sign'] = md5(querystring.stringify(params));
delete params.token;

console.log('请求参数：');
console.log(params);

var data = new FormData();
data.append('image', 'ADD');

axios({
    method: 'POST',
    data: data,
    url: '/common/upload/image?' + querystring.stringify(params),
    headers: {
        'Content-Type': 'multipart/form-data'
    }
}).then(function (response) {
    console.log('返回数据');
    console.log(response.data);
}).catch(function (error) {
    console.log(error);
});
```
