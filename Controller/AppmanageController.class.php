<?php
namespace Home\Controller;
use Think\Controller;
 use Think\Upload;  
class AppmanageController extends CommController {
    
    /*
    产品分类列表及添加修改
    页面里有两个表单 post.type==1的时候是修改
     */
    
     public function index(){

               $data=M('app_version')->select();
                 $this->assign('data',$data);
           $this->display(); 

     }


  
 
 

       public function appUpload() {  
         $upload = new \Think\Upload();
        //这里划分一下允许上传的文件类型，与3.1相比，文件类型不再是数组类型了，而是字符串，这是个区别。  
    $upload->maxSize = 10000000000 ;// 设置附件上传大小
  $upload->exts = array('jpg', 'gif', 'png', 'jpeg','apk');// 设置附件上传类型
  $upload->rootPath = './Uploads/'; // 设置附件上传根目录
  $upload->savePath = ''; // 设置附件上传（子）目录
 $info = $upload->upload();

         //这里判断是否上传成功  
        if ($info) {  
          
  $this->success('上传成功！');
        } else {  
             $this->error($upload->getError());
        }  
    } 

     public function edit(){

 
           $this->display(); 

     }


     public function delrelease(){

 
         $id=I('post.id');
         $row=M('app_version')->where(array('id'=>$id))->delete();
         if ($row) {
                $this->success('删除成功');
         }else{
                $this->error('删除失败');
         }

     }


    




  
     public function Appmanage_cate(){
        if (IS_POST) {
            $type=I('post.type');
            $id=I('post.typeid');
            $data['typename'] =  I('post.typename');
            $data['seq'] =I('post.seq');
            if ($type==1) {
                $row=M('Appmanage_type')->where(array('typeid'=>$id))->save($data);
                if ($row) {
                   $this->success('修改成功');
                }else{
                    $this->error('修改失败或无改动');
                }
            }else{
                $row=M('Appmanage_type')->add($data);
                if ($row) {
                   $this->success('添加成功');
                }else{
                    $this->error('添加失败');
                }
            }
         
        }else{
           $cate=M('Appmanage_type')->select();
           $this->assign('cate',$cate);
           $this->display(); 
        }
        
        
     }

    /*
    删除分类
     */
     public function del_pcate(){
         $id=I('get.id');
         $row=M('Appmanage_type')->where(array('typeid'=>$id))->delete();
         if ($row) {
               echo $row;
         }else{
                $this->error('删除失败');
         }
     }

     /*
     产品列表
      */
     public function Appmanage_list(){
        if (I('post.keyword')) {
           $map['Appmanage_name']=array('like','%'.I('post.keyword').'%');
        }
        $list=$this->lists('lj_Appmanage',$map);
        foreach ($list as $k => $v) {
            $list[$k]['typename']=M('Appmanage_type')->where(array('typeid'=>$v['typeid']))->getfield('typename');
            $list[$k]['plcname']=M('lj_plctype')->where(array('plcid'=>$v['plcid']))->getfield('plcname');
        }
        $this->assign('list',$list);
        $this->display();
     }

     /*
     产品添加
      */
     public function Appmanage_add(){
        if (IS_POST) {
           $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728321;
            $upload->rootPath = './Uploads/';
            /*$upload->savePath = './Uploads/Trademark/';*/
            // $upload->saveName = array('uniqid','');
            $upload->saveRule=array();
    $upload->exts= array('apk','zip','txt','jpg','png');
                $upload->autoSub  = true;
         // 上传文件 
            $info   =   $upload->upload();  
             
       
            $data=I('post.');
            var_dump($data);
            $data['file']=$info['filename']['savename'];
           // if($info['photo'])$data['photo']='/Uploads/'.$info['photo']['savepath'].$info['photo']['savename'];
           $data['time']=date('Y-m-d H:i:s');

            $row=M('app_version')->add($data);
            if ($row) {
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        } 
        
     }
     /*
     删除产品
      */
     public function del_Appmanage(){
        $Appmanage_id = I('get.id');
        $row=M('lj_Appmanage')->where(array('Appmanage_id'=>$Appmanage_id))->delete();
        if ($row) {
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
     }
     /*
     产品编辑
      */
     public function Appmanage_edit(){
        $id=I('get.id');
        if (IS_POST) {
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
            
        
            $data=I('post.');//接受全部POST过来的数据。

            if($info['pic'])$data['pic']='/Uploads/'.$info['pic']['savepath'].$info['pic']['savename'];
            if($info['photo'])$data['photo']='/Uploads/'.$info['photo']['savepath'].$info['photo']['savename'];
            $row=M('lj_Appmanage')->where(array('Appmanage_id'=>$id))->save($data);
            if ($row) {
                $this->success('编辑成功');
            }else{
                $this->error('编辑失败');
            }
        }else{
            $info=M('lj_Appmanage')->where(array('Appmanage_id'=>$id))->find();
            $cate=M('Appmanage_type')->select();
            $plc=M('lj_plctype')->select();
            $this->assign('plc',$plc);
            $this->assign('cate',$cate);
            $this->assign('info',$info);
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