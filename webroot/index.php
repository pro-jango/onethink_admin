<?php
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
define('APP_DEBUG', true );
define('BIND_MODULE','Admin');
define('RUNTIME_PATH','../Runtime/');
define('INDEX_PATH',dirname(__FILE__));

/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */
define ( 'APP_PATH', '/data/vhosts/admin.xxx.com/admin/Application/' );
require '../ThinkPHP3.2/ThinkPHP.php';