<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Regular_tasks extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message='';
        $this->permissions=User_helper::get_permission('Regular_tasks');
        $this->controller_url='regular_tasks';
    }

    public function index($action='list',$id=0)
    {
        if($action=='list')
        {
            $this->system_list();
        }
        elseif($action=='get_items')
        {
            $this->system_get_items();
        }
        elseif($action=='add')
        {
            $this->system_add();
        }
        elseif($action=='edit')
        {
            $this->system_edit($id);
        }
        elseif($action=='save')
        {
            $this->system_save();
        }
        else
        {
            $this->system_list();
        }
    }

    private function system_list()
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']='List of Regular Task';
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/list',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
    }

    private function system_get_items()
    {
        $this->db->from($this->config->item('table_tms_activities_regular_task').' rt');
        $this->db->select('rt.*');
        $this->db->select('department.name department_name');
        $this->db->select('interval.name interval_name');
        $this->db->join($this->config->item('table_login_setup_department').' department','department.id = rt.department_id','LEFT');
        $this->db->join($this->config->item('table_tms_setup_interval').' interval','interval.id = rt.interval_id','LEFT');
        $this->db->order_by('rt.ordering','ASC');
        $this->db->where('rt.revision',1);
        $items=$this->db->get()->result_array();
        $this->json_return($items);
    }

    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $data['title']='Create New Task';
            $data['task'] = array(
                'id' => 0,
                'name' => '',
                'description' => '',
                'interval_id' => '',
                'department_id' => '',
                'remarks' => '',
                'ordering' => ''
            );
            $data['intervals']=Query_helper::get_info($this->config->item('table_tms_setup_interval'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $user = User_helper::get_user();
            $user_id=$user->user_id;
            $self_department_id=$user->department_id;
            $this->db->from($this->config->item('table_tms_setup_assign_departments').' ad');
            $this->db->select('ad.department_id,ad.user_id');
            $this->db->select('department.name department_name');
            $this->db->join($this->config->item('table_login_setup_department').' department','department.id = ad.department_id','LEFT');
            $this->db->where('ad.user_id',$user_id);
            $this->db->where('revision',1);
            $data['accessed_departments']=$this->db->get()->result_array();
            if(empty($data['accessed_departments']))
            {
                $data['self_department_name']=Query_helper::get_info($this->config->item('table_login_setup_department'),array('name'),array('id ='.$self_department_id),1);
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add');
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/add_edit',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
    }

    private function system_edit($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $task_id=$this->input->post('id');
            }
            else
            {
                $task_id=$id;
            }
            $data['task']=Query_helper::get_info($this->config->item('table_tms_activities_regular_task'),array('*'),array('id ='.$task_id),1);
            $data['title']="Edit User (".$data['task']['name'].')';
            $data['intervals']=Query_helper::get_info($this->config->item('table_tms_setup_interval'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $user = User_helper::get_user();
            $user_id=$user->user_id;
            $self_department_id=$user->department_id;
            $this->db->from($this->config->item('table_tms_setup_assign_departments').' ad');
            $this->db->select('ad.department_id,ad.user_id');
            $this->db->select('department.name department_name');
            $this->db->join($this->config->item('table_login_setup_department').' department','department.id = ad.department_id','LEFT');
            $this->db->where('ad.user_id',$user_id);
            $this->db->where('revision',1);
            $data['accessed_departments']=$this->db->get()->result_array();
            if(empty($data['accessed_departments']))
            {
                $data['self_department_name']=Query_helper::get_info($this->config->item('table_login_setup_department'),array('name'),array('id ='.$self_department_id),1);
            }
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url.'/add_edit',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$task_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $time=time();
            $this->db->trans_start();  //DB Transaction Handle START
            $revision_data=array();
            $revision_data['date_updated']=$time;
            $revision_data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_tms_activities_regular_task'),$revision_data,array('revision=1','id='.$id));
            $this->db->where('id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_tms_activities_regular_task'));
            $data=$this->input->post('task');
            if(empty($data['department_id']))
            {
                $data['department_id']=$user->department_id;
            }
            $data['user_created'] = $user->user_id;
            $data['date_created'] = $time;
            $data['revision'] = 1;
            Query_helper::add($this->config->item('table_tms_activities_regular_task'),$data);
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    private function check_validation()
    {
        return true;
    }
}