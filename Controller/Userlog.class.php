<?php
namespace Home\Controller;
use Think\Controller;
class Userlog extends CommController {
 
    public function __construct(){
        parent::__construct();
        $this->load->model('appmange_model');
        $this->load->model('user_model');
        $this->load->library('pagination');
        $this->config->load('pagination');
    }

    //用户app操作日志列表
    public function index(){
        $args = $this->uri->uri_to_assoc ();
        $offset = empty ( $args['page'] ) ? '0' : $args['page'];
        $config['base_url'] = site_url('userlog/index/page');
        $config['total_rows'] = $this->appmange_model->get_logs_num();
        $config['uri_segment'] = 4;
        $config['per_page'] = 10;
        $limit = $config['per_page'];

        $this->pagination->initialize($config); 
        $data['page'] = $this->pagination->create_links();
        $data['num'] = $config['total_rows'];

        $data['cmdsList'] = $this->appmange_model->get_logs_page($offset,$limit);
        $this->load->view('appmanage/logindex',$data);
    }


    //获取用户信息
    public function info(){
        $args=$this->uri->uri_to_assoc ();
        $data['info']=$this->user_model->get_user_info($args['uid']);
        $this->load->view('appmanage/userinfo',$data);
    }

//app版本管理列表
    public function update(){
        $args = $this->uri->uri_to_assoc ();
        $offset = empty ( $args['page'] ) ? '0' : $args['page'];
        $config['base_url'] = site_url('userlog/update/page');
        $config['total_rows'] = $this->appmange_model->get_version_num();
        $config['uri_segment'] = 4;
        $config['per_page'] = 10;
        $limit = $config['per_page'];

        $this->pagination->initialize($config); 
        $data['page'] = $this->pagination->create_links();
        $data['num'] = $config['total_rows'];

        $data['updateList'] = $this->appmange_model->get_version_page($offset,$limit);
        $this->load->view('appmanage/update',$data);
    }

//版本发布
    public function release(){
        if($_POST){
            //提交表单验证...
            //...

            //文件上传
            $file = $this->appUpload();
            //删除相同文件名的文件夹

            $data = array(
                'version'   => $this->input->post('version'),
                'type'      => $this->input->post('type'),
                'static'    => $this->input->post('static'),
                'file'      => $file,
                'remark'    => $this->input->post('remark'),
                'time'      => date("Y-m-d H:i:s")
            );
            $this->appmange_model->add_version($data);

            $type['msg'] = '添加成功';
            $this->load->view('appmanage/success',$type);
        }else{
            $this->load->view('appmanage/release');
        }
    }

//编辑发布
    public function editRelease(){
        if($_POST){

            $file = $this->appUpload();

            $data = array(
                'version'   => $this->input->post('version'),
                'type'      => $this->input->post('type'),
                'static'    => $this->input->post('static'),
                'file'      => $file,
                'remark'    => $this->input->post('remark'),
                'time'      => date("Y-m-d H:i:s")
            );

            $this->appmange_model->edit_version_byID($this->input->post('id'),$data);

            $type['msg'] = '修改成功';
            $this->load->view('appmanage/success',$type);
        }else{
            $id = $this->uri->segment(3);
            $data['releaseInfo'] = $this->appmange_model->get_version_byID($id);
            $this->load->view('appmanage/editRelease',$data);
        }
    }

//删除发布
    public function delRelease(){
        $id = $this->input->post('id');
        echo $this->appmange_model->del_version_byID($id);
    }


//上传apk

    private function appUpload(){
        if(empty($_FILES['filename']['name'])){
            return;
        }
        $type = $this->input->post('type')==1 ? 'apk' : 'ipa';
        $is_file = '../uploads/app/'.$this->input->post('type').'_'.$this->input->post('version').'.'.$type;
        if(file_exists($is_file)){
            unlink($is_file);
        }
        $fileConfig = array(
            'upload_path'   => '../uploads/app/',
            'allowed_types' => '*',
            'file_name'     => $this->input->post('type').'_'.$this->input->post('version').'.'.$type
        );
        $this->load->library('upload', $fileConfig);
        if ($this->upload->do_upload('filename')){
            $data = array('upload_data' => $this->upload->data());
            return $data['upload_data']['file_name'];
        }else{
            //var_dump(array('error' => $this->upload->display_errors()));exit; //调试
            return '';
        }
    }
}