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

| 字段名   | 类型     | 是否必填 | 描述          | 示例值                                    |
| ----- | ------ | ---- | ----------- | -------------------------------------- |
| token | string | 是    | 登录令牌        | b13ef1152c4e7539947b14defbb35d6987365e |
| login | string | 是    | 1.已登录 0.未登录 | 1                                      |


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

##### 登出

| URL                      | HTTP请求方式 | 是否需要登陆 |
| ------------------------ | ------------ | ------------ |
| {domain}/api/user/logout | POST         | 否           |

> 请求参数

| 字段名 | 类型 | 是否必填 | 描述 | 示例值 |
| ------ | ---- | -------- | ---- | ------ |
|        |      |          |      |        |

> 响应参数

```json
{
    "code": 0,
    "msg": "ok"
}
```

##### 手机号登录

| URL                            | HTTP请求方式 | 是否需要登陆 |
| ------------------------------ | -------- | ------ |
| {domain}/api/user/loginByPhone | POST     | 否      |

> 请求参数

| 字段名         | 类型     | 是否必填 | 描述   | 示例值         |
| ----------- | ------ | ---- | ---- | ----------- |
| account     | string | 是    | 手机号  | 18612172345 |
| verify_code | string | 是    | 验证码  | 283614      |

> 响应参数同 【登录】

##### 小程序端获取openid

| URL                      | HTTP请求方式 | 是否需要登陆 |
| ------------------------ | -------- | ------ |
| {domain}/api/user/openid | POST     | 否      |

> 请求参数

| 字段名  | 类型     | 是否必填 | 描述              | 示例值                              |
| ---- | ------ | ---- | --------------- | -------------------------------- |
| code | string | 是    | wx.login返回的code | 061BjJi10AcOvE16aok10Umzi10BjJih |

> 响应参数

```
{
    "code": 0,
    "data": {
        "session_key": "GQWFsLDNJ4aVpZHAU9kRgQ==",
        "openid": "o2BWA4mDLdp-LxyFz42wy_kfIDR8",
        "create_at": 1533809142
    }
}
```

##### 充值|套餐卡

| URL                                   | HTTP请求方式 | 是否需要登陆 |
| ------------------------------------- | ------------ | ------------ |
| {domain}/api/user/rechargeAndWashCard | POST         | 是           |

> 请求参数

| 字段名  | 类型   | 是否必填 | 描述   | 示例值  |
| ---- | ---- | ---- | ---- | ---- |
|      |      |      |      |      |

> 响应参数

| 字段名          | 类型   | 是否必填 | 描述              | 示例值         |
| --------------- | ------ | -------- | ----------------- | -------------- |
| balance         | string | 是       | 余额，单位：元    | 550.00         |
| washCards       | array  | 是       | 洗车卡列表        |                |
| L id            | int    | 是       | 洗车卡id          | 51             |
| L name          | string | 是       | 洗车卡名称        | 猫头鹰洗车月卡 |
| LL price        | object | 是       | 卡券价格          | 120            |
| LL price_ori    | object | 是       | 卡券原价          | 160            |
| LL expire_date  | object | 是       | 卡券有效期        | 30             |
| LL hot_status   | object | 是       | 是否热销，1是 0否 | 1              |
| LL introduction | object | 是       | 卡券介绍          |                |

> 响应示例

```json
{
    "code": 0,
    "data": {
        "balance": "550.00",
        "washCards": [
            {
                "id": 51,
                "name": "猫头鹰洗车月卡",
                "sub_name": null,
                "detail": {
                    "price": {
                        "text": "价格",
                        "value": "120"
                    },
                    "price_ori": {
                        "text": "原价",
                        "value": "180"
                    },
                    "expire_date": {
                        "text": "有效期",
                        "value": "30"
                    },
                    "hot_status": {
                        "text": "热销券",
                        "value": "1"
                    },
                    "introduction": {
                        "text": "介绍",
                        "value": "没用过，想体验？首选尝鲜套餐！\r\n套餐内容：全外观精洗\r\n使用时间：购买之日起1个月内有效（节假日通用）\r\n使用方法：购买套餐之后，系统将在账号内自动放入洗车券，下单即可使用！"
                    }
                }
            },
            {
                "id": 52,
                "name": "猫头鹰洗车季卡",
                "sub_name": null,
                "detail": {
                    "price": {
                        "text": "价格",
                        "value": "300"
                    },
                    "price_ori": {
                        "text": "原价",
                        "value": "500"
                    },
                    "expire_date": {
                        "text": "有效期",
                        "value": "90"
                    },
                    "hot_status": {
                        "text": "热销券",
                        "value": "0"
                    },
                    "introduction": {
                        "text": "介绍",
                        "value": "季度套餐卡，绝对划算！！！\r\n套餐内容：全外观精洗 + 车窗打蜡\r\n使用时间：购买之日起90天内有效（节假日通用）\r\n使用方法：购买套餐之后，系统将在账号内自动放入洗车券，下单即可使用！"
                    }
                }
            }
        ]
    }
}
```

### 车辆  car

##### 我的车辆

| URL                    | HTTP请求方式 | 是否需要登陆 |
| ---------------------- | -------- | ------ |
| {domain}/api/car/myCar | POST     | 是      |

> 请求参数

| 字段名  | 类型   | 是否必填 | 描述   | 示例值  |
| ---- | ---- | ---- | ---- | ---- |
|      |      |      |      |      |

> 响应参数

| 字段名    | 类型     | 是否必填 | 描述   | 示例值     |
| ------ | ------ | ---- | ---- | ------- |
| car_id | int    | 是    | 车辆id | 2       |
| brand  | string | 是    | 品牌   | 宝骏      |
| model  | string | 是    | 车型   | 宝骏630   |
| color  | string | 是    | 颜色   | 黄色      |
| plate  | string | 是    | 车牌号  | 京A234FH |

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
| --------------------- | -------- | ------ |
| {domain}/api/car/save | POST     | 是      |

> 请求参数

| 字段名          | 类型     | 是否必填 | 描述   | 示例值    |
| ------------ | ------ | ---- | ---- | ------ |
| brand_id     | int    | 是    | 品牌id | 2      |
| model_id     | int    | 是    | 车型id | 166    |
| province_id  | int    | 是    | 省份id | 4      |
| plate_number | string | 是    | 车牌号  | A09534 |
| color_id     | int    | 是    | 颜色id | 3      |


> 响应参数

| 字段名  | 类型   | 是否必填 | 描述   | 示例值  |
| ---- | ---- | ---- | ---- | ---- |
|      |      |      |      |      |

> 响应示例

##### 删除车辆

| URL                     | HTTP请求方式 | 是否需要登陆 |
| ----------------------- | -------- | ------ |
| {domain}/api/car/delete | POST     | 是      |

> 请求参数

| 字段名    | 类型   | 是否必填 | 描述       | 示例值  |
| ------ | ---- | ---- | -------- | ---- |
| car_id | int  | 是    | 要删除的车辆id | 3    |

> 响应参数

| 字段名  | 类型   | 是否必填 | 描述   | 示例值  |
| ---- | ---- | ---- | ---- | ---- |
|      |      |      |      |      |

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

### 订单 order

##### 首页

| URL                      | HTTP请求方式 | 是否需要登陆 |
| ------------------------ | -------- | ------ |
| {domain}/api/order/index | POST     | 否      |

> 请求参数

| 字段名  | 类型   | 是否必填 | 描述   | 示例值  |
| ---- | ---- | ---- | ---- | ---- |
|      |      |      |      |      |

> 响应参数

| 字段名      | 类型     | 是否必填 | 描述       | 示例值  |
| -------- | ------ | ---- | -------- | ---- |
| banners  | array  | 是    | 首页banner |      |
| product  | object | 是    | 服务项目     |      |
| contact  | object | 是    | 联系人      |      |
| car      | object | 是    | 车辆信息     |      |
| total    | int    | 是    | 总金额      |      |
| totalOri | int    | 否    | 原价总金额    |      |

> 响应示例

```json
{
    "code": 0,
    "data": {
        "banners": [
            {
                "id": 48,
                "name": "服务范围介绍",
                "sub_name": null,
                "detail": {
                    "image": "http://www.wash.com/src/upload/image/20180805/153343739525151.png",
                    "link": "https://www.baidu.com"
                }
            },
            {
                "id": 50,
                "name": "活动宣传",
                "sub_name": null,
                "detail": {
                    "image": "http://www.wash.com/src/upload/image/20180804/153337291458660.gif",
                    "link": "https://www.jd.com"
                }
            }
        ],
        "product": {
            "id": 46,
            "name": "全外观清洗"
        },
        "contact": {
            "user": "",
            "phone": "18512174045"
        },
        "car": {
            "id": 51,
            "plate_number": "粤AHULXT",
            "brand": "一汽轿车",
            "model": "",
            "color": "黑色"
        },
        "paymentMethod": [
            "wechat",
            "balance"
        ],
        "total": 128,
        "totalOri": 200,
        "userInfo": {
            "user_id": 45,
            "nickname": "",
            "phone": "185****4045",
            "email": "",
            "gender": "0",
            "avatar": "",
            "birthday": "",
            "country": "",
            "province": "",
            "city": "",
            "language": "zh_CN",
            "create_at": "2018-08-03 15:37:57",
            "last_login_at": "2018-08-11 09:46:05",
            "last_login_ip": "127.0.0.1",
            "gender_text": "",
            "token": "a626a96529fd1ca5eb782c5cd672e3905b6e3fdddd132",
            "balance": "200.00"
        }
    }
}
```

##### 服务列表

| URL                         | HTTP请求方式 | 是否需要登陆 |
| --------------------------- | -------- | ------ |
| {domain}/api/order/washList | POST     | 是      |

> 请求参数

| 字段名  | 类型   | 是否必填 | 描述   | 示例值  |
| ---- | ---- | ---- | ---- | ---- |
|      |      |      |      |      |

> 响应参数

| 字段名    | 类型   | 是否必填 | 描述         | 示例值     |
| --------- | ------ | -------- | ------------ | ---------- |
| id        | int    | 是       | 服务项目id   | 46         |
| name      | string | 是       | 服务项目名称 | 全外观清洗 |
| price     | object | 是       | 价格         |            |
| price_ori | object | 是       | 原价         |            |
| discount  | string | 是       | 折扣         | 6.4折      |

> 响应示例

```json
{
    "code": 0,
    "data": {
        "list": [
            {
                "id": 46,
                "name": "全外观清洗",
                "price": {
                    "text": "价格",
                    "value": "128"
                },
                "price_ori": {
                    "text": "原价",
                    "value": "200"
                },
                "discount": "6.4折"
            },
            {
                "id": 47,
                "name": "车窗打蜡",
                "price": {
                    "text": "价格",
                    "value": "50"
                },
                "price_ori": {
                    "text": "原价",
                    "value": "80"
                },
                "discount": "6.3折"
            }
        ]
    }
}
```

##### 服务详情

| URL                           | HTTP请求方式 | 是否需要登陆 |
| ----------------------------- | -------- | ------ |
| {domain}/api/order/washDetail | POST     | 是      |

> 请求参数

| 字段名  | 类型   | 是否必填 | 描述     | 示例值  |
| ---- | ---- | ---- | ------ | ---- |
| id   | int  | 是    | 服务项目id | 46   |

> 响应参数

| 字段名               | 类型     | 是否必填 | 描述     | 示例值   |
| ----------------- | ------ | ---- | ------ | ----- |
| id                | int    | 是    | 服务项目id | 46    |
| name              | string | 是    | 服务项目名称 | 全外观清洗 |
| detail            | object | 是    | 详情     |       |
| L banner          | object | 是    | 展示图，多张 |       |
| L price           | object | 是    | 价格     |       |
| L price_ori       | object | 是    | 原价     |       |
| L promise         | object | 是    | 保障     |       |
| L service_content | object | 是    | 服务项目内容 |       |
| L sale_count      | object | 是    | 已售单量   |       |

> 响应示例

```json
{
    "code": 0,
    "data": {
        "id": 46,
        "name": "全外观清洗",
        "detail": {
            "banner": {
                "text": "清洗照片",
                "value": [
                    "http://www.wash.com/src/upload/image/20180729/153287033344870.jpg",
                    "http://www.wash.com/src/upload/image/20180729/153287033313320.jpg",
                    "http://www.wash.com/src/upload/image/20180729/153287033352539.jpg"
                ]
            },
            "price": {
                "text": "价格",
                "value": "￥128.00"
            },
            "price_ori": {
                "text": "原价",
                "value": "￥200.00"
            },
            "promise": {
                "text": "保障",
                "value": [
                    "未服务全额退",
                    "爽约包赔",
                    "不满意重服务"
                ]
            },
            "service_content": {
                "text": "服务项目",
                "value": "1.除尘\r\n\r\n2.冲洗车身(大水量尽可能洗掉多余的泥沙）\r\n\r\n3.水蜡泡泡浴（含有蜡分子成分使车辆洗完更亮，而且抗静电更耐脏）\r\n\r\n4.精洗车身\r\n\r\n5.轮毂清洗\r\n\r\n6.擦净车身\r\n\r\n服务时间：35-45分钟\r\n\r\n一流的服务，专业清洗设备，源于传统洗车方式，让您体验到店一样的清洗效果，把您的爱车交给我们来打理！更省时省力更省心。"
            },
            "sale_count": {
                "text": "已售7单",
                "value": 7
            }
        }
    }
}
```
##### 清洗时间

| URL                         | HTTP请求方式 | 是否需要登陆 |
| --------------------------- | -------- | ------ |
| {domain}/api/order/washTime | POST     | 是      |

> 请求参数

| 字段名  | 类型   | 是否必填 | 描述   | 示例值  |
| ---- | ---- | ---- | ---- | ---- |
|      |      |      |      |      |

> 响应参数

| 字段名   | 类型     | 是否必填 | 描述        | 示例值                    |
| ----- | ------ | ---- | --------- | ---------------------- |
| text  | string | 是    | 清洗时间的文字展示 | 今天 21:00-22:00         |
| value | string | 是    | 清洗时间的值    | 2018-08-03 21:00-22:00 |

> 响应示例

```json
{
    "code": 0,
    "data": {
        "list": [
            {
                "text": "今天 21:00-22:00",
                "value": "2018-08-03 21:00-22:00"
            },
            {
                "text": "今天 22:00-23:00",
                "value": "2018-08-03 22:00-23:00"
            },
            {
                "text": "今天 23:00-24:00",
                "value": "2018-08-03 23:00-24:00"
            },
            {
                "text": "明天 00:00-01:00",
                "value": "2018-08-04 00:00-01:00"
            },
            {
                "text": "明天 01:00-02:00",
                "value": "2018-08-04 01:00-02:00"
            }
        ]
    }
}
```
##### 下单

| URL                           | HTTP请求方式 | 是否需要登陆 |
| ----------------------------- | -------- | ------ |
| {domain}/api/order/placeOrder | POST     | 是      |

> 请求参数

| 字段名                | 类型     | 是否必填 | 描述          | 示例值                    |
| ------------------ | ------ | ---- | ----------- | ---------------------- |
| wash_product_id    | int    | 是    | 服务项目id      | 46                     |
| car_id             | int    | 是    | 车辆id        | 2                      |
| address            | string | 是    | 地址          | 公司附近                   |
| address_coordinate | string | 是    | 地址坐标，xy逗号隔开 | 112.23,123.25          |
| contact_user       | string | 是    | 联系人         | 马先生                    |
| contact_phone      | string | 是    | 联系电话        | 18712314393            |
| wash_time          | string | 是    | 清洗时间        | 2018-08-04 00:00-01:00 |

> 响应参数

| 字段名         | 类型     | 是否必填 | 描述      | 示例值          |
| ----------- | ------ | ---- | ------- | ------------ |
| order_id    | string | 是    | 洗车订单号   | 1808032810   |
| success_msg | string | 是    | 下单成功提示语 | 提交成功\n正在为您派单 |

> 响应示例

```json
{
    "code": 0,
    "data": {
        "order_id": "1808032810",
        "success_msg": "提交成功\n正在为您派单"
    }
}
```

##### 洗车订单列表

```
# 订单状态
const ORDER_STATUS = [
    1 => '未付款',
    2 => '等待接单中',
    3 => '已接单',
    4 => '服务中',
    5 => '已完成',
    6 => '已退款',
    7 => '已关闭',
    8 => '申请退款中',
];

# 动作名称
const ORDER_ACTION = [
    'add_order'    => '提交订单',
    'order_pay'    => '订单支付',
    'confirm_pay'  => '确认支付',
    'take_order'   => '派单成功',
    'serve_start'  => '开始服务',
    'serve_finish' => '完成服务',
    'refund_order' => '订单退款',
    'cancel_order' => '取消订单',
    'apply_refund' => '申请退款',
];
```

| URL                     | HTTP请求方式 | 是否需要登陆 |
| ----------------------- | -------- | ------ |
| {domain}/api/order/list | POST     | 是      |

> 请求参数

| 字段名  | 类型   | 是否必填 | 描述         | 示例值  |
| ---- | ---- | ---- | ---------- | ---- |
| page | int  | 是    | 显示的页数，默认为1 | 2    |

> 响应参数

| 字段名          | 类型     | 是否必填 | 描述     | 示例值  |
| ------------ | ------ | ---- | ------ | ---- |
| order_id     | object | 是    | 订单号    |      |
| create_at    | object | 是    | 创建时间   |      |
| wash_product | object | 是    | 清洗服务项目 |      |
| wash_time    | object | 是    | 清洗时间   |      |
| car          | object | 是    | 车辆信息   |      |
| address      | object | 是    | 地址     |      |
| status       | object | 是    | 订单状态   |      |

> 响应示例

```json
{
    "code": 0,
    "data": {
        "list": [
            {
                "order_id": {
                    "text": "订单编号",
                    "value": 1808067316
                },
                "create_at": {
                    "text": "创建时间",
                    "value": "2018-08-06 16:09:35"
                },
                "status": {
                    "text": "订单状态",
                    "value": "未付款",
                    "status": 1
                },
                "wash_product": {
                    "text": "服务项目",
                    "value": "全外观清洗"
                },
                "wash_time": {
                    "text": "清洗时间",
                    "value": "2018-08-06 22:00-23:00"
                },
                "car": {
                    "text": "车辆信息",
                    "value": {
                        "plate_number": "沪EA77M2",
                        "brand": "奥迪",
                        "model": "桑塔纳",
                        "color": "蓝色"
                    }
                },
                "address": {
                    "text": "服务地址",
                    "value": "公司附近"
                }
            },
            {
                "order_id": {
                    "text": "订单编号",
                    "value": 1807318633
                },
                "create_at": {
                    "text": "创建时间",
                    "value": "2018-07-31 18:35:38"
                },
                "status": {
                    "text": "订单状态",
                    "value": "未付款",
                    "status": 1
                },
                "wash_product": {
                    "text": "服务项目",
                    "value": "全外观清洗"
                },
                "wash_time": {
                    "text": "清洗时间",
                    "value": "2018-07-31 22:00-23:00"
                },
                "car": {
                    "text": "车辆信息",
                    "value": {
                        "plate_number": "沪BG0201",
                        "brand": "奥迪",
                        "model": "宝骏630",
                        "color": "蓝色"
                    }
                },
                "address": {
                    "text": "服务地址",
                    "value": "公司附近"
                }
            }
        ]
    }
}
```

##### 洗车订单详情

| URL                       | HTTP请求方式 | 是否需要登陆 |
| ------------------------- | -------- | ------ |
| {domain}/api/order/detail | POST     | 是      |

> 请求参数

| 字段名      | 类型     | 是否必填 | 描述   | 示例值        |
| -------- | ------ | ---- | ---- | ---------- |
| order_id | string | 是    | 订单号  | 1808067316 |

> 响应参数

| 字段名              | 类型     | 是否必填 | 描述     | 示例值                                 |
| ---------------- | ------ | ---- | ------ | ----------------------------------- |
| detail           | object | 是    | 订单信息   |                                     |
| L order_id       | string | 是    |        |                                     |
| L contact_user   | string | 是    | 联系人    | Mr Zhang                            |
| L contact_phone  | string | 是    | 联系号码   | 18712314393                         |
| L address        | string | 是    | 服务地址   | 公司附近                                |
| L wash_time      | string | 是    | 清洗时间   | 2018-08-06 22:00-23:00              |
| L payment_status | string | 是    | 支付状态   | 1支付 0未支付                            |
| L plate_number   | string | 是    | 车牌     | 沪EA77M2                             |
| L brand          | string | 是    | 车辆品牌   | 奥迪                                  |
| L model          | string | 是    | 车辆型号   | 奥迪A6L                               |
| L color          | string | 是    | 车辆颜色   | 黑色                                  |
| L cancel_at      | int    | 是    | 取消时间戳  | 1533546575                          |
| L button         | string | 是    | 操作按钮   | 取消、退款或不存在此字段                        |
| L washer         | string | 否    | 服务人员   | 王梅                                  |
| L washer_phone   | string | 否    | 服务人员电话 | 18716238273                         |
| L status         | int    | 是    | 订单状态   | 1                                   |
| L status_text    | string | 是    | 订单状态描述 | 未付款                                 |
| log              | array  | 是    | 订单操作日志 |                                     |
| L create_at      | string | 是    | 操作时间   | 2018-08-05 14:10:09                 |
| L action_text    | string | 是    | 操作名称   | 派单成功                                |
| L images         | array  |      | 清洗前后照片 |                                     |
| LLL thumb        | string | 否    | 缩略图    | http://www.wash.com/src...34541.png |
| LLL src          | string | 否    | 原图     | http://www.wash.com/sr...634541.png |

> 响应示例

```json
{
    "code": 0,
    "data": {
        "detail": {
            "title": "订单信息",
            "data": {
                "id": 7,
                "order_id": 1808022032,
                "user_id": 45,
                "wash_product_id": 46,
                "contact_user": "李小同",
                "contact_phone": "18512174044",
                "address": "西虹市",
                "wash_time": "2018-07-31 22:00-23:00",
                "payment_status": "1",
                "total": "￥128.00",
                "status": 5,
                "create_at": "2018-08-02 22:44:50",
                "plate_number": "沪EA77M2",
                "brand": "奥迪",
                "model": "桑塔纳",
                "color": "蓝色",
                "username": "无昵称用户",
                "phone": "18512174045",
                "wash_product": "全外观清洗",
                "order_status_msg": "",
                "button": {
                    "text": "退款",
                    "action": "refund"
                },
                "washer": "root",
                "washer_phone": "18512174044",
                "status_text": "已完成"
            }
        },
        "log": {
            "title": "订单进度",
            "data": [
                {
                    "create_at": "2018-08-02 22:44:50",
                    "action_text": "提交订单"
                },
                {
                    "create_at": "2018-08-05 14:10:05",
                    "action_text": "确认支付"
                },
                {
                    "create_at": "2018-08-05 14:10:09",
                    "action_text": "派单成功"
                },
                {
                    "create_at": "2018-08-05 14:58:13",
                    "action_text": "开始服务",
                    "images": {
                        "title": "服务前照片",
                        "images": [
                            {
                                "thumb": "http://www.wash.com/src/upload/image/20180805/thumb_153344944634541.png",
                                "src": "http://www.wash.com/src/upload/image/20180805/153344944634541.png"
                            },
                            {
                                "thumb": "http://www.wash.com/src/upload/image/20180805/thumb_153344944645137.png",
                                "src": "http://www.wash.com/src/upload/image/20180805/153344944645137.png"
                            },
                            {
                                "thumb": "http://www.wash.com/src/upload/image/20180805/thumb_153344944627198.png",
                                "src": "http://www.wash.com/src/upload/image/20180805/153344944627198.png"
                            }
                        ]
                    }
                },
                {
                    "create_at": "2018-08-05 15:03:33",
                    "action_text": "完成服务",
                    "images": {
                        "title": "服务前照片",
                        "images": [
                            {
                                "thumb": "http://www.wash.com/src/upload/image/20180805/thumb_153345346249163.jpg",
                                "src": "http://www.wash.com/src/upload/image/20180805/153345346249163.jpg"
                            },
                            {
                                "thumb": "http://www.wash.com/src/upload/image/20180805/thumb_153345346270944.jpg",
                                "src": "http://www.wash.com/src/upload/image/20180805/153345346270944.jpg"
                            },
                            {
                                "thumb": "http://www.wash.com/src/upload/image/20180805/thumb_153345346270266.jpg",
                                "src": "http://www.wash.com/src/upload/image/20180805/153345346270266.jpg"
                            }
                        ]
                    }
                }
            ]
        }
    }
}
```

##### 修改订单状态

| URL                             | HTTP请求方式 | 是否需要登陆 |
| ------------------------------- | -------- | ------ |
| {domain}/api/order/changeStatus | POST     | 是      |

> 请求参数

| 字段名      | 类型     | 是否必填 | 描述    | 示例值                        |
| -------- | ------ | ---- | ----- | -------------------------- |
| order_id | string | 是    | 订单号   | 1808073410                 |
| action   | string | 是    | 退款或取消 | refund_order\|cancel_order |

> 响应参数

| 字段名  | 类型   | 是否必填 | 描述   | 示例值  |
| ---- | ---- | ---- | ---- | ---- |
|      |      |      |      |      |

> 响应示例

```json
{
    "code": 0,
    "msg": "ok"
}
```

### 工具 tool

##### 发送短信

| URL                           | HTTP请求方式 | 是否需要登陆 |
| ----------------------------- | ------------ | ------------ |
| {domain}/api/tool/sendSMSCode | POST         | 是           |

> 请求参数

| 字段名   | 类型   | 是否必填 | 描述     | 示例值                    |
| -------- | ------ | -------- | -------- | ------------------------- |
| phone    | string | 是       | 手机号码 | 18541234572               |
| use_type | string | 是       | 用途类型 | login_by_phone,register等 |

> 响应参数

| 字段名  | 类型   | 是否必填 | 描述   | 示例值  |
| ---- | ---- | ---- | ---- | ---- |
|      |      |      |      |      |

> 响应示例

```json
{
    "code": 0,
    "msg": "ok"
}
```

##### 充值

| URL                        | HTTP请求方式 | 是否需要登陆 |
| -------------------------- | ------------ | ------------ |
| {domain}/api/tool/recharge | POST         | 是           |

> 请求参数

| 字段名  | 类型   | 是否必填 | 描述   | 示例值  |
| ---- | ---- | ---- | ---- | ---- |
|      |      |      |      |      |

> 响应参数

| 字段名 | 类型  | 是否必填 | 描述     | 示例值 |
| ------ | ----- | -------- | -------- | ------ |
| amount | float | 是       | 充值金额 | 50.00  |

> 响应示例

```json
{
    "code": 0,
    "msg": "ok"
}
```

##### demo

| URL                         | HTTP请求方式 | 是否需要登陆 |
| --------------------------- | -------- | ------ |
| {domain}/api/order/washList | POST     | 是      |

> 请求参数

| 字段名  | 类型   | 是否必填 | 描述   | 示例值  |
| ---- | ---- | ---- | ---- | ---- |
|      |      |      |      |      |

> 响应参数

| 字段名  | 类型   | 是否必填 | 描述   | 示例值  |
| ---- | ---- | ---- | ---- | ---- |
|      |      |      |      |      |

> 响应示例

```json
{
    "code": 0,
    "data": {
        "list": [
            {
                "id": 46,
                "name": "全外观清洗"
            },
            {
                "id": 47,
                "name": "车窗打蜡"
            }
        ]
    }
}
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

