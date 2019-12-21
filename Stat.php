<?php
// +----------------------------------------------------------------------
// | AdminIOT
// +----------------------------------------------------------------------
// | Copyright (c) 2017 https://www.adminiot.com.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed (Without the authorship of the author, the code can not be
// | transmitted two times or used for other business practices)
// +----------------------------------------------------------------------
// | Author: Robert <78320701@qq.com> Date:2017/12
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\model\AdminLogs;
use app\common\model\AdminMenus;
use app\common\model\AdminUsers;
use app\common\model\Syslogs;
use app\common\model\AdminTriggerLog;
use think\Log;
use onenetapi\OneNetApi;
use app\common\helper\OneNetCloud;
use think\Config;
use think\Session;
use app\common\model\AdminDevice;
use app\common\model\AdminCompany;
use app\common\model\AdminGroup;

class Stat extends Base
{
    
    // 统计概览
    public function index()
    {
		
$today = date( "D M j H:i:s T Y "); // Sat Mar 10 15:16:08 MST 2001
//dump($today.'001');
		
		// 查询自己下面的设备 开始 
	   	$users = new AdminUsers();
		$user_id = Session::get('user.user_id');
		//系统管理员
		$sql_1 = "select * from iot_admin_users a left join iot_admin_auth_group_access b on a.user_id = b.uid where (b.group_id = 1) and a.user_id = 9".$user_id;
		//公司管理员
		$sql_2 = "select * from iot_admin_users a left join iot_admin_auth_group_access b on a.user_id = b.uid where (b.group_id = 2) and a.user_id = ".$user_id;	
		//普通司管理员
		$sql_3 = "select * from iot_admin_users a left join iot_admin_auth_group_access b on a.user_id = b.uid where (b.group_id = 2) and a.user_id = ".$user_id;		
		$info_1 = $users->query($sql_1);
		$info_2 = $users->query($sql_2);	
		$info_3 = $users->query($sql_3);	
	   	//dump($info_2);
		$info_1 = new AdminDevice();
	    $user_info_1 = $this->getUserInfo($user_id);
		$admin =0;
		//判断是不是系统管理员
	    if(0!=$user_info_1['group_id']){
			//判断是不是公司管理员
			if(count($info_2)==1){
				$admin =1;
			}else{
				$admin =2;
			}
			
		}
       $today = date( "D M j H:i:s T Y "); // Sat Mar 10 15:16:08 MST 2001
       //dump($today.'002');
		
		
        $admin_users = new AdminUsers();
		if($admin==0){
			$admin_user_count = $admin_users->count();
		}else{
			 $admin_user_count = $admin_users->where("company_id=".$user_info_1['company_id'])->count();
		}
       
        $admin_logs = new AdminLogs();
        
		
		if($admin==0){
			$admin_log_count = $admin_logs->count();
		}else{
			//$admin_log_count = $admin_logs->count();
			 $admin_log_count = $admin_logs->where("user_id=".$user_info_1['user_id'])->count();
			 //$admin_user_count = $admin_users->where("company_id=".$user_info_1['company_id'])->count();
		} 
		
        $admin_menus = new AdminMenus();
        $admin_menu_count = $admin_menus->count();
        $admin_trigger_log = new AdminTriggerLog();
		 $admin_trigger_log_count = $admin_trigger_log->count();
		
		
		
       
        
        $onenet_cloud = new OneNetCloud();
        
        // 统计当前在线/离线设备总数
        $device_total_count = $onenet_cloud->get_device_total_count();
        $device_online_count = $onenet_cloud->get_device_online_count();
        $device_offline_count = $onenet_cloud->get_device_offline_count();
        
		//$device_info = $onenet_cloud->device("45250027");
		//dump($device_info['title']);
		//dump($device_info['online']);
		
		
		
		//$Model = new Model();
        //$sql = 'select a.id,a.title,b.content from think_test1 as a, think_test2 as b where a.id=b.id '.$map.' order by a.id '.$sort.' limit '.$p->firstRow.','.$p->listRows;
        //$voList = $Model->query($sql);
		$users = new AdminUsers();
		$user_id = Session::get('user.user_id');
		//系统管理员
		$sql_1 = "select * from iot_admin_users a left join iot_admin_auth_group_access b on a.user_id = b.uid where (b.group_id = 1) and a.user_id = 9".$user_id;
		//公司管理员
		$sql_2 = "select * from iot_admin_users a left join iot_admin_auth_group_access b on a.user_id = b.uid where (b.group_id = 2) and a.user_id = ".$user_id;		
		$info_1 = $users->query($sql_1);
		$info_2 = $users->query($sql_2);		
		//dump(count($info_1));
		//dump(count($info_2));
		//dump($info_2);
		
		$info1 = new AdminDevice();
	    $user_info = $this->getUserInfo($user_id);
		//dump($user_info);	
		
		$total =0;
		$on =0;
		$off=0;
		//前台权限判断
		$access = 0;
		//如果不是管理员，则走
		
		$cur_page = 1;
                $page_size = 1000;
				 $keyword = NULL;
				$tag = NULL;
				$is_online = NULL;
				$is_private = NULL;
				$device_ids = NULL;
				$device_listss = $onenet_cloud->device_list($cur_page, $page_size, $keyword, $tag, $is_online, $is_private, $device_ids);
				//dump($device_listss['devices'][0]);
				
				
		if(0!=$user_info['group_id']){
			$access = 1;
			//如果是公司管理员 显示该公司的数据
			if(count($info_2)==1){
				$sql_company = "select * from iot_admin_device a where a.company_id = ".$info_2[0]['company_id'];
				$info2 = $info1->query($sql_company);
				$total = count($info2);
				
				
				
				

				 foreach ($info2 as $key => $d){
					 //dump($info2[$key]);
					 //$device = $onenet_cloud->device($info2[$key]['d_id']);
					 
					  foreach ($device_listss['devices'] as $key => $ds){
						  if($ds['id']==$d['d_id']){
								if($ds['online']){
									 $on=$on+1;
								 }else{
									 $off=$off+1;
								 }
								  break; 
						  }
					  }
					 
					 
					  
				 }
$today = date( "D M j H:i:s T Y "); // Sat Mar 10 15:16:08 MST 2001
//dump($today.'003');
				//dump(1);
				//dump($info2);
				//$info2 = $info1->where("company_id=".$info_2['company_id'])->select(); 
				
			}else{//如果是普通管理员，则只显示改分组下的设备；
			    $sql_group = "select * from iot_admin_device a where a.group_id = ".$info_2[0]['group_id'];
				$info2 = $info1->query($sql_group);
				$total = count($info2);
				 foreach ($info2 as $key => $d){
					 //dump($info2[$key]);
					 $device = $onenet_cloud->device($info2[$key]['d_id']);
					
					 if($device['online']){
						 $on=$on+1;
					 }else{
						 $off=$on+1;
					 }
					  
				 }
				
				//dump(2);
				//dump($info2);
				//$info2 = $info1->where("group_id=".$info_2['group_id'])->select(); 
	   $today = date( "D M j H:i:s T Y "); // Sat Mar 10 15:16:08 MST 2001
      // dump($today.'004');
			}
			
			 $device_total_count = $total;
			 $device_online_count= $on;
			 $device_offline_count = $off;
			 
			 
			 
		}
		
		
		$today = date( "D M j H:i:s T Y "); // Sat Mar 10 15:16:08 MST 2001
      // dump($today.'005');	
		
		
		
		$cur_page = 1;
        $page_size = 1000;
        //$page_size = $this->web_data['list_rows'];
        
        $keyword = NULL;
        $tag = NULL;
        $is_online = NULL;
        $is_private =NULL ;
        $device_ids = NULL;
		
		 $device2 = new AdminDevice();
		 //$sql_company_group2 = "select * from iot_admin_daystatus where id in (select max(id) from iot_admin_daystatus group by did)";
		  $sql_company_group2 ="select c.* from iot_admin_daystatus c   INNER JOIN    (select max(s.id) id,s.did,max(s.updatetime) as maxupdatetime from iot_admin_daystatus s group by s.did) b  on c.id = b.id ";
		 $divices2 = $device2->query($sql_company_group2);
		 
		 
    

		
		$device_listss=null;
		$high=0;
		$middle=0;
		$low=0;
		$cold=0;
		$hot=0;
		$nenghao=0;
		
		 foreach ($divices2 as $key => $d) {
			     
			     
				
				 $device = new AdminDevice();
				 $sql_company_group = "select a.*,b.high,b.middle,b.low from iot_admin_device a left  join iot_admin_devicetype b on a.devicetype = b.id  where a.d_id = ".$d['did'];
                 $divices = $device->query($sql_company_group);

                  if(count($divices)>0){
					 $device_listss[$key]['title']=$divices[0]['title'];
			      $device_listss[$key]['imei']=$divices[0]['imei'];
				  $device_listss[$key]['did'] = $d['did'];
				  $cmd = $d['status'];
				  $cmd_bianyi = $this->translate($cmd);
				 //dump($cmd_bianyi);
				 $string_arr = explode(":", $cmd_bianyi);
					
					//dump($string_arr);
					$string_arr_bianyi = explode("&", $string_arr[1]);
					$high=$high+$string_arr_bianyi[16];
					$middle=$middle+$string_arr_bianyi[17];
					$low=$low+$string_arr_bianyi[18];
					$cold=$cold+$string_arr_bianyi[19];
					$hot=$hot+$string_arr_bianyi[20];
					
					$device_listss[$key]['high'] = $string_arr_bianyi[16];
					$device_listss[$key]['middle'] = $string_arr_bianyi[17];
					$device_listss[$key]['low'] = $string_arr_bianyi[18];
					$device_listss[$key]['cold'] = $string_arr_bianyi[19];
					$device_listss[$key]['hot'] = $string_arr_bianyi[20]; 
					$nenghao=$nenghao+$device_listss[$key]['high']*$divices[0]['high'] + $device_listss[$key]['middle']*$divices[0]['middle'] + $device_listss[$key]['low']*$divices[0]['low'];
					
					$device_listss[$key]['nenghao'] = $device_listss[$key]['high']*$divices[0]['high'] + $device_listss[$key]['middle']*$divices[0]['middle'] + $device_listss[$key]['low']*$divices[0]['low'];
					
					 
				  }

			  
		  }
		
		
		
		
		
		
		
		$this->assign([
            'high' => $high,
			'middle' => $middle,
			'low' => $low,
			'cold' => $cold,
			'hot' => $hot,
			'nenghao' => $nenghao
			
        ]);
		
		
		
        $this->assign([
		    'access' => $access,
            'adminuser_count' => $admin_user_count,
            'admin_log_count' => $admin_log_count,
            'admin_menu_count' => $admin_menu_count,
            'device_total_count' => $device_total_count,
            'device_online_count' => $device_online_count,
            'device_offline_count' => $device_offline_count,
            'admin_trigger_log_count' => $admin_trigger_log_count
        ]);
		
		
        
        if (isset($this->param['app'])) {
            $this->view->engine->layout(false);
            Config::set('app_trace', false);
            return $this->fetch("indexm");
        } else
            return $this->fetch();
    }



	function translate($cmd) {
		//初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, 'http://localhost:8081//compile2.php');
    //设置头文件的信息作为数据流输出
    //curl_setopt($curl, CURLOPT_HEADER, 1);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);
    //设置post数据
    $post_data = array(
        "xx" => $cmd
        );
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    //显示获得的数据
    return $data;

	}
	
	
	
    public function getStat()
    {
        $lists = array();
        
        $admin_trigger_log = new AdminTriggerLog();
        $admin_trigger_log_count = $admin_trigger_log->count();
        
        $onenet_cloud = new OneNetCloud();
        // 统计当前在线/离线设备总数
        $device_total_count = $onenet_cloud->get_device_total_count();
        $device_online_count = $onenet_cloud->get_device_online_count();
        $device_offline_count = $onenet_cloud->get_device_offline_count();
        
        $lists["errno"] = 0; // 数据获取成功
        $lists["device_total_count"] = $device_total_count;
        $lists["device_online_count"] = $device_online_count;
        $lists["device_offline_count"] = $device_offline_count;
        $lists["trigger_log_count"] = $admin_trigger_log_count;
        
        return json($lists);
    }

    public function mapdata()
    {
        
        // 每次最多返回设备地图数目
        $max_ajax_devices_per = 10;
        
        $start_page = 1;
        $page_size = 100;
        $key_word = NULL;
        $tag = NULL;
        $is_online = NULL;
        $is_private = NULL;
        $device_ids = NULL;
        
        $page = isset($this->param['cur_page']) ? $this->param['cur_page'] : 1;
        if (filter_var($page, FILTER_VALIDATE_INT) !== false && $page >= 1) {
            $start_page = $page; // 当前请求分页页面
        }
        
        $max_ajax_pages_per = (int) ceil($max_ajax_devices_per / $page_size);
        $end_page = 0;
        
        $devicemapdata = array();
        $devicemapdata["devices"] = array();
        
        $cur_page = $start_page;
        
        $onenet_cloud = new OneNetCloud();
        
        // 只获取一个设备，目的是获取总设备数
        $device_total_count = $onenet_cloud->get_device_total_count();
        $lastPage = (int) ceil($device_total_count / $page_size);
        $pagenum = ($lastPage - $start_page) + 1;
        
        // 云端设备总数如果大于客户端请求设备数时，需要分页获取。
        $loopcnt = (int) $pagenum > $max_ajax_pages_per ? $max_ajax_pages_per : $pagenum;
        
        do {
            
            $device_list = $onenet_cloud->device_list($cur_page, $page_size, $key_word, $tag, $is_online, $is_private, $device_ids);
            if (! empty($device_list)) {
                $devicemapdata["devices"] = array_merge($devicemapdata["devices"], $device_list["devices"]);
            }
            
            $cur_page ++;
            $loopcnt --;
        } while ($loopcnt > 0);
        
        if (! empty($device_list)) {
            $devicemapdata["total_count"] = $device_list['total_count'];
            $devicemapdata["per_page"] = $device_list['per_page'];
            $devicemapdata["page"] = $device_list['page'];
        }
        
        // FIXME FOR TETS
        $devicemapdata["has_more"] = ($lastPage - $start_page) + 1 > $max_ajax_pages_per ? 1 : 0;
        
        return json($devicemapdata);
    }
}