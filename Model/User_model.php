 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {
    public function get_user_page($start_time='',$last_time='',$title='',$offset,$limit){
        if($start_time && $last_time && $title){
            $start_time = strtotime($start_time);
            $last_time = strtotime($last_time);
            $where = " WHERE state!= -1 AND UNIX_TIMESTAMP(regtime) > $start_time AND  UNIX_TIMESTAMP(regtime) < $last_time  AND ( nick like '%".$title."%' ) ";

        }elseif($start_time && $last_time){
            $start_time = strtotime($start_time);
            $last_time = strtotime($last_time);
            $where = " WHERE state!= -1 AND UNIX_TIMESTAMP(regtime) > $start_time AND  UNIX_TIMESTAMP(regtime) < $last_time ";
        }elseif($title){
            $where = " WHERE state!= -1 AND ( nick like '%".$title."%' ) ";
        }else{
            $where = " WHERE state!= -1 ";
        }

        $sql = "SELECT * FROM users ".$where. "ORDER BY uid asc limit $offset,$limit";
        return $this->db->query($sql)->result_array();
    }
    public function get_user_info($uid){
    	$sql = "SELECT * FROM users WHERE uid=".$uid;
    	$result =$this->db->query($sql)->row_array();
    	return $result;
    }
    public function update_user($uid,$data){
        $where['uid'] = $uid;
        $result=$this->db->update('users', $data, $where);
        //返回值  
        return $result;  
    }
    public function get_user_num($start_time='',$last_time='',$title=''){
        if($start_time && $last_time && $title){
            $start_time = strtotime($start_time);
            $last_time = strtotime($last_time);
            $where = " WHERE state!= -1 AND UNIX_TIMESTAMP(regtime) > $start_time AND  UNIX_TIMESTAMP(regtime) < $last_time  AND ( nick like '%".$title."%' ) ";

        }elseif($start_time && $last_time){
            $start_time = strtotime($start_time);
            $last_time = strtotime($last_time);
            $where = " WHERE state!= -1 AND UNIX_TIMESTAMP(regtime) > $start_time AND  UNIX_TIMESTAMP(regtime) < $last_time ";
        }elseif($title){
            $where = " WHERE state!= -1 AND ( nick like '%".$title."%' ) ";
        }else{
            $where = " WHERE state!= -1 ";
        }

        $sql = "SELECT * FROM users".$where;
        return $this->db->query($sql)->num_rows();
        }
    public function uidsdel($uids){
        if ($uids) {
            /*$sql = "UPDATE user SET state = -1 WHERE uid in ".$uids; 
            $result = $this->db->query($sql)->result();
            return $result;*/
            $data=array('state'=>-1);
            $this->db->where_in('uid',$uids);
            $result=$this->db->update('users',$data);
            return $result;  
        }else{
            return false;
        }
       
    }
    

}