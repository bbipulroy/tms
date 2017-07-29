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
        'label'=>$CI->lang->line("ACTION_EDIT"),
        'href'=>site_url($CI->controller_url.'/index/edit/'.$interval['id'])
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/details/'.$interval['id'])
);
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
            <?php echo $interval['name'];?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MONTH');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php
                for($i=1;$i<13;$i++)
                {
                    if($interval['month_'.$i]==1)
                    {
            ?>
                        <ul><li><?php echo date("F", mktime(0, 0, 0,$i,1, 2000));?></li></ul>
            <?php
                    }
                }
            ?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $interval['remarks'];?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $this->lang->line('STATUS');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $interval['status'];?>
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
