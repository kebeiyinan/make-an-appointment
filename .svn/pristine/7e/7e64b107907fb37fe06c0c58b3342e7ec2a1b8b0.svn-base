<?php
//ajax控制器
namespace Ajax\Controller;
use Common\Controller\CommonController;
class AjaxController extends CommonController{
	/*
     * 返回行政区域json字符串
     */
	public function getRegion(){
		$Region=M("region");
		$map['pid']=I('pid');
		$map['type']=I('type');
		$list=$Region->where($map)->select();
		echo json_encode($list);
	}
	//Ajax获取地区
	public function getCity()
	{
		$pid = I('pid');
		$citylist = M('region')->where("pid = $pid")->select();
		$str = '';
		foreach ($citylist as $v) {
			$str .= "<option value='".$v['cityid']."'>".$v['name']."</option>";
		}
		$this->ajaxReturn($str);
	}
	//验证会员手机号
	public function isTel(){
		$where['member_list_username'] =I('tel');
		if(I('id')){
			$where['member_list_id'] = array('neq',I('id'));
		}
		$result = M('member_list')->where($where)->find();
		if($result){
			echo 1;
		}else{
			echo 2;
		}
	}
	//验证会员邮箱
	public function isEmail(){
		$where['member_list_email'] =I('email');
		if(I('id')){
			$where['member_list_id'] = array('neq',I('id'));
		}
		$result = M('member_list')->where($where)->find();
		if($result){
			echo 1;
		}else{
			echo 2;
		}
	}
	//验证管理员被占用
	public function isAdmin(){
		$where['admin_username'] =I('username');
		if(I('id')){
			$where['admin_id'] = array('neq',I('id'));
		}
		$result = M('admin')->where($where)->find();
		if($result){
			echo 1;
		}else{
			echo 2;
		}
	}
	 
}