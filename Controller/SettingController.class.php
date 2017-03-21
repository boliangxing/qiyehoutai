<?php
namespace Home\Controller;
use Think\Controller;
class SettingController extends CommController {


    /**
     行业分类列表与添加
     */
    public function setting_hangye(){
        if(IS_POST){
            $data['typename'] = I('post.typename');
            $data['xuhao'] = I('post.xuhao');

            $row = M('oa_hangye')->add($data);

            if ($row) {
                   $this->success('添加成功');
            }else{
                   $this->error('添加失败');
            }
        }else{
            $hangye = M('oa_hangye')->select();
            $this->assign('hangye',$hangye);
            $this->display();
        }
    }

    /*
    删除行业
     */
    public function setting_hangyedel(){
        $id = I('get.id');
        $row = M('oa_hangye')->where("typeid = $id")->delete();

        if ($row) {
               $this->success('删除成功');
        }else{
               $this->error('删除失败');
        }
    }
    /*
    行业编辑
     */
    public function setting_hangyeedit(){
        $id = I('post.typeid');
        $data['typename']= I('post.typename');
        $data['xuhao']= I('post.xuhao');

        $row = M('oa_hangye')->where("typeid = $id")->save($data);

        if ($row) {
               $this->success('编辑成功');
        }else{
               $this->error('编辑失败');
        }
    }


    /**
     区域分类
     */
    public function setting_zone(){
        if(IS_POST){
            $data['pid'] = I('post.pid');
            $data['zonename'] = I('post.zonename');

            $row = M('oa_zone')->add($data);

            if ($row) {
                   $this->success('添加成功');
            }else{
                   $this->error('添加失败');
            }
        }else{
            $select = M('oa_zone')->where('pid = 0')->order('xuhao asc')->select();
            $pid=I('get.pid');

            $pid = empty($pid) ? 0 :  $pid;
            $zone=M('oa_zone')->where("pid = $pid")->order('xuhao asc')->select();
            
            $this->assign('zone',$zone);
            $this->assign('select',$select);
            $this->assign('pid',$pid);
            
            $this->display();
        }
    }
    /*
    区域删除
     */
    public function setting_zonedel(){
        $id=I('get.id');
        $row=M('oa_zone')->where(array("zoneid= $id or pid = $id"))->delete();

        if ($row) {
               $this->success('删除成功');
        }else{
               $this->error('删除失败');
        }
    }
    /*
    区域编辑
     */
    public function setting_zonesave(){
        $id = I('post.zoneid');
        $data['zonename'] = I('post.zonename');
        $data['xuhao'] = I('post.xuhao');

        $row = M('oa_zone')->where("zoneid = $id")->save($data);

        if ($row) {
               $this->success('编辑成功');
        }else{
               $this->error('编辑失败');
        }
    }
    /*
    plc列表与编辑
     */
    public function plc(){
        if (IS_POST) {
            $data=I('post.');

            if (empty($data['Available'])) {
                $data['Available']=0;
            }
            $row=M('lj_plctype')->where(array('plcid'=>$data['plcid']))->save($data);
            if ($row) {
               $this->success('编辑成功');
            }else{
                   $this->error('编辑失败或未改变');
            }
        }else{
            $list=M('lj_plctype')->select();
            $this->assign('list',$list);
            $this->display();
        }
        
    }

    /*
    PLC删除
     */
    public function plcdel(){
        $id=I('get.id');
        $row=M('lj_plctype')->where(array('plcid'=>$id))->delete();
        if ($row) {
               $this->success('删除成功');
        }else{
               $this->error('删除失败');
        }
    }

    /*
    panel预设值列表
     */

    public function panel(){
        if (I('get.plcid')) {
           $map['plcid']=I('get.plcid');
        }
        $list=$this->lists('lj_panel',$map,'ishow desc,seq asc');
        foreach ($list as $k => $v) {
            $list[$k]['plcname']=M('lj_plctype')->where(array('plcid'=>$v['plcid']))->getfield('plcname');
        }
        $plcs=M('lj_plctype')->getfield('plcid,plcname');
        $this->assign('plcs',$plcs);
        $this->assign('now_panel',$list);
        $this->display();
    }

    /*
    panel预设值添加  $post['qishi']为起始位置  $post['piliang']为一次批量多少条  
     */
    public function panel_add(){
       
        if (IS_POST) {
            $post=I('post.');
            $qi=$post['qishi'];
            $zhi=$post['piliang'];
            for($i=0;$i<$zhi;$i++){
                $data[$i]['plcid']=$post['plcid'];
                $data[$i]['parcode']=$qi+$i;
            }
            $row=M('lj_panel')->addAll($data);
            if ($row) {
               $this->success('添加成功',U('setting/panel'));
            }else{
               $this->error('添加失败');
            }
        }else{
            $plcs=M('lj_plctype')->select();
            $this->assign('plcs',$plcs);
            $this->display();
        }
        
    }
    /*
    panel预设值编辑AJAX方法
    @param array|$data  $data['name']为要修改的字段。$data['val']为修改的值
     */
    public function panel_ajax(){
        $data=I('post.');

        $row=M('lj_panel')->where(array('id'=>$data['id']))->setfield($data['name'],$data['val']);
        if ($row) {
            echo 1;
        }else{
            echo 2;
        }
    }

    /*
    删除一行panel预设值
     */
    public function del_panel(){
        $id=I('get.id');
        $row=M('lj_panel')->where(array('id'=>$id))->delete();
        if ($row) {
               $this->success('删除成功');
        }else{
               $this->error('删除失败');
        }
    }

    /*
    方案列表
     */
    public function plan(){

        $list=M('lj_plan')->select();
        foreach ($list as $k => $v) {
            $list[$k]['plcname']=M('lj_plctype')->where(array('plcid'=>$v['plcid']))->getfield('plcname');
        }
        $plc=M('lj_plctype')->select();
        $this->assign('plc',$plc);
        $this->assign('list',$list);
        $this->display();
    }

    /*
    方案添加 

    I('post.planid')为-1时说明选择的是暂不复制方案
     */
    public function plan_add(){
        $data['planname']=I('post.planname');
        $data['plcid']=I('post.plcid');
        $planid=I('post.planid');
        if ($planid==-1) {
            $row=M('lj_plan')->add($data);
            copy(APP_PATH."Home/View/Facility/detail_1001.html", APP_PATH."Home/View/Facility/detail_".$row.".html");//新建方案给员工电脑端复制一个名为detail_.方案ID的模板
            copy(APP_PATH."Home/View/Facility/detail_1001.html", APP_PATH."Customer/View/Facility/detail_".$row.".html");//新建方案给客户电脑端复制一个名为detail_.方案ID的模板
          
            if ($row) {
               $this->success('添加成功');
            }else{
               $this->error('添加失败');
            }
        }else{
            $rowid=M('lj_plan')->add($data);
            $copy=M('lj_plan_parame')->where(array('planid'=>$planid))->select();
            $datalist=array();
            foreach ($copy as $k => $v) {
                $datalist[$k]['planid']=$rowid;
                $datalist[$k]['parname']=$v['parname'];
                $datalist[$k]['dbtype']=$v['dbtype'];
                $datalist[$k]['parcode']=$v['parcode'];
                $datalist[$k]['formula']=$v['formula'];
                $datalist[$k]['units']=$v['units'];
                // $datalist[$k]['bjzt']=$v['bjzt'];
                // $datalist[$k]['bjtj']=$v['bjtj'];
                // $datalist[$k]['bjnote']=$v['bjnote'];
                $datalist[$k]['ishow']=$v['ishow'];
                $datalist[$k]['seq']=$v['seq'];

            }
            //dump($datalist);die;
            $res=M('lj_plan_parame')->addAll($datalist);
            if ($res) {
               $this->success('添加成功');
            }else{
               $this->error('添加失败');
            }
        }
    }

    /*
    删除方案
     */
    public function plan_del(){
        $id=I('get.id');
        $row=M('lj_plan')->where(array('planid'=>$id))->delete();
        if ($row) {
               $this->success('删除成功');
        }else{
               $this->error('删除失败');
        }
    }

    /*
    方案编辑
     */
    public function plan_edit(){
        $planid=I('get.id');
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
            
        
            $data=I('post.');

            if($info['photo'])$data['photo']='/Uploads/'.$info['photo']['savepath'].$info['photo']['savename'];
             if (empty($data['ifsave'])) {
                $data['ifsave']=0;
            }
            if (empty($data['ifbaojing'])) {
                $data['ifbaojing']=0;
            }
            if (empty($data['offishow'])) {
                $data['offishow']=0;
            }
            $row=M('lj_plan')->where(array('planid'=>$planid))->save($data);
            if ($row) {
               $this->success('编辑成功');
            }else{
                   $this->error('编辑失败或未改变');
            }
        }else{
             $info=M('lj_plan')->where(array('planid'=>$planid))->find();
             $plc=M('lj_plctype')->select();
             $this->assign('plc',$plc);
             $this->assign('info',$info);
             $this->display();
        }
       
    }

    /*
    方案参数设置
     */
    public function plan_set(){
        /*
        now_panel 已有的
        diff_panel 未有的
         */
        $planid=I('get.planid');
        $plcid=I('get.plcid');
        $all_panel=M('lj_panel')->where(array('plcid'=>$plcid))->select();//找出该plcid下全部panel
        $all_parcode=M('lj_panel')->where(array('plcid'=>$plcid))->getfield('parcode',true);//找出该plcid下全部parcode
        $now_panel=M('lj_plan_parame')->where(array('planid'=>$planid))->order('ishow desc,seq asc')->select();//找出该planid下全部panel
        $now_parcode=M('lj_plan_parame')->where(array('planid'=>$planid))->getfield('parcode',true);//找出该planid下全部parcode

        
        if ($now_parcode==null) {
           $now_parcode=array();
        }
        $diff_parcode=array_diff($all_parcode, $now_parcode);/*求所有的与现有的parcode的差集*/
        
        $diff_parcode=implode(',',$diff_parcode);
        $diff_panel=M('lj_panel')->where(array('parcode'=>array('in',$diff_parcode),'plcid'=>$plcid))->order('ishow desc,seq asc')->select();
        $this->assign('planid',$planid);
        $this->assign('now_panel',$now_panel);
        $this->assign('diff_panel',$diff_panel);
        $this->display();
    }
    /*
    方案参数设置中的ajax修改
     */
    public function plan_ajax(){
        $data=I('post.');
        $row=M('lj_plan_parame')->where(array('id'=>$data['id']))->setfield($data['name'],$data['val']);
        if ($row) {
            echo 1;
        }else{
            echo 2;
        }
        
    }
    /*
    把未有的panel添加进来
     */
    public function parame_ajax(){
        $tmp=I('post.');
        $plcid= I('get.plcid');
        $tmp['parcode']=implode(',',$tmp['parcode']);
        $copy=M('lj_panel')->where(array('plcid'=>$plcid,'parcode'=>array('in',$tmp['parcode'])))->select();//找出选择的parcode每行数据
        $datalist=array();
        foreach ($copy as $k => $v) {
            $datalist[$k]['planid']=$tmp['planid'];
            $datalist[$k]['parname']=$v['parname'];
            $datalist[$k]['dbtype']=$v['dbtype'];
            $datalist[$k]['parcode']=$v['parcode'];
            $datalist[$k]['formula']=$v['formula'];
            $datalist[$k]['units']=$v['units'];
            
            $datalist[$k]['ishow']=$v['ishow'];
            $datalist[$k]['seq']=$v['seq'];

        }

        //赋值一次性添加
        $res=M('lj_plan_parame')->addAll($datalist);
        if ($res) {
           $this->success('添加成功');
        }else{
           $this->error('添加失败');
        }
    }
    /*
    删除某条方案参数
     */
    public function del_parame(){
        $id=I('get.id');
        $row=M('lj_plan_parame')->where(array('id'=>$id))->delete();
        if ($row) {
               $this->success('删除成功');
            }else{
               $this->error('删除失败');
            }
    }

    /*
    数据采集记录
     */
    public function data(){
        $ymdate=date('Ym',time());
        $table="lj_apilog_".$ymdate;
        C('LIST_ROWS','50');
        $dtuid=I('get.dtuid');
        if ($dtuid) {
            $map['dtuid']=$dtuid;
        }
        $list=$this->lists($table,$map);
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * 数据词典分类
     */
    public function dict_type(){
        $id = I('get.id');

        //数据字典分类
        if(empty($id)) {
            $list = $this->lists('lj_dict_type','','seq','dict_type_id as id, dict_type_pid as pid, dict_type_name as name');

//            echo json_encode($list);
//            exit;
            $this->assign('list', $list);
            $this->display();
        }
        //数据字典详情
        else{
            $list =  M(lj_dict)->where(array('dict_type'=>$id))->order('dict_seq')->select();
            $this->assign('list', $list);
            $this->display('dict_info');
        }
    }

    /**
     * 数据词典操作
     */
    public function dict_oper(){
        //添加
        $act = I('get.act');
        $model = M('lj_dict');
        switch($act){
            case 'add' :
                $data = I('post.');
                if($model->add($data)){
                    $this->success('添加成功');
                }else{
                    $this->error('添加失败');
                }
                break;
            case 'change' :
                $data = I('post.');
                $id = I('get.id');
                if($model->where(array('dict_id'=>$id))->save($data)){
                    $this->success('修改成功');
                }else{
                    $this->error('修改失败');
                }
                exit;
                break;
            case 'del' :
                $this->checkDicthave();
                break;
            default :
                $this->error('操作失败');
                break;
        }

    }

    //检查数据词典是否存在
    public function checkDicthave(){
        //在删除的时候要检查一下数据库里面是否还存在有数据词典

        //逻辑代码暂时不写直接return
        return false;
        /*
        if('存在'){
            $this->display('模板');
            return false;
        }else{
            return true;
        }
        */
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