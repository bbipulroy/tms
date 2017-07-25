<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE_NEW"),
    'id'=>'button_action_save_new',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $task['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_TASK_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="task[name]" class="form-control" value="<?php echo $task['name'];?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DESCRIPTION');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="task[description]" class="form-control"><?php echo $task['description']; ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_INTERVAL_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="interval_id" name="task[interval_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($intervals as $interval)
                    {?>
                        <option value="<?php echo $interval['value']?>" <?php if($interval['value']==$task['interval_id']){ echo "selected";}?>><?php echo $interval['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DEPARTMENT_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <?php if(empty($accessed_departments)){?>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label pull-left"><?php echo $self_department_name['name'];?></label>
            </div>
            <?php } else{?>
            <div class="col-sm-4 col-xs-8">
                <select id="department_id" name="task[department_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($accessed_departments as $accessed_department)
                    {?>
                        <option value="<?php echo $accessed_department['department_id']?>" <?php if($accessed_department['department_id']==$task['department_id']){ echo "selected";}?>><?php echo $accessed_department['department_name'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <?php } ?>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="task[remarks]" class="form-control"><?php echo $task['remarks']; ?></textarea>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ORDER');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="task[ordering]" class="form-control" value="<?php echo $task['ordering'] ?>" >
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>

