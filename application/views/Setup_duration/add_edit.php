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
    <input type="hidden" id="id" name="id" value="<?php echo $interval['id']; ?>" />
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
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="interval[name]" id="name" class="form-control" value="<?php echo $interval['name'];?>"/>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Months</label>
            </div>
            <div class="col-xs-8">
                <?php
                for($i=1;$i<13;$i++)
                {
                ?>
                    <?php  if($i==1){?>
                        <div class="checkbox">
                            <label><input type="checkbox" class="select_all" name="select_all" value="1"
                                    <?php
                                    $count=0;
                                    for($c=1;$c<13;$c++)
                                    {
                                        if($interval['month_'.$c]<1)
                                        {
                                            break;
                                        }
                                        else{++$count;}
                                    }
                                    if($count==12)
                                    {
                                        echo 'checked';
                                    }
                                    ?>> Select All</label>
                        </div>
                    <?php } ?>
                    <div class="checkbox">
                        <label><input type="checkbox" class="select" name="interval[month_<?php echo $i;?>]" value="1" <?php if($interval['month_'.$i]==1){echo 'checked';} ?>><?php echo date("F", mktime(0, 0, 0,$i,1, 2000));?></label>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="interval[remarks]" class="form-control"><?php echo $interval['remarks']; ?></textarea>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('STATUS');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status" name="interval[status]" class="form-control">
                    <option value="<?php echo $CI->config->item('system_status_active'); ?>"
                        <?php
                        if ($interval['status'] == $CI->config->item('system_status_active')) {
                            echo "selected='selected'";
                        }
                        ?> ><?php echo $CI->lang->line('ACTIVE') ?>
                    </option>
                    <option value="<?php echo $CI->config->item('system_status_inactive'); ?>"
                        <?php
                        if ($interval['status'] == $CI->config->item('system_status_inactive')) {
                            echo "selected='selected'";
                        }
                        ?> ><?php echo $CI->lang->line('INACTIVE') ?></option>
                </select>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>

<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(document).off('change','.select_all');
        $(document).on('change','.select_all',function(event)
        {
            if($(this).is(':checked'))
            {
                $('.select').prop('checked', true);
            }
            else
            {
                $('.select').prop('checked', false);

            }
        });

    });
</script>
