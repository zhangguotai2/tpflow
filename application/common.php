<?php
/**
 * Pms
 * 公共方法
 * @2018年01月
 * @Gwb
 */

use think\facade\Session;
use think\Db;
use PHPMailer\PHPMailer\PHPMailer;

function ids_parse($str,$dot_tmp=',')
{
    if(!$str) return '';
    if(is_array($str))
    {
        $idarr = $str;
    }else
    {
        $idarr = explode(',',$str);
    }
    $idarr = array_unique($idarr);
	$dot = '';
	$idstr ='';
    foreach($idarr as $id)
    {
        $id = intval($id);
        if($id>0)
        {
            $idstr.=$dot.$id;
            $dot = $dot_tmp;
        }
    }
    if(!$idstr) $idstr=0;
    return $idstr;
}
// 数组保存到文件
function arr2file($filename, $arr='')
{
    if(is_array($arr)){
        $con = var_export($arr,true);
    } else{
        $con = $arr;
    }
    $con = "<?php\nreturn $con;\n?>";//\n!defined('IN_MP') && die();\nreturn $con;\n
    write_file($filename, $con);
}
function get_commonval($table,$id,$val)
{
	return Db($table)->where('id',$id)->value($val);
}
//文件写入
function write_file($l1, $l2='')
{
    $dir = dirname($l1);
    if(!is_dir($dir)){
        mkdirss($dir);
    }
    return @file_put_contents($l1, $l2);
}

//对象转化数组
function obj2arr($obj) 
{
    return json_decode(json_encode($obj),true);
}
/**
 * ajax数据返回，规范格式
 */
function msg_return($msg = "操作成功！", $code = 0,$data = [],$redirect = 'parent',$alert = '', $close = false, $url = '')
{
    $ret = ["code" => $code, "msg" => $msg, "data" => $data];
	$extend['opt'] = [
        'alert'    => $alert,
        'close'    => $close,
        'redirect' => $redirect,
        'url'      => $url,
    ];
    $ret = array_merge($ret, $extend);
    return Response::create($ret, 'json');
}