<?php
Vendor('PHPMailer.PHPMailerAutoload');
import('ORG.Util.PasswordHash');


/**
 * 递归方式的对变量中的空格trim
 *
 * @access  public
 * @param   mix   $value
 *
 * @return  mix
 */
function cleanCharacterDeep($value) {
    if (empty($value)) {
        return $value;
    } else {
        return is_array($value)?array_map("cleanCharacterDeep", $value):trim($value);
    }
}

/* 递归调用addslashes，为字符串中的'或者"等加上反斜线转义 */
function new_addslashes($string)
{
    if(get_magic_quotes_gpc())
    {
        return $string;
    }
    else
    {
        if(!is_array($string)) return addslashes($string);
        foreach($string as $key => $val) $string[$key] = new_addslashes($val);
        return $string;
    }
}

/**
 * 递归方式的对变量htmlspecialchars
 */
function htmlspecialcharsRecurs($value) {
    if (empty($value)) {
        return $value;
    } else {
        return is_array($value)?array_map("htmlspecialcharsRecurs", $value):htmlspecialchars(trim($value));
    }
}


// 发送邮件
function sendMail($address, $title, $message, $cc, $from = "360alarm@alarm.360.cn")
{
    $mail 				= new \PHPMailer ();
    $mail->Host 	    = '127.0.0.1'; // 设置SMTP服务器
    $mail->Port         = 25;   //SMTP服务器端口
    $mail->SMTPAuth     = false; // 设置为"不需要验证"
    //$mail->Username     = 'username@163.com';  //发送人的邮箱账户
    //$mail->Password     = 'xxxxxxxxxx';   //发送人的邮箱密码
    $mail->CharSet      = 'UTF-8'; // 设置邮件的字符编码，若不指定，则为'UTF-8'
    $mail->SMTPDebug 	= false; // 关闭调试
    $mail->WordWrap     = 50;  // 换行字符数
    $mail->IsHTML ( true ); // 设置邮件格式为HTML
    $mail->IsSMTP (); // 设置PHPMailer使用SMTP服务器发送Email   
    
    // 添加抄送地址，可以多次使用来添加多个收件人
    if (is_array ( $cc )) {
        foreach ( $cc as $val ) {
            $mail->AddCC ( $val );
        }
    } else if (!empty($cc)) {
        $mail->AddCC ( $cc );
    }

    // 添加收件人地址，可以多次使用来添加多个收件人
    if (is_array ( $address )) {
        foreach ( $address as $val ) {
            $mail->AddAddress ( $val );
        }
    } else {
        $mail->AddAddress ( $address );
    }
    
    $mail->Subject 	= $title; // 设置邮件标题
    $mail->FromName = $from; // 设置发件人名字
    $mail->From 	= $from; // 设置邮件头的From字段
    $mail->Body 	= $message; // 设置邮件正文
    
    // 发送邮件。
    $ret 			= $mail->Send ();
    if (! $ret) {
        // 发送错误记录日志
        \Think\Log::record("send mail failed, Error detail: " . $mail->ErrorInfo, 'ERR');
    }
    return $ret;
}

/**
 * 根据指定的键对数组排序
 *
 * 用法：
 * @code php
 * $rows = array(
 * array('id' => 1, 'value' => '1-1', 'parent' => 1),
 * array('id' => 2, 'value' => '2-1', 'parent' => 1),
 * array('id' => 3, 'value' => '3-1', 'parent' => 1),
 * array('id' => 4, 'value' => '4-1', 'parent' => 2),
 * array('id' => 5, 'value' => '5-1', 'parent' => 2),
 * array('id' => 6, 'value' => '6-1', 'parent' => 3),
 * );
 *
 * $rows = Helper_Array::sortByCol($rows, 'id', SORT_DESC);
 * dump($rows);
 * // 输出结果为：
 * // array(
 * // array('id' => 6, 'value' => '6-1', 'parent' => 3),
 * // array('id' => 5, 'value' => '5-1', 'parent' => 2),
 * // array('id' => 4, 'value' => '4-1', 'parent' => 2),
 * // array('id' => 3, 'value' => '3-1', 'parent' => 1),
 * // array('id' => 2, 'value' => '2-1', 'parent' => 1),
 * // array('id' => 1, 'value' => '1-1', 'parent' => 1),
 * // )
 * @endcode
 *
 * @param array $array 要排序的数组
 * @param string $keyname 排序的键
 * @param int $dir 排序方向
 *
 * @return array 排序后的数组
 */
function sortByCol($array, $keyname, $dir = SORT_ASC)
{
    return sortByMultiCols($array, array($keyname => $dir));
} 

/**
 * 将一个二维数组按照多个列进行排序，类似 SQL 语句中的 ORDER BY
 *
 * 用法：
 * @code php
 * $rows = Helper_Array::sortByMultiCols($rows, array(
 * 'parent' => SORT_ASC,
 * 'name' => SORT_DESC,
 * ));
 * @endcode
 *
 * @param array $rowset 要排序的数组
 * @param array $args 排序的键
 *
 * @return array 排序后的数组
 */
function sortByMultiCols($rowset, $args)
{
    $sortArray = array();
    $sortRule = '';
    foreach ($args as $sortField => $sortDir)
    {
        foreach ($rowset as $offset => $row)
        {
            $sortArray[$sortField][$offset] = $row[$sortField];
        }
        $sortRule .= '$sortArray[\'' . $sortField . '\'], ' . $sortDir . ', ';
    }
    if (empty($sortArray) || empty($sortRule)) { return $rowset; }
    eval('array_multisort(' . $sortRule . '$rowset);');
    return $rowset;
}

/**
*计算密码的hash值
*/
function hashPassword($password) {
    $passwordHash = new Org\Util\PasswordHash(8, false);
    return $passwordHash->HashPassword($password);
}

/**
*比对密码的hash值是否正确
*/
function checkPassword($password, $stored_hash) {
    $passwordHash = new Org\Util\PasswordHash(8, false);
    return $passwordHash->CheckPassword($password, $stored_hash);
}

/**
 * @param $url
 * @param array $post
 * @param int $timeout
 * @param string $proxy
 * @param string $ua
 * @return mixed
 */
function xcurl($url, $post = array(), $timeout = 10, $proxy = '', $ua = " Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36", $header = '')
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    if (!empty($header)) {
        curl_setopt($ch, CURLOPT_HEADER, $header);
    } else {
        curl_setopt($ch, CURLOPT_HEADER, 0);
    }
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    if (!empty($ua)) {
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    }

    if (!empty($proxy)) {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
    }

    if (count($post) > 0) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, (is_array($post)) ? http_build_query($post) : $post);
    }

    $output = curl_exec($ch);
    if ($output === false) {
        \Think\Log::record("curl failed, Error detail: " . curl_error($ch), 'ERR');
    }
    curl_close($ch);

    return $output;
}

/**
*HTTP GET Requests
*/
function xcurl_get($url, $header = array(), $options = array()) {
    // First, include Requests
    Vendor('Requests.library.Requests');

    // Next, make sure Requests can load internal classes
    Requests::register_autoloader();
    
    $response = Requests::get($url, $header, $options);
    if ($response->success) {
        return $response->body;
    } else {
        \Think\Log::record("xcurl_get failed, Error detail: " . $response->status_code, 'ERR');
        return false;
    }
}

/**
*HTTP POST Requests
*/
function xcurl_post($url, $post = array(), $header = array(), $options = array()) {
    // First, include Requests
    Vendor('Requests.library.Requests');

    // Next, make sure Requests can load internal classes
    Requests::register_autoloader();
    
    $response = Requests::post($url, $header, $post, $options);
    if ($response->success) {
        return $response->body;
    } else {
        \Think\Log::record("xcurl_post failed, Error detail: " . $response->status_code, 'ERR');
        return false;
    }
}

/**
 * 跨平台执行脚本
 *
 * @return array
 * array['output'] 执行后的输出结果
 * array['status'] 执行状态 0为正常 127 找不到脚本 126没有执行该shell脚本权限
 */
function terminal($command) {
    // system
    if (function_exists ( 'system' )) {
        ob_start ();
        system ( $command, $return_var );
        $output = ob_get_contents ();
        ob_end_clean ();
    }   // passthru
    else if (function_exists ( 'passthru' )) {
        ob_start ();
        passthru ( $command, $return_var );
        $output = ob_get_contents ();
        ob_end_clean ();
    }   

    // exec
    else if (function_exists ( 'exec' )) {
        exec ( $command, $output, $return_var );
        $output = implode ( "\n", $output );
    }   

    // shell_exec
    else if (function_exists ( 'shell_exec' )) {
        $output = shell_exec ( $command );
    } 

    else {
        $output = 'Command execution not possible on this system';
        $return_var = 1;
    }
    
    return array (
        'output' => $output,
        'status' => $return_var 
    );
}
