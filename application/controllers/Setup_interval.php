<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_interval extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message='';
        $this->permissions=User_helper::get_permission('Setup_interval');
        $this->controller_url='setup_interval';
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
            $data['title']='List of Intervals';
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
        $items=array();
        $month=array();
        $user=User_helper::get_user();
        $check=Query_helper::get_info($this->config->item('table_tms_setup_assign_departments'),array('*'),array('user_id='.$user->user_id,'revision=1'));

        $this->db->from($this->config->item('table_tms_setup_interval').' si');
        $this->db->select('si.*');
        if(count($check)==0)
        {
            $this->db->select('ui.user_id');
            $this->db->select('sd.name department_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui','ui.department_id = si.department_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_department').' sd','sd.id = ui.department_id','INNER');
            $this->db->where('ui.user_id',$user->user_id);
            $this->db->where('ui.revision',1);
        }
        else
        {
            $this->db->select('asd.user_id');
            $this->db->select('sd.name department_name');
            $this->db->join($this->config->item('table_tms_setup_assign_departments').' asd','asd.department_id = si.department_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_department').' sd','sd.id = asd.department_id','INNER');
            $this->db->where('asd.user_id',$user->user_id);
            $this->db->where('asd.revision',1);
        }
        $this->db->where('sd.status',$this->config->item('system_status_active'));
        $items=$this->db->get()->result_array();

        foreach($items as $index=>&$item)
        {
            for($i=1;$i<13;$i++)
            {
                if($item['month_'.$i]==1)
                {
                    $month[$index][]=date("F", mktime(0, 0, 0,$i,1, 2000));
                }
            }
        }

        foreach($month as $index=>$m)
        {
            $items[$index]['month']=implode(", ",$m);
        }

        $this->json_return($items);
    }

    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['interval']=array(
                'id' => 0,
                'name' => '',
                'department_id' => '',
                'remarks' => '',
                'status' => 'Active'
            );
            for($i=1;$i<13;$i++)
            {
                $data['interval']['month_'.$i]=0;
            }
            $data['title']="Create Interval";

            $user=User_helper::get_user();
            $this->db->from($this->config->item('table_tms_setup_assign_departments').' ad');
            $this->db->select('ad.department_id value');
            $this->db->select('sd.name text');
            $this->db->join($this->config->item('table_login_setup_department').' sd','sd.id = ad.department_id','INNER');
            $this->db->where('ad.user_id',$user->user_id);
            $this->db->where('ad.revision',1);
            $this->db->where('sd.status',$this->config->item('system_status_active'));
            $data['departments']=$this->db->get()->result_array();
            if(!$data['departments'])
            {
                $data['departments']=Query_helper::get_info($this->config->item('table_login_setup_department'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"','id='.$user->department_id));
            }

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url.'/add_edit',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add/');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
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
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $data['interval']=Query_helper::get_info($this->config->item('table_tms_setup_interval'),array('*'),array('status ="'.$this->config->item('system_status_active').'"','id='.$item_id),1);
            if(!$data['interval'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong input. You use illegal way.';
                $this->json_return($ajax);
            }
            $user=User_helper::get_user();
            $this->db->from($this->config->item('table_tms_setup_assign_departments').' ad');
            $this->db->select('ad.department_id value');
            $this->db->select('sd.name text');
            $this->db->join($this->config->item('table_login_setup_department').' sd','sd.id = ad.department_id','INNER');
            $this->db->where('ad.user_id',$user->user_id);
            $this->db->where('ad.revision',1);
            $this->db->where('sd.status',$this->config->item('system_status_active'));
            $data['departments']=$this->db->get()->result_array();
            if(!$data['departments'])
            {
                $data['departments']=Query_helper::get_info($this->config->item('table_login_setup_department'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"','id='.$user->department_id));
            }

            $data['title']="Edit Interval (".$data['interval']['name'].")";
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
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();

            }
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $data=$this->input->post('interval');
            for($i=1;$i<13;$i++)
            {
                if(!array_key_exists('month_'.$i,$data))
                {
                    $data['month_'.$i]=0;
                }
            }

            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = time();
                Query_helper::update($this->config->item('table_tms_setup_interval'),$data,array("id = ".$id));
            }
            else
            {
                $data['user_created'] = $user->user_id;
                $data['date_created'] = time();
                Query_helper::add($this->config->item('table_tms_setup_interval'),$data);
            }
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_add();
                }
                else
                {
                    $this->system_list();
                }
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
        $this->form_validation->set_rules('interval[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('interval[department_id]',$this->lang->line('LABEL_DEPARTMENT_NAME'),'required');
        $this->form_validation->set_rules('interval[status]',$this->lang->line('STATUS'),'required');

        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
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

            $this->db->from($this->config->item('table_tms_setup_interval').' si');
            $this->db->select('si.*');
            $this->db->select('sd.name department_name');
            $this->db->join($this->config->item('table_login_setup_department').' sd','sd.id = si.department_id','INNER');
            $this->db->where('si.id',$item_id);
            $data['interval']=$this->db->get()->row_array();
            if(!$data['interval'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong input. You use illegal way.';
                $this->json_return($ajax);
            }
            $data['title']="Interval Details (".$data['interval']['name'].')';
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

}