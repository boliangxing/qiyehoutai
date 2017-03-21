<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	 protected function _initialize(){
       function is_mobile(){  
        $user_agent = $_SERVER['HTTP_USER_AGENT'];  

        $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");  
        $is_mobile = false;  
        foreach ($mobile_agents as $device) {//这里把值遍历一遍，用于查找是否有上述字符串出现过  
           if (stristr($user_agent, $device)) { //stristr 查找访客端信息是否在上述数组中，不存在即为PC端。  
                $is_mobile = true;  
                break;  
            }  
        }  
        return $is_mobile;  
        }  
        if(is_mobile()){ //跳转至wap分组  
        $this->redirect('Wap/facility/facility_list');
        }
       define('UID',session('user'));
        if( !UID&&!(ACTION_NAME=='login') ){// 还没登录 跳转到登录页面
            $this->redirect('login');
        }
       
    }

    /*
    首页
     */
    public function index(){
 

        $group_auth_rules_string = M('think_auth_group')->where(array('id'=>session(jsid)))->getfield('rules');
		$auth_array=explode(',', $group_auth_rules_string); 
              
        $menu_list = M('think_auth_rule')->where('menu=1 and status=1 and pid>0  and id in ('.implode(',', $auth_array).')')->order('pid,seq')->limit(1000)->select(); 
        foreach ($menu_list as $value) { 
                $newlist[$value['pid']][$value['id']]=$value; 
               //拼成组ID
                $grouplista[]=$value['pid'];
                
         }
        //去除重复
		$grouplista = array_flip(array_flip($grouplista));
      	//dump($grouplista); exit();

        $menu_guorp = M('think_auth_rule')->where('pid=0 and status=1 and id in ('.implode(',', $grouplista).')')->order('seq')->limit(100)->select();  


       //dump($newlist);exit();
        foreach ($menu_guorp as $vg) { 
                $new_guorp[$vg['id']]['title'] =$vg['title'];
                $new_guorp[$vg['id']]['list']=$newlist[$vg['id']];
         }
        //echo session(staff_name);  
        //
        //echo $_SESSION['staff_name'];
        $this->assign('menu_guorp',$new_guorp);
        $this->assign('menu_list',$newlist);  
        $this->display();
    }
    public function map(){

        //获取设备的经纬度和名称
        $facility = M('lj_facility')->where('lat>0 and lng > 0')->getfield('fid,lng,lat,dtuid,userr');
        //dump($facility);

        foreach($facility as $key=>$val){
            //$sql = "select bjnote,bjtime from ".lj_baojing." where dtuid='$dutid' order by bjtime desc limit 1";
            $tp = M('lj_baojing')->where("dtuid = {$val['dtuid']}")->order('bjtime desc')->limit(1)->field('bjnote,bjtime')->find();

            $facility[$key]['isbj'] = 0;
            $facility[$key]['bjnote'] = $tp['bjnote'];


            if($tp){
                if(time()-$tp['jbtime']<300){
                    $facility[$key]['isbj'] = 1;
                    $facility[$key]['bjnote'] = $tp['bjnote'];
                }
            }
            
        }
        // dump($facility);
        $facility = json_encode($facility);

        $this->assign('facilit',$facility);
        $this->display();
    }

    /*
    登录
     */
    public function login(){
    	if (IS_POST) {
    		$data=I('post.');
    		
    			//$pass=M('oa_staff')->where(array('login_name'=>$data['name']))->getfield('staff_pwd');
                $userinfo=M('oa_staff')->where(array('login_name'=>$data['name']))->find();

	  			if ($userinfo['staff_pwd']) {
    				if ($userinfo['staff_pwd']==md5($data['pass'])) {
    					session('user',$userinfo['staff_id']);
    					session('staff_name',$userinfo['staff_name']);
    					session('jsid',$userinfo['staff_jsid']);

    					$login['login_time']=time();
    					//$login['login_ip']=get_client_ip();
    					M('oa_staff')->where(array('login_name'=>$data['name']))->save($login);
    					$this->success('登陆成功', U('Index/index'));
    				}else{
    					$this->error('密码错误');
    				}
    			}else{
    					 // dump($data);
    					 // dump($userinfo);exit();
    				$this->error('没有找到此账号');
    			}

    		
    	}else{
            $this->display();
        }
    	
    }

    /*
    退出登录
     */
    public function login_out(){
    	session('user',null);
    	session('jsid',null);
    	redirect('index/login');
    }


   



     /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     *
     * @return array|false
     */
    protected function lists ($model,$where=array(),$order='',$field=true){
        $options    =   array();
        $REQUEST    =   (array)I('get.');
        if(is_string($model)){
            $model  =   M($model);
        }
    
        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);
    
        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);
    
        if(empty($where)){
            $where  =   array('status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->where($options['where'])->count();
    
        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows, $REQUEST);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $page->setConfig('prev', '«');
            $page->setConfig('next', '»');
        }
        $p =$page->show();
        $this->assign('page', $p? $p: '');
        $this->assign('total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;
    
        $model->setProperty('options',$options);
    
        return $model->field($field)->select();
    }

}