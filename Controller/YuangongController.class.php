<?php
namespace Home\Controller;
use Think\Controller;
class YuangongController extends CommController {
    

    /*
    部门管理
     */
    public function bumen(){
        $list = M('oa_bumen')->getfield('bmid,bmname,p_bmid');
        foreach($list as $k=>$v){
            $list[$k]['name'] = $v['bmname'];
            $list[$k]['title'] = "<a  href=".U('yuangong/bumen_add?id='.$v['bmid']).">增加</a> <a href=".U('yuangong/bumen_del?id='.$v['bmid']).">删除</a> <a href=".U('yuangong/bumen_edit?id='.$v['bmid']).">修改</a>";
            
        }

        //$this->tree($list);
        $list = list_to_tree($list,$pk='bmid',$pid='p_bmid','children',$root=0,array('name','children','title'));
        $list=$list[0];
        //dump($list);
       
        $list=json_encode($list);
        $this->assign('list',$list);
        $this->display();
    }
    /*
    部门添加
     */
    public function bumen_add(){
        $id=I('get.id');
        if (IS_POST) {
            $data=I('post.');
            $data['p_bmid']=$id;
            $row=M('oa_bumen')->add($data);
            if ($row) {
                $this->success('添加成功',U('yuangong/bumen'));
            }else{
                $this->error('添加失败');
            }
        }else{
             $this->display();
        }
       
    }

    /*
    部门修改
     */
    public function bumen_edit(){
        $id=I('get.id');
        if (IS_POST) {
            $data=I('post.');
            
                $row=M('oa_bumen')->where(array('bmid'=>$id))->save($data);
                if ($row) {
                    $this->success('修改成功',U('yuangong/bumen'));
                }else{
                    $this->error('修改失败');
                }
            
        }else{
            $info=M('oa_bumen')->where(array('bmid'=>$id))->find();
            $list= M('oa_bumen')->select();
            $this->assign('list',$list);
            $this->assign('info',$info);
            $this->display();
        }
        
    }

    /*
    部门删除
     */
    public function bumen_del(){
        $id=I('get.id');
        $res=M('oa_bumen')->where(array('p_bmid'=>$id))->find();
        if ($res) {
            $this->error('请先删除该部门下的子部门');
        }else{
            $row=M('oa_bumen')->where(array('bmid'=>$id))->delete();
            if ($row) {
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
    }
    /*
    员工添加与修改
     */
    public function yuangong_add(){
        $id=I('get.id');
        if (session('user')<>1 and  $id==1){
            $this->error('操作失败');
            exit();
        }
        if (IS_POST) {
            $data=I('post.');
            if ($data['staff_pwd2']!=$data['staff_pwd1']) {
                $this->error('两次密码输入不一致');die;
            }else{
                
                if (strlen($data['staff_pwd1'])==32) {
                    $data['staff_pwd']=$data['staff_pwd1'];
                    unset($data['staff_pwd1']);
                    unset($data['staff_pwd2']);
                }else{
                    $data['staff_pwd']=md5($data['staff_pwd1']);
                    unset($data['staff_pwd1']);
                    unset($data['staff_pwd2']);
                }
                
            }
            if ($id) {
                $row=M('oa_staff')->where(array('staff_id'=>$id))->save($data);
                $access['group_id']=$data['staff_jsid'];
                M('think_auth_group_access')->where(array('uid'=>$id))->save($access);
                if ($row) {
                $this->success('修改成功',U('yuangong/yuangong_list'));
                }else{
                    $this->error('修改失败或无改动');
                }
            }else{

                $row=M('oa_staff')->add($data);
                $access['group_id']=$data['staff_jsid'];
                $access['uid']=$row;
                M('think_auth_group_access')->add($access);

                if ($row) {
                $this->success('添加成功',U('yuangong/yuangong_list'));
                }else{
                    $this->error('添加失败');
                }
            }
           
           
        }else{
            $bm_list=M('oa_bumen')->select();

            //超级管理员
            if (session('user')==1)  { 
                $js_list=M('think_auth_group')->select();
            }else{
                $js_list=M('think_auth_group')->where("id>1")->select();
            }
            
            $info=M('oa_staff')->where(array('staff_id'=>$id))->find();
            $this->assign('info',$info);
            $this->assign('bm_list',$bm_list);
            $this->assign('js_list',$js_list);
            $this->display(); 
        }
       
    }

    /*
    员工列表
     */
    public function yuangong_list(){


          //超级管理员
            if (session('user')==1)  { 
                $list=$this->lists('oa_staff','');
            }else{
                $list=$this->lists('oa_staff','staff_id>1');
            }
            

        foreach ($list as $k => $v) {
            $list[$k]['bmname']=M('oa_bumen')->where(array('bmid'=>$v['staff_bmid']))->getfield('bmname');
            $list[$k]['jsname']=M('think_auth_group')->where(array('id'=>$v['staff_jsid']))->getfield('title');
        }
        $this->assign('list',$list);
        $this->display();
    }
    
    /*
    员工启用与停用
     */
    public function yuangong_status(){
        $get=I('get.');
        $data['Available']=$get['status'];
        $row=M('oa_staff')->where(array('staff_id'=>$get['id']))->save($data);
        if ($row) {
                $this->success('修改成功');
        }else{
            $this->error('修改失败');
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

    /**
     * 将格式数组转换为树
     *
     * @param array $list
     * @param integer $level 进行递归时传递用的参数
     */
    private $formatTree; //用于树型数组完成递归格式的全局变量
    private function _toFormatTree($list,$level=0,$name = 'bmname') {
        foreach($list as $key=>$val){
            $tmp_str=str_repeat("&nbsp;",$level*2);
            $tmp_str.="└";

            $val['level'] = $level;
            $val['name_show'] =$level==0?$val[$name]."&nbsp;":$tmp_str.$val[$name]."({$val['number']})&nbsp;";
                // $val['title_show'] = $val['id'].'|'.$level.'级|'.$val['title_show'];
            if(!array_key_exists('_child',$val)){
                array_push($this->formatTree,$val);
            }else{
                $tmp_ary = $val['_child'];
                unset($val['_child']);
                array_push($this->formatTree,$val);
                   $this->_toFormatTree($tmp_ary,$level+1,$name); //进行下一层递归
                }
            }
            return;
        }

        public function toFormatTree($list,$name = 'bmname',$pk='bmid',$pid = 'p_bmid',$root = 0){
            $list = list_to_tree($list,$pk,$pid,'_child',$root);
            $this->formatTree = array();
            $this->_toFormatTree($list,0,$name);
            return $this->formatTree;
        }


}