<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line('ACTION_EDIT'),
        'href'=>site_url($CI->controller_url.'/index/edit/'.$item['id'])
    );
}
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['name'];?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DESCRIPTION');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['description'];?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DEPARTMENT_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['department_name'];?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_INTERVAL_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['interval_name'];?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['remarks'];?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ASSIGN_USERS'); ?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php
                if(count($assigned_users)<1)
                {
                    echo '<br/>None of users are assigned in this Regular Task.';
                }
                else
                {
                    ?>
                    <ul>
                        <?php
                        foreach($assigned_users as $user)
                        {
                            ?>
                            <li><?php echo $user['name'].' - '.$user['employee_id'].' ('.$user['designation_name'].')'; ?></li>
                            <?php
                        }
                        ?>
                    </ul>
                    <?php
                }
            ?>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ORDER');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['ordering'];?>
        </div>
    </div>
</div>

<div class="clearfix"></div>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>
