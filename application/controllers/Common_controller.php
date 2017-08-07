<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_controller extends Root_Controller
{
    private  $message;
    public function __construct()
    {
        parent::__construct();
        $this->message='';

    }

    public function get_employee_by_department_id()
    {
        $department_id = $this->input->post('department_id');
        $task_id = $this->input->post('task_id');
        $html_container_id='#employee_id';
        if($this->input->post('$html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $this->db->select('user.id,user.employee_id');
        $this->db->select('user_info.name');
        $this->db->select('designation.name designation_name');
        $this->db->from($this->config->item('table_login_setup_user').' user');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
        $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id=user_info.designation','LEFT');
        $this->db->where('user.status',$this->config->item('system_status_active'));
        $this->db->where('user_info.department_id',$department_id);
        $this->db->where('user_info.revision',1);
        $data['items']=$this->db->get()->result_array();
        $results=Query_helper::get_info($this->config->item('table_tms_activities_assign_user_regular_task'),'user_id',array('regular_task_id ='.$task_id,'revision =1'));
        $data['assigned_users']=array();
        foreach($results as $result)
        {
            $data['assigned_users'][]=$result['user_id'];
        }
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("check_box_with_select",$data,true));
        $this->json_return($ajax);
    }
}
