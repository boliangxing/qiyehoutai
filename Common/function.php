<?php

    
        /**
         * 把返回的数据集转换成Tree
         * @param array $list 要转换的数据集
         * @param string $pid parent标记字段
         * @param string $level level标记字段
         * @return array
         */
        function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0, $savekey=array()) {
            // 创建Tree
            $tree = array();
            if(is_array($list)) {
                // 创建基于主键的数组引用
                $refer = array();
                foreach ($list as $key => $data) {
                    $refer[$data[$pk]] =& $list[$key];
                    //echo $key;
                }
                foreach ($list as $key => $data) {
                    // 判断是否存在parent
                    $parentId =  $data[$pid];

                    // 遍历需要非保存key的字段
                    foreach($list[$key] as $k=>$v){
                        if(!in_array($k,$savekey))
                            unset($list[$key][$k]);
                    }

                    if ($root == $parentId) {
                    	// echo $list[$key];
                    	//if(in_array($key,$savekey)){
	                        $tree[] =& $list[$key];
                    	//}
                    }else{
                        if (isset($refer[$parentId])) {
                            $parent =& $refer[$parentId];
                            $parent[$child][] =& $list[$key];
                            //unset($list['bmname']);
                        }
                    }
                }
            }
            return $tree;
        }

        function showSelectOpertion($typeid){
            $data = M('lj_dict')->where(array('dict_type'=>$typeid))->select();
            return $data;
        }

        function showSelectOpertionVal($id){
            $data = M('lj_dict')->where(array('dict_id'=>$id))->field('dict_name')->find();
            return $data['dict_name'];
        }

?>