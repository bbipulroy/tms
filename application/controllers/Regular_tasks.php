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
        $user=User_helper::get_user();
        $accessed_department=Query_helper::get_info($this->config->item('table_tms_setup_assign_departments'),array('*'),array('user_id='.$user->user_id,'revision=1'));
        $this->db->from($this->config->item('table_tms_activities_regular_task').' rt');
        $this->db->select('rt.*');
        if(!$accessed_department)
        {
            $this->db->select('ui.user_id');
            $this->db->select('department.name department_name');
            $this->db->select('interval.name interval_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui','ui.department_id = rt.department_id','LEFT');
            $this->db->join($this->config->item('table_tms_setup_interval').' interval','interval.id = rt.interval_id','INNER');
            $this->db->join($this->config->item('table_login_setup_department').' department','department.id = ui.department_id','INNER');
            $this->db->where('ui.user_id',$user->user_id);
            $this->db->where('ui.revision',1);
        }else
        {
            $this->db->select('ad.user_id');
            $this->db->select('department.name department_name');
            $this->db->select('interval.name interval_name');
            $this->db->join($this->config->item('table_tms_setup_assign_departments').' ad','ad.department_id = rt.department_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_department').' department','department.id = ad.department_id','INNER');
            $this->db->join($this->config->item('table_tms_setup_interval').' interval','interval.id = rt.interval_id','INNER');
            $this->db->where('ad.user_id',$user->user_id);
            $this->db->where('ad.revision',1);
        }
        $this->db->order_by('rt.ordering','ASC');
        $this->db->where('rt.revision',1);
        $this->db->where('rt.status =',$this->config->item('system_status_active'));
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
            $user=User_helper::get_user();
            $accessed_department=Query_helper::get_info($this->config->item('table_tms_setup_assign_departments'),array('*'),array('user_id='.$user->user_id,'revision=1'));
            $this->db->from($this->config->item('table_tms_setup_interval').' interval');
            $this->db->select('interval.*');
            if(!$accessed_department)
            {
                $this->db->select('ui.user_id');
                $this->db->select('department.name department_name');
                $this->db->join($this->config->item('table_login_setup_user_info').' ui','ui.department_id = interval.department_id','LEFT');
                $this->db->join($this->config->item('table_login_setup_department').' department','department.id = ui.department_id','INNER');
                $this->db->where('ui.user_id',$user->user_id);
                $this->db->where('ui.revision',1);
            }else
            {
                $this->db->select('ad.user_id');
                $this->db->select('department.name department_name');
                $this->db->join($this->config->item('table_tms_setup_assign_departments').' ad','ad.department_id = interval.department_id','LEFT');
                $this->db->join($this->config->item('table_login_setup_department').' department','department.id = ad.department_id','INNER');
                $this->db->where('ad.user_id',$user->user_id);
                $this->db->where('ad.revision',1);
            }
            $data['intervals']=$this->db->get()->result_array();
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
            if(empty($self_department_id))
            {
                $data['self_department_name']['name']='Not Assigned Yet';
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
            if($id)
            {
                $item_id=$id;
            }else
            {
                $item_id=$this->input->post('id');
            }
            $data['task']=Query_helper::get_info($this->config->item('table_tms_activities_regular_task'),array('*'),array('id ='.$item_id),1);
            $data['title']="Edit User (".$data['task']['name'].')';
            $user=User_helper::get_user();
            $accessed_department=Query_helper::get_info($this->config->item('table_tms_setup_assign_departments'),array('*'),array('user_id='.$user->user_id,'revision=1'));
            $this->db->from($this->config->item('table_tms_setup_interval').' interval');
            $this->db->select('interval.*');
            if(!$accessed_department)
            {
                $this->db->select('ui.user_id');
                $this->db->select('department.name department_name');
                $this->db->join($this->config->item('table_login_setup_user_info').' ui','ui.department_id = interval.department_id','LEFT');
                $this->db->join($this->config->item('table_login_setup_department').' department','department.id = ui.department_id','INNER');
                $this->db->where('ui.user_id',$user->user_id);
                $this->db->where('ui.revision',1);
            }else
            {
                $this->db->select('ad.user_id');
                $this->db->select('department.name department_name');
                $this->db->join($this->config->item('table_tms_setup_assign_departments').' ad','ad.department_id = interval.department_id','LEFT');
                $this->db->join($this->config->item('table_login_setup_department').' department','department.id = ad.department_id','INNER');
                $this->db->where('ad.user_id',$user->user_id);
                $this->db->where('ad.revision',1);
            }
            $data['intervals']=$this->db->get()->result_array();
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
            if(empty($self_department_id))
            {
                $data['self_department_name']['name']='Self Department Needed To Edit';
            }
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url.'/add_edit',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$item_id);
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
        $task_info=$this->input->post('task');
        $department_id=false;
        if(isset($task_info['department_id']))
        {
            $department_id=$task_info['department_id'];
        }
        $user = User_helper::get_user();
        $self_department_id=$user->department_id;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('task[name]',$this->lang->line('LABEL_NAME'),'required');
        if(empty($self_department_id) && !$department_id)
        {
            $this->form_validation->set_rules('task[department_id]',$this->lang->line('LABEL_DEPARTMENT_NAME'),'required');
        }
        $user_id=$user->user_id;
        $data['accessed_department']=Query_helper::get_info($this->config->item('table_tms_setup_assign_departments'),array('user_id'),array('user_id ='.$user_id,'revision ='.'1'));
        $counter=count($data['accessed_department']);
        if($counter>1 && $self_department_id)
        {
            if(!$department_id)
            {
                $this->form_validation->set_rules('task[department_id]',$this->lang->line('LABEL_DEPARTMENT_NAME'),'required');
            }
        }
        $this->form_validation->set_rules('task[interval_id]',$this->lang->line('LABEL_INTERVAL_NAME'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
}
