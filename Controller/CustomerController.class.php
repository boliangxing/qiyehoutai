<?php
namespace Home\Controller;
use Think\Controller;
class CustomerController extends CommController {

    /*
    客户分类
     */
    public function customer_cate(){
        if (IS_POST) {
            /*
            前台表单传来一个type值  type等于1是页面上方的表单，表示修改。 
            页面下方的就是添加
             */
            $type=I('post.type');
            $id=I('post.typeid');
            $data['typename'] =  I('post.typename');
            $data['xuhao'] =I('post.xuhao');

            if ($type==1) {
                $row=M('oa_customer_type')->where(array('typeid'=>$id))->save($data);
                if ($row) {
                   $this->success('修改成功');
                }else{
                    $this->error('修改失败或无改动');
                }
            }else{
                $row=M('oa_customer_type')->add($data);


                if ($row) {
                   $this->success('添加成功');
                }else{
                    $this->error('添加失败');
                }
            }
         
        }else{
           $cate=M('oa_customer_type')->order('xuhao asc')->select();
           $this->assign('cate',$cate);
           $this->display(); 
        }

    }

    /*
        删除客户分类。
     */
    public function del_ccate(){
        $id=I('get.id');
        $row=M('oa_customer_type')->where(array('typeid'=>$id))->delete();
        if ($row) {
               $this->success('删除成功');
        }else{
               $this->error('删除失败');
        }
    }


    /**
    客户列表     
     */
    public function customer_list(){

        $keywords = I("get.keywords");
        $map['customer_name'] =  array('like',"%$keywords%");
    if(!I('get.ord')){

               $list = $this->lists('oa_customer', $map );

        }else{

        if(I('get.sx')=='sx'||I('get.sx')==''){

               $ord=I('get.ord');
               $list=$this->lists('oa_customer', $map,$ord.' asc');
               $sx='jx';
               $this->assign('sx', $sx);
                
        }else{

                $ord=I('get.ord');
                $list=$this->lists('oa_customer', $map,$ord.' desc');
                $sx='sx';
                $this->assign('sx', $sx);

        } 
        }
        
        foreach($list as $k=>$v){
            $list[$k]['typename']=M('oa_customer_type')->where(array('typeid'=>$v['typeid']))->getfield('typename');
            $list[$k]['contact_name']=M('oa_contact')->where(array('customer_id'=>$v['customer_id']))->getfield('contact_name');
        }
        $this->assign('list',$list);
        $this->display();
    }

    /*
    客户添加与修改 add（）是添加 save是修改
     */
    public function customer_add(){
        if (IS_POST) {
            $data = I("post.");

            $id = I("post.customer_id");

            //将时间改成时间戳
            $data['customer_ctime'] =  strtotime($data['customer_ctime']);
            $data['customer_gtime'] = strtotime($data['customer_gtime']);
           
           //用户名称和密码
               // if($id>0&&$id<999999){$cid=sprintf("%06d", $id);}else { $cid=$id; }
                // $data['cusloginname']= 'xhx'.$cid;
                // $data['cuslogimpass']=  $id;
                $oldcid = M('oa_customer')->order('customer_id desc')->getField('customer_id');
                $str1='xhx0000';
                $str2=$oldcid+1;
           
           $data['clname']= $str1.$str2;
             //$data['cuslogimpass']=  $cid;
            //创建时间
            $data['create_time'] = time();

            if(empty($id)){
                $data['customer_id'] = M('oa_customer')->add($data);
                M('oa_contact')->add($data);

                 
                 
                 $this->success('添加成功');
            }else{
                M('oa_customer')->where("customer_id = $id")->save($data);
                M('oa_contact')->where("customer_id = $id")->save($data);
                $this->success('修改成功');
            }
        }elseif(IS_AJAX){

  $clname = I("get.clname");
$r=M('oa_customer')->where(array('clname'=>$clname))->select();
if(!empty($r)){


echo 0;
}else{
echo 1;
}




        }else{

            $id = I("get.id");
            if(!empty($id)){
                $data['customer'] = M('oa_customer')->where("customer_id=$id")->find();
                $data['contact'] = M('oa_contact')->where("customer_id=$id")->find();
            }

            $data['customer_type'] = M('oa_customer_type')->order('xuhao asc')->select();
            $data['zone'] = $this->tree( M('oa_zone')->select() );
            $data['hangye'] = M('oa_hangye')->select();
               $data['cid'] = M('oa_customer')->order('customer_id desc')->getField('customer_id');
            $this->assign('data',$data);
            $this->display();
        }
    }
  
    public function tree($arr=array(),$id=0,$lev=0){
        $subs = array(); // 子孙数组
        foreach($arr as $v) {
            if($v['pid'] == $id) {
                $v['lev'] = $lev;
                $subs[] = $v; // 举例说找到array('id'=>1,'name'=>'安徽','parent'=>0),
                $subs = array_merge($subs,$this->tree($arr,$v['zoneid'],$lev+1));
            }
        }
        return $subs;
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