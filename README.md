## 卡密销售及后台管理

此仓库用于备份文件及后续部署，更多功能正在开发。

##关于支付方式
使用的是支付宝的电脑/手机网站支付，会根据支付状态下的浏览器标识进行跳转

##目前已经实现的功能
1、增删改卡密\
2、显示订单内容并允许24小时内订单退款\
3、管理员控制\
【WAIT】黑名单用户禁止下单\
以后的事情以后再说

##安装需要注意的事情

### 1、设置NGINX配置文件
**若不设置后续会产生一系列“file not found”问题，请留意**

```
location / {
	try_files $uri $uri/ $uri.php?$args;
}
```
### 2、设置所有文件的数据库密码
**全都要设置更改，不然会发生一些意想不到的事情**

### 3、关于运行环境
我是在NGINX+PHP7.4的环境下进行开发和测试，本程序只是一个半半成品。\
请谨慎在生产环境运行