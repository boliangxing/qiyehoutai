<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Userlog_model extends CI_Model {
    	
		public function get_logs_page($offset,$limit){
        $sql = "SELECT * FROM app_cmds limit $offset,$limit";
        return $this->db->query($sql)->result_array();
    	}	

    	public function get_logs_num(){
        $sql = "SELECT * FROM app_cmds";
        return $this->db->query($sql)->num_rows();
    	}
    
    
}
