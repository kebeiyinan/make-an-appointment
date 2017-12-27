<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<!-- <title><?php echo ($info["cfg_webname"]); ?></title> -->
	<title>电气与电子工程实验中心管理系统</title>
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

	<!-- bootstrap & fontawesome必须的css -->
	<link rel="stylesheet" href="/public/ace/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/public/font-awesome/css/font-awesome.min.css" />

	<!-- 此页插件css -->

	<!-- ace的css -->
	<link rel="stylesheet" href="/public/ace/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<!-- IE版本小于9的ace的css -->
	<!--[if lte IE 9]>
	<link rel="stylesheet" href="/public/ace/css/ace-part2.min.css" class="ace-main-stylesheet" />
	<![endif]-->

	<!--[if lte IE 9]>
	<link rel="stylesheet" href="/public/ace/css/ace-ie.css" />
	<![endif]-->

	<link rel="stylesheet" href="/public/yfcmf/yfcmf.css" />
	<!-- 此页相关css -->

	<!-- ace设置处理的css -->

	<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

	<!--[if lte IE 8]>
	<script src="/public/others/html5shiv.min.js"></script>
	<script src="/public/others/respond.min.js"></script>
	<![endif]-->
    <!-- 引入基本的js -->
    <script type="text/javascript">
        var admin_ueditor_handle = "<?php echo U('Admin/Sys/upload');?>";
        var admin_ueditor_lang ='zh-cn';
    </script>
    <!--[if !IE]> -->
    <script src="/public/others/jquery.min-2.2.1.js"></script>
    <!-- <![endif]-->
    <!-- 如果为IE,则引入jq1.12.1 -->
    <!--[if IE]>
    <script src="/public/others/jquery.min-1.12.1.js"></script>
    <![endif]-->

    <!-- 如果为触屏,则引入jquery.mobile -->
    <script type="text/javascript">
        if('ontouchstart' in document.documentElement) document.write("<script src='/public/others/jquery.mobile.custom.min.js'>"+"<"+"/script>");
    </script>
    <script src="/public/others/bootstrap.min.js"></script>
</head>

<body class="no-skin">
<!-- 导航栏开始 -->
<div id="navbar" class="navbar navbar-default navbar-fixed-top">
	<div class="navbar-container" id="navbar-container">
		<!-- 导航左侧按钮手机样式开始 -->
		<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
			<span class="sr-only">Toggle sidebar</span>

			<span class="icon-bar"></span>

			<span class="icon-bar"></span>

			<span class="icon-bar"></span>
		</button><!-- 导航左侧按钮手机样式结束 -->
		<button data-target="#sidebar2" data-toggle="collapse" type="button" class="pull-left navbar-toggle collapsed">
			<span class="sr-only">Toggle sidebar</span>
			<i class="ace-icon fa fa-dashboard white bigger-125"></i>
		</button>
		<!-- 导航左侧正常样式开始 -->
		<div class="navbar-header pull-left">
			<!-- logo -->
			<a href="<?php echo U('Admin/Index/index');?>" class="navbar-brand">
				<small>
					<!-- <i class="fa fa-leaf"></i> -->
					<img src="/public/img/logo.png" style="height:50px;width:auto" />
					<!-- <?php echo ($info["cfg_webname"]); ?> -->
					电气与电子工程实验中心管理系统
				</small>
			</a>
		</div><!-- 导航左侧正常样式结束 -->

		<!-- 导航栏开始 -->
		<div class="navbar-buttons navbar-header pull-right" role="navigation">
			<ul class="nav ace-nav">
				<li class="purple">
					<a data-info="确定要清理缓存吗？" class="confirm-rst-btn" href="<?php echo U('Admin/Sys/clear');?>" style="width:160px;height: 70px;line-height: 70px;font-size: 14px;">
						清除缓存
					</a>
				</li>

				<!-- 用户菜单开始 -->
				<li class="light-blue dropdown-modal" style="height:70px;">
					<a data-toggle="dropdown" href="#" class="dropdown-toggle" style="height: 70px;padding-top: 10px;">
						<?php if($_SESSION['admin_avatar'] != ''): ?><img class="nav-user-photo" src="/data/upload/avatar/<?php echo ($_SESSION['admin_avatar']); ?>" alt="<?php echo ($_SESSION['admin_username']); ?>" />
							<?php else: ?>
							<img class="nav-user-photo" src="/public/img/girl.jpg" alt="<?php echo ($_SESSION['admin_username']); ?>" /><?php endif; ?>
								<span class="user-info">
									<small>欢迎,</small>
									<?php echo ($_SESSION['admin_username']); ?>
								</span>

						<i class="ace-icon fa fa-caret-down"></i>
					</a>

					<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
						<li>
							<a href="<?php echo U('Admin/Sys/profile');?>">
								<i class="ace-icon fa fa-user"></i>
								会员中心
							</a>
						</li>

						<li class="divider"></li>

						<li>
							<a href="<?php echo U('Admin/Login/logout');?>"  data-info="你确定要退出吗？" class="confirm-btn">
								<i class="ace-icon fa fa-power-off"></i>
								注销
							</a>
						</li>
					</ul>
				</li><!-- 用户菜单结束 -->
			</ul>
		</div><!-- 导航栏结束 -->
	</div><!-- 导航栏容器结束 -->
</div><!-- 导航栏结束 -->

<!-- 整个页面内容开始 -->
<div class="main-container" id="main-container">
	<!-- 菜单栏开始 -->
	<div id="sidebar" class="sidebar responsive sidebar-fixed">
	<div class="sidebar-shortcuts" id="sidebar-shortcuts">
		<!--左侧顶端按钮-->
		<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
			<a class="btn btn-success" href="<?php echo U('Admin/Index/index');?>" style="width:150px;"  title="">系统首页</a>
		</div>
	</div>
	<!-- 菜单列表开始 -->
	<ul class="nav nav-list">
		<!--一级菜单遍历开始-->
		<?php if(is_array($menus)): foreach($menus as $key=>$v): if(!empty($v['_child'])): ?><li class="<?php if((count($menus_curr) >= 1) AND ($menus_curr[0] == $v['id'])): ?>open<?php endif; ?>">
			<a href="#" class="dropdown-toggle">
				<i class="menu-icon fa <?php echo ($v["css"]); ?>"></i>
				<span class="menu-text"><?php echo ($v["title"]); ?></span>
				<b class="arrow fa fa-angle-down"></b>
			</a>
			<ul class="submenu">
				<!--二级菜单遍历开始-->
				<?php if(is_array($v["_child"])): foreach($v["_child"] as $key=>$vv): if(!empty($vv['_child'])): ?><li class="<?php if((count($menus_curr) >= 2) AND ($menus_curr[1] == $vv['id'])): ?>active open<?php endif; ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-caret-right"></i>
						<?php echo ($vv["title"]); ?>
						<b class="arrow fa fa-angle-down"></b>
					</a>
					<b class="arrow"></b>
					<ul class="submenu">
						<!--三级菜单遍历开始-->
						<?php if(is_array($vv["_child"])): foreach($vv["_child"] as $key=>$vvv): ?><li class="<?php if((count($menus_curr) >= 3) AND ($menus_curr[2] == $vvv['id'])): ?>active<?php endif; ?>">
							<a href="<?php echo U($vvv['name']);?>">
								<i class="menu-icon fa fa-caret-right"></i>
								<?php echo ($vvv["title"]); ?>
							</a>
							<b class="arrow"></b>
							</li><?php endforeach; endif; ?><!--三级菜单遍历结束-->
					</ul>
					</li>
					<?php else: ?>
					<li class="<?php if((count($menus_curr) >= 2) AND ($menus_curr[1] == $vv['id'])): ?>active<?php endif; ?>">
					<a href="<?php echo U($vv['name']);?>">
						<i class="menu-icon fa fa-caret-right"></i>
						<?php echo ($vv["title"]); ?>
					</a>
					<b class="arrow"></b>
					</li><?php endif; endforeach; endif; ?><!--二级菜单遍历结束-->
			</ul>
			</li>
			<?php else: ?>
			<li class="<?php if((count($menus_curr) >= 1) AND ($menus_curr[0] == $v['id'])): ?>active<?php endif; ?>">
			<a href="<?php echo U($v['name']);?>">
				<i class="menu-icon fa fa-caret-right"></i>
				<?php echo ($v["title"]); ?>
			</a>
			<b class="arrow"></b>
			</li><?php endif; endforeach; endif; ?><!--一级菜单遍历结束-->
	</ul><!-- 菜单列表结束 -->

	<!-- 菜单栏缩进开始 -->
	<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
		<i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
	</div><!-- 菜单栏缩进结束 -->
</div>
	<!-- 菜单栏结束 -->

	<!-- 主要内容开始 -->
	<div class="main-content">
		<div class="main-content-inner">
			<!-- 右侧主要内容页顶部标题栏开始 -->
			<div id="sidebar2" class="sidebar h-sidebar navbar-collapse collapse sidebar-fixed menu-min" data-sidebar="true" data-sidebar-scroll="true" data-sidebar-hover="true">
	<div class="nav-wrap-up pos-rel">
		<div class="nav-wrap">
			<ul class="nav nav-list">
				<li>
					<a href="<?php echo U('Admin/Index/index');?>">
						<o class="font12">欢迎进入<?php echo ($info["cfg_webname"]); ?>管理系统</o>
					</a>
					<b class="arrow"></b>
				</li>
			
			</ul>
		</div>
	</div><!-- /.nav-list -->
</div>
			<!-- 右侧主要内容页顶部标题栏结束 -->

			<!-- 右侧下主要内容开始 -->
			
    <link href="/public/content/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <script src="/public/content/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/public/content/js/bootstrap-datetimepicker.zh-CN.js" type="text/javascript"></script>
    <style>
    	.over-h a{
    		display: block;
    		width:300px;
    		overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
    	}
    </style>
    <div class="page-content">
		<div class="row">
			<div class="col-xs-8 col-sm-8">
				<div class="col-xs-12 col-sm-12">
					<p style="text-align: center;font-size: 20px;font-weight: bold;">教学日历</p>
					<form name="admin_list_sea" class="form-search" method="post" action="<?php echo U('index');?>">
						<div class="row maintop">
							<div class="col-xs-4 hidden-xs btn-sespan">
								<div class="input-group col-sm-12 col-xs-12">
									<select name="s_id" class="col-sm-12 col-xs-12">
										<?php if(is_array($lab_list)): foreach($lab_list as $key=>$v): ?><option value="<?php echo ($v['s_id']); ?>" <?php if($s_id == $v['s_id']): ?>selected<?php endif; ?>><?php echo ($v['lab_name']); ?></option><?php endforeach; endif; ?>
									</select>
								</div>
							</div>
							<div class="col-xs-4 hidden-xs btn-sespan">
								<div class="input-group col-sm-12 col-xs-12">
									<select name="number_of_weeks" class="col-sm-12 col-xs-12">
										<?php $__FOR_START_2191__=1;$__FOR_END_2191__=$weeks;for($i=$__FOR_START_2191__;$i <= $__FOR_END_2191__;$i+=1){ ?><option value="<?php echo ($i); ?>" <?php if($ww == $i): ?>selected<?php endif; ?>>学期第<?php echo ($i); ?>周</option><?php } ?>
									</select>
								</div>
							</div>
							<div class="col-xs-2 btn-sespan">
								<div class="input-group">
									<span class="input-group-btn">
										<button type="submit" class="btn btn-xs btm-input btn-purple">
											<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
											搜索
										</button>
									</span>
								</div>
							</div>
						</div>
					</form>
				</div>
	            <div class="col-xs-12">
	                <table class="table table-striped table-bordered table-hover" id="dynamic-table">
	                    <thead>
	                        <tr>
	                        	<th class="hidden-sm hidden-xs" style="width: 100px;">课时\周期</th>
	                        	<?php if(is_array($weekarray)): foreach($weekarray as $key=>$v): ?><th class="hidden-sm hidden-xs" <?php if($weekday == $v): ?>style="background-color:green;color: #fff;"<?php endif; ?> ><?php echo ($v); ?></th><?php endforeach; endif; ?> 
	                        </tr>
	                    </thead>
	                    <tbody>
	                    	
	                    	<?php if(is_array($classhour_list)): foreach($classhour_list as $key=>$v): ?><tr>
			                    	<th class="hidden-sm hidden-xs" ><?php echo ($v['classhour_name']); ?></th>
			                    	 <!--style="background-color: #FF0000;color: #fff;"-->
			                    	<?php if($ww == 1): $__FOR_START_22434__=1;$__FOR_END_22434__=$now_w;for($i=$__FOR_START_22434__;$i < $__FOR_END_22434__;$i+=1){ ?><td class="hidden-sm hidden-xs"></td><?php } ?>
			                    	 	<?php $__FOR_START_23603__=$now_w;$__FOR_END_23603__=7;for($i=$__FOR_START_23603__;$i <= $__FOR_END_23603__;$i+=1){ if($v['kk']['week'] == $i): ?><td class="hidden-sm hidden-xs" style="background-color: #FF0000;color: #fff;">已占用</td>
		                    	 			<?php else: ?>
		                    	 				<td class="hidden-sm hidden-xs"><a href="<?php echo U('Member/Member/subscribePre');?>">未占用</a></td><?php endif; } ?>
			                    	<?php elseif($ww == $weeks): ?>
			                    	
			                    		<?php $__FOR_START_31728__=1;$__FOR_END_31728__=$end_w;for($i=$__FOR_START_31728__;$i <= $__FOR_END_31728__;$i+=1){ if($v['kk']['week'] == $i): ?><td class="hidden-sm hidden-xs" style="background-color: #FF0000;color: #fff;">已占用</td>
		                    	 			<?php else: ?>
		                    	 				<td class="hidden-sm hidden-xs"><a href="<?php echo U('Member/Member/subscribePre');?>">未占用</a></td><?php endif; } ?>
			                    	 	
			                    	 	<?php $__FOR_START_5481__=$end_w;$__FOR_END_5481__=7;for($i=$__FOR_START_5481__;$i < $__FOR_END_5481__;$i+=1){ ?><td class="hidden-sm hidden-xs"></td><?php } ?>
			                    	<?php else: ?>
			                    		<?php $__FOR_START_19438__=1;$__FOR_END_19438__=7;for($i=$__FOR_START_19438__;$i <= $__FOR_END_19438__;$i+=1){ if($v['kk']['week'] == $i): ?><td class="hidden-sm hidden-xs" style="background-color: #FF0000;color: #fff;">已占用</td>
		                    	 			<?php else: ?>
		                    	 				<td class="hidden-sm hidden-xs"><a href="<?php echo U('Member/Member/subscribePre');?>">未占用</a></td><?php endif; } endif; ?>		                   		                        
			                    </tr><?php endforeach; endif; ?>
		                    
	                    </tbody>
	                </table>
	            </div>
			</div>
            <div class="col-xs-4">
            	<div class="col-xs-12">
            		<p style="text-align: center;font-size: 20px;font-weight: bold;line-height: 70px;">公告展示</p>
            		 <table class="table table-striped table-bordered table-hover" id="dynamic-table">
            		 	<thead>
                            <tr>
                                <th class="hidden-sm hidden-xs ">标题</th>
                                <th class="hidden-sm hidden-xs">发布时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(is_array($noticeList)): foreach($noticeList as $key=>$vo): ?><tr>
                                    <td class="hidden-sm hidden-xs"><p class="over-h"><a href="<?php echo U('Feedback/Notice/noticeInfo',array('id'=>$vo['n_id']));?>"><?php echo ($vo["title"]); ?></a></p></td>
                                    <td class="hidden-sm hidden-xs"><?php echo (date("Y-m-d H:i:s",$vo["addtime"])); ?></td>
                                </tr><?php endforeach; endif; ?>
                        </tbody>
            		 </table>
            	</div>
            	<?php if($groupid < 3): ?><div class="col-xs-12">
            		<p style="text-align: center;font-size: 20px;font-weight: bold;">教务提醒</p>
            		 <table class="table table-striped table-bordered table-hover" id="dynamic-table">
            		 	<thead>
                            <tr>
                                <th class="hidden-sm hidden-xs">实验室名称</th>
                                <th class="hidden-sm hidden-xs">预约老师</th>
                                <th class="hidden-sm hidden-xs">预约日期</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php if(is_array($office)): foreach($office as $key=>$v): ?><tr>
	                                <td class="hidden-sm hidden-xs"><a href="<?php echo U('Member/Member/checked');?>"><?php echo ($v["lab_name"]); ?></a></td>
	                                <td class="hidden-sm hidden-xs"><?php echo ($v["subscribe_person"]); ?></td>
	                                <td class="hidden-sm hidden-xs"><?php echo (date("Y-m-d",$v["subscribe_time"])); ?></td>
	                            </tr><?php endforeach; endif; ?>
                        </tbody>
            		 </table>
            	</div><?php endif; ?>
            </div>
        </div>
    </div><!-- /.page-content -->                            

			<!-- 右侧下主要内容结束 -->
		</div>
	</div><!-- 主要内容结束 -->
	<!-- 页脚开始 -->
	<div class="footer">
	<div class="footer-inner">
		<div class="footer-content">
			<span class="bigger-120">
				<span class="blue bolder"><a href="javascript:;"><?php echo ($info["cfg_webname"]); ?></a></span>
				后台管理系统 &copy; 2016-<?php echo date('Y');?>
			</span>
		</div>
	</div>
</div>

	<!-- 页脚结束 -->
	<!-- 返回顶端开始 -->
	<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
		<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
	</a>
	<!-- 返回顶端结束 -->
</div><!-- 整个页面内结束 -->

<!-- ace的js,可以通过打包生成,避免引入文件数多 -->
<script src="/public/ace/js/ace.min.js"></script>
<!-- jquery.form、layer、yfcmf的js -->
<script src="/public/others/jquery.form.js"></script>
<script src="/public/others/maxlength.js"></script>
<script src="/public/layer/layer.js"></script>
<?php $t=time(); ?>
<script src="/public/sanmi/sanmi.js?<?php echo ($t); ?>"></script>
<!-- 此页相关插件js -->

    <script type="text/javascript">
     /*时间插件*/
    $('#start_time').datetimepicker({
        format: 'yyyy-mm-dd',
        language: 'zh-CN', /*加载日历语言包，可自定义*/
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView:'month',
        forceParse: 0
    }).on("click",function(ev){
        $("#start_time").datetimepicker("setEndDate", $("#end_time").val());
    });
    /*时间插件*/
    $('#end_time').datetimepicker({
        format: 'yyyy-mm-dd',
        language: 'zh-CN', /*加载日历语言包，可自定义*/
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView:'month',
        forceParse: 0
    }).on("click", function (ev) {
        $("#end_time").datetimepicker("setStartDate", $("#start_time").val());
    });
    </script>

<!-- 与此页相关的js -->
</body>
</html>