<?php
/*
 * 应用配置项，能够覆盖掉"惯例配置"，可以将各个模块的公共配置放在此处
 * 此外，根据运行环境，自动加载环境配置
 */
$public_config = array(
	//'配置项'=>'配置值'
);

$env_config = require_once APP_ENV . '/config.php';

return array_merge($public_config, $env_config);