<?php
// +----------------------------------------------------------------------
// | 公告管理模块
// +----------------------------------------------------------------------
// | 高利杰 2017.12.9
// +----------------------------------------------------------------------
namespace Feedback\Controller;
use Common\Controller\AuthController;
use Feedback\Model\CCPRestSDK;
class NoticeController extends AuthController{
	//公告列表页
	public function index(){
        $cur_admin_id = $_SESSION['aid'];
        $notice_db = M('notice');
		$search = I("search");
    	if($search){
            if($search['start_time']){
                $start_time = strtotime($search['start_time']);
                $where['addtime'] = array('egt',$start_time);
            }
            if($search['end_time']){
                $end_time = strtotime($search['end_time']." 23:59:59");
                $where['addtime'] = array("elt",$end_time);
            }
            if($search['start_time'] && $search['end_time']){
                $start_time = strtotime($search['start_time']);
                $end_time = strtotime($search['end_time']." 23:59:59");
                $where['addtime'] = array(array('egt',$start_time),array('elt',$end_time),'AND');
            }
            if(trim($search['title'])){
            	$where['title']=array('like','%'.trim($search['title']).'%');
            }
        }
        $where['a.is_del'] = 2;
    	$count = $notice_db->join("AS a LEFT JOIN __ADMIN__ as b ON a.admin_id = b.admin_id")->where($where)->count('n_id');
    	$Page= new \Think\Page($count,15);
    	$show= $Page->show();
    	$list = $notice_db->join("AS a LEFT JOIN __ADMIN__ as b ON a.admin_id = b.admin_id")->field('a.*,b.admin_realname')->where($where)->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as $key => $value) {
            $list[$key]['title'] = subtext($value['title'],20);//截取20...

            $list[$key]['content'] = subtext($value['content'],200);//截取200...
        }
        //查询当前用户的身份级别
        $group = M('auth_group_access')->where(array('uid'=>$cur_admin_id))->getField('group_id');
        $this->assign('group',$group);
        $this->assign('cur_admin_id',$cur_admin_id);
    	$this->assign('search',$search);
    	$this->assign('page',$show);
    	$this->assign("list",$list);
    	$this->display();	
	}
    //添加公告
    public function noticeAdd()
    {
        if(IS_AJAX)
        {
            $title = trim(I('title'));
            $content = I('content');
            if(!$title || !$content)
            {
                $this->error('缺少上传参数',0);
            }
            else
            {
                $indata = array(
                    'title'=>$title,
                    'content'=>$content,
                    'addtime'=>time(),
                    'admin_id'=>session('aid'),
                    'is_del'=>2,
                );
                $re = M('notice')->add($indata);
                if($re)
                {
                    $this->success('发布成功',U('index'),1);
                }else{
                    $this->error('发布失败',U('index'),1);
                }
            }
        }
        else
        {
            $admin_realname = M('admin')->where(array('admin_id'=>session('aid')))->getField('admin_realname');
            $this->assign('admin_realname',$admin_realname);
            $this->display();
        }  
    }
	//公告详情
    public function noticeInfo()
    {
        $p = I('p');
        $n_id = I('id');
        $notice_db = M('notice');
        $info = $notice_db->field('a.*,b.admin_realname')->where(array('n_id'=>$n_id))->join("AS a LEFT JOIN __ADMIN__ as b ON a.admin_id = b.admin_id")->find();
        $this->assign('info',$info);
        $this->assign('p',$p);
        $this->display();
    }
    //删除公告
    public function noticeDel()
    {
        $id = I('id');
        $data['is_del'] = 1;
        $re = M('notice')->where(array('n_id'=>$id))->save($data);
        if($re > 0){
            $this->success('删除成功',U('index',array('p'=>I('p'))),1);
        }else{
            $this->error('删除失败',U('index',array('p'=>I('p'))),1);
        }
    }
    //多选删除
    public function noticeAlldel(){
        $n_id = I('n_id');
        $data['is_del'] = 1;
        $map['n_id']  = array('in',$n_id);
        $re = M('notice')->where($map)->save($data);
        if($re > 0){
            $this->success('删除成功',U('index',array('p'=>I('p'))),1);
        }else{
            $this->error('删除失败',U('index',array('p'=>I('p'))),1);
        }
    }
    //短信测试验证
    public function smsTest(){
        $this->display();
    }
    public function mytest(){
        $to = '13081102378';
        $datas = '你今天吃饭了吗?';
        $tempId = 1;
        $this->sendTemplateSMS($to,$datas,$tempId);
    }
    public function sendTemplateSMS($to,$datas,$tempId)
    {
         // 初始化REST SDK
         //global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
         //主帐号
        $accountSid= '8aaf07086077a6e6016078fc8be0012b';

        //主帐号Token
        $accountToken= '979c9a2936c8427ba30589cc767a3337';

        //应用Id
        $appId='8aaf07086077a6e6016078fc8c400132';

        //请求地址，格式如下，不需要写https://
        $serverIP='app.cloopen.com';

        //请求端口 
        $serverPort='8883';

        //REST版本号
        $softVersion='2013-12-26';

         $rest = new REST($serverIP,$serverPort,$softVersion);
         $rest->setAccount($accountSid,$accountToken);
         $rest->setAppId($appId);
        
         // 发送模板短信
         echo "Sending TemplateSMS to $to <br/>";
         $result = $rest->sendTemplateSMS($to,$datas,$tempId);
         if($result == NULL ) {
             echo "result error!";
             break;
         }
         if($result->statusCode!=0) {
             echo "error code :" . $result->statusCode . "<br>";
             echo "error msg :" . $result->statusMsg . "<br>";
             //TODO 添加错误处理逻辑
         }else{
             echo "Sendind TemplateSMS success!<br/>";
             // 获取返回信息
             $smsmessage = $result->TemplateSMS;
             echo "dateCreated:".$smsmessage->dateCreated."<br/>";
             echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
             //TODO 添加成功处理逻辑
         }
    }
}