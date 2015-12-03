<?php
/*
 * 应用配置项，能够覆盖掉"惯例配置"，可以将各个模块的公共配置放在此处
 * 此外，根据运行环境，自动加载环境配置
 */
$public_config = array(
	//'配置项'=>'配置值'
    'DEFAULT_MODULE'        =>  'Home',  // 默认模块
    'MODULE_DENY_LIST'      =>  array('Common','Runtime'),  // 设置禁止访问的模块列表
    
    'TOKEN_ON'      =>    true,  // 是否开启令牌验证 默认关闭
    'TOKEN_NAME'    =>    '__hash__',    // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE'    =>    'md5',  // 令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'   =>    true,  // 令牌验证出错后是否重置令牌 默认为true
    
//    'TMPL_ENGINE_TYPE'      => 'Smarty',
//   'TMPL_ENGINE_CONFIG'    => array(
//        'plugins_dir'=>'./ThinkPHP/Library/Vendor/',
//    ),

    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 3, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

    /* 加载扩展配置文件 */
    'LOAD_EXT_CONFIG' => 'sdk_config',
);

$env_config = require_once APP_ENV."/config.php";

return array_merge($public_config, $env_config);