<?php
Vendor('PHPMailer.PHPMailerAutoload');
import('ORG.Util.PasswordHash');


/**
 * �ݹ鷽ʽ�ĶԱ����еĿո�trim
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

/* �ݹ����addslashes��Ϊ�ַ����е�'����"�ȼ��Ϸ�б��ת�� */
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
 * �ݹ鷽ʽ�ĶԱ���htmlspecialchars
 */
function htmlspecialcharsRecurs($value) {
    if (empty($value)) {
        return $value;
    } else {
        return is_array($value)?array_map("htmlspecialcharsRecurs", $value):htmlspecialchars(trim($value));
    }
}


// �����ʼ�
function sendMail($address, $title, $message, $cc, $from = "360alarm@alarm.360.cn")
{
    $mail 				= new \PHPMailer ();
    $mail->Host 	    = '127.0.0.1'; // ����SMTP������
    $mail->Port         = 25;   //SMTP�������˿�
    $mail->SMTPAuth     = false; // ����Ϊ"����Ҫ��֤"
    //$mail->Username     = 'username@163.com';  //�����˵������˻�
    //$mail->Password     = 'xxxxxxxxxx';   //�����˵���������
    $mail->CharSet      = 'UTF-8'; // �����ʼ����ַ����룬����ָ������Ϊ'UTF-8'
    $mail->SMTPDebug 	= false; // �رյ���
    $mail->WordWrap     = 50;  // �����ַ���
    $mail->IsHTML ( true ); // �����ʼ���ʽΪHTML
    $mail->IsSMTP (); // ����PHPMailerʹ��SMTP����������Email   
    
    // ��ӳ��͵�ַ�����Զ��ʹ������Ӷ���ռ���
    if (is_array ( $cc )) {
        foreach ( $cc as $val ) {
            $mail->AddCC ( $val );
        }
    } else if (!empty($cc)) {
        $mail->AddCC ( $cc );
    }

    // ����ռ��˵�ַ�����Զ��ʹ������Ӷ���ռ���
    if (is_array ( $address )) {
        foreach ( $address as $val ) {
            $mail->AddAddress ( $val );
        }
    } else {
        $mail->AddAddress ( $address );
    }
    
    $mail->Subject 	= $title; // �����ʼ�����
    $mail->FromName = $from; // ���÷���������
    $mail->From 	= $from; // �����ʼ�ͷ��From�ֶ�
    $mail->Body 	= $message; // �����ʼ�����
    
    // �����ʼ���
    $ret 			= $mail->Send ();
    if (! $ret) {
        // ���ʹ����¼��־
        \Think\Log::record("send mail failed, Error detail: " . $mail->ErrorInfo, 'ERR');
    }
    return $ret;
}

/**
 * ����ָ���ļ�����������
 *
 * �÷���
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
 * // ������Ϊ��
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
 * @param array $array Ҫ���������
 * @param string $keyname ����ļ�
 * @param int $dir ������
 *
 * @return array ����������
 */
function sortByCol($array, $keyname, $dir = SORT_ASC)
{
    return sortByMultiCols($array, array($keyname => $dir));
} 

/**
 * ��һ����ά���鰴�ն���н����������� SQL ����е� ORDER BY
 *
 * �÷���
 * @code php
 * $rows = Helper_Array::sortByMultiCols($rows, array(
 * 'parent' => SORT_ASC,
 * 'name' => SORT_DESC,
 * ));
 * @endcode
 *
 * @param array $rowset Ҫ���������
 * @param array $args ����ļ�
 *
 * @return array ����������
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
*���������hashֵ
*/
function hashPassword($password) {
    $passwordHash = new Org\Util\PasswordHash(8, false);
    return $passwordHash->HashPassword($password);
}

/**
*�ȶ������hashֵ�Ƿ���ȷ
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
