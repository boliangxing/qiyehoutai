<?php
namespace Home\Controller;
use Think\Controller;
class HetongController extends CommController {
	
    /*
    合同添加与修改
     */
    public function hetong_add(){
        $id = I("get.id");

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
            $data = I("post.");//接收post过来的所有数据
            if($info['attachment'])$data['attachment']='/Uploads/'.$info['attachment']['savepath'].$info['attachment']['savename'];

            //转换时间戳
            $data['tstime'] = strtotime($data['tstime']);
            $data['qdtime'] = strtotime($data['qdtime']);

            //处理合同中的产品
            $hetong_buy = array();
            $i=0;
            foreach($data['product_id'] as $key => $val){
                $hetong_buy[$i]['product_id'] = $data['product_id'][$key];
                $hetong_buy[$i]['amount'] = $data['amount'][$key];
                $hetong_buy[$i]['product_code'] = $data['product_code'][$key];
                $hetong_buy[$i]['rsqxh'] = $data['rsqxh'][$key];
                $hetong_buy[$i]['rsqbh'] = $data['rsqbh'][$key];
                $hetong_buy[$i]['kzxxh'] = $data['kzxxh'][$key];
                $hetong_buy[$i]['kzxbh'] = $data['kzxbh'][$key];
                $i++;
            }
            
            /*
            如果get上有id 为修改，如果get上没有id则为添加
             */
            if(empty($id)){
                $data['CreateData'] = time();
                $data['htid'] = M('lj_hetong')->add($data);
                //$hetong_buy['htid']=$data['htid'];
                foreach ($hetong_buy as $k => $v) {
                    $hetong_buy[$k]['htid']=$data['htid'];
                }

                M('lj_hetong_buy')->addAll($hetong_buy);
                M('lj_hetong_zbj')->add($data);

                $this->success('添加成功');
            }else{
                M('lj_hetong')->where("htid = $id")->save($data);
                M('lj_hetong_buy')->where("htid = $id")->delete();
                foreach ($hetong_buy as $k => $v) {
                    $hetong_buy[$k]['htid']=$id;
                }
                M('lj_hetong_buy')->addAll($hetong_buy);
                M('lj_hetong_zbj')->where("htid = $id")->save($data);

                $this->success('修改成功');
            }
            

        }else{

            if(!empty($id)){
                $data['hetong'] = M('lj_hetong')->where("htid = $id")->find();
            }
            $data['hetong']['staff_name']=M('oa_staff')->where(array('staff_id'=>$data['hetong']['zeren_id']))->getField('staff_name');
            $data['customer'] = M('oa_customer')->select();
            $data['product'] = M('lj_product')->select();
            $buy=M('lj_hetong_buy')->where(array('htid'=>$id))->select();
            $count=M('lj_hetong_buy')->where(array('htid'=>$id))->count();
            $count=$count+1;
            $this->assign('count',$count);
            $this->assign('buy',$buy);
            $this->assign('data',$data);
            $this->display();
        }
    }

    /*
    合同列表
     */
    public function hetong_list(){
    	    if(!I('get.ord')){

               $list = $this->lists('lj_hetong', $map );

        }else{

        if(I('get.sx')=='sx'||I('get.sx')==''){

               $ord=I('get.ord');
               $list=$this->lists('lj_hetong', $map,$ord.' asc');
               $sx='jx';
               $this->assign('sx', $sx);
                
        }else{

                $ord=I('get.ord');
                $list=$this->lists('lj_hetong', $map,$ord.' desc');
                $sx='sx';
                $this->assign('sx', $sx);

        } 
        }
        
        foreach ($list as $k => $v) {
            $list[$k]['staff_name']=M('oa_staff')->where(array('staff_id'=>$v['zeren_id']))->getfield('staff_name');
            $list[$k]['customer_name']=M('oa_customer')->where(array('customer_id'=>$v['customer_id']))->getfield('customer_name');
        }

        //var_dump($list);
        $this->assign('list',$list);
        $this->display();
    }

    /*
    员工列表
     */
    public function yuangong(){
        if(I('get.search')){
            $map['staff_name']=array('like','%'.I('get.search').'%');
        }
          C('LIST_ROWS','28');//设置为一页28条
        $list=$this->lists('oa_staff',$map);
        $this->assign('list',$list);
        $this->display();
    }

    /*
    合同删除
     */
    public function hetong_del(){
        $id = I("get.id");
        $row = M('lj_hetong')->where("htid = $id")->delete();

        if ($row) {
               $this->success('删除成功');
        }else{
               $this->error('删除失败');
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