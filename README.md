# imooc_php_api
慕课网高性能PHP API开发


可以按照以下步骤来部署和运行程序:
1. 请确保机器已经安装了Yaf框架, 并且已经加载入PHP;
2. 把imooc_php_api目录Copy到Webserver的DocumentRoot目录下;
3. 需要在php.ini里面启用如下配置，生产的代码才能正确运行：
	yaf.environ="product"
4. 重启Webserver;
5. 访问http://yourhost/phpapi/,出现Hellow Word!, 表示运行成功,否则请查看php错误日志;



项目分两部分  
第一步、基本功能的开发，主要包括如下功能
1. 用户类API
2. 文章类API
3. 邮件API
4. 短信API
5. Push消息推送
6. IP地址转详细地址
7. 微信支付API   

第二步、API接口的提炼

  1. API自测脚本
  2. API公共Lib分离
  3. 建立数据操作层DAO
  4. 接口异常时的处理规范
  5. API功能的整合
  6. API文档