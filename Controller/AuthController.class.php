<?php
namespace Home\Controller;
use Think\Controller;
class AuthController extends CommController {

    //管理员组 列表 >1 不显示 系统管理员
    public function group(){
        $list = M('think_auth_group')->where("id>1")->select();
        $this->assign('list',$list);
        $this->display(); 
    }

    //组添加
    public function group_add(){
        if(IS_POST){
            $data = I("post.");
            $res = M('think_auth_group')->add($data);
            $row=M('oa_product_type')->add($data);
            if ($row) {
               $this->success('添加成功',U('auth/group'));
            }else{
                $this->error('添加失败');
            }
            //var_dump($data);
        }else{
            $this->display();
        }
    }

    //组修改
    public function group_edit(){
        $id = I("get.id");
        if(IS_POST){
            $data = I("post.");
            $res = M('think_auth_group')->where("id=$id")->save($data);
            $row=M('oa_product_type')->add($data);
            if ($row) {
               $this->success('编辑成功',U('auth/group'));
            }else{
                $this->error('编辑失败');
            }
        }else{
            $info = M('think_auth_group')->where("id=$id")->find();
            $this->assign('info',$info);
            $this->display('group_add');
        }
    }

    //组删除
    public function group_del(){
        $id = I('get.id');

        $res = M('think_auth_group')->where("id=$id")->delete();

        if ($res) {
           $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    //规则
    public function rule(){
        $id = I('get.id');
        if(IS_POST){
            $data['rules'] = implode(",", I('post.rule'));
            $res = M('think_auth_group')->where("id=$id")->save($data);

            if ($res) {
               $this->success('编辑成功');
            }else{
                $this->error('编辑失败');
            }
        }else{

            //读取所有规则列表
            if ($id==1){
                $rule = M('think_auth_rule')->select();
            }else{
                $rule = M('think_auth_rule')->where("iselect=1")->select();
            }
            $group = M('think_auth_group')->where("id=$id")->find();

            $this->assign('rule',$rule);
            $this->assign('select',explode(",",$group['rules']));
            $this->assign('title',$group['title']);
            $this->display();
        }
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