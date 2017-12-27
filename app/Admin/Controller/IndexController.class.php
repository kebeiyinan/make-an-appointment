<?php
// +----------------------------------------------------------------------
// 后台首页
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AuthController;
class IndexController extends AuthController {
	//首页教学日历
	public function index(){
		//未登录
		if (empty($_SESSION['aid'])){
			$this->redirect('Admin/Login/login');
		}
		/*
		 *2017冬：2017-09-19 周二     1505836800 至 2018-01-26  1516896000
		 */
		//教学周期总数
		$data['is_del'] = 2;
		$info = M('semester')->where($data)->order('semester_addtime desc')->limit(1)->find();
		$start = $info['semester_startime'];$end = $info['semester_endtime'];
		$now_w = date("w",$info['semester_startime']);//当前星期,返回1,2,3 注：日期 开学当天为周几
		$end_w = date("w",$info['semester_endtime']);//当前星期,返回1,2,3 注：日期 放假当天为周几
		$days = intval(($end - $start )/60/60/24);//总天数
		
		//第一个个教学周天数
		if($now_w == 0){
			$first_w = 1;
			$now_w =7;
		}else{
			$first_w = 7 - $now_w +1;
		}
		//总共教学周
		$weeks = 1+ ceil(($days-$first_w)/7);
		$weekarray=array("一","二","三","四","五","六","日"); //先定义一个数组
		$this->assign('weekarray',$weekarray);
		$this->assign('weeks',$weeks);
		
		//学期开始时间
		$start = date('Y-m-d',$start);
		//当前时间
		$current_time = date('Y-m-d');
		$end= date('Y-m-d',time());
		//当前为第几周
		$ww = $this->get_weekend_days($start,$end);	
		$ww = $ww + 1;
		if($ww == $weeks){
			$ww = $weeks;
		}
		$this->assign('weekday',$weekarray[date("w")-1]);
		//周几开学
		$this->assign('now_w',$now_w);
		//周几放假 0代表星期日
		if($end_w==0){
			$end_w = 7;
		}
		$this->assign('end_w',$end_w);
		//实验室
		$lab_list = M('lab_list')->field('s_id,lab_name')->where(array('status'=>1,'is_del'=>1))->select();
		$this->assign('lab_list',$lab_list);
		if(I('s_id')){
			$subscribe['aboratory_id'] = I('s_id');
			$this->assign('s_id',I('s_id'));
		}
		if(time() < $info['semester_startime']){
			$ww = 1;
		}elseif(time()+0 > $info['semester_endtime']){
			$ww = $weeks;
		}
		//预约列表
		$subscribe['status'] = 2;
		$subscribe['is_del'] = 2;
		if(I('number_of_weeks')){
			$ww = I('number_of_weeks');
		}
		$subscribe['number_of_weeks'] = $ww;
		//课时
		$classhour_list = M('classhour_list')->where(array('is_del'=>1))->order('sort ASC')->select();
		$classhour = array();
		foreach($classhour_list as $k=>$v){
			$subscribe[0] = 'FIND_IN_SET('.$v['c_id'].',class_hour)';
			$classhour = M("subscribe")->where($subscribe)->select();
			if($classhour){
				foreach($classhour as $key=>$val){
					$classhour_list[$k]['kk']["week"] = $val['week'];
				}
			}
		}
		$this->assign('classhour_list',$classhour_list);
		$this->assign('list',$list);
		$this->assign('ww',$ww);
		//公告表
		$noticeList = M('notice')->where(array('is_del'=>2))->order('addtime DESC')->limit(3)->select();
		$this->assign('noticeList',$noticeList);
		//教务提醒
		$office['a.status'] = 1;
		$office['a.is_del'] = 2;
		$id = session('aid'); //当前登陆人员ID
		$groupid = M('auth_group_access')->where(array('uid'=>$id))->getField('group_id');
		if($groupid==2){
			$sid = M('lab_list')->where(array('admin_id'=>$id))->field('s_id')->select();//查询当前人员管理的实验室
			foreach ($sid as $key => $value) {
				$sidd[] = $value['s_id'];
			}
			$siddd = implode(',',$sidd);
			if($siddd){
				$office['a.aboratory_id'] = array('in',$siddd);
			}else{
				$office['a.aboratory_id'] = 0;
			}
		}
		$office = M('subscribe')->field('a.subscribe_person,a.subscribe_time,b.lab_name')->alias('a')->join('__LAB_LIST__ b ON b.s_id= a.aboratory_id')->where($office)->limit(3)->order('a.addtime DESC')->select();
		$this->assign('groupid',$groupid);
		$this->assign('office',$office);
		//渲染模板
		$this->display();
	}
	/*
	| Author:
	| @param char|int $start_date 一个有效的日期格式，例如：20091016，2009-10-16
	| @param char|int $end_date 同上
	| @return 给定日期之间的周末天数
	*/
	//有几个周日就是有几周
	public function get_weekend_days($start_date,$end_date){
	
		if (strtotime($start_date) > strtotime($end_date)) list($start_date, $end_date) = array($end_date, $start_date);
		
		$start_reduce = $end_add = 0;
		
		$start_N = date('N',strtotime($start_date));
		$start_reduce = ($start_N == 7) ? 1 : 0;
		
		$end_N = date('N',strtotime($end_date));
		in_array($end_N,array(7)) && $end_add = ($end_N == 7) ? 0 : 1;
		
		$days = abs(strtotime($end_date) - strtotime($start_date))/86400 + 1;
		return (floor(($days + $start_N - 1 - $end_N) / 7) - $start_reduce + $end_add);
	}
}