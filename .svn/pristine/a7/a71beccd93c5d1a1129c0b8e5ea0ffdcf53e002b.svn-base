<?php
namespace Common\Controller;
use Think\Controller;
class CommonController extends Controller{
	protected function _initialize(){
		if (!file_exists('./data/install.lock')){
            //不存在，则进入安装
           // header('Location: ' . U('Install/index/index'));
            exit();
        }
		//系统参数
        $info2 = webInfo();
        $this->assign('info',$info2);
       
	}
    //空操作
    public function _empty(){
        $this->error('此操作无效');
    }
}