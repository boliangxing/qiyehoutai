<?php
namespace Home\Controller;
use Think\Controller;
class FacilityController extends CommController
{
    /*
        设备列表
    */
    public function facility_list()
    {

        $outtimes = time() - 3600;
        //15分钟内
        $outdatetime = date('Y-m-d H:i:s', $outtimes);
        $datachaoshi = time() - 3600;
         if(I('get.start_time')||I('get.last_time')){
                  $min=I('get.start_time');
                $max=I('get.last_time');
             $newx= strtotime($max);
               $newn= strtotime($min);
           $map = array('_logic' => 'and');
               $map['uptime'] = array('egt', $min);
           
        } 


      
        if (I('get.lianji') == 1) {
            $map['uptime'] = array('gt', $outdatetime);
        }
        if (I('get.run') == 1) {
            $map['run_status'] = 1;
            $map['uptime'] = array('gt', $outdatetime);
            $map['lastdatatime'] = array('gt', $datachaoshi);
        }
        if (I('get.shenhe') === '0') {
            $map['shenhe'] = 0;
        }
         if (I('get.shenhe') === '1') {
            $map['shenhe'] = 1;
        }
        if (I('get.search')) {
        if (empty($map)) {
                $map = array('_logic' => 'or');
                $map['dtuid'] = array('like', '%' . I('get.search') . '%');
                $map['fid'] = array('like', '%' . I('get.search') . '%');
        } else {
                $where['dtuid'] = array('like', '%' . I('get.search') . '%');
                $where['fid'] = array('like', '%' . I('get.search') . '%');
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $map['_logic'] = 'and';
        }
        }
        
        if(!I('get.ord')){

               $list = $this->lists('lj_facility', $map );

        }else{

        if(I('get.sx')=='sx'||I('get.sx')==''){

               $ord=I('get.ord');
               $list=$this->lists('lj_facility', $map,$ord.' asc');
               $sx='jx';
               $this->assign('sx', $sx);
                
        }else{

                $ord=I('get.ord');
                $list=$this->lists('lj_facility', $map,$ord.' desc');
                $sx='sx';
                $this->assign('sx', $sx);

        } 
        }

       

        $this->assign('lianji', I('get.lianji'));
        $this->assign('run',I('get.run') ); 
        $this->assign('shenhe',I('get.shenhe'));   
 
 
        foreach ($list as $k => $v) {
                $list[$k]['plcname'] = M('lj_plctype')->where(array('plcid' => $v['plcid']))->getfield('plcname');
        if ($v['uptime'] > $outdatetime) {
                $list[$k]['lianji'] = 1;
        } else {
                $list[$k]['lianji'] = 0;
        }
        }


  
 
 


        // echo "<pre>";
        // print_r($list);echo "<pre>";die;
         $this->assign('list', $list);
         $this->display();
    }
 
    /*
        
        详情页面..李元坤
    */
    public function detail_liyuankun()
    {
        $fid = I('get.fid');
        $fac_info = M('lj_facility')->where(array('fid' => $fid))->find();
        $customer = M('oa_customer')->where(array('customer_id' => $fac_info['customer_id']))->find();
        $customer['plcname'] = M('lj_plctype')->where(array('plcid' => $fac_info['plcid']))->getfield('plcname');
        $plcid = $fac_info['plcid'];
        $planid = $fac_info['planid'];
        $ymdate = date('Ym', time());
        $table = "zgdata_plcid" . $plcid . "_" . $ymdate;
        //$table="zgdata_plcid".$plcid."_201606";
        $lastdata = M($table)->where(array('fid' => $fid))->order('uptime desc')->find();
        $parame = M('lj_plan_parame')->where(array('planid' => $planid, 'isshow' => array('gt', 0)))->order('seq asc')->select();
        //dump($lastdata);
        //print_r($lastdata);
        if (empty($lastdata)) {
            exit('没有数据');
        }
        foreach ($parame as $k => $v) {
            // if (strpos($v['parcode'],'.') ) {
            //      $tempcode=str_replace('.','',$v['parcode']);
            //      $parame[$k]['parcode']='k'.$tempcode;
            // }else{
            //      $parame[$k]['parcode']='d'.$v['parcode'];
            // }
            if ($parame[$k]['dbtype'] == 'switch') {
                $tempcode = str_replace('.', '', $v['parcode']);
                $parame[$k]['parcode'] = 'k' . $tempcode;
            } else {
                $parame[$k]['parcode'] = 'd' . $v['parcode'];
            }
            $parame[$k]['value'] = $lastdata[$parame[$k]['parcode']];
            if ($parame[$k]['dbtype'] == 'simula') {
                if ($parame[$k]['formula'] == 'arr') {
                    $units_arr = explode(',', $parame[$k]['units']);
                    foreach ($units_arr as $key => $val) {
                        $units_arr[explode(':', $val)[0]] = explode(':', $val)[1];
                    }
                    $parame[$k]['value_name'] = $units_arr[$parame[$k]['value']];
                    //$parame[$k]['units'] = ''; //控制器状态是模拟量, 后面不应显示单位
                } elseif ($parame[$k]['formula'] == '1') {
                    $parame[$k]['value_name'] = $parame[$k]['value'] . $parame[$k]['units'];
                } else {
                    if ($parame[$k]['value'] > 32000) {
                        $parame[$k]['value_name'] = mt_rand(-100, -40) . $parame[$k]['units'];
                    } else {
                        $parame[$k]['value_name'] = eval("return {$parame[$k]['value']} {$parame[$k]['formula']};") . $parame[$k]['units'];
                    }
                }
            } elseif ($parame[$k]['dbtype'] == 'switch') {
                $units_swi = explode(',', $parame[$k]['units']);
                foreach ($units_swi as $ke => $va) {
                 $units_swi[explode(':', $va)[0]] = explode(':', $va)[1];
                }
                $parame[$k]['value_name'] = $units_swi[$parame[$k]['value']];
            }
        }
        $this->assign('customer', $customer);
        $this->assign('info', $fac_info);
        $this->assign('parame', $parame);
        //dump($parame);die;
        echo '<!--' . $fac_info['planid'] . '-->';
        $this->display('detail_' . $fac_info['planid']);
    }
    /*
        
        详情页面.. 王岳定
    */
    public function detail()
    {
        $fid = I('get.fid');
        $fac_info = M('lj_facility')->where(array('fid' => $fid))->find();
        $customer = M('oa_customer')->where(array('customer_id' => $fac_info['customer_id']))->find();
        $customer['plcname'] = M('lj_plctype')->where(array('plcid' => $fac_info['plcid']))->getfield('plcname');
        $map['dtuid']=$fac_info['dtuid'];
        $listss=$this->lists('lj_baojing',$map);

           foreach ($listss as $k => $v) {
           $listss[$k]['userr']=M('lj_facility')->where(array('dtuid'=>$v['dtuid']))->getfield('userr');
           $s_status=explode(',', $v['s_status']);
           if (in_array(UID,$s_status)) {
                $listss[$k]['is_read']=1;
           }else{
                $listss[$k]['is_read']=0;
           }
        }
       $plcid = $fac_info['plcid'];
        $planid = $fac_info['planid'];
        $ymdate = date('Ym', time());
        $table = "zgdata_plcid" . $plcid . "_" . $ymdate;
        //$table="zgdata_plcid".$plcid."_201606";
        $lastdata = M($table)->where(array('fid' => $fid))->order('uptime desc')->find();
        $parame = M('lj_plan_parame')->where(array('planid' => $planid, 'isshow' => array('gt', 0)))->order('seq asc')->select();
        //dump($lastdata);
        //print_r($lastdata);
        if (empty($lastdata)) {
            exit('没有数据');
        }
        foreach ($parame as $k => $v) {
            // if (strpos($v['parcode'],'.') ) {
            //      $tempcode=str_replace('.','',$v['parcode']);
            //      $parame[$k]['parcode']='k'.$tempcode;
            // }else{
            //      $parame[$k]['parcode']='d'.$v['parcode'];
            // }
            if ($parame[$k]['dbtype'] == 'switch') {
                $tempcode = str_replace('.', '', $v['parcode']);
                $parame[$k]['parcode'] = 'k' . $tempcode;
            } else {
                $parame[$k]['parcode'] = 'd' . $v['parcode'];
            }
            $parame[$k]['value'] = $lastdata[$parame[$k]['parcode']];
            if ($parame[$k]['dbtype'] == 'simula') {
                if ($parame[$k]['formula'] == 'arr') {
                    $units_arr = explode(',', $parame[$k]['units']);
                    foreach ($units_arr as $key => $val) {
                        $simula_unit=explode(':', $val)[1];
                        if (strstr($simula_unit,'R')){
                            $units_color[explode(':', $val)[0]] = 'RED';
                            $simula_unit=str_replace('R', '',$simula_unit);

                        }
                        if (strstr($simula_unit,'B')){
                            $units_color[explode(':', $val)[0]] = 'RED';
                            $simula_unit=str_replace('B', '',$simula_unit);
                        }
                        if (strstr($simula_unit,'Y')){
                            $units_color[explode(':', $val)[0]] = 'YELLOW';
                            $simula_unit=str_replace('Y', '',$simula_unit);
                        }
                        if (strstr($simula_unit,'G')){
                            $units_color[explode(':', $val)[0]] = 'GREEN';
                            $simula_unit=str_replace('G', '',$simula_unit);
                        }

                        $units_arr[explode(':', $val)[0]] = $simula_unit;
                    }
                    $parame[$k]['color'] = $units_color[$parame[$k]['value']];
                    $parame[$k]['value_name'] = $units_arr[$parame[$k]['value']];
                    //$parame[$k]['units'] = ''; //控制器状态是模拟量, 后面不应显示单位
                } elseif ($parame[$k]['formula'] == '1') {
                    $parame[$k]['value_name'] = $parame[$k]['value'] . $parame[$k]['units'];
                } else {
                    if ($parame[$k]['value'] > 32000) {
                        $parame[$k]['value_name'] = mt_rand(-100, -40) . $parame[$k]['units'];
                    } else {
                        $parame[$k]['value_name'] = eval("return {$parame[$k]['value']} {$parame[$k]['formula']};") . $parame[$k]['units'];
                    }
                }
            } elseif ($parame[$k]['dbtype'] == 'switch') {
                $units_swi = explode(',', $parame[$k]['units']);
                foreach ($units_swi as $ke => $va) {
                        $kunit=explode(':', $va)[1];
                        if (strstr($kunit,'R')){
                            $units_color[explode(':', $va)[0]] = 'red';
                            $kunit=str_replace('R', '',$kunit);

                        }
                        if (strstr($kunit,'B')){
                            $units_color[explode(':', $va)[0]] = 'blue';
                            $kunit=str_replace('B', '',$kunit);
                        }
                        if (strstr($kunit,'Y')){
                            $units_color[explode(':', $va)[0]] = 'yellow';
                            $kunit=str_replace('Y', '',$kunit);
                        }
                        if (strstr($kunit,'G')){
                            $units_color[explode(':', $va)[0]] = 'green';
                            $kunit=str_replace('G', '',$kunit);
                        }


                    $units_swi[explode(':', $va)[0]] = $kunit;
                }
                $parame[$k]['value_name'] = $units_swi[$parame[$k]['value']];
                $parame[$k]['color'] = $units_color[$parame[$k]['value']];
            }
        }
                $this->assign('fid', $fid);
        $this->assign('listss', $listss);

        $this->assign('customer', $customer);
        $this->assign('info', $fac_info);
        $this->assign('parame', $parame);
        //dump($parame);die;
        echo '<!--' . $fac_info['planid'] . '-->';
        $this->display('detail_' . $fac_info['planid']);
    }
    /*  详情页面获取动态数据的AJAX
     */ 
    public function ajax_detail()
    {
        $fid = I('get.fid');
        $fac_info = M('lj_facility')->where(array('fid' => $fid))->find();
        $plcid = $fac_info['plcid'];
        $planid = $fac_info['planid'];
        $ymdate = date('Ym', time());
        $table = "zgdata_plcid" . $plcid . "_" . $ymdate;
        //$table="zgdata_plcid".$plcid."_201606";
        //图形报表显示200条
        $fac_data = M($table)->where(array('fid' => $fid))->order('uptime desc')->limit(100)->select();
        $parame = M('lj_plan_parame')->where(array('planid' => $planid, 'ishow' => array('in', '2,3')))->order('ishow desc,seq asc')->select();
        //dump($fac_data);
        // dump($parame);
        $facility_date = array();
        foreach ($fac_data as $fk => $fv) {
            //更新时间
            $facility_date[$fk]['uptime'] = $fv['uptime'];
            $facility_date[$fk]['dbtype'] = $fv['dbtype'];
            foreach ($parame as $pk => $pv) {
                //数据名称
                $facility_date[$fk]['info'][$pk]['name'] = $pv['parname'];
                $facility_date[$fk]['info'][$pk]['parcode'] = $pv['parcode'];
                //开关位，去掉点找位置
                //错误,有的开关位不带小数点//if(strpos($pv['parcode'],'.')){
                if ($parame[$pk]['dbtype'] == 'switch') {
                    $tempcode = str_replace('.', '', $pv['parcode']);
                    $parame[$pk]['code'] = 'k' . $tempcode;
                } else {
                    $parame[$pk]['code'] = 'd' . $pv['parcode'];
                }
                //根据位置找值
                $parame[$pk]['value'] = $fv[$parame[$pk]['code']];
                //判断数据量或者开关量组装数据
                if ($parame[$pk]['dbtype'] == 'simula') {
                    if ($parame[$pk]['formula'] == 'arr') {
                        exit;
                        $units_arr = explode(',', $parame[$pk]['units']);
                        foreach ($units_arr as $key => $val) {
                            $units_arr[explode(':', $val)[0]] = explode(':', $val)[1];
                        }
                        $facility_date[$fk]['info'][$pk]['true_value'] = $units_arr[$parame[$pk]['value']];
                    } elseif ($parame[$k]['formula'] == '1') {
                        $facility_date[$fk]['info'][$pk]['true_value'] = $parame[$k]['value'] . $parame[$k]['units'];
                    } else {
                        if (is_numeric($parame[$pk]['value'])) {
                            //负数
                            if ($parame[$pk]['value'] > 32000) {
                                //$facility_date[$fk]['info'][$pk]['true_value']="--78";
                                $facility_date[$fk]['info'][$pk]['true_value'] = mt_rand(-100, -40);
                            } else {
                                $facility_date[$fk]['info'][$pk]['true_value'] = eval("return {$parame[$pk]['value']} {$parame[$pk]['formula']};");
                            }
                        }
                    }
                    //模拟量带上单位
                    $facility_charts[$fk]['info'][$pk]['name'] = $pv['parname'];
                    $facility_charts[$fk]['info'][$pk] = $facility_date[$fk]['info'][$pk];
                    $facility_charts[$fk]['uptime'] = $facility_date[$fk]['uptime'];
                } elseif ($parame[$pk]['dbtype'] == 'switch') {
                    $units_swi = explode(',', $parame[$pk]['units']);
                    foreach ($units_swi as $ke => $va) {
                        $units_swi[explode(':', $va)[0]] = explode(':', $va)[1];
                    }
                    $facility_date[$fk]['info'][$pk]['true_value'] = $units_swi[$parame[$pk]['value']];
                }
            }
        }
        //dump($facility_charts);die;
        foreach ($facility_charts as $key => $val) {
            foreach ($val['info'] as $k => $v) {
                if ($v['name'] == '采暖出水') {
                    $wendu1['value'][] = $v['true_value'];
                    $wendu1['uptime'][] = $val['uptime'];
                }
                if ($v['name'] == '介质温度') {
                    $wendu2['value'][] = $v['true_value'];
                    $wendu2['uptime'][] = $val['uptime'];
                }
                if ($v['name'] == '采暖回水') {
                    $wendu3['value'][] = $v['true_value'];
                    $wendu3['uptime'][] = $val['uptime'];
                }
            }
        }
        $wendu1['uptime'] = array_reverse($wendu1['uptime']);
        $wendu1['value'] = array_reverse($wendu1['value']);
        $wendu2['uptime'] = array_reverse($wendu2['uptime']);
        $wendu2['value'] = array_reverse($wendu2['value']);
        //dump($wendu2);die;
        $wendu3['uptime'] = array_reverse($wendu3['uptime']);
        $wendu3['value'] = array_reverse($wendu3['value']);
        //dump($wendu1);die;
        for ($i = 0; $i < 100; $i++) {
            $data[0][$i]['name'] = $wendu1['uptime'][$i];
            $data[0][$i]['value'] = array($wendu1['uptime'][$i], $wendu1['value'][$i]);
            $data[1][$i]['name'] = $wendu2['uptime'][$i];
            $data[1][$i]['value'] = array($wendu2['uptime'][$i], $wendu2['value'][$i]);
            $data[2][$i]['name'] = $wendu3['uptime'][$i];
            $data[2][$i]['value'] = array($wendu3['uptime'][$i], $wendu3['value'][$i]);
        }
        echo json_encode($data);
    }

    public function ajax_dy(){
  $fid = I('get.fid');
        $fac_info = M('lj_facility')->where(array('fid' => $fid))->find();

        $customer = M('oa_customer')->where(array('customer_id' => $fac_info['customer_id']))->find();
        $customer['plcname'] = M('lj_plctype')->where(array('plcid' => $fac_info['plcid']))->getfield('plcname');

      
            $map['dtuid']=$fac_info['dtuid'];
        
      $listss=$this->lists('lj_baojing',$map);

           foreach ($listss as $k => $v) {
           $listss[$k]['userr']=M('lj_facility')->where(array('dtuid'=>$v['dtuid']))->getfield('userr');
           $s_status=explode(',', $v['s_status']);
           if (in_array(UID,$s_status)) {
                $listss[$k]['is_read']=1;
           }else{
                $listss[$k]['is_read']=0;
           }
        }
        $plcid = $fac_info['plcid'];
        $planid = $fac_info['planid'];
        $ymdate = date('Ym', time());
        $table = "zgdata_plcid" . $plcid . "_" . $ymdate;
        //$table="zgdata_plcid".$plcid."_201606";
        $lastdata = M($table)->where(array('fid' => $fid))->order('uptime desc')->find();
        $parame = M('lj_plan_parame')->where(array('planid' => $planid, 'isshow' => array('gt', 0)))->order('seq asc')->select();
        //dump($lastdata);
        //print_r($lastdata);
        if (empty($lastdata)) {
            exit('没有数据');
        }
        foreach ($parame as $k => $v) {
            // if (strpos($v['parcode'],'.') ) {
            //      $tempcode=str_replace('.','',$v['parcode']);
            //      $parame[$k]['parcode']='k'.$tempcode;
            // }else{
            //      $parame[$k]['parcode']='d'.$v['parcode'];
            // }
            if ($parame[$k]['dbtype'] == 'switch') {
                $tempcode = str_replace('.', '', $v['parcode']);
                $parame[$k]['parcode'] = 'k' . $tempcode;
            } else {
                $parame[$k]['parcode'] = 'd' . $v['parcode'];
            }
            $parame[$k]['value'] = $lastdata[$parame[$k]['parcode']];
            if ($parame[$k]['dbtype'] == 'simula') {
                if ($parame[$k]['formula'] == 'arr') {
                    $units_arr = explode(',', $parame[$k]['units']);
                    foreach ($units_arr as $key => $val) {
                        $simula_unit=explode(':', $val)[1];
                        if (strstr($simula_unit,'R')){
                            $units_color[explode(':', $val)[0]] = 'RED';
                            $simula_unit=str_replace('R', '',$simula_unit);

                        }
                        if (strstr($simula_unit,'B')){
                            $units_color[explode(':', $val)[0]] = 'RED';
                            $simula_unit=str_replace('B', '',$simula_unit);
                        }
                        if (strstr($simula_unit,'Y')){
                            $units_color[explode(':', $val)[0]] = 'YELLOW';
                            $simula_unit=str_replace('Y', '',$simula_unit);
                        }
                        if (strstr($simula_unit,'G')){
                            $units_color[explode(':', $val)[0]] = 'GREEN';
                            $simula_unit=str_replace('G', '',$simula_unit);
                        }

                        $units_arr[explode(':', $val)[0]] = $simula_unit;
                    }
                    $parame[$k]['color'] = $units_color[$parame[$k]['value']];
                    $parame[$k]['value_name'] = $units_arr[$parame[$k]['value']];
                    //$parame[$k]['units'] = ''; //控制器状态是模拟量, 后面不应显示单位
                } elseif ($parame[$k]['formula'] == '1') {
                    $parame[$k]['value_name'] = $parame[$k]['value'] . $parame[$k]['units'];
                } else {
                    if ($parame[$k]['value'] > 32000) {
                        $parame[$k]['value_name'] = mt_rand(-100, -40) . $parame[$k]['units'];
                    } else {
                        $parame[$k]['value_name'] = eval("return {$parame[$k]['value']} {$parame[$k]['formula']};") . $parame[$k]['units'];
                    }
                }
            } elseif ($parame[$k]['dbtype'] == 'switch') {
                $units_swi = explode(',', $parame[$k]['units']);
                foreach ($units_swi as $ke => $va) {
                        $kunit=explode(':', $va)[1];
                        if (strstr($kunit,'R')){
                            $units_color[explode(':', $va)[0]] = 'red';
                            $kunit=str_replace('R', '',$kunit);

                        }
                        if (strstr($kunit,'B')){
                            $units_color[explode(':', $va)[0]] = 'blue';
                            $kunit=str_replace('B', '',$kunit);
                        }
                        if (strstr($kunit,'Y')){
                            $units_color[explode(':', $va)[0]] = 'yellow';
                            $kunit=str_replace('Y', '',$kunit);
                        }
                        if (strstr($kunit,'G')){
                            $units_color[explode(':', $va)[0]] = 'green';
                            $kunit=str_replace('G', '',$kunit);
                        }


                    $units_swi[explode(':', $va)[0]] = $kunit;
                }
                $parame[$k]['value_name'] = $units_swi[$parame[$k]['value']];
                $parame[$k]['color'] = $units_color[$parame[$k]['value']];
                    $parame[$k]['uptime'] = $fac_info['uptime'];
            }
        }
        

      $data=array("success"=>true,"data"=>$parame);
 
        echo json_encode($data);
   }
    /*
        简洁页面
        根据fid去facility表查plcid，planid    根据plcid找到zgdata_plcid表    再根据fid去zgdata_plcid表里找出最后一条数据    根据planid去plan_parame计算。  
    
        计算方式  查出所有planid=planid列
        parcode前加D
        dbtype=switch的
        则为str2arr(units)['value'];
        dbtype=simula的
        则判断
        formula=1    value.units
        formula=arr  str2arr(units)['value'];
    */
    public function facility_zjym()
    {
        $fid = I('get.fid');
        $fac_info = M('lj_facility')->where(array('fid' => $fid))->find();
        $plcid = $fac_info['plcid'];
        $planid = $fac_info['planid'];
        $ymdate = date('Ym', time());
        $table = "zgdata_plcid" . $plcid . "_" . $ymdate;
        $lastdata = M($table)->where(array('fid' => $fid))->order('uptime desc')->find();
        $parame = M('lj_plan_parame')->where(array('planid' => $planid, 'isshow' => array('gt', 0)))->order('seq asc')->select();
        foreach ($parame as $k => $v) {
            // if (strpos($v['parcode'],'.') ) {
            //      $tempcode=str_replace('.','',$v['parcode']);
            //      $parame[$k]['parcode']='k'.$tempcode;
            // }else{
            //      $parame[$k]['parcode']='d'.$v['parcode'];
            // }
            if ($parame[$k]['dbtype'] == 'switch') {
                $tempcode = str_replace('.', '', $v['parcode']);
                $parame[$k]['parcode'] = 'k' . $tempcode;
            } else {
                $parame[$k]['parcode'] = 'd' . $v['parcode'];
            }
            $parame[$k]['value'] = $lastdata[$parame[$k]['parcode']];
            if ($parame[$k]['dbtype'] == 'simula') {
                if ($parame[$k]['formula'] == 'arr') {
                    $units_arr = explode(',', $parame[$k]['units']);
                    foreach ($units_arr as $key => $val) {
                        $units_arr[explode(':', $val)[0]] = explode(':', $val)[1];
                    }
                    $parame[$k]['value_name'] = $units_arr[$parame[$k]['value']];
                } elseif ($parame[$k]['formula'] == '1') {
                    $parame[$k]['value_name'] = $parame[$k]['value'] . $parame[$k]['units'];
                } else {
                    if ($parame[$k]['value'] > 32000) {
                        $parame[$k]['value_name'] = mt_rand(-100, -40) . $parame[$k]['units'];
                    } else {
                        //dump($parame[$k]['value']);dump($parame[$k]['formula']);
                        if (empty($parame[$k]['value'])) {
                            $parame[$k]['value'] = 0;
                        }
                        $parame[$k]['value_name'] = eval("return {$parame[$k]['value']} {$parame[$k]['formula']};") . $parame[$k]['units'];
                    }
                }
            } elseif ($parame[$k]['dbtype'] == 'switch') {
                $units_swi = explode(',', $parame[$k]['units']);
                foreach ($units_swi as $ke => $va) {
                    $units_swi[explode(':', $va)[0]] = explode(':', $va)[1];
                }
                $parame[$k]['value_name'] = $units_swi[$parame[$k]['value']];
            }
            $color = "RYWB";
            $bijiaostr = $this->bijiaostr($parame[$k]['value_name'], $color);
            if ($bijiaostr) {
                $parame[$k]['color'] = $bijiaostr;
            } else {
                $parame[$k]['color'] = 'G';
            }
        }
        $this->assign('parame', $parame);
        $this->display();
    }
    /*
        比较两个字符串相同的部分
    */
    function bijiaostr($str1, $str2)
    {
        //将字符串转成数组
        $arr1 = str_split($str1);
        $arr2 = str_split($str2);
        //计算字符串的长度
        $len1 = strlen($str1);
        $len2 = strlen($str2);
        //初始化相同字符串的长度
        $len = 0;
        //初始化相同字符串的起始位置
        $pos = -1;
        for ($i = 0; $i < $len1; $i++) {
            for ($j = 0; $j < $len2; $j++) {
                //找到首个相同的字符
                if ($arr1[$i] == $arr2[$j]) {
                    //判断后面的字符是否相同
                    for ($p = 0; $i + $p < $len1 && $j + $p < $len2 && $arr1[$i + $p] == $arr2[$j + $p] && $arr1[$i + $p] != ''; $p++) {
                    }
                    if ($p > $len) {
                        $pos = $i;
                        $len = $p;
                    }
                }
            }
        }
        if ($pos == -1) {
            return;
        } else {
            return substr($str1, $pos, $len);
        }
    }
    /*
       添加设备
    */
    public function add_sb()
    {
        $id = I('get.fid');
        if (IS_POST) {
            $upload = new \Think\Upload();
            // 实例化上传类
            $upload->maxSize = 3145728;
            $upload->rootPath = './Uploads/';
            /*$upload->savePath = './Uploads/Trademark/';*/
            $upload->saveName = array('uniqid', '');
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
            $upload->autoSub = true;
            $upload->subName = array('date', 'Ymd');
            // 上传文件
            $info = $upload->upload();
            $data = I('post.');
             $data['userr'] = I('post.customer_name');
             $data['customer_id'] = I('post.customer_id');
                          $data['userr_id'] = I('post.cid');

            $data['tcpudp'] = 'tcp';

            //print_r($data);exit();

            if (empty($data['jhtime'])) {
                $data['jhtime'] = date('Y-m-d H:i:s');
            }
            if (empty($data['qytime'])) {
                $data['qytime'] = date('Y-m-d H:i:s');
            }
            if (empty($data['regtime'])) {
                $data['regtime'] = date('Y-m-d H:i:s');
            }
            if (empty($data['uptime'])) {
                $data['uptime'] = date('Y-m-d H:i:s');
            }

            if ($info['photo']) {
                $data['photo'] = '/Uploads/' . $info['photo']['savepath'] . $info['photo']['savename'];
            }
            if ($id) {
                $row = M('lj_facility')->where(array('fid' => $id))->save($data);
            } else {
                $row = M('lj_facility')->add($data);
            }
            if ($row) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }
        } else {
            if ($id) {
                $info = M('lj_facility')->where(array('fid' => $id))->find();
                $info['planname'] = M('lj_plan')->where(array('planid' => $info['planid']))->getfield('planname');
                $info['product_name'] = M('lj_product')->where(array('product_id' => $info['product_id']))->getfield('product_name');
                $this->assign('info', $info);
            }
            $this->display();
        }
    }
    /*
        停止采集
    */
    public function tzcj()
    {
        $fid = I('get.fid');
        $data['available'] = 0;
        $row = M('lj_facility')->where(array('fid' => $fid))->save($data);
        if ($row) {
            $this->success('设备禁用成功');
        } else {
            $this->error('设备禁用失败');
        }
    }
    /*
        启用设备
    */
    public function qiyong()
    {
        $fid = I('get.fid');
        $data['available'] = 1;
        $row = M('lj_facility')->where(array('fid' => $fid))->save($data);
        if ($row) {
            $this->success('设备启用成功');
        } else {
            $this->error('设备启用失败');
        }
    }
    /*
        删除设备
    */
    public function del_facility()
    {
        $fid = I('get.fid');
        $dtuid = I('get.dtuid');
        $row = M('lj_facility')->where(array('fid' => $fid))->delete();
        if ($row) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
    /*
        添加设备中 用户列表
    */
    public function add_userr()
    {
        if (I('get.search')) {
            $map['userr'] = array('like', '%' . I('get.search') . '%');
        }
        $list = $this->lists('oa_customer', $map);
        $this->assign('list', $list);
        $this->display();
    }

     public function ad_userr()
    {
        if (I('get.search')) {
            $map['userr'] = array('like', '%' . I('get.search') . '%');
        }
        $list = $this->lists('oa_customer', $map);
        $this->assign('list', $list);
        $this->display();
    }
    /*
        添加设备中 方案列表
    */
    public function add_plan()
    {
        if (I('get.search')) {
            $map['planname'] = array('like', '%' . I('get.search') . '%');
        }
        C('LIST_ROWS', '28');
        $list = $this->lists('lj_plan', $map);
        $this->assign('list', $list);
        $this->display();
    }
    /*
        添加设备中 产品列表
    */
    public function add_product()
    {
        if (I('get.search')) {
            $map['product_name'] = array('like', '%' . I('get.search') . '%');
        }
        C('LIST_ROWS', '28');
        $list = $this->lists('lj_product', $map, 'product_id asc');
        $this->assign('list', $list);
        $this->display();
    }
    //设备报表
    public function facility_report()
    {
        //global $ZG;
        //phpinfo();
        $fid = I('get.fid');
        $showtype = I('get.type');
        $fac_info = M('lj_facility')->where(array('fid' => $fid))->find();
        $plcid = $fac_info['plcid'];
        $planid = $fac_info['planid'];
        $ymdate = date('Ym', time());
        $prevdate = date("Ym", strtotime("-1 month"));
        // $table="zgdata_plcid".$plcid."_".$ymdate;
        // $table="zgdata_plcid".$plcid."_201606";
        //图形报表显示200条
        // if ($showtype=='charts'){
        //     $fac_data=M($table)->where(array('fid'=>$fid))->order('uptime desc')->limit(200)->select();
        // }else{
        //     $fac_data=M($table)->where(array('fid'=>$fid))->order('uptime desc')->limit(100)->select();
        // }
        $Pdo = M();
        $fac_data = $Pdo->query("select * from zgdata_plcid" . $plcid . "_" . $ymdate . " where fid = '{$fid}' order by uptime desc limit 100");
        $i = 1;
        //print "<pre>";
        //print_r($fac_data);
        while (count($fac_data) < 100 && !empty($Pdo->query("SHOW TABLES LIKE '%zgdata_plcid" . $plcid . "_" . $prevdate . "%'"))) {
            //var_dump($Pdo->query("select * from zgdata_plcid".$plcid."_".$prevdate." where fid = '$fid' order by uptime desc limit ".(100-count($fac_data))));
            $fac_data = array_merge($fac_data, $Pdo->query("select * from zgdata_plcid" . $plcid . "_" . $prevdate . " where fid = '{$fid}' order by uptime desc limit " . (100 - count($fac_data))));
            $prevdate = date("ym", strtotime("-" . $i . " month"));
            $i++;
        }
        $parame = M('lj_plan_parame')->where(array('planid' => $planid, 'ishow' => array('in', '2,3')))->order('ishow desc,seq asc')->select();
        //print_r($parame);
        $facility_date = array();
        foreach ($fac_data as $fk => $fv) {
            //更新时间
            $facility_date[$fk]['uptime'] = $fv['uptime'];
            $facility_date[$fk]['dbtype'] = $fv['dbtype'];
            foreach ($parame as $pk => $pv) {
                //数据名称
                $facility_date[$fk]['info'][$pk]['name'] = $pv['parname'];
                $facility_date[$fk]['info'][$pk]['parcode'] = $pv['parcode'];
                //开关位，去掉点找位置
                //错误,有的开关位不带小数点//if(strpos($pv['parcode'],'.')){
                if ($parame[$pk]['dbtype'] == 'switch') {
                    $tempcode = str_replace('.', '', $pv['parcode']);
                    $parame[$pk]['code'] = 'k' . $tempcode;
                } else {
                    $parame[$pk]['code'] = 'd' . $pv['parcode'];
                }
                //根据位置找值
                $parame[$pk]['value'] = $fv[$parame[$pk]['code']];
                //$newvals[$parcode]=$ZG->getnewval($parame[$pk]['value'],$parame[$pk]['formula'] ,$parame[$pk]['units']);
                //判断数据量或者开关量组装数据
                if ($parame[$pk]['dbtype'] == 'simula') {
                    if ($parame[$pk]['formula'] == 'arr') {
                        $units_arr = explode(',', $parame[$pk]['units']);
                        foreach ($units_arr as $key => $val) {
                            $units_arr[explode(':', $val)[0]] = explode(':', $val)[1];
                        }
                        $facility_date[$fk]['info'][$pk]['true_value'] = $units_arr[$parame[$pk]['value']];
                    } elseif ($parame[$k]['formula'] == '1') {
                        $facility_date[$fk]['info'][$pk]['true_value'] = $parame[$k]['value'] . $parame[$k]['units'];
                    } else {
                        if (is_numeric($parame[$pk]['value'])) {
                            //负数
                            if ($parame[$pk]['value'] > 32000) {
                                //$facility_date[$fk]['info'][$pk]['true_value']="--78";
                                $facility_date[$fk]['info'][$pk]['true_value'] = mt_rand(-100, -40);
                                //$this->zg->convvalue($parame[$pk]['value']);
                                //$facility_date[$fk]['info'][$pk]['true_value']=$ZG->getnewval($parame[$pk]['value'],$parame[$pk]['formula'],$parame[$pk]['units']);
                            } else {
                                // echo  $pv['parcode'].': (';
                                // echo $gongshi= "{$parame[$pk]['value']}  {$parame[$pk]['formula']}";
                                // echo  ")<br> ";
                                $facility_date[$fk]['info'][$pk]['true_value'] = eval("return {$parame[$pk]['value']} {$parame[$pk]['formula']};");
                                //$facility_date[$fk]['info'][$pk]['true_value'] = $ZG->getnewval($parame[$pk]['value'],$parame[$pk]['formula'],$parame[$pk]['units']);
                            }
                        }
                        //echo $fk.' : '.$pv['parcode'].' : '.$facility_date[$fk]['info'][$pk]['true_value'].' <br> ';
                    }
                    //模拟量带上单位
                    $facility_charts[$fk]['info'][$pk]['name'] = $pv['parname'];
                    $facility_charts[$fk]['info'][$pk] = $facility_date[$fk]['info'][$pk];
                } elseif ($parame[$pk]['dbtype'] == 'switch') {
                    $units_swi = explode(',', $parame[$pk]['units']);
                    foreach ($units_swi as $ke => $va) {
                        $units_swi[explode(':', $va)[0]] = explode(':', $va)[1];
                    }
                    $facility_date[$fk]['info'][$pk]['true_value'] = $units_swi[$parame[$pk]['value']];
                }
            }
        }
        //print_r($facility_date);
        $this->assign('fid', $fid);
        $this->assign('facility_date', $facility_date);
        $this->assign('facility_charts', $facility_charts);
        //简化图形报表数据
        $this->assign('type', I('get.type'));
        $this->display();
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
    protected function lists($model, $where = array(), $order = '', $field = true)
    {
        $options = array();
        $REQUEST = (array) I('get.');
        if (is_string($model)) {
            $model = M($model);
        }
        $OPT = new \ReflectionProperty($model, 'options');
        $OPT->setAccessible(true);
        $pk = $model->getPk();
        if ($order === null) {
            //order置空
        } else {
            if (isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']), array('desc', 'asc'))) {
                $options['order'] = '`' . $REQUEST['_field'] . '` ' . $REQUEST['_order'];
            } elseif ($order === '' && empty($options['order']) && !empty($pk)) {
                $options['order'] = $pk . ' desc';
            } elseif ($order) {
                $options['order'] = $order;
            }
        }
        unset($REQUEST['_order'], $REQUEST['_field']);
        if (empty($where)) {
            $where = array('status' => array('egt', 0));
        }
        if (!empty($where)) {
            $options['where'] = $where;
        }
        $options = array_merge((array) $OPT->getValue($model), $options);
        $total = $model->where($options['where'])->count();
        if (isset($REQUEST['r'])) {
            $listRows = (int) $REQUEST['r'];
        } else {
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 20;
        }
        $page = new \Think\Page($total, $listRows, $REQUEST);
        if ($total > $listRows) {
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $page->setConfig('prev', '«');
            $page->setConfig('next', '»');
        }
        $p = $page->show();
        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $options['limit'] = $page->firstRow . ',' . $page->listRows;
        $model->setProperty('options', $options);
        return $model->field($field)->select();
    }
}