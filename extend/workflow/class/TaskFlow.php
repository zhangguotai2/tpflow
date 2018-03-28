<?php
namespace workflow;

use think\Db;

class TaskFlow{
	/**
	 * 执行任务
	 *
	 */
	public function doTask($config) {
		//任务全局类
		$wf_title = $config['wf_title'];
		$wf_fid = $config['wf_fid'];
		$wf_type = $config['wf_type'];
		$flow_id = $config['flow_id'];
		$run_flow_process = $config['run_flow_process'];
		$run_id = $config['run_id'];
		$check_con = $config['check_con'];
		$submit_to_save = $config['submit_to_save'];
		$action = FlowDb::getflowprocess($run_flow_process);//获取当前任务
		
		dump($action);
		return $action;
		exit;
		if(!empty($action[0]['process_to'])){//判断是否为最后
			//结束流程
			 $end = $this->end_flow($run_id);
			if(!$end){
				return ['msg'=>'结束流程错误！！！','code'=>'-1'];
			} 
			//获取下一个流程信息
			$nex_process = $this->getnexprocess($action[0]['process_to']);
			//记录下一个流程
			foreach($nex_process as $k=>$v){
				$run = $this->Run($flow_id,$v['id'],$wf_fid,$wf_type);
			}
			//消息通知
			
			//日志记录
		
			}else{ //结束流程
			//结束该流程
			$end = $this->end_flow($run_id);
			if(!$end){
				return ['msg'=>'结束流程错误！！！','code'=>'-1'];
			} 
			//消息通知发起人
		}
	}
	public function end_flow($run_id)
	{
		$result = Db::execute('update leipi_run set status = 1,endtime='.time().' where id = '.$run_id.' ');
		return $result;	
	}
	public function getnexprocess($ids)
	{
		$process = FlowDb::getnexprocess($ids);//获取当前任务
		return  $process;
	}
	public function Run($wf_id,$wf_process,$wf_fid,$wf_type)
	{
		$wf_run = InfoDB::addWorkflowRun($wf_id,$wf_process,$wf_fid,$wf_type);
			if(!$wf_run){
				return ['msg'=>'流程发起失败，数据库操作错误！！','code'=>'-1'];
			}
		//添加流程步骤日志
		$wf_process_log = InfoDB::addWorkflowProcess($wf_id,$wf_process,$wf_run);
			if(!$wf_process_log){
				return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
			}
		$run_log = InfoDB::AddrunLog(1,$wf_run,'审批意见',$wf_fid,$wf_type);
	}
	
	

}