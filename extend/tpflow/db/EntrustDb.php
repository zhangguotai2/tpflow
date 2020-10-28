<?php
/**
*+------------------
* Tpflow 节点事务处理
*+------------------
* Copyright (c) 2006~2020 http://cojz8.cn All rights reserved.
*+------------------
* Author: guoguo(1838188896@qq.com)
*+------------------
*/
namespace tpflow\db;
use think\facade\Db;

class EntrustDb{
	
	/**
     * API 获取所有当前生效的授权信息
     */
    public static function get_Entrust($map=[],$Raw='')
    {
		return Db::name('entrust')
		->whereBetweenTimeField('entrust_stime','entrust_etime')
		->where($map)
		->whereRaw($Raw)
		->field('id,flow_process,old_user')
		->select();
    }
	/**
     * API 查询
     * @param $data POST提交的数据
     */
    public static function lists()
    {
		return Db::name('entrust')->select();
    }
	/**
     * API 查询
     * @param $data POST提交的数据
     */
    public static function find($id)
    {
		$info = Db::name('entrust')->find($id);
		if($info==''){
			$info['entrust_stime'] = '';
			$info['entrust_etime'] = '';
			$info['type'] ='';
			$info['userinfo'] = '';
			$info['id'] = '';
			$info['entrust_title'] = '';
			$info['entrust_con'] = '';
		}else{
			$info['entrust_stime'] = date('Y-m-d',$info['entrust_stime'])."T".date('H:i:s',$info['entrust_stime']);
			$info['entrust_etime'] = date('Y-m-d',$info['entrust_etime'] ?? '')."T".date('H:i:s',$info['entrust_etime'] ?? '');
			$info['type'] = $info['flow_process']."@".$info['flow_id'];
			$info['userinfo'] = $info['entrust_user']."@".$info['entrust_name'];
		}
		return $info;
    }
	/**
     * API 新增
     * @param $data POST提交的数据
     */
    public static function Add($uid,$data)
    {
		if($uid==''){
			 return ['code' => 1, 'data' => '请先登入账号信息哦！'];
		}
		$data['old_user'] =$uid;
		$data['entrust_stime'] = strtotime($data['entrust_stime']);
		$data['entrust_etime'] = strtotime($data['entrust_etime']);
		$type = explode("@",$data['type']);
		$data['flow_process'] =$type[0];
		$data['flow_id'] =$type[1];
		$user = explode("@",$data['userinfo']);
		$data['entrust_user'] =$user[0];
		$data['entrust_name'] =$user[1];
		unset($data['userinfo']);
		unset($data['type']);
		$data['add_time'] =time();
		if($data['id']!=''){
			 $ret = Db::name('entrust')->update($data);
		}else{
			 $ret = Db::name('entrust')->insertGetId($data);
		}
        if ($ret) {
            return ['code' => 0, 'data' => $ret];
        } else {
            return ['code' => 1, 'data' => 'Db0001-写入数据库出错！'];
        }
    }
	/**
     * API 保存关系
     */
    public static function save_rel($data,$run_process)
    {
		foreach($data as $k=>$v){
			$rel=[
				'entrust_id'=>$v['id'],
				'process_id'=>$run_process,
				'add_time'=>date('Y-m-d H:i:s'),
			];
			$ret =  Db::name('entrust_rel')->insertGetId($rel);
			if (!$ret) {
				return ['code' => 1, 'data' => 'Db0001-写入关系失败！'];
			}
		}
    }
	/**
     * change 权限转换
     */
	 public static function change($info)
    {
		$has_rel = Db::name('entrust_rel')->where('process_id',$info['id'])->find();
		if(!$has_rel){
			return $info;
		}
		$entrust = self::find($has_rel['entrust_id']);
		$info['sponsor_text'] = $info['sponsor_text'].',[代理]'.$entrust['entrust_name'];
		$info['sponsor_ids'] = $info['sponsor_text'].','.$entrust['entrust_user'];
		return $info;
    }
	
}