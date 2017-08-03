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
        elseif($action=='details')
        {
            $this->system_details($id);
        }
        elseif($action=='assign_users')
        {
            $this->system_assign_users($id);
        }
        elseif($action=='save')
        {
            $this->system_save();
        }
        elseif($action=='save_assign_users')
        {
            $this->system_save_assign_users();
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
        $this->db->where('rt.status !=',$this->config->item('system_status_delete'));
        $this->db->where('rt.revision',1);
        $this->db->order_by('rt.id','DESC');
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
                'status' => $this->config->item('system_status_active')
            );
            $data['departments']=Query_helper::get_info($this->config->item('table_login_setup_department'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            $data['intervals']=Query_helper::get_info($this->config->item('table_tms_setup_interval'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
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
            if($id>0)
            {
                $item_id=$id;
            }else
            {
                $item_id=$this->input->post('id');
            }
            $data['task']=Query_helper::get_info($this->config->item('table_tms_activities_regular_task'),array('*'),array('id ='.$item_id),1);
            $data['title']="Edit Task (".$data['task']['name'].')';
            $data['departments']=Query_helper::get_info($this->config->item('table_login_setup_department'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            $data['intervals']=Query_helper::get_info($this->config->item('table_tms_setup_interval'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
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

    private function system_assign_users($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $user=User_helper::get_user();

            $data['item']=Query_helper::get_info($this->config->item('table_tms_activities_regular_task'),'*',array('id ='.$item_id),1);
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong input. You use illegal way.';
                $this->json_return($ajax);
            }

            $this->db->select('user.id,user.employee_id');
            $this->db->select('user_info.name');
            $this->db->select('designation.name designation_name');
            $this->db->from($this->config->item('table_login_setup_user').' user');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
            $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id=user_info.designation','LEFT');
            $this->db->where('user.status',$this->config->item('system_status_active'));
            $this->db->where('user_info.department_id',$data['item']['department_id']);
            $this->db->where('user_info.revision',1);
            $data['subordinates']=$this->db->get()->result_array();

            $results=Query_helper::get_info($this->config->item('table_tms_activities_assign_user_regular_task'),'user_id',array('regular_task_id ='.$item_id,'revision =1'));
            $data['assigned_users']=array();
            foreach($results as $result)
            {
                $data['assigned_users'][]=$result['user_id'];
            }
            
            $data['title']="Assign Users to Regular Task (".$data['item']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url.'/assign_users',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/assign_users/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_details($id)
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->select('rt.*');
            $this->db->select('d.name department_name');
            $this->db->select('i.name interval_name');
            $this->db->from($this->config->item('table_tms_activities_regular_task').' rt');
            $this->db->join($this->config->item('table_login_setup_department').' d','d.id=rt.department_id','INNER');
            $this->db->join($this->config->item('table_tms_setup_interval').' i','i.id=rt.interval_id','INNER');
            $this->db->where('rt.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong input. You use illegal way.';
                $this->json_return($ajax);
            }

            $this->db->select('user.employee_id');
            $this->db->select('user_info.name');
            $this->db->select('d.name designation_name');
            $this->db->from($this->config->item('table_tms_activities_assign_user_regular_task').' aurt');
            $this->db->join($this->config->item('table_login_setup_user').' user','user.id=aurt.user_id','INNER');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=aurt.user_id','INNER');
            $this->db->join($this->config->item('table_login_setup_designation').' d','d.id=user_info.designation','LEFT');
            $this->db->where('aurt.regular_task_id',$item_id);
            $this->db->where('aurt.revision',1);
            $this->db->where('user_info.revision',1);
            $data['assigned_users']=$this->db->get()->result_array();

            $data['title']="Assigned users of Regular Task - ".$data['item']['name'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url.'/details',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$item_id);
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
        $this->load->library('form_validation');
        $this->form_validation->set_rules('task[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('task[department_id]',$this->lang->line('LABEL_DEPARTMENT_NAME'),'required');
        $this->form_validation->set_rules('task[interval_id]',$this->lang->line('LABEL_INTERVAL_NAME'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function system_save_assign_users()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if(!$this->check_validation_for_assign_users())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $time=time();
            $this->db->trans_start();  //DB Transaction Handle START
            $users=$this->input->post('users');
            $revision_history_data=array();
            $revision_history_data['date_updated']=$time;
            $revision_history_data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_tms_activities_assign_user_regular_task'),$revision_history_data,array('revision=1','regular_task_id='.$id));

            $this->db->where('regular_task_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_tms_activities_assign_user_regular_task'));

            if(is_array($users))
            {
                foreach($users as $assign_user)
                {
                    $data=array();
                    $data['regular_task_id']=$id;
                    $data['user_id']=$assign_user;
                    $data['user_created'] = $user->user_id;
                    $data['date_created'] = $time;
                    $data['revision'] = 1;
                    Query_helper::add($this->config->item('table_tms_activities_assign_user_regular_task'),$data);
                }
            }

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
    private function check_validation_for_assign_users()
    {
        return true;
    }
}
