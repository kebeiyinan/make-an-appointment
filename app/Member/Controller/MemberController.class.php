<?php
// +----------------------------------------------------------------------
// |预约管理模块
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// |高利杰 2017/12/12
// +----------------------------------------------------------------------
namespace Member\Controller;
use Common\Controller\AuthController;
use Org\Util\Stringnew;
class MemberController extends AuthController {
	/*
     * 个人预约 
     */
	public function  subscribe(){
		//当前登录的用户的id
		$cur_admin_id = $_SESSION['aid'];
		$search = I("search");
    	if($search){
            if($search['start_time']){
                $start_time = strtotime($search['start_time']);
                $where['a.addtime'] = array('egt',$start_time);
            }
            if($search['end_time']){
                $end_time = strtotime($search['end_time']." 23:59:59");
                $where['a.addtime'] = array("elt",$end_time);
            }
            if($search['start_time'] && $search['end_time']){
                $start_time = strtotime($search['start_time']);
                $end_time = strtotime($search['end_time']." 23:59:59");
                $where['a.addtime'] = array(array('egt',$start_time),array('elt',$end_time),'AND');
            }
            if($search['aboratory_id']){
            	$where['aboratory_id'] = $search['aboratory_id'];
            }
            if($search['status']){
            	$where['a.status'] = $search['status'];
            }
        }
        //排序-升序降序
        $orde = I("orde");
        if($orde){
        	if($orde == 'DESC'){
        		$orde = 'ASC';
        	}elseif($orde == 'ASC'){
        		$orde = 'DESC';	
        	}
        }else{
        	$orde = 'DESC';//默认排序时间倒序
        }
        $order = array('addtime'=>$orde,'status');//审核状态排序
        //查询当前用户的身份级别
        $group = M('auth_group_access')->where(array('uid'=>$cur_admin_id))->getField('group_id');
        $where['a.admin_id']  = $cur_admin_id;
        /*if($group == 3){	//如果当前用户是老师
        	$where['a.admin_id'] = $cur_admin_id;
        }elseif($group == 2){//如果当前用户是管理员
        	$aboratory_id = M('lab_list')->where(array('admin_id'=>$cur_admin_id))->getField('s_id');
        	if($aboratory_id){
        		$where['aboratory_id'] = array('in',$aboratory_id);
        	}else{
        		$where['aboratory_id'] = 0;
        	}	
        }*/
        //未删除
        $where['a.is_del'] = 2;
    	$count = M('subscribe')->join(" AS a LEFT JOIN __LAB_LIST__ as b ON a.aboratory_id = b.s_id ")->join(" __CLASSHOUR_LIST__ as c on a.class_hour = c.c_id")->where($where)->count();
    	$Page= new \Think\Page($count,15);
    	$show= $Page->show();
		
		$list = M('subscribe')->join(" AS a LEFT JOIN __LAB_LIST__ as b ON a.aboratory_id = b.s_id ")->join(" __CLASSHOUR_LIST__ as c on a.class_hour = c.c_id")->field('a.*,b.lab_name,c.classhour_name')->where($where)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
		//实验室列表
		$lab_list = M("lab_list")->field("s_id,lab_name")->where(array('is_del'=>1,'status'=>1))->select();
		//教学起止时间
		$data['is_del'] = 2;
		$info = M('semester')->where($data)->order('semester_addtime desc')->limit(1)->find();
		$start = $info['semester_startime'];$end = $info['semester_endtime'];

		//判断已经超过实际预约时间的可以删除
		$now_time = time();
		foreach($list as $k=>$v){
			//查询课时
			$class_hour = explode(',', $v['class_hour']);
			$cl_n = '';
			foreach ($class_hour as $key => $value) {
				$class_name = M('classhour_list')->where(array('c_id'=>$value))->getField('classhour_name');
				$cl_n .= $class_name.",";
			}
			$cl_n = rtrim($cl_n, ",");	
			$list[$k]['cl_n'] = $cl_n;

			$subscribe_time = $v['subscribe_time'] + 60*60*24;
			
			if($now_time>$subscribe_time || $v['status'] == 3){//如果超过预约时间或者已拒绝
				$list[$k]['overtime'] = 1;
			}else{
				$list[$k]['overtime'] = 2;
			}
		}
		
        $this->assign('orde',$orde);
        $this->assign('cur_admin_id',$cur_admin_id);
    	$this->assign('search',$search);
    	$this->assign('page',$show);
    	$this->assign('list',$list);
    	$this->assign('lab_list',$lab_list);
    	$this->display();
	}
	/*
	 * 预约详情
	 */
	 public function subscribeDetails(){
	 	$subscribe_id = I('subscribe_id');
		$info = M('subscribe')->where('subscribe_id='.$subscribe_id)->find();
		$info['aboratory_id'] = M('lab_list')->where('s_id='.$info['aboratory_id'])->getField('lab_name');
		$info['department_id'] = M('department_list')->where('department_id='.$info['department_id'])->getField('department_name');
		$data['c_id'] = array('in',$info['class_hour']);
		$classhour_list = M('classhour_list')->field('classhour_name')->where($data)->select();
		$this->assign('classhour_list',$classhour_list);
		$this->assign('info',$info);
		$this->display();
	 }
	/*

	预约详情
	 */
	public function detail(){
		$p = I('p');
        $subscribe_id = I('subscribe_id');
        $info = M('subscribe')->join(" AS a LEFT JOIN __LAB_LIST__ as b ON a.aboratory_id = b.s_id ")->join(" __CLASSHOUR_LIST__ as c on a.class_hour = c.c_id")->join(" __DEPARTMENT_LIST__ as d on a.department_id = d.department_id")->field('a.*,b.lab_name,c.classhour_name,d.department_name')->where(array('subscribe_id'=>$subscribe_id))->find();
       	$data['c_id'] = array('in',$info['class_hour']);
		$classhour_list = M('classhour_list')->field('classhour_name')->where($data)->select();
		$this->assign('classhour_list',$classhour_list);
        $this->assign('info',$info);
        $this->assign('p',$p);
        $this->display();
	}
	/*
	 * 个人审核
	 */
	public function checked(){
		$id = session('aid'); //当前登陆人员ID
		$groupid = M('auth_group_access')->where(array('uid'=>$id))->getField('group_id');
		if($groupid == 2){ //如果为管理员，只可查询自己管理的实验室
			$sid = M('lab_list')->where(array('admin_id'=>$id))->field('s_id')->select();//查询当前人员管理的实验室
			foreach ($sid as $key => $value) {
				$sidd[] = $value['s_id'];
			}
			$siddd = implode(',',$sidd);
			if($siddd){
				$data['a.aboratory_id'] = array('in',$siddd);
			}else{
				$data['a.aboratory_id'] = 0;
			}
		}
		if(I('search')){
			$search = I('search');
			$data['a.addtime'] =array(array('egt',strtotime($search['start_time'])),array('elt',strtotime($search['end_time'].' 23:55:55')),'AND');
			if($search['status']){
				$data['a.status'] = $search['status'];
			}
			if($search['lab_name']){
				$data['b.lab_name'] = array('like','%'.$search['lab_name'].'%');
			}
			if($search['aboratory_id']){
            	$data['aboratory_id'] = $search['aboratory_id'];
            }
			$this->assign('search',$search);
		}
		if(I('order')){
			$orderby = I('order');
			if($orderby == 1){
				$order = "a.status ASC,a.addtime ASC";
				$orderby = 2;
			}else{
				$order = "a.status ASC,a.addtime DESC";
			}
		}else{
			$order = "a.status ASC,a.addtime DESC";
			$orderby = 1;
		}
		$data['a.is_del'] = 2; 
		$count  = $list = M("subscribe")->join('AS a LEFT JOIN __LAB_LIST__ AS b ON a.aboratory_id = b.s_id')->where($data)->count();// 查询满足要求的总记录数
		$Page   = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show   = $Page->show();// 分页显示输出
		$list = M("subscribe")->field('a.*,b.lab_name')->where($data)->join('AS a LEFT JOIN __LAB_LIST__ AS b ON a.aboratory_id = b.s_id')->limit($Page->firstRow.','.$Page->listRows)->order($order)->select();
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		foreach ($list as $key => $value) {
			//查询课时
			$class_hour = explode(',', $value['class_hour']);
			$cl_n = '';
			foreach ($class_hour as $k => $v) {
				$class_name = M('classhour_list')->where(array('c_id'=>$v))->getField('classhour_name');
				$cl_n .= $class_name.",";
			}
			$cl_n = rtrim($cl_n, ",");	
			$list[$key]['cl_n'] = $cl_n;
			//判断已经超过实际预约时间的可以删除
			$now_time = time();
			$subscribe_time = $value['subscribe_time'] + 60*60*24;
			if($now_time > $subscribe_time || $value['status'] == 3){//如果超过预约时间或者已拒绝
				$list[$key]['overtime'] = 1;
			}else{
				$list[$key]['overtime'] = 2;
			}
		}
		//实验室列表
		$lab_list = M("lab_list")->field("s_id,lab_name")->where(array('is_del'=>1,'status'=>1))->select();
		$this->assign('orderby',$orderby);
		$this->assign('list',$list);
		$this->assign('lab_list',$lab_list);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	//审核操作
	public function checkedRun(){
		$subscribe_id = I('subscribe_id');
		if(I('status') == 2){
			$map['subscribe_id'] = $subscribe_id;
			$info = M('subscribe')->field('aboratory_id,class_hour,number_of_weeks,week')->where($map)->find();
			$class_hour = explode(',', $info['class_hour']);
			foreach ($class_hour as $key => $value) {
				$is_check['status'] = 2;
				$is_check['aboratory_id'] = $info['aboratory_id'];
				$is_check['number_of_weeks'] = $info['number_of_weeks'];
				$is_check['week'] = $info['week'];
				$is_check['_string'] = "FIND_IN_SET(".$value.",class_hour)";
				$check = M('subscribe')->where($is_check)->find();
				$classhour_name = M('classhour_list')->where('c_id='.$value)->getField('classhour_name');
				if($check){
					$this->error($classhour_name.'已被预约',U('checked'),1);
				}
			}
		}
		
		$data['opinion'] = I('opinion');
		$data['status'] =  I('status');
		$re = M('subscribe')->where('subscribe_id ='.$subscribe_id)->save($data);
		if($re!==FALSE){
			if(I('status')==2){
				$this->success('同意成功',U('checked'),1);
			}else{
				$this->success('驳回成功',U('checked'),1);
			}
            
        }else{
        	if(I('status')==2){
				$this->error('同意失败',U('checked'),1);
			}else{
				$this->error('驳回失败',U('checked'),1);
			}
        }
	}
	/*
	 * 预约 查询
	 */
	public function subscribeSelect(){
		$id = session('aid'); //当前登陆人员ID
		$groupid = M('auth_group_access')->where(array('uid'=>$id))->getField('group_id');
		if($groupid == 2){ //如果为管理员，只可查询自己管理的实验室
			$sid = M('lab_list')->where(array('admin_id'=>$id))->field('s_id')->select();//查询当前人员管理的实验室
			foreach ($sid as $key => $value) {
				$sidd[] = $value['s_id'];
			}
			$siddd = implode(',',$sidd);
			if($siddd){
				$where['a.aboratory_id'] = array('in',$siddd);
			}else{
				$where['a.aboratory_id'] = 0;
			}
		}
		$search = I('search');
		if($search){
			if($search['aboratory_id']){
            	$where['aboratory_id'] = $search['aboratory_id'];
            }
		}
		//查询最近两周
		$starttime = strtotime(date('Y-m-d', time())); //获取当日期的时间戳
		$endtime = strtotime(date('Y-m-d', time()))+1209599;

		$where['a.subscribe_time'] = array(array('gt',$starttime),array('elt',$endtime),'AND');
		$where['a.status'] = 2;//已通过
		$where['a.is_del'] = 2; //未删除
		$count = M('subscribe')->where($where)->join('AS a LEFT JOIN __LAB_LIST__ AS b ON a.aboratory_id=b.s_id')->count();
    	$Page= new \Think\Page($count,10);
    	$show= $Page->show();
		$list = M('subscribe')
				->where($where)
				->join('AS a LEFT JOIN __LAB_LIST__ AS b ON a.aboratory_id=b.s_id')
				->field('a.*,b.lab_name')
				->order('a.subscribe_time DESC')
				->limit($Page->firstRow.','.$Page->listRows)
				->select();
		//查找课时名称
		foreach($list as $key => $value){
			$hour = explode(',',$value['class_hour']);	
			$hourName = array();
			foreach ($hour as $k => $v){
				$hourName[] = M('classhour_list')->where(array('c_id'=>$v))->getField('classhour_name');
			}
			$list[$key]['class_hour'] = implode(',',$hourName);
		}
		//实验室列表
		$lab_list = M("lab_list")->field("s_id,lab_name")->where(array('is_del'=>1,'status'=>1))->select();
		$this->assign('lab_list',$lab_list);		
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->assign('search',$search);
		$this->display();
	}
	/*
	 * 预约
	 */
	public function  subscribePre(){
		//当前登录的用户的id\信息
		$cur_admin_id = $_SESSION['aid'];
		$cur_admin_realname = M("admin")->where(array("admin_id"=>$cur_admin_id))->getField("admin_realname");
		$cur_admin_tel = M("admin")->where(array("admin_id"=>$cur_admin_id))->getField("admin_tel");
		//实验室列表	
		$lab_list = M("lab_list")->field("s_id,lab_name")->where(array('status'=>1,'is_del'=>1))->select();//未删除，并且状态可用
		$this->assign("lab_list",$lab_list);
		//用户列表
		$admin_list = M("admin")->field("admin_id,admin_realname")->where(array("admin_open"=>1,"is_del"=>1))->select();
		$this->assign("admin_list",$admin_list);
		//课时列表
		$classhour_list = M("classhour_list")->field("c_id,classhour_name")->where(array("is_del"=>1))->order("sort ASC")->select();
		$this->assign("classhour_list",$classhour_list);
		//系部列表
		$department_list = M("department_list")->field("department_id,department_name")->where(array("is_del"=>1))->order("sort DESC")->select();
		//教学周期总数
		$data['is_del'] = 2;
		$info = M('semester')->where($data)->order('semester_addtime desc')->limit(1)->find();
		$start = $info['semester_startime'];$end = $info['semester_endtime'];
		$now_w = date("w",$info['semester_startime']);//当前星期,返回1,2,3 注：日期
		$end_w = date('w',$info['semester_endtime']);//最后一天星期
		$this->assign("start_w",$now_w);
		$this->assign("end_w",$end_w);
		$days = intval(($end - $start )/60/60/24);//总天数
		//第一个个教学周天数
		if($now_w == 0){
			$first_w = 1;
		}else{
			$first_w = 7 - $now_w +1;
		}
		//总共教学周
		$weeks = 1+ ceil(($days-$first_w)/7);
		$this->assign("weeks",$weeks);
		$weekarray=array("一","二","三","四","五","六","日"); //先定义一个数组
		$this->assign('weekarray',$weekarray);

		//学期开始时间
		$start = date('Y-m-d',$start);
		//当前时间
		$current_time = date('Y-m-d',time());
		$cur =strtotime($current_time);
		if($cur - $end > 0){
			$is_outtime = 1;
		}else{
			$is_outtime = 2;
		}

		//当前是第几周
		$ww = $this->get_weekend_days(date('Y-m-d',$info['semester_startime']),$current_time);
		$ww = $ww +1;
		if(time() < $info['semester_startime']){
			$ww = 1;
		}elseif(time()+0 > $info['semester_endtime']){
			$ww = $weeks;
		}
		
		$all_ww_arr = range(1,$weeks);
		$this->assign("all_ww_arr",$all_ww_arr);
		$this->assign("cur_ww",$cur_ww);
		$this->assign("ww",$ww);
		//今天为周几
		$this->assign('weekday',$weekarray[date("w")-1]);
		$this->assign("department_list",$department_list);
		$this->assign("cur_admin_id",$cur_admin_id);
		$this->assign("cur_admin_realname",$cur_admin_realname);
		$this->assign("cur_admin_tel",$cur_admin_tel);
		$this->assign('is_outtime',$is_outtime);
		$this->display();
	}
	/*
	预约处理-添加
	 */
	public function subscribeAdd(){

		$data = $_POST;
		$data['status'] = 1;
		$data['is_del'] = 2;
		$data['addtime'] = time();
		$class_hour_arr = $data['class_hour'];
		$data['class_hour'] = implode(',', $data['class_hour']);
		$number_of_weeks = $_POST['number_of_weeks'];//选的周数
		
		unset($_POST['number_of_weeks']);
		$data['admin_id'] = M('admin')->where(array('admin_realname'=>$_POST['subscribe_person']))->getField('admin_id');
		//判断当前周几
		$cur_week = date("w");
		//教学开始时间
		$info = M('semester')->where(array('is_del'=>2))->order('semester_addtime desc')->limit(1)->find();
		$start = $info['semester_startime'];
		$week = $data['week'];//选择的周几
		if($week == 0){
			$week = 7;
		}

		$start_weekday = date("w",$start); //开学是周几
		if($start_weekday == 0){
			$start_weekday = 7;
		}
		
		if(count($number_of_weeks)>1){//如果周数是多选
			foreach($number_of_weeks as $v){
				$data['number_of_weeks'] = $v;//周数
				if($week > $start_weekday){	//如果选择的周几比开学的周大 则追加几天的时间戳
					$days = $week - $start_weekday;
					$data['subscribe_time'] = $start + (60*60*24*7)*($v-1) + (60*60*24*$days); 
				}elseif($week < $start_weekday){//如果选择的周几比开学的周大
					$days = $start_weekday - $week; //如果选择的周几比开学的周大 则减去几天的时间戳
					$data['subscribe_time'] = $start + (60*60*24*7)*($v-1) - (60*60*24*$days); 
				}else{
					$data['subscribe_time'] = $start + (60*60*24*7)*($v-1);
				}
				//判断同一天同一课时同一实验室是否已经被预约
				$class_hour = explode(',', $data['class_hour']);
				foreach ($class_hour as $key => $value) {
					$is_check['aboratory_id'] = $data['aboratory_id'];
					$is_check['number_of_weeks'] = $data['number_of_weeks'];
					$is_check['week'] = $data['week'];
					$is_check['status'] = 2;
					$is_check['_string'] = "FIND_IN_SET(".$value.",class_hour)";
					$check = M('subscribe')->where($is_check)->find();
					$classhour_name = M('classhour_list')->where('c_id='.$value)->getField('classhour_name');
					if($check){
						$this->error($classhour_name.'已被预约',U('subscribe'),1);
					}
				}
				$cur_time = date('Ymd').'001';//预约标号。 20171214001 
				$result = M('subscribe')->where(array('subscribe_code'=>$cur_time))->find();
				if($result){
					$max_code = M('subscribe')->max('subscribe_code');
					$data['subscribe_code'] = $max_code + 1;
				}else{
					$data['subscribe_code'] = $cur_time;
				}
				$re = M('subscribe')->add($data);
			}
		}else{
			$number_of_weeks = $number_of_weeks[0];
			$cur_time = date('Ymd').'001';
			$result = M('subscribe')->where(array('subscribe_code'=>$cur_time))->find();
			//判断同一天同一课时同一实验室是否已经被预约
			$class_hour = explode(',', $data['class_hour']);
			foreach ($class_hour as $key => $value) {
				$is_check['aboratory_id'] = $data['aboratory_id'];
				$is_check['number_of_weeks'] = $number_of_weeks;
				$is_check['week'] = $data['week'];
				$is_check['status'] = 2;
				$is_check['_string'] = "FIND_IN_SET(".$value.",class_hour)";
				$check = M('subscribe')->where($is_check)->find();
				$classhour_name = M('classhour_list')->where('c_id='.$value)->getField('classhour_name');
				if($check){
					$this->error($classhour_name.'已被预约',U('subscribe'),1);
				}
			}
			if($result){
				$max_code = M('subscribe')->max('subscribe_code');
				$data['subscribe_code'] = $max_code + 1;
			}else{
				$data['subscribe_code'] = $cur_time;
			}
			$data['number_of_weeks'] = $number_of_weeks[0];
			if($week > $start_weekday){	//如果选择的周几比开学的周大 则追加几天的时间戳
					$days = $week - $start_weekday;
					$data['subscribe_time'] = $start + (60*60*24*7)*($data['number_of_weeks']-1) + (60*60*24*$days); 
				}elseif($week < $start_weekday){//如果选择的周几比开学的周大
					$days = $start_weekday - $week; //如果选择的周几比开学的周大 则减去几天的时间戳
					$data['subscribe_time'] = $start + (60*60*24*7)*($data['number_of_weeks']-1) - (60*60*24*$days); 
				}else{
					$data['subscribe_time'] = $start + (60*60*24*7)*($data['number_of_weeks']-1);
				}
			$re = M('subscribe')->add($data);
		}
		if($re > 0){
            $this->success('提交成功',U('subscribe',array('p'=>I('p'))),1);
        }else{
            $this->error('提交失败',U('subscribe',array('p'=>I('p'))),1);
        }
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
	//多选删除
    public function alldel(){
    	$typeid = I('typeid');
        $subscribe_id = I('subscribe_id');
        $map['subscribe_id']  = array('in',$subscribe_id);
        $re = M('subscribe')->where($map)->save(array('is_del'=>1));
        if($typeid == 2){
        	if($re !== false){
	            $this->success('删除成功',U('subscribeSelect'),1);
	        }else{
	            $this->error('删除失败',U('subscribeSelect'),1);
	        }
        }elseif($typeid == 3){
        	if($re !== false){
	            $this->success('删除成功',U('checked'),1);
	        }else{
	            $this->error('删除失败',U('checked'),1);
	        }
        }else{
        	if($re !== false){
	            $this->success('删除成功',U('subscribe'),1);
	        }else{
	            $this->error('删除失败',U('subscribe'),1);
	        }
        }
    }
}