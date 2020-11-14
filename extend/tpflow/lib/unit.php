<?php
/**
*+------------------
* Tpflow 公共类，模板文件
*+------------------
* Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
*+------------------
* Author: guoguo(1838188896@qq.com)
*+------------------ 
*/
namespace tpflow\lib;


class unit{
	/**
	 * 根据键值加载全局配置文件
	 *
	 * @param  $key 键值
	 */
	public static function gconfig($key) {
		$ret = require ( BEASE_URL . '/config/common.php');
		return $ret[$key] ?? '';
	}
	/**
	 * 消息返回统一处理
	 *
	 * @param  $msg  返回消息
	 * @param  $code 返回代码 0 成功，1操作失败
	 * @param  $data 返回数据
	 */
	public static function msg_return($msg = "操作成功！", $code = 0,$data = [])
	{
		return json(["code" => $code, "msg" => $msg, "data" => $data]);
	}
	/**
	 * 步骤转换
	 *
	 */
	public static function nexnexprocessinfo($wf_mode,$npi){
		if($wf_mode!=2){
			if($npi['auto_person']!=3){
				//非自由模式
				return $npi['process_name'].'('.$npi['todo'].')';
			}else{
				$todu = "<select name='todo' id='todo'  class='select'  datatype='*' ><option value=''>请指定办理人员</option>";
				$op ='';
				foreach($npi['todo']['ids'] as $k=>$v){
					   $op .='<option value="'.$v.'*%*'.$npi['todo']['text'][$k].'">'.$npi['todo']['text'][$k].'</option>'; 
				}
				return $todu.$op.'</select>';;
			}
			$pr = '';
		}else{
			$pr = '[同步]';
			$op ='';
			foreach($npi['nexprocess'] as $k=>$v){
				   $op .=$v['process_name'].'('.$v['todo'].')'; 
			}
			return $pr.$op;
		}
	}
	/**
	 * IDS数组转换
	 *
	 * @param  $str  字符串
	 * @param  $dot_tmp 分割字符串
	 */
   public static function ids_parse($str, $dot_tmp = ',')
    {
        if (!$str) return '';
        if (is_array($str)) {
            $idarr = $str;
        } else {
            $idarr = explode(',', $str);
        }
        $idarr = array_unique($idarr);
        $dot = '';
        $idstr = '';
        foreach ($idarr as $id) {
            $id = intval($id);
            if ($id > 0) {
                $idstr .= $dot . $id;
                $dot = $dot_tmp;
            }
        }
        if (!$idstr) $idstr = 0;
        return $idstr;
    }
	/**
     * JSON 转换处理
	 *
     * @param $flow_id 
	 * @param $process_info 
     */
    public static function parse_out_condition($json_data, $field_data)
    {
        $array = json_decode($json_data, true);
        if (!$array) {
            return [];
        }
        $json_data = array();//重置
        foreach ($array as $key => $value) {
            $condition = '';
            foreach ($value['condition'] as $val) {
                $preg = "/'(data_[0-9]*|checkboxs_[0-9]*)'/s";
                preg_match_all($preg, $val, $temparr);
                $val_text = '';
                foreach ($temparr[0] as $k => $v) {
                    $field_name = self::get_field_name($temparr[1][$k], $field_data);
                    if ($field_name)
                        $val_text = str_replace($v, "'" . $field_name . "'", $val);
                    else
                        $val_text = $val;
                }
                $condition .= '<option value="' . $val . '">' . $val . '</option>';
            }
            $value['condition'] = $condition;
            $json_data[$key] = $value;
        }
        return $json_data;
    }

    /**
     * 获取字段名称
     */
    public static function get_field_name($field, $field_data)
    {
        $field = trim($field);
        if (!$field) return '';
        $title = '';
        foreach ($field_data as $value) {
            if ($value['plugins'] == 'checkboxs' && $value['parse_name'] == $field) {
                $title = $value['title'];
                break;
            } else if ($value['name'] == $field) {
                $title = $value['title'];
                break;
            }
        }
        return $title;
    }

}

