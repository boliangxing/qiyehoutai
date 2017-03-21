<?php
namespace Home\Controller;
use Think\Controller;
class CommController extends Controller {
     protected function _initialize(){
       define('UID',session('user'));
        if( !UID&&!(ACTION_NAME=='login') ){// 还没登录 跳转到登录页面
            $this->redirect('Index/login');
        }


        /*
        开启权限认证
         */
        $this->checkAuth();       
    }


    public function checkAuth(){
        //声明Auth认证类 
        $auth = new \Think\Auth();
        //var_dump( $auth->check( 'product/product_list', UID ) ); // boolean true
        
        /*
            验证单个条件
            验证 会员id 为 1 的 小红是否有 增加信息的权限
            
            check方法中的参数解释：
                参数1：Admin/Article/Add 假设我现在请求 Admin模块下Article控制器的Add方法
                参数2： 1 为当前请求的会员ID
        */
        //var_dump( $auth->check( 'Admin/Article/Add', UID ) ); // boolean true
        
        /*
            同时验证多个条件
            验证 会员id 为 1 的小红是否有增加信息，修改信息 和一个不存在的规则 的权限
            
            参数解释：
                参数1：多条规则同时验证 ， 验证是否拥有增加、修改、删除的权限
                参数2：当前请求的会员ID   
            ps ：XXX是一个不存在的规则为什么会返回真呢？ 因为check方法 第5个参数默认为 or 也就是说 多个规则中只要满足一个条件即为真   
        */
        //var_dump( $auth->check( 'Admin/Article/Add,Admin/Article/Edit,Admnin/Article/Xxx', 1 ) ); //  boolean true        
        /*
            同时验证多个条件 并且 都为真
            验证 会员id 为 1 的小红是否具有 增加 修改 删除 的权限
            参数解释
                参数1：多条规则同时验证 ，验证是否拥有 增加 修改 删除的权限
                参数2：当前请求的会员ID
                参数3：是否用正则验证condition中的内容
                参数4：
                参数5：必须满足全部规则才通过 
        */
        //var_dump( $auth->check( 'Admin/Article/Add,Admin/Article/Edit,Admin/Article/Xxx', 1, 1, '', 'and' ) );        //boolean false 
        $pathinfo = CONTROLLER_NAME.'/'.ACTION_NAME;
		//var_dump($pathinfo);

        if( !$auth->check( $pathinfo, UID ) ){
            // $this->error('权限不足',"",1);die()
            $this->display('Auth/error');
            die();
        };
    }

}