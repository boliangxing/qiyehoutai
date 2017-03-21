<?php 


class Appmange_model extends CI_Model {
    const CMDS = 'app_cmds';
    const VERSION = 'app_version';

    public function get_logs_page($offset,$limit){
        $sql = "SELECT * FROM ".self::CMDS." ORDER BY time DESC LIMIT $offset,$limit";
        return $this->db->query($sql)->result_array();
    }

    public function get_logs_num(){
        $sql = "SELECT * FROM ".self::CMDS;
        return $this->db->query($sql)->num_rows();
    }

    public function get_version_num(){
        $sql = "SELECT * FROM ".self::VERSION;
        return $this->db->query($sql)->num_rows();
    }
    
    public function get_version_byID($id){
        $sql = "SELECT * FROM ".self::VERSION." WHERE id = '$id'";
        return $this->db->query($sql)->row_array();
    }

    public function get_version_page($offset,$limit){
        $sql = "SELECT * FROM ".self::VERSION." ORDER BY id DESC LIMIT $offset,$limit";
        return $this->db->query($sql)->result_array();
    }

    public function add_version($data){
        $sql = "INSERT INTO ".self::VERSION." VALUES ('','{$data['version']}','{$data['file']}','{$data['static']}','{$data['type']}','{$data['remark']}','{$data['time']}')";
        return $this->db->query($sql);
    }

    public function edit_version_byID($id ,$data){
        if(!empty($data['file'])){
            $sql = "UPDATE ".self::VERSION." set vname = '{$data['version']}',file = '{$data['file']}', static = '{$data['static']}',type='{$data['type']}', remark= '{$data['remark']}',time = '{$data['time']}' where id = '$id'";
        }else{
            $sql = "UPDATE ".self::VERSION." set vname = '{$data['version']}', static = '{$data['static']}',type='{$data['type']}', remark= '{$data['remark']}',time = '{$data['time']}' where id = '$id'";
        }
        return $this->db->query($sql);
    }

    public function del_version_byID($id){
        echo $sql = "DELETE FROM ".self::VERSION." WHERE id = '$id'";
        return $this->db->query($sql);
    }
}
