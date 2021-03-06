<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_office_staff extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message='';
        $this->permissions=User_helper::get_permission('Setup_office_staff');
        $this->controller_url='setup_office_staff';
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
        elseif($action=='edit_subordinate_employee')
        {
            $this->system_edit_subordinate_employee($id);
        }
        elseif($action=='edit_coworker')
        {
            $this->system_edit_coworker($id);
        }
        elseif($action=='save_subordinate_employee')
        {
            $this->system_save_subordinate_employee();
        }
        elseif($action=='save_coworker')
        {
            $this->system_save_coworker();
        }
        elseif($action=='details')
        {
            $this->system_details($id);
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
            $data['title']='List of Office Staff';
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
        $user = User_helper::get_user();
        $this->db->from($this->config->item('table_login_setup_user').' user');
        $this->db->select('user.id,user.employee_id,user.user_name,user.status');
        $this->db->select('user_info.name,user_info.email,user_info.ordering,user_info.blood_group,user_info.mobile_no');
        $this->db->select('ug.name group_name');
        $this->db->select('designation.name designation_name');
        $this->db->select('department.name department_name');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user.id = user_info.user_id','INNER');
        $this->db->join($this->config->item('table_system_user_group').' ug','ug.id = user_info.user_group','LEFT');
        $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
        $this->db->join($this->config->item('table_login_setup_department').' department','department.id = user_info.department_id','LEFT');
        $this->db->where('user_info.revision',1);
        $this->db->where('user.status',$this->config->item('system_status_active'));
        $this->db->where('user_info.user_type_id',$this->config->item('USER_TYPE_EMPLOYEE'));
        $this->db->order_by('user_info.ordering','ASC');
        if($user->user_group!=1)
        {
            $this->db->where('user_info.user_group !=',1);
        }
        $items=$this->db->get()->result_array();

        $office_staffs=array();
        foreach($items as &$item)
        {
            if($item['group_name']==null)
            {
                $item['group_name']='Not Assigned';
            }
            $office_staffs[$item['id']]=$item;
        }

        $this->db->select('user_id, COUNT(user_id) as coworker_number');
        $this->db->from($this->config->item('table_tms_setup_coworker'));
        $this->db->where('revision',1);
        $this->db->group_by('user_id');
        $results=$this->db->get()->result_array();
        $coworkers=array_column($results,'coworker_number','user_id');

        $this->db->select('user_id, COUNT(user_id) as subordinate_number');
        $this->db->from($this->config->item('table_tms_setup_subordinate_employee'));
        $this->db->where('revision',1);
        $this->db->group_by('user_id');
        $results=$this->db->get()->result_array();
        $subordinates=array_column($results,'subordinate_number','user_id');

        foreach($office_staffs as $key=>$office_staff)
        {
            if (array_key_exists($key,$coworkers))
            {
                $office_staffs[$key]['coworker_number']=$coworkers[$key];
            }
            else
            {
                $office_staffs[$key]['coworker_number']=0;
            }
            if (array_key_exists($key,$subordinates))
            {
                $office_staffs[$key]['subordinate_number']=$subordinates[$key];
            }
            else
            {
                $office_staffs[$key]['subordinate_number']=0;
            }
        }

        $items=array();
        foreach($office_staffs as $office_staff)
        {
            $items[]=$office_staff;
        }
        $this->json_return($items);
    }

    private function system_edit_subordinate_employee($id)
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
            $user=User_helper::get_user();
            $data['user']=Query_helper::get_info($this->config->item('table_login_setup_user'),array('id','employee_id','user_name','status'),array('id ='.$item_id),1);
            if(!$data['user'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong input. You use illegal way.';
                $this->json_return($ajax);
            }
            $this->db->from($this->config->item('table_login_setup_user').' user');
            $this->db->select('user.employee_id');
            $this->db->select('user_info.*');
            $this->db->select('designation.name designation_name');
            $this->db->select('department.name department_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user.id = user_info.user_id','INNER');
            $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
            $this->db->join($this->config->item('table_login_setup_department').' department','department.id = user_info.department_id','LEFT');
            $this->db->where('user_info.revision',1);
            $this->db->where('user_info.user_type_id',$this->config->item('USER_TYPE_EMPLOYEE'));
            $this->db->order_by('user_info.ordering','ASC');
            if($user->user_group!=1)
            {
                $this->db->where('user_info.user_group !=',1);
            }
            $this->db->where('user.id !=',$item_id);
            $this->db->where('user.status =',$this->config->item('system_status_active'));
            $results=$this->db->get()->result_array();

            foreach($results as $result)
            {
                $data['office_staffs'][$result['department_id']][]=$result;
            }
            $results=Query_helper::get_info($this->config->item('table_tms_setup_subordinate_employee'),'*',array('user_id ='.$item_id,'revision =1'));
            $data['assigned_subordinate_employee']=array();
            foreach($results as $result)
            {
                $data['assigned_subordinate_employee'][]=$result['subordinate_id'];
            }
            $data['staff_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$item_id,'revision =1'),1);
            $data['title']="Edit Subordinate Employee of (".$data['staff_info']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url.'/edit_subordinate_employee',$data,true));
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

    private function system_edit_coworker($id)
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
            $user=User_helper::get_user();
            $data['user']=Query_helper::get_info($this->config->item('table_login_setup_user'),array('id','employee_id','user_name','status'),array('id ='.$item_id),1);
            if(!$data['user'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong input. You use illegal way.';
                $this->json_return($ajax);
            }
            $this->db->from($this->config->item('table_login_setup_user').' user');
            $this->db->select('user.employee_id');
            $this->db->select('user_info.*');
            $this->db->select('designation.name designation_name');
            $this->db->select('department.name department_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user.id = user_info.user_id','INNER');
            $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
            $this->db->join($this->config->item('table_login_setup_department').' department','department.id = user_info.department_id','LEFT');
            $this->db->where('user_info.revision',1);
            $this->db->where('user_info.user_type_id',$this->config->item('USER_TYPE_EMPLOYEE'));
            $this->db->order_by('user_info.ordering','ASC');
            if($user->user_group!=1)
            {
                $this->db->where('user_info.user_group !=',1);
            }
            $this->db->where('user.id !=',$item_id);
            $this->db->where('user.status =',$this->config->item('system_status_active'));
            $results=$this->db->get()->result_array();

            foreach($results as $result)
            {
                $data['office_staffs'][$result['department_id']][]=$result;
            }
            $results=Query_helper::get_info($this->config->item('table_tms_setup_coworker'),'*',array('user_id ='.$item_id,'revision =1'));
            $data['assigned_coworker']=array();
            foreach($results as $result)
            {
                $data['assigned_coworker'][]=$result['coworker_id'];
            }
            $data['coworker_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$item_id,'revision =1'),1);
            $data['title']="Edit Coworker of (".$data['coworker_info']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url.'/edit_coworker',$data,true));
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
    private function system_details($id)
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }
            $data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id='.$item_id,'revision=1'),1);
            if(!$data['user_info'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong input. You use illegal way.';
                $this->json_return($ajax);
            }
            $this->db->from($this->config->item('table_tms_setup_coworker').' co');
            $this->db->select('co.coworker_id');
            $this->db->select('user_info.name co_name,user_info.ordering');
            $this->db->select('designation.name designation_name');
            $this->db->select('department.name department_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=co.coworker_id');
            $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id=user_info.designation','left');
            $this->db->join($this->config->item('table_login_setup_department').' department','department.id=user_info.department_id','left');
            $this->db->where('co.user_id',$data['user_info']['user_id']);
            $this->db->where('co.revision',1);
            $this->db->order_by('user_info.ordering','ASC');
            $data['co_info']=$this->db->get()->result_array();

            $this->db->from($this->config->item('table_tms_setup_subordinate_employee').' sub');
            $this->db->select('sub.subordinate_id');
            $this->db->select('user_info.name sub_name,user_info.ordering');
            $this->db->select('designation.name designation_name');
            $this->db->select('department.name department_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=sub.subordinate_id');
            $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id=user_info.designation','left');
            $this->db->join($this->config->item('table_login_setup_department').' department','department.id=user_info.department_id','left');
            $this->db->where('sub.user_id',$data['user_info']['user_id']);
            $this->db->where('sub.revision',1);
            $this->db->order_by('user_info.ordering','ASC');
            $data['sub_info']=$this->db->get()->result_array();

            $data['title']="Workplace relationships of ".$data['user_info']['name'];
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
    private function system_save_subordinate_employee()
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
        if(!$this->check_validation_for_subordinate_employee())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $time=time();
            $this->db->trans_start();  //DB Transaction Handle START
            $subordinate_employees=$this->input->post('subordinate_employees');
            $revision_history_data=array();
            $revision_history_data['date_updated']=$time;
            $revision_history_data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_tms_setup_subordinate_employee'),$revision_history_data,array('revision=1','user_id='.$id));
            $this->db->where('user_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_tms_setup_subordinate_employee'));
            if(is_array($subordinate_employees))
            {
                foreach($subordinate_employees as $subordinate_employee)
                {
                    $data=array();
                    $data['user_id']=$id;
                    $data['subordinate_id']=$subordinate_employee;
                    $data['user_created'] = $user->user_id;
                    $data['date_created'] = $time;
                    $data['revision'] = 1;
                    Query_helper::add($this->config->item('table_tms_setup_subordinate_employee'),$data);
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

    private function system_save_coworker()
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
        if(!$this->check_validation_for_coworker())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $time=time();
            $this->db->trans_start();  //DB Transaction Handle START
            $coworkers=$this->input->post('coworkers');
            $revision_history_data=array();
            $revision_history_data['date_updated']=$time;
            $revision_history_data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_tms_setup_coworker'),$revision_history_data,array('revision=1','user_id='.$id));
            $this->db->where('user_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_tms_setup_coworker'));
            if(is_array($coworkers))
            {
                foreach($coworkers as $coworker)
                {
                    $data=array();
                    $data['user_id']=$id;
                    $data['coworker_id']=$coworker;
                    $data['user_created'] = $user->user_id;
                    $data['date_created'] = $time;
                    $data['revision'] = 1;
                    Query_helper::add($this->config->item('table_tms_setup_coworker'),$data);
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
    private function check_validation_for_subordinate_employee()
    {
        return true;
    }
    private function check_validation_for_coworker()
    {
        return true;
    }
}
