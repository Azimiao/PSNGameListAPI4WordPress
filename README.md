# PSNGameList4WordPressAPI
WordPress PSN 游戏列表数据 API 插件，供其他插件使用。

# 前置依赖
~~本插件依赖[PSN-PHP-RESTAPI](https://github.com/Noob-Biosphere/PSN-PHP-RESTApi)提供的服务。~~

本插件依赖[PSN-API-With-Node-Express](https://github.com/Noob-Biosphere/PSN-API-With-Node-Express)提供的服务。

~~前置服务还处于开发及测试阶段，因此该仓库目前为私有仓库。前置服务开发与测试完成后，仓库将公开。~~

正处于开发阶段，交流请加群`313732000`，注明`Github PSN 奖杯插件`。

# Demo
~~[http://test.azimiao.com/?p=16](http://test.azimiao.com/?p=16)~~
[https://www.azimiao.com/playstation_gamelist](https://www.azimiao.com/playstation_gamelist)



# 功能
本插件后台提供设置账号、npsso 信息的设置面板，同时基于 wp-ajax 提供一个游戏列表接口。

每次访问游戏列表，可自动获取 token，并保存 token。Token 过期前，将自动获取新 Token。

# 特别说明
邀请码一栏是前置依赖服务内设置的，未来前置服务公开后，你可以自己修改前置服务的逻辑，去掉验证等。