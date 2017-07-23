<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>

<style>
    ul.sqr {list-style-type: square;}
</style>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="panel-group" id="accordion">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="external" data-toggle="collapse" data-target="#collapse1" href="#">
                        <?php echo $CI->lang->line('LABEL_COWORKER');?></a>
                </h4>
            </div>
            <div id="collapse1" class="panel-collapse collapse in" >
                <?php
                if($co_info)
                {
                    foreach($co_info as $co_worker)
                    {
                ?>
                        <div class="row show-grid">
                            <div class="col-xs-1">
                                <label class="control-label pull-right"><ul class="sqr"><li></li></ul></label>
                            </div>
                            <div class="col-xs-6">
                                <?php echo $co_worker['designation_name'];?><br>
                                <label class="control-label pull-left"><?php echo ' - '.$co_worker['co_name'];?></label>
                            </div>
                        </div>
                <?php
                    }
                }
                else
                {
                ?>
                    <div class="row show-grid">
                        <div class="col-xs-4">
                            <label class="control-label pull-left">Not Assigned</label>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="external" data-toggle="collapse" data-target="#collapse2" href="#">
                        <?php echo $CI->lang->line('LABEL_SUBORDINATE');?></a>
                </h4>
            </div>
            <div id="collapse2" class="panel-collapse collapse in">
                <?php
                if($sub_info)
                {
                    foreach($sub_info as $sub)
                    {
                    ?>
                        <div class="row show-grid">
                            <div class="col-xs-1">
                                <label class="control-label pull-right"><ul class="sqr"><li></li></ul></label>
                            </div>
                            <div class="col-xs-6">
                                <?php echo $sub['designation_name'];?><br>
                                <label class="control-label pull-left"><?php echo ' - '.$sub['sub_name'];?></label>
                            </div>
                        </div>
                <?php
                    }
                }
                else
                {
                ?>
                    <div class="row show-grid">
                        <div class="col-xs-4">
                            <label class="control-label pull-left">Not Assigned</label>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>

    </div>

</div>
<div class="clearfix"></div>
