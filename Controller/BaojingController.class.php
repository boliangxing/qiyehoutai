<?php
namespace Home\Controller;
use Think\Controller;
class BaojingController extends CommController {

    
    /*
    报警记录列表
     */
    
     public function baojing(){



       
        if(I('get.start_time')||I('get.last_time')){
                  $min=I('get.start_time');
                $max=I('get.last_time');
             $newx= strtotime($max);
               $newn= strtotime($min);
               $where="  uptime>'$newn' and uptime<$newx";
               $list=$this->lists('lj_baojing',$where); 
             

        }else{
            if(!I('get.ord')){

               $list = $this->lists('lj_baojing', $map );

        }else{

        if(I('get.sx')=='sx'||I('get.sx')==''){

               $ord=I('get.ord');
               $list=$this->lists('lj_baojing', $map,$ord.' asc');
               $sx='jx';
               $this->assign('sx', $sx);
                
        }else{

                $ord=I('get.ord');
                $list=$this->lists('lj_baojing', $map,$ord.' desc');
                $sx='sx';
                $this->assign('sx', $sx);

        } 
        }
        

              
        }
       
        foreach ($list as $k => $v) {
           $list[$k]['userr']=M('lj_facility')->where(array('dtuid'=>$v['dtuid']))->getfield('userr');
           $s_status=explode(',', $v['s_status']);
           if (in_array(UID,$s_status)) {
                $list[$k]['is_read']=1;
           }else{
                $list[$k]['is_read']=0;
           }
        }
        
        $this->assign('list',$list);
        $this->display();
    }

    /*
    设置报警记录为已读
     */
    public function baojing_detail(){
        $id=I('get.id');
        $s_status=M('lj_baojing')->where(array('bjid'=>$id))->getField('s_status');
        if (empty($s_status)) {
            $row=M('lj_baojing')->where(array('bjid'=>$id))->setField('s_status','0,'.UID.',');
        }else{
            $uids=$s_status.','.UID;
            $row=M('lj_baojing')->where(array('bjid'=>$id))->setField('s_status',$uids.',');
        }
        if ($row) {
            $this->success('设置成功');
        }else{
            $thi->error('设置失败');
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