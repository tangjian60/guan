<?php defined('BASEPATH') OR exit('No direct script access allowed');

function build_response_str($code, $msg, $data = [])
{
    $response_array = array(
        'code' => $code,
        'msg' => $msg,
        'data' => $data
    );
    return json_encode($response_array);
}

function check_request_sign($req_data)
{
    // build sign string
    $to_sign_str = '';
    foreach ($req_data as $key => $value) {
        if ($key != 'sign') {
            $to_sign_str .= $key;
            $to_sign_str .= '=';
            $to_sign_str .= $value;
            $to_sign_str .= '&';
        }
    }
    // remove last &
    $to_sign_str = rtrim($to_sign_str, '&');
    // check sign
    if (md5($to_sign_str . REQUEST_SIGN_SALT) == $req_data['sign']) {
        return true;
    }
    return false;
}

function invalid_parameter($p)
{
    foreach ($p as $value) {
        if (!isset($value) || $value === '') {
            return true;
        }
    }
    return false;
}

function encode_id($id)
{
    $sid = ($id & 0xff000000);
    $sid += ($id & 0x0000ff00) << 8;
    $sid += ($id & 0x00ff0000) >> 8;
    $sid += ($id & 0x0000000f) << 4;
    $sid += ($id & 0x000000f0) >> 4;
    $sid ^= 11184810;
    return $sid;
}

function decode_id($sid)
{
    if (!is_numeric($sid)) {
        return false;
    }
    $sid ^= 11184810;
    $id = ($sid & 0xff000000);
    $id += ($sid & 0x00ff0000) >> 8;
    $id += ($sid & 0x0000ff00) << 8;
    $id += ($sid & 0x000000f0) >> 4;
    $id += ($sid & 0x0000000f) << 4;
    return $id;
}

function beauty_display($str, $len)
{
    if (mb_strlen($str, 'UTF-8') <= $len) {
        return $str;
    } else {
        return mb_substr($str, 0, $len, 'UTF-8') . '...';
    }
}

function get_prov_pic_ele($u)
{
    if (empty($u)) {
        return '未上传';
    } else {
        $url = CDN_DOMAIN . $u;
        return '<a href="' . $url . '" class="fancybox"><img class="item-pic-box" src="' . $url . '"></a>';
    }
}

function limit_page($page, $pagesize){
    if(empty($page))
    {
        $startrow = 0;
    }else
    {
        $page = (int)$page;
        $startrow = ($page-1)*$pagesize;
    }

    return "LIMIT $startrow, $pagesize";
}

/**
 * 获取两个日期之间的日期（天）
 * @param string $date1
 * @param string $date2
 * @return array 返回以天为单位日期数组
 */
function getDays($date1, $date2)
{
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $new_date = $date1;
    $res = [];
    while ($new_date <= $date2) {
        $res[] = date('Y-m-d', $new_date);
        $new_date += 86400;
    }
    return $res;
}

function getDays1($date1, $date2)
{
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $new_date = $date1;
    $res = [];
    while ($new_date >= $date2) {
        $res[] = date('Y-m-d', $new_date);
        $new_date -= 86400;
    }
    return $res;
}

function load_config($filename, $item = '')
{
    $config = [];
    $CI =& get_instance();
    if ($CI->config->load($filename, TRUE, TRUE))
    {
        $config = $CI->config->item($filename);
        $config = !empty($item) ? $config[$item] : $config;
    }
    return $config;
}

/**
 * 对象 转 数组
 *
 * @param object $obj 对象
 * @return array
 */
function object_to_array($obj) {
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return [];
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)object_to_array($v);
        }
    }

    return $obj;
}

