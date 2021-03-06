<?php
// +----------------------------------------------------------------------
// | 字典管理模块
// +----------------------------------------------------------------------
// | 字典管理
// +----------------------------------------------------------------------
// | 石保帅  2017.11.7
// +----------------------------------------------------------------------
namespace Dictionary\Controller;
use Common\Controller\AuthController;
use Org\Util\Stringnew;
class IndexController extends AuthController {
	//顶级分类列表
	public function index()
	{
		$site_db = M('site_dictionary');
		$count = $site_db->where(array('parent_id'=>0))->count();
		$Page= new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show= $Page->show();
		$list = $site_db->where(array('parent_id'=>0))->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	/*
	 * 实验室管理列表
	 */
	public function laboratoryEdit()
	{	
		$count =  M('lab_list')->count();
		$Page= new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show= $Page->show();
		$list = M('lab_list')
				->where(array('a.is_del'=>1))
				->join('AS a LEFT JOIN __ADMIN__ AS b ON a.admin_id=b.admin_id')
				->join('LEFT JOIN __DEPARTMENT_LIST__ AS c ON a.department_id=c.department_id')
				->field('a.*,b.admin_realname,c.department_name')
				->order('addtime DESC')
				->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}
	/*
	 * 实验室添加
	 */
	public function laboratoryAdd()
	{	
		//查找系部
		$department_list = M('department_list')->where(array('is_del'=>1))->select();
		//查找管理员
		$admin_list = M('admin')->where(array('a.admin_open'=>1,'a.is_del'=>1,'b.group_id'=>2))->alias('a')->join('__AUTH_GROUP_ACCESS__ b ON b.uid= a.admin_id')->select();
		if(IS_AJAX){
			$data['lab_name'] = I('lab_name');
			$data['lab_loudong'] = I('lab_loudong');
			$data['lab_floor'] = I('lab_floor');
			$data['lab_number'] = I('lab_number');
			$data['lab_capacity'] = I('lab_capacity');
			$data['admin_id'] = I('admin_id');
			$data['department_id'] = I('department_id');
			$data['status'] = I('status');
			$data['content'] = I('content');
			$data['remark'] = I('remark');
			$data['addtime'] = time();
			$res = M('lab_list')->where(array('lab_name'=>$data['lab_name']))->find();
			if($res){
				$this->error('该实验室已有管理员',U('laboratoryAdd'),1);
			}else{
				$ress = M('lab_list')->add($data);
				if($ress){
					$this->success('添加成功',U('laboratoryEdit'),1);
				}else{
					$this->error('添加失败',U('laboratoryEdit'),1);
				}
			}
		}
		$this->assign('delist',$department_list);
		$this->assign('adlist',$admin_list);
		$this->display();
	}
	/*
	 *编辑实验室
	 */
	public function laboratorygl()
	{	
		$s_id = I('s_id');
		$p = I('p');
		//查找系部
		$department_list = M('department_list')->where(array('is_del'=>1))->select();
		//查找管理员
		// $admin_list = M('admin')->where(array('admin_open'=>1,'is_del'=>1))->select();
		$admin_list = M('admin')->where(array('a.admin_open'=>1,'a.is_del'=>1,'b.group_id'=>2))->alias('a')->join('__AUTH_GROUP_ACCESS__ b ON b.uid= a.admin_id')->select();
		$info = M('lab_list')->where(array('s_id'=>$s_id))->find();
		if(IS_AJAX){
			$s_id = I('s_id');
			$data['lab_name'] = I('lab_name');
			$data['lab_loudong'] = I('lab_loudong');
			$data['lab_floor'] = I('lab_floor');
			$data['lab_number'] = I('lab_number');
			$data['lab_capacity'] = I('lab_capacity');
			$data['admin_id'] = I('admin_id');
			$data['department_id'] = I('department_id');
			$data['status'] = I('status');
			$data['content'] = I('content');
			$data['remark'] = I('remark');
			$data['addtime'] = time();
			$res = M('lab_list')->where(array('s_id'=>$s_id))->save($data);
			if($res){
				$this->success('修改成功',U('laboratorygl',array('s_id'=>$s_id,'p'=>$p)));
			}else{
				$this->error('修改失败',U('laboratorygl',array('s_id'=>$s_id,'p'=>$p)));
			}
		}
		$this->assign('delist',$department_list);
		$this->assign('adlist',$admin_list);
		$this->assign('s_id',$s_id);
		$this->assign('info',$info);
		$this->assign('p',$p);
		$this->display();
	}
	/*
	 * 系部批量删除
	 */
	public function labMoneyDel(){
		$checkboxes = I('s_id');
		$ids = implode(',', $checkboxes);
		$where['s_id'] = array('in',$ids);
		if (!empty($ids)) {
			M('lab_list')->where($where)->setField('is_del',2);
			$this->success('删除成功',U('laboratoryEdit'));
		} else {
			$this->error("请选择要删除的数据");
		}
	}
	/*
	 * 专业管理列表
	 */
	public function majorEdit()
	{
		$this->display();
	}
	/*
	 * 专业添加
	 */
	public function majorAdd()
	{
		$this->display();
	}
 
	/*
	 * 系部管理列表
	 */
	public function branchEdit()
	{	
		$count = M('department_list')->where(array('is_del'=>1))->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show  = $Page->show();// 分页显示输出
		$list = M('department_list')->where(array('is_del'=>1))->order('sort DESC')->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}
	/*
	 * 系部添加
	 */
	public function branchAdd()
	{
		if(IS_AJAX){
			$data['department_name'] = I('department_name');
			$data['note'] = I('note');
			$data['sort'] = I('sort');
			$res = M('department_list')->add($data);
			if($res){
				$this->success('添加成功',U('branchEdit'),1);
			}else{
				$this->error('添加失败',U('branchAdd'),1);
			}
		}
		$this->display();
	}
	/*
	 * 系部删除
	 */
	public function branchDel(){
		$department_id = I('id');
		$p = I('p');
		$re = M('department_list')->where(array('department_id'=>$department_id))->setField('is_del',2);
		if($re)
		{
			$this->success('删除成功',U('branchEdit',array('p'=>I('p'))),1);
		}else{
			$this->error('删除失败',U('branchEdit',array('p'=>I('p'))),1);
		}
	}
	/*
	 * 系部批量删除
	 */
	public function branchMoneyDel(){
		$checkboxes = I('department_id');
		$ids = implode(',', $checkboxes);
		$where['department_id'] = array('in',$ids);
		if (!empty($ids)) {
			M('department_list')->where($where)->setField('is_del',2);
			$this->success('删除成功',U('branchEdit'));
		} else {
			$this->error("请选择要删除的数据");
		}
	}
	/*
	 * 课时管理列表
	 */
	public function periodEdit()
	{	
		$count = M('classhour_list')->where(array('is_del'=>1))->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show  = $Page->show();// 分页显示输出
		$list = M('classhour_list')->where(array('is_del'=>1))->order('sort ASC')->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}
	/*
	 * 课时添加
	 */
	public function periodAdd()
	{	
		if(IS_AJAX){
			$data['classhour_name'] = I('classhour_name');
			$data['note'] = I('note');
			$data['sort'] = I('sort');
			$res = M('classhour_list')->add($data);
			if($res){
				$this->success('添加成功',U('periodEdit'),1);
			}else{
				$this->error('添加失败',U('periodEdit'),1);
			}
		}
		$this->display();
	}
	/*
	 * 课时删除
	 */
	public function periodDel(){
		$c_id = I('c_id');
		$p = I('p');
		$re = M('classhour_list')->where(array('c_id'=>$c_id))->setField('is_del',2);
		if($re)
		{
			$this->success('删除成功',U('periodEdit',array('p'=>I('p'))),1);
		}else{
			$this->error('删除失败',U('periodEdit',array('p'=>I('p'))),1);
		}
	}
	/*
	 * 课时批量删除
	 */
	public function periodMoneyDel(){
		$checkboxes = I('c_id');
		$ids = implode(',', $checkboxes);
		$where['c_id'] = array('in',$ids);
		if (!empty($ids)) {
			M('classhour_list')->where($where)->setField('is_del',2);
			$this->success('删除成功',U('periodEdit'));
		} else {
			$this->error("请选择要删除的数据");
		}
	}
	/*
	 * 学期管理列表
	 */
	public function semesterEdit()
	{
		$count = M('semester')->where("is_del = 2")->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show  = $Page->show();// 分页显示输出
		$list = M('semester')->where("is_del = 2")->order('semester_addtime DESC')->select();
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('list',$list);
		$this->display();
	}
	/*
	 * 学期添加
	 */
	public function semesterAdd()
	{
		if(IS_AJAX){
			$data['semester_name'] = I('semester_name');
			$search = I('search');
			$data['semester_startime'] = strtotime($search['start_time']."0:0:0");
			$data['semester_endtime'] =  strtotime($search['end_time']."23:59:59");
			$data['semester_remark'] = I('semester_remark');
			$data['semester_addtime'] = time();
			$res = M('semester')->add($data);
			if($res !== false){
				$this->success('添加成功',U('semesterEdit'),1);
			}else{
				$this->error('添加失败',U('semesterEdit'),1);
			}
		}
		
		$this->display();
	}
	//删除
	public function semesterDel()
	{
		$id = I('semester_id');
		$data['semester_id'] = $id;
		$re = M('semester')->where($data)->save(array('is_del'=>1));
		if($re)
		{
			$this->success('删除成功',U('semesterEdit',array('p'=>I('p'))),1);
		}
	}
	//批量删除
	public function batch()
	{
		$checkboxes = I('semester_id');
		$ids = implode(',', $checkboxes);
		$semester_db = M('semester');
		if (!empty($ids)) {
			$semester_db->where("semester_id in ($ids)")->save(array('is_del'=>1));

			$this->success('删除成功',U('semesterEdit'));
		} else {
			$this->error("请选择要删除的数据");
		}
	}
	//删除分类
	public function catDel()
	{
		$id = I('id');
		$re = M('site_dictionary')->where('d_id ='.$id)->delete();
		if($re)
		{
			$this->success('删除成功',U('index',array('p'=>I('p'))),1);
		}
		else
		{
			$this->error('操作失败',U('index',array('p'=>I('p'))),0);
		}
	}

	//添加分类
	public function catAdd()
	{
		if(IS_AJAX)
		{
			$p_id = I('p_id',0);
			$name = I('names');
			$indata = array(
				'parent_id' =>	$p_id,
				'name'		=> 	$name,
			);
			M('site_dictionary')->add($indata);
			$this->success('添加成功',U('index'),1);
		}
		else
		{
			$this->display();
		}
	}
	//修改排序页面
	public function catEdit()
	{
		if(IS_AJAX)
		{
			$p_id = I('p_id',0);
			$d_id = I('d_id');
			$name = I('names');
			$indata = array(
				'parent_id' =>	$p_id,
				'name'		=> 	$name,
			);
			M('site_dictionary')->where('d_id ='.$d_id)->save($indata);
			$this->success('修改成功',U('index',array('p'=>I('p',1))),1);
		}
		else
		{
			$id = I('id');
			$data = M('site_dictionary')->where('d_id ='.$id)->find();
			$this->assign('p',I('p'));
			$this->assign('data',$data);
			$this->display();
		}	
	}

}