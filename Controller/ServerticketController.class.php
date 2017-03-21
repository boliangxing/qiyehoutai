<?php
namespace Home\Controller;
use Think\Controller;
class ServerticketController extends CommController {

    //工单列表
    public function ticketlist(){
        $id = I('get.id');

        if(empty($id)){
            $list = M('lj_service_ticket')->select();
        }else{
            $list = M('lj_service_ticket')->where("CreatorId=$id")->select();
        }

//         print "<pre>";
// print_r($list);


        $this->assign('list',$list);
        $this->display(); 
    }
    /*
    工单添加
     */
    public function ticketadd(){
        if(IS_POST){

           
             $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728;
            $upload->rootPath = './Uploads/';
            /*$upload->savePath = './Uploads/Trademark/';*/
            $upload->saveName = array('uniqid','');
            $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
            $upload->autoSub  = true;
            $upload->subName  = array('date','Ymd');
        // 上传文件 
            $info   =   $upload->upload();
            $data = I("post.");//接收全部POST数据
            if($info['attachment'])$data['attachment']='/Uploads/'.$info['attachment']['savepath'].$info['attachment']['savename'];
            $data['CreateDate'] =  strtotime($data['CreateDate']);
            $data['service_time'] = strtotime($data['service_time']);
            $data['userr']=I("post.customer_name");
            $data['CreatorId'] = '20000'.session('user');//员工添加的工单前是20000，客户添加的工单前为10000.  这样做的原因是：有一个我创建的工单，但是UID不在一个表，会重复。
            //写入到数据库
            $row = M('lj_service_ticket')->add($data);
            if ($row) {
               $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }else{
            $staff=M('oa_staff')->select();
            $this->assign('staff',$staff);
            $customerList = M('oa_customer')->getfield('customer_id,customer_name');    //获取客户列表

            $this->assign('customerList',$customerList);
            $this->display(); 
        }
    }
    /*
    工单编辑
     */
    public function ticketedit(){
        $id = I('get.id');
        if(IS_POST){
            
             $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728;
            $upload->rootPath = './Uploads/';
            /*$upload->savePath = './Uploads/Trademark/';*/
            $upload->saveName = array('uniqid','');
            $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
            $upload->autoSub  = true;
            $upload->subName  = array('date','Ymd');
        // 上传文件 
            $info   =   $upload->upload();
            $data = I("post.");//接收全部POST数据

            
            if($info['attachment'])$data['attachment']='/Uploads/'.$info['attachment']['savepath'].$info['attachment']['savename'];
            $data['CreateDate'] =  strtotime($data['CreateDate']);
            $data['service_time'] = strtotime($data['service_time']);

            $row = M('lj_service_ticket')->where("ticket_id=$id")->save($data);

            if($row){
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }else{

            $info = M('lj_service_ticket')->where("ticket_id=$id")->find();
            $customerList = M('oa_customer')->getfield('customer_id,customer_name');    //获取客户列表
            $staff=M('oa_staff')->select();//获取员工列表
            $this->assign('staff',$staff);
            $this->assign('customerList',$customerList);
            $this->assign('info',$info);
            $this->display(); 
        }
    }


    public function ticketdel(){

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