# Project Document 

## 基础信息

> 测试地址：https://m.wmlxt.top/

### header参数

|    参数名     |   类型   | 是否必填 | 缺省值                                      | 描述                                       |
| :--------: | :----: | :--: | :--------------------------------------- | :--------------------------------------- |
|   token    | string |  N   | 例：0v5dktq69qfgh02n782aeass95             | 用户登陆状态验证用                                |
|    lang    | string |  N   | 例：zh_CN                                  | 语言参数,默认为中文                               |
| User-Agent | string |  Y   | 例：FieldsChina\|iPhone\|3.0\|zh\|iPhone 6\|iOS 10.2.1 | 请求终端信息，必须含有FieldsChina，FieldsChina\|渠道\|App版本\|语言\|设备型号\|操作系统 |
|   vfrom    | string |  Y   | 例：iPad                                   | 用于部分活动促销等限制客户端来源                         |


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

> 地址：https://m.wmlxt.top/api/car/brand
>
> 模块：Car
>
> 功能：brand
>
> 参数：post传输数据，header数据

### 公共返回码

| code  | description | comment       |
| ----- | ----------- | ------------- |
| 0     | 业务请求成功      |               |
| 40001 | 缺少参数        | 包括字段为空        |
| 40002 | 目标数据已存在     | 账号已注册         |
| 40003 | 非法参数        | 长度、格式等不正确     |
| 40004 | 业务处理失败      | 操作失败，请稍后重试    |
| 40005 | 未登录         | 未登录，请先登录      |
| 40006 | 权限不足        | 权限不足，拒绝访问     |
| 40007 | 页面不存在       | 页面不存在，路由未配权限  |
| 50001 | 操作失败        | 条件不满足（业务请求成功） |

### 公共返回json

| 字段名   | 类型     | 是否必填 | 描述                        | 示例值     |
| ----- | ------ | ---- | ------------------------- | ------- |
| code  | int    | 是    | 状态码字段                     | 0~40004 |
| error | string | 是    | 错误提示消息,当code>0时存在该字段error | 请求失败    |
| data  | object | 是    | code=0时，返回数据对象            | json对象  |


### 公共返回header

| 字段名 | 类型   | 是否必填 | 描述              | 示例值                                 |
| ------ | ------ | -------- | ----------------- | -------------------------------------- |
| token  | string | 是       | 登录令牌          | b13ef1152c4e7539947b14defbb35d6987365e |
| login  | string | 是       | 1.已登录 0.未登录 | 1                                      |


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

## 接口 api

### 账户 user

##### 注册

| URL                        | HTTP请求方式 | 是否需要登陆 |
| -------------------------- | -------- | ------ |
| {domain}/api/user/register | POST     | 否      |

> 请求参数

| 字段名         | 类型     | 是否必填 | 描述   | 示例值    |
| ----------- | ------ | ---- | ---- | ------ |
| account     | string | 是    | 用户名  | ahulxt |
| verify_code | string | 是    | 验证码  | 192113 |
| password    | string | 是    | 密码   | 123pwd |

> 响应示例

```json
// 成功
{
  "code": 0,
  "data": {
    "msg": "注册成功！"
  }
}
// 失败
{
    "code": 40002,
    "error": "此手机号码已被注册！"
}
```



##### 登录

| URL                     | HTTP请求方式 | 是否需要登陆 |
| ----------------------- | -------- | ------ |
| {domain}/api/user/login | POST     | 否      |

> 请求参数

| 字段名      | 类型     | 是否必填 | 描述   | 示例值         |
| -------- | ------ | ---- | ---- | ----------- |
| account  | string | 是    | 用户名  | 18612172345 |
| password | string | 是    | 密码   | 123123      |

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

### 车辆  car

##### 我的车辆

| URL                    | HTTP请求方式 | 是否需要登陆 |
| ---------------------- | ------------ | ------------ |
| {domain}/api/car/myCar | POST         | 是           |

> 请求参数

| 字段名 | 类型 | 是否必填 | 描述 | 示例值 |
| ------ | ---- | -------- | ---- | ------ |
|        |      |          |      |        |

> 响应参数

| 字段名 | 类型   | 是否必填 | 描述   | 示例值   |
| ------ | ------ | -------- | ------ | -------- |
| car_id | int    | 是       | 车辆id | 2        |
| brand  | string | 是       | 品牌   | 宝骏     |
| model  | string | 是       | 车型   | 宝骏630  |
| color  | string | 是       | 颜色   | 黄色     |
| plate  | string | 是       | 车牌号 | 京A234FH |

> 响应示例

```json
{
    "code": 0,
    "data": {
        "list": [
            {
                "car_id": 1,
                "brand": "奥迪",
                "model": "宝骏630",
                "color": "蓝色",
                "plate": "京A234FH"
            },
            {
                "car_id": 2,
                "brand": "奥迪",
                "model": "宝骏630",
                "color": "蓝色",
                "plate": "京BG0201"
            }
        ]
    }
}
```

##### 保存车辆

| URL                   | HTTP请求方式 | 是否需要登陆 |
| --------------------- | ------------ | ------------ |
| {domain}/api/car/save | POST         | 是           |

> 请求参数

| 字段名 | 类型 | 是否必填 | 描述 | 示例值 |
| ------ | ---- | -------- | ---- | ------ |
|        |      |          |      |        |

> 响应参数

| 字段名 | 类型 | 是否必填 | 描述 | 示例值 |
| ------ | ---- | -------- | ---- | ------ |
|        |      |          |      |        |

> 响应示例

##### 删除车辆

| URL                     | HTTP请求方式 | 是否需要登陆 |
| ----------------------- | ------------ | ------------ |
| {domain}/api/car/delete | POST         | 是           |

> 请求参数

| 字段名 | 类型 | 是否必填 | 描述           | 示例值 |
| ------ | ---- | -------- | -------------- | ------ |
| car_id | int  | 是       | 要删除的车辆id | 3      |

> 响应参数

| 字段名 | 类型 | 是否必填 | 描述 | 示例值 |
| ------ | ---- | -------- | ---- | ------ |
|        |      |          |      |        |

> 响应示例

```json
{
    "code": 0,
    "msg": "ok"
}
```

#####品牌
| URL                    | HTTP请求方式 | 是否需要登陆 |
| ---------------------- | -------- | ------ |
| {domain}/api/car/brand | POST     | 是      |

> 请求参数

| 字段名      | 类型   | 是否必填 | 描述      | 示例值  |
| -------- | ---- | ---- | ------- | ---- |
| brand_id | int  | 否    | 选择的品牌id | 12   |

> 响应参数

| 字段名            | 类型     | 是否必填 | 描述        | 示例值  |
| -------------- | ------ | ---- | --------- | ---- |
| hot            | array  | 是    | 热门品牌      | 同all |
| all            | array  | 是    | 所有品牌      |      |
| L id           | int    | 是    | 品牌id      | 12   |
| L name         | string | 是    | 品牌名称      | 宝马   |
| L hot          | int    | 是    | 热度值       | 10   |
| L first_letter | string | 是    | 品牌中文名称首字母 | B    |

> 响应示例

```json
{
    "code": 0,
    "data": {
        "hot": [
            {
                "id": 12,
                "name": "宝马",
                "logo": "http://www.wash.com/src/car/brand/BMW.png",
                "hot": 10,
                "first_letter": "B"
            },
            {
                "id": 182,
                "name": "大众",
                "logo": "http://www.wash.com/src/car/brand/Volkswagen.png",
                "hot": 10,
                "first_letter": "D"
            },
            {
                "id": 183,
                "name": "沃尔沃",
                "logo": "http://www.wash.com/src/car/brand/Volvo.png",
                "hot": 10,
                "first_letter": "W"
            },
            {
                "id": 19,
                "name": "凯迪拉克",
                "logo": "http://www.wash.com/src/car/brand/Cadillac.png",
                "hot": 5,
                "first_letter": "K"
            },
            {
                "id": 6,
                "name": "奥迪",
                "logo": "http://www.wash.com/src/car/brand/Audi.png",
                "hot": 4,
                "first_letter": "A"
            }
        ],
        "all": [
            {
                "title": "A",
                "list": [
                    {
                        "id": 6,
                        "name": "奥迪",
                        "logo": "http://www.wash.com/src/car/brand/Audi.png",
                        "hot": 4,
                        "first_letter": "A"
                    },
                    {
                        "id": 2,
                        "name": "阿尔法·罗密欧",
                        "logo": "http://www.wash.com/src/car/brand/Alfa Romeo.png",
                        "hot": 0,
                        "first_letter": "A"
                    },
                    {
                        "id": 133,
                        "name": "奥斯莫比尔",
                        "logo": "http://www.wash.com/src/car/brand/Oldsmobile.png",
                        "hot": 0,
                        "first_letter": "A"
                    }
                ]
            },
            {
                "title": "Z",
                "list": [
                    {
                        "id": 191,
                        "name": "中华",
                        "logo": "http://www.wash.com/src/car/brand/Zhonghua.png",
                        "hot": 0,
                        "first_letter": "Z"
                    },
                    {
                        "id": 193,
                        "name": "中裕",
                        "logo": "http://www.wash.com/src/car/brand/Zhongyu.png",
                        "hot": 0,
                        "first_letter": "Z"
                    },
                    {
                        "id": 195,
                        "name": "众泰",
                        "logo": "http://www.wash.com/src/car/brand/Zotye.png",
                        "hot": 0,
                        "first_letter": "Z"
                    }
                ]
            }
        ]
    }
}
```

##### 车型

| URL                    | HTTP请求方式 | 是否需要登陆 |
| ---------------------- | -------- | ------ |
| {domain}/api/car/model | POST     | 是      |

> 请求参数

| 字段名      | 类型   | 是否必填 | 描述      | 示例值  |
| -------- | ---- | ---- | ------- | ---- |
| brand_id | int  | 是    | 选择的品牌id | 12   |
| model_id | int  | 否    | 选择的车型id | 166  |

> 响应参数

| 字段名  | 类型     | 是否必填 | 描述   | 示例值   |
| ---- | ------ | ---- | ---- | ----- |
| id   | int    | 是    | 车型id | 166   |
| name | string | 是    | 车型名称 | 宝骏630 |

> 响应示例	

```json
{
    "code": 0,
    "data": {
        "list": [
            {
                "id": 166,
                "name": "宝骏630"
            },
            {
                "id": 1441,
                "name": "宝骏530"
            },
            {
                "id": 1475,
                "name": "宝骏360"
            }
        ]
    }
}
```



##### 省份

| URL                       | HTTP请求方式 | 是否需要登陆 |
| ------------------------- | -------- | ------ |
| {domain}/api/car/province | POST     | 是      |

> 请求参数

| 字段名         | 类型   | 是否必填 | 描述           | 示例值  |
| ----------- | ---- | ---- | ------------ | ---- |
| province_id | int  | 否    | 选择省份id，提交时必填 | 22   |

> 响应参数

| 字段名  | 类型     | 是否必填 | 描述   | 示例值  |
| ---- | ------ | ---- | ---- | ---- |
| id   | int    | 是    | 省份id | 2    |
| name | string | 是    | 省份简称 | 沪    |

> 响应示例

```json
{
    "code": 0,
    "data": {
        "list": [
            {
                "id": 1,
                "name": "京"
            },
            {
                "id": 2,
                "name": "沪"
            },
            {
                "id": 3,
                "name": "浙"
            },
            {
                "id": 30,
                "name": "赣"
            },
            {
                "id": 31,
                "name": "湘"
            }
        ]
    }
}
```

##### 颜色

| URL                    | HTTP请求方式 | 是否需要登陆 |
| ---------------------- | -------- | ------ |
| {domain}/api/car/color | POST     | 是      |

> 请求参数

| 字段名      | 类型   | 是否必填 | 描述            | 示例值  |
| -------- | ---- | ---- | ------------- | ---- |
| color_id | int  | 否    | 选择的颜色id，提交是必填 | 7    |

> 响应参数

| 字段名  | 类型     | 是否必填 | 描述       | 示例值      |
| ---- | ------ | ---- | -------- | -------- |
| id   | int    | 是    | 颜色id     | 1        |
| name | string | 是    | 颜色名称     | 红        |
| code | string | 是    | 颜色码（RGB） | \#ff0000 |

> 响应示例

```json
# 固定返回12个值，其中包括其他
# 其他颜色，id=0，颜色码为空，客户端不显示预览

{
    "code": 0,
    "data": {
        "list": [
            {
                "id": 12,
                "name": "橙色",
                "code": "#F9901E"
            },
            {
                "id": 11,
                "name": "银色",
                "code": "#D3D3D3"
            },
            {
                "id": 10,
                "name": "灰色",
                "code": "#6D6D6D"
            },
            {
                "id": 9,
                "name": "紫色",
                "code": "#B071FE"
            },
            {
                "id": 7,
                "name": "白色",
                "code": "#FFFFFF"
            },
            {
                "id": 6,
                "name": "黑色",
                "code": "#000000"
            },
            {
                "id": 5,
                "name": "米色",
                "code": "#F9E6D3"
            },
            {
                "id": 4,
                "name": "黄色",
                "code": "#FFFF00"
            },
            {
                "id": 3,
                "name": "蓝色",
                "code": "#266EF1"
            },
            {
                "id": 2,
                "name": "绿色",
                "code": "#226500"
            },
            {
                "id": 1,
                "name": "红色",
                "code": "#FF0000"
            },
            {
                "id": 0,
                "name": "其他",
                "code": ""
            }
        ]
    }
}
```


##### 车牌省份

| URL                       | HTTP请求方式 | 是否需要登陆 |
| ------------------------- | ------------ | ------------ |
| {domain}/api/car/province | POST         | 是           |

> 请求参数

| 字段名 | 类型 | 是否必填 | 描述 | 示例值 |
| ------ | ---- | -------- | ---- | ------ |
|        |      |          |      |        |

> 响应参数

| 字段名 | 类型 | 是否必填 | 描述 | 示例值 |
| ------ | ---- | -------- | ---- | ------ |
|        |      |          |      |        |

> 响应示例

```json

```

##### 车牌省份

| URL                       | HTTP请求方式 | 是否需要登陆 |
| ------------------------- | ------------ | ------------ |
| {domain}/api/car/province | POST         | 是           |

> 请求参数

| 字段名 | 类型 | 是否必填 | 描述 | 示例值 |
| ------ | ---- | -------- | ---- | ------ |
|        |      |          |      |        |

> 响应参数

| 字段名 | 类型 | 是否必填 | 描述 | 示例值 |
| ------ | ---- | -------- | ---- | ------ |
|        |      |          |      |        |

> 响应示例

```json

```

## 页面 web

### 用户

#### 个人中心

> 接入微信，可自动注册、自动登录，并展示基本信息

## 后台 admin

### 会员管理

> 会员即前台用户，用户的部分信息只允许用户自己修改，如验证的手机和邮箱（可用于登录）

### 文档管理

### 管理员管理

> 管理员可以有多个角色，管理员的权限 = 所拥有的每个角色的权限的并集

### 权限管理

#### 权限

* 菜单权限：可见且可访问

#### 角色

> 多角色系统，每个角色关联多个权限

#### 备注
> \vendor\laravel\framework\src\Illuminate\Database\Connectors\MySqlConnector.php
> NO_AUTO_CREATE_USER可能不可用，在不适用的服务器上删除这个值

