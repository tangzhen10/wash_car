# Project Document 

## 基础信息

> 测试地址：

### header参数

|     参数名     |   类型   | 是否必填 | 缺省值                                      | 描述                                       |
| :---------: | :----: | :--: | :--------------------------------------- | :--------------------------------------- |
|  weblogid   | string |  N   | 例：0v5dktq69qfgh02n782aeass95             | 用户登陆状态验证用                                |
|    appv     | string |  N   | 例：1.0                                    | App的版本号                                  |
|   cityid    |  int   |  N   | 例：1                                      | 城市id,默认为1上海                              |
|    lang     | string |  N   | 例：zh                                     | 语言参数,默认为zh中文                             |
| User-Agent  | string |  Y   | 例：FieldsChina\|iPhone\|3.0\|zh\|iPhone 6\|iOS 10.2.1 | 请求终端信息，必须含有FieldsChina，FieldsChina\|渠道\|App版本\|语言\|设备型号\|操作系统 |
|    vfrom    | string |  Y   | 例：iPad                                   | 用于部分活动促销等限制客户端来源                         |
| devicetoken | string |  Y   | 例：7c49b14ed692acff43988fec95xxx          | 设备token                                  |


### body参数

| 参数名  | 类型     | 是否必填 | 缺省值                              | 描述   |
| ---- | ------ | ---- | -------------------------------- | ---- |
| sign | string | 是    | 470c6aa19a99126e9ae7c8f935c07a3b | 签名参数 |



```php
加密过程

1.将数组形式的post参数以升序排列数组
2.json encode post数组
3.拼接 请求url+?data=json
4.base64 url
5.md5 base64

例：
$post = array(
	'account'  => '326101710@qq.com',
	'password' => 'abc123123',
	'param1'   => 'value1',
	'a_param1' => 'a_val',
);

# 将数组升序排列
ksort($post);

$post = array(
	'a_param1' => 'a_val',
	'account'  => '326101710@qq.com',
	'param1'   => 'value1',
	'password' => 'abc123123',
	);
#$post_param = {"a_param1":"a_val","account":"326101710@qq.com","param1":"value1","password":"abc123123"}
# 将数组 json转码
$post_param = json_encode($post);

# 请求url + data 参数（即$post_param）
$url = 'http://api.dev.fieldschina.com/1.0/Customer/login?data=';

# 先base64加密 再 md5 加密
$md5 = md5(base64_encode($url.$post_param));

$md5即是sign的参数值

```





### 请求示例

> api.dev.fieldschina.com/1.0/Customer/login
>
> 版本号：1.0
>
> 模块：Customer
>
> 功能：login
>
> 参数：post传输数据，header数据

### 公共返回码

| code(返回码) | error(返回码描述)   |
| --------- | -------------- |
| 0         | 业务请求成功         |
| 10403     | 权限不足           |
| 10404     | 不存在的服务         |
| 10405     | 拒绝请求 通常是请求方式错误 |
| 20000     | 该接口服务已停止       |
| 20001     | 未登录            |
| 20002     | 权限不足           |
| 40001     | 缺少必填参数         |
| 40002     | 非法参数           |
| 40003     | 非法请求           |
| 40004     | 业务处理失败         |
| 40006     | 暂不支持的功能        |
| 42003     | 应用程序bug        |
| 50001     | 条件不满足（业务请求成功）  |



### 公共返回json

| 字段名                  | 类型     | 是否必填 | 描述                            | 示例值     |
| -------------------- | ------ | ---- | ----------------------------- | ------- |
| status               | string | 是    | 状态码字段                         | 0~40004 |
| error                | string | 是    | 错误提示消息,当status不等于0时存在该字段error | 请求失败    |
| data                 | array  | 否    | 返回数据数组                        |         |
| has_update           | array  | 否    | 强制更新数组                        |         |
| ├ url                | string | 否    | 更新地址                          |         |
| └ msg                | string | 否    | 提示信息                          |         |
| ├ global_alert       | array  | 否    | 全局弹窗数组(优先级高于首页alert)          |         |
| ├ alert              | array  | 否    | 返回数据数组                        |         |
| └ image              | string | 否    | 弹窗图片                          |         |
| ├ event              | array  | 否    | 返回数据数组                        |         |
| ├ route              | string | 否    | 回调路由                          |         |
| └ param              | string | 否    | 回调参数                          |         |
| └ cart_product_count | string | 是    | 购物车商品数量                       | 10      |
| └ has_notify         | string | 是    | 是否有新的消息 1:有 0:无               | 1       |


### 公共返回header

| 字段名          | 类型     | 是否必填 | 描述                 | 示例值                        |
| ------------ | ------ | ---- | ------------------ | -------------------------- |
| weblogid     | string | 是    | 会话id               | vscit3phk7b29g9orgl7ei5fi3 |
| logged       | string | 是    | 1.已登陆 0.未登陆        | 1                          |
| X-Powered-By | string | 是    | 返回节点信息 目前2个节点01，03 | Fields IT01                |


## 接口统一返回格式(json)

```json
// 有错误，code > 0
{
    "code": 40002,
    "error": "此邮箱已被注册！"
}

// 无错误，code = 0
{
    "code": 0,
    "data": {
        "account": "1s55lxt@q.qq",
        "password": "123123",
        "identityType": "email"
    }
}
```



### Error Code

| code  | description | comment       |
| ----- | ----------- | ------------- |
| 40001 | 缺少参数        | 包括字段为空        |
| 40002 | 目标数据已存在     | 账号已注册         |
| 40003 | 非法参数        | 长度、格式等不正确     |
| 40004 | 业务处理失败      | 操作失败，请稍后重试    |
| 40005 | 未登录         | 未登录，请先登录      |
| 50001 | 操作失败        | 条件不满足（业务请求成功） |

### 账户模块 user

##### 注册

| URL                        | HTTP请求方式 | 是否需要登陆 |
| -------------------------- | -------- | ------ |
| {domain}/api/user/register | POST     | 否      |

> 请求参数

| 字段名      | 类型     | 是否必填 | 描述   | 示例值    |
| -------- | ------ | ---- | ---- | ------ |
| account  | string | 是    | 用户名  | ahulxt |
| password | string | 是    | 密码   | 123123 |

> 响应示例

```json
{
  "status": "0",
  "data": {
    "msg": "注册成功"
  }
}
```



##### 登录

| URL                     | HTTP请求方式 | 是否需要登陆 |
| ----------------------- | -------- | ------ |
| {domain}/api/user/login | POST     | 否      |

> 请求参数

| 字段名      | 类型     | 是否必填 | 描述   | 示例值    |
| -------- | ------ | ---- | ---- | ------ |
| account  | string | 是    | 用户名  | ahulxt |
| password | string | 是    | 密码   | 123123 |

> 响应参数

| 字段名           | 类型     | 是否必填 | 描述     | 示例值                                      |
| ------------- | ------ | ---- | ------ | ---------------------------------------- |
| user_id       | int    | 是    | 用户id   | 19                                       |
| nickname      | string | 是    | 用户昵称   | wm                                       |
| phone         | string | 是    | 手机号    | 18512345678                              |
| email         | string | 是    | 邮箱     | 1552655742@qq.com                        |
| gender        | string | 是    | 性别     | 性别 0未知 1男 2女                             |
| avatar        | string | 是    | 头像     | http://www.wmlxt.top/image/beian_police.png |
| birthday      | string | 是    | 生日     | 1992-09-21                               |
| last_login_at | string | 是    | 最近登录时间 | 2018-06-29 14:50:00                      |
| last_login_ip | string | 是    | 最近登录ip | 127.0.0.1                                |
| token         | string | 是    | 登录令牌   | b13ef1152c...b446c9b5b35d6987365e        |

> 响应示例
```json
{
    "code": 0,
    "data": {
        "user_id": 19,
        "nickname": "wm",
        "phone": "",
        "email": "",
        "gender": "0",
        "avatar": "",
        "birthday": 0,
        "last_login_at": "2018-06-29 14:50:00",
        "last_login_ip": "127.0.0.1",
        "token": "b13ef1152c4e7539947b14defb446c9b5b35d6987365e"
    }
}
```

### 工具模块 tool

##### 发送短信验证码

| URL                           | HTTP请求方式 | 是否需要登陆 |
| ----------------------------- | -------- | ------ |
| {domain}/api/tool/sendSMSCode | POST     | 否      |

> 请求参数

| 字段名      | 类型     | 是否必填 | 描述   | 示例值                      |
| -------- | ------ | ---- | ---- | ------------------------ |
| phone    | string | 是    | 手机号码 | 18512345678              |
| use_type | string | 是    | 用途   | 仅支持：register\|bind_phone |

> 响应示例

```json

```



