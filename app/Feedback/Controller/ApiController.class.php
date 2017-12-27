<?php
// +----------------------------------------------------------------------
// | 大屏接口
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
namespace Feedback\Controller;
use Think\Controller;
class ApiController extends Controller{
	//大屏列表页
	public function index(){
		$starttime = strtotime(date('Y-m-d', time())); //获取当日期的时间戳
		$endtime = strtotime(date('Y-m-d', time()))+1209599;
		$where['a.subscribe_time'] = array(array('gt',$starttime),array('elt',$endtime),'AND');
		$where['a.status'] = 2;
		$where['a.is_del'] = 2;
		$list = M('subscribe')
				->where($where)
				->join('AS a LEFT JOIN __LAB_LIST__ AS b ON a.aboratory_id=b.s_id')
				->field('a.*,b.lab_number')
				->order('a.subscribe_time ASC')
				->select();
		foreach ($list as $key => $value) {
			$hour = explode(',',$value['class_hour']);
			$hours = array();
			foreach ($hour as $k => $v) {
				$hours[] = M('classhour_list')->where(array('c_id'=>$v))->getField('classhour_name');
			}
			// $first = current($hour); //获取第一个值
			// $end = end($hour);  //获取最后一个值
			// $f = M('classhour_list')->where(array('c_id'=>$first))->getField('classhour_name');
			// $e = M('classhour_list')->where(array('c_id'=>$end))->getField('classhour_name');
			// $list[$key]['class_hour'] = $f.'-'.$e;
			// $list[$key]['class_hour'] = str_replace(",","-",$value['class_hour']);
			$list[$key]['class_hour'] = implode('-',$hours);
		}
		// dump($list);die;
		//获取当前日期的农历日期
		$year = date("Y",time());
		$month = date("m",time());
		$day = date("d",time());
		$arr = $this->getchinesedate($year,$month,$day);
		$yinli = $this->zhNum($arr[1],$arr[2]);
		$this->assign('yinli',$yinli);
		$this->assign('list',$list);
    	$this->display();	
	}
	//计算阴历日期
	public function getchinesedate($year,$month,$day)
	{
	    $cdate_monthdata=array(
	        0=>array(8,0,0,0,0,0,0,0,0,0,0,0,29,30,7,1),
	        1=>array(0,29,30,29,29,30,29,30,29,30,30,30,29,0,8,2),
	        2=>array(0,30,29,30,29,29,30,29,30,29,30,30,30,0,9,3),
	        3=>array(5,29,30,29,30,29,29,30,29,29,30,30,29,30,10,4),
	        4=>array(0,30,30,29,30,29,29,30,29,29,30,30,29,0,1,5),
	        5=>array(0,30,30,29,30,30,29,29,30,29,30,29,30,0,2,6),
	        6=>array(4,29,30,30,29,30,29,30,29,30,29,30,29,30,3,7),
	        7=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,4,8),
	        8=>array(0,30,29,29,30,30,29,30,29,30,30,29,30,0,5,9),
	        9=>array(2,29,30,29,29,30,29,30,29,30,30,30,29,30,6,10),
	        10=>array(0,29,30,29,29,30,29,30,29,30,30,30,29,0,7,11),
	        11=>array(6,30,29,30,29,29,30,29,29,30,30,29,30,30,8,12),
	        12=>array(0,30,29,30,29,29,30,29,29,30,30,29,30,0,9,1),
	        13=>array(0,30,30,29,30,29,29,30,29,29,30,29,30,0,10,2),
	        14=>array(5,30,30,29,30,29,30,29,30,29,30,29,29,30,1,3),
	        15=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0,2,4),
	        16=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,3,5),
	        17=>array(2,30,29,29,30,29,30,30,29,30,30,29,30,29,4,6),
	        18=>array(0,30,29,29,30,29,30,29,30,30,29,30,30,0,5,7),
	        19=>array(7,29,30,29,29,30,29,29,30,30,29,30,30,30,6,8),
	        20=>array(0,29,30,29,29,30,29,29,30,30,29,30,30,0,7,9),
	        21=>array(0,30,29,30,29,29,30,29,29,30,29,30,30,0,8,10),
	        22=>array(5,30,29,30,30,29,29,30,29,29,30,29,30,30,9,11),
	        23=>array(0,29,30,30,29,30,29,30,29,29,30,29,30,0,10,12),
	        24=>array(0,29,30,30,29,30,30,29,30,29,30,29,29,0,1,1),
	        25=>array(4,30,29,30,29,30,30,29,30,30,29,30,29,30,2,2),
	        26=>array(0,29,29,30,29,30,29,30,30,29,30,30,29,0,3,3),
	        27=>array(0,30,29,29,30,29,30,29,30,29,30,30,30,0,4,4),
	        28=>array(2,29,30,29,29,30,29,29,30,29,30,30,30,30,5,5),
	        29=>array(0,29,30,29,29,30,29,29,30,29,30,30,30,0,6,6),
	        30=>array(6,29,30,30,29,29,30,29,29,30,29,30,30,29,7,7),
	        31=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0,8,8),
	        32=>array(0,30,30,30,29,30,29,30,29,29,30,29,30,0,9,9),
	        33=>array(5,29,30,30,29,30,30,29,30,29,30,29,29,30,10,10),
	        34=>array(0,29,30,29,30,30,29,30,29,30,30,29,30,0,1,11),
	        35=>array(0,29,29,30,29,30,29,30,30,29,30,30,29,0,2,12),
	        36=>array(3,30,29,29,30,29,29,30,30,29,30,30,30,29,3,1),
	        37=>array(0,30,29,29,30,29,29,30,29,30,30,30,29,0,4,2),
	        38=>array(7,30,30,29,29,30,29,29,30,29,30,30,29,30,5,3),
	        39=>array(0,30,30,29,29,30,29,29,30,29,30,29,30,0,6,4),
	        40=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0,7,5),
	        41=>array(6,30,30,29,30,30,29,30,29,29,30,29,30,29,8,6),
	        42=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0,9,7),
	        43=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,10,8),
	        44=>array(4,30,29,30,29,30,29,30,29,30,30,29,30,30,1,9),
	        45=>array(0,29,29,30,29,29,30,29,30,30,30,29,30,0,2,10),
	        46=>array(0,30,29,29,30,29,29,30,29,30,30,29,30,0,3,11),
	        47=>array(2,30,30,29,29,30,29,29,30,29,30,29,30,30,4,12),
	        48=>array(0,30,29,30,29,30,29,29,30,29,30,29,30,0,5,1),
	        49=>array(7,30,29,30,30,29,30,29,29,30,29,30,29,30,6,2),
	        50=>array(0,29,30,30,29,30,30,29,29,30,29,30,29,0,7,3),
	        51=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0,8,4),
	        52=>array(5,29,30,29,30,29,30,29,30,30,29,30,29,30,9,5),
	        53=>array(0,29,30,29,29,30,30,29,30,30,29,30,29,0,10,6),
	        54=>array(0,30,29,30,29,29,30,29,30,30,29,30,30,0,1,7),
	        55=>array(3,29,30,29,30,29,29,30,29,30,29,30,30,30,2,8),
	        56=>array(0,29,30,29,30,29,29,30,29,30,29,30,30,0,3,9),
	        57=>array(8,30,29,30,29,30,29,29,30,29,30,29,30,29,4,10),
	        58=>array(0,30,30,30,29,30,29,29,30,29,30,29,30,0,5,11),
	        59=>array(0,29,30,30,29,30,29,30,29,30,29,30,29,0,6,12),
	        60=>array(6,30,29,30,29,30,30,29,30,29,30,29,30,29,7,1),
	        61=>array(0,30,29,30,29,30,29,30,30,29,30,29,30,0,8,2),
	        62=>array(0,29,30,29,29,30,29,30,30,29,30,30,29,0,9,3),
	        63=>array(4,30,29,30,29,29,30,29,30,29,30,30,30,29,10,4),
	        64=>array(0,30,29,30,29,29,30,29,30,29,30,30,30,0,1,5),
	        65=>array(0,29,30,29,30,29,29,30,29,29,30,30,29,0,2,6),
	        66=>array(3,30,30,30,29,30,29,29,30,29,29,30,30,29,3,7),
	        67=>array(0,30,30,29,30,30,29,29,30,29,30,29,30,0,4,8),
	        68=>array(7,29,30,29,30,30,29,30,29,30,29,30,29,30,5,9),
	        69=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,6,10),
	        70=>array(0,30,29,29,30,29,30,30,29,30,30,29,30,0,7,11),
	        71=>array(5,29,30,29,29,30,29,30,29,30,30,30,29,30,8,12),
	        72=>array(0,29,30,29,29,30,29,30,29,30,30,29,30,0,9,1),
	        73=>array(0,30,29,30,29,29,30,29,29,30,30,29,30,0,10,2),
	        74=>array(4,30,30,29,30,29,29,30,29,29,30,30,29,30,1,3),
	        75=>array(0,30,30,29,30,29,29,30,29,29,30,29,30,0,2,4),
	        76=>array(8,30,30,29,30,29,30,29,30,29,29,30,29,30,3,5),
	        77=>array(0,30,29,30,30,29,30,29,30,29,30,29,29,0,4,6),
	        78=>array(0,30,29,30,30,29,30,30,29,30,29,30,29,0,5,7),
	        79=>array(6,30,29,29,30,29,30,30,29,30,30,29,30,29,6,8),
	        80=>array(0,30,29,29,30,29,30,29,30,30,29,30,30,0,7,9),
	        81=>array(0,29,30,29,29,30,29,29,30,30,29,30,30,0,8,10),
	        82=>array(4,30,29,30,29,29,30,29,29,30,29,30,30,30,9,11),
	        83=>array(0,30,29,30,29,29,30,29,29,30,29,30,30,0,10,12),
	        84=>array(10,30,29,30,30,29,29,30,29,29,30,29,30,30,1,1),
	        85=>array(0,29,30,30,29,30,29,30,29,29,30,29,30,0,2,2),
	        86=>array(0,29,30,30,29,30,30,29,30,29,30,29,29,0,3,3),
	        87=>array(6,30,29,30,29,30,30,29,30,30,29,30,29,29,4,4),
	        88=>array(0,30,29,30,29,30,29,30,30,29,30,30,29,0,5,5),
	        89=>array(0,30,29,29,30,29,29,30,30,29,30,30,30,0,6,6),
	        90=>array(5,29,30,29,29,30,29,29,30,29,30,30,30,30,7,7),
	        91=>array(0,29,30,29,29,30,29,29,30,29,30,30,30,0,8,8),
	        92=>array(0,29,30,30,29,29,30,29,29,30,29,30,30,0,9,9),
	        93=>array(3,29,30,30,29,30,29,30,29,29,30,29,30,29,10,10),
	        94=>array(0,30,30,30,29,30,29,30,29,29,30,29,30,0,1,11),
	        95=>array(8,29,30,30,29,30,29,30,30,29,29,30,29,30,2,12),
	        96=>array(0,29,30,29,30,30,29,30,29,30,30,29,29,0,3,1),
	        97=>array(0,30,29,30,29,30,29,30,30,29,30,30,29,0,4,2),
	        98=>array(5,30,29,29,30,29,29,30,30,29,30,30,29,30,5,3),
	        99=>array(0,30,29,29,30,29,29,30,29,30,30,30,29,0,6,4),
	        100=>array(0,30,30,29,29,30,29,29,30,29,30,30,29,0,7,5),
	        101=>array(4,30,30,29,30,29,30,29,29,30,29,30,29,30,8,6),
	        102=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0,9,7),
	        103=>array(0,30,30,29,30,30,29,30,29,29,30,29,30,0,10,8),
	        104=>array(2,29,30,29,30,30,29,30,29,30,29,30,29,30,1,9),
	        105=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,2,10),
	        106=>array(7,30,29,30,29,30,29,30,29,30,30,29,30,30,3,11),
	        107=>array(0,29,29,30,29,29,30,29,30,30,30,29,30,0,4,12),
	        108=>array(0,30,29,29,30,29,29,30,29,30,30,29,30,0,5,1),
	        109=>array(5,30,30,29,29,30,29,29,30,29,30,29,30,30,6,2),
	        110=>array(0,30,29,30,29,30,29,29,30,29,30,29,30,0,7,3),
	        111=>array(0,30,29,30,30,29,30,29,29,30,29,30,29,0,8,4),
	        112=>array(4,30,29,30,30,29,30,29,30,29,30,29,30,29,9,5),
	        113=>array(0,30,29,30,29,30,30,29,30,29,30,29,30,0,10,6),
	        114=>array(9,29,30,29,30,29,30,29,30,30,29,30,29,30,1,7),
	        115=>array(0,29,30,29,29,30,29,30,30,30,29,30,29,0,2,8),
	        116=>array(0,30,29,30,29,29,30,29,30,30,29,30,30,0,3,9),
	        117=>array(6,29,30,29,30,29,29,30,29,30,29,30,30,30,4,10),
	        118=>array(0,29,30,29,30,29,29,30,29,30,29,30,30,0,5,11),
	        119=>array(0,30,29,30,29,30,29,29,30,29,29,30,30,0,6,12),
	        120=>array(4,29,30,30,30,29,30,29,29,30,29,30,29,30,7,1)
	    );
	    $cdate_tianganarray=array("null","Jia","Yi","Bing","Ding","Wu","Ji","Geng","Xin","Ren","Kui");
	    $cdate_dizhiarray=array("null","Zi","Chou","Yin","Mao","Chen","Si","Wu","Wei","Shen","You","Xu","Hai");
	    $cdate_zodiacarray=array("null","Rat","Ox","Tiger","Rabbit","Dragon","Snake","Horse","Sheep","Monkey","Rooster","Dog","Pig");
	    $cdate_total=11;
	    $cdate_cntotal=0;
	    for ($y=1901;$y<$year;$y++){
	        $cdate_total+=365;
	        if ($y%4==0) $cdate_total ++;
	    }
	    switch ($month){
	        case 12:
	            $cdate_total+=30;
	        case 11:
	            $cdate_total+=31;
	        case 10:
	            $cdate_total+=30;
	        case 9:
	            $cdate_total+=31;
	        case 8:
	            $cdate_total+=31;
	        case 7:
	            $cdate_total+=30;
	        case 6:
	            $cdate_total+=31;
	        case 5:
	            $cdate_total+=30;
	        case 4:
	            $cdate_total+=31;
	        case 3:
	            $cdate_total+=28;
	        case 2:
	            $cdate_total+=31;
	    }
	    if ($year%4==0 and $month>2){
	        $cdate_total++;
	    }
	    $cdate_total = $cdate_total+($day-1);
	    $myeardiff = $year-1900;
	    for ($x=0;$x<=$myeardiff;$x++){
	        for ($y=1;$y<=13;$y++){
	            if ($cdate_cntotal<$cdate_total){
	                $cdate_cntotal+=$cdate_monthdata[$x][$y];
	                $cdate_cnyear = $x;
	                $cdate_cnmonth = $y;
	            }
	        }
	    }
	    if (($cdate_cnmonth==$cdate_monthdata[$cdate_cnyear][0]+1)&&($cdate_monthdata[$cdate_cnyear][0]>0)) {
	        $cdate_leap=1;
	    }else{
	        $cdate_leap=0;
	    }
	    $cdate_cnday=$cdate_monthdata[$cdate_cnyear][$cdate_cnmonth]-($cdate_cntotal-$cdate_total);

	    if (($cdate_monthdata[$cdate_cnyear][0]>0)&&($cdate_monthdata[$cdate_cnyear][0]<$cdate_cnmonth)) {
	        $cdate_cnmonth = $cdate_cnmonth-1;
	    }
	    $cdate_tiangan = $cdate_tianganarray[$cdate_monthdata[$cdate_cnyear][14]];
	    $cdate_dizhi = $cdate_dizhiarray[$cdate_monthdata[$cdate_cnyear][15]];
	    $cdate_zodiac = $cdate_zodiacarray[$cdate_monthdata[$cdate_cnyear][15]];
	    $cdate_cnyear += 1900;
	    $cdate_result = array($cdate_cnyear,$cdate_cnmonth,$cdate_cnday,$cdate_leap,$cdate_tiangan,$cdate_dizhi);
	    return $cdate_result;
	}
	//转化数字
	public function zhNum($month,$day){
		//月转换
		if($month == 1){
			$month = "一";
		}elseif($month == 2){
			$month = "二";
		}elseif($month == 3){
			$month = "三";
		}elseif($month == 4){
			$month = "四";
		}elseif($month == 5){
			$month = "五";
		}elseif($month == 6){
			$month = "六";
		}elseif($month == 7){
			$month = "七";
		}elseif($month == 8){
			$month = "八";
		}elseif($month == 9){
			$month = "九";
		}elseif($month == 10){
			$month = "十";
		}elseif($month == 11){
			$month = "十一";
		}elseif($month == 12){
			$month = "十二";
		}else{
			$month = "";
		}
		//日专函
		if($day == 1){
			$day = "一";
		}elseif($day == 2){
			$day = "二";
		}elseif($day == 3){
			$day = "三";
		}elseif($day == 4){
			$day = "四";
		}elseif($day == 5){
			$day = "五";
		}elseif($day == 6){
			$day = "六";
		}elseif($day == 7){
			$day = "七";
		}elseif($day == 8){
			$day = "八";
		}elseif($day == 9){
			$day = "九";
		}elseif($day == 10){
			$day = "十";
		}elseif($day == 11){
			$day = "十一";
		}elseif($day == 12){
			$day = "十二";
		}elseif($day == 13){
			$day = "十三";
		}elseif($day == 14){
			$day = "十四";
		}elseif($day == 15){
			$day = "十五";
		}elseif($day == 16){
			$day = "十六";
		}elseif($day == 17){
			$day = "十七";
		}elseif($day == 18){
			$day = "十八";
		}elseif($day == 19){
			$day = "十九";
		}elseif($day == 20){
			$day = "二十";
		}elseif($day == 21){
			$day = "廿一";
		}elseif($day == 22){
			$day = "廿二";
		}elseif($day == 23){
			$day = "廿三";
		}elseif($day == 24){
			$day = "廿四";
		}elseif($day == 25){
			$day = "廿五";
		}elseif($day == 26){
			$day = "廿六";
		}elseif($day == 27){
			$day = "廿七";
		}elseif($day == 28){
			$day = "廿八";
		}elseif($day == 29){
			$day = "廿九";
		}elseif($day == 30){
			$day = "三十";
		}else{
			$day="";
		}
		return $month."月".$day;
	}
}