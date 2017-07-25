<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_NEW"),
        'href'=>site_url($CI->controller_url.'/index/add')
    );
}
if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_EDIT'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );
}
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line('ACTION_DETAILS'),
    'class'=>'button_jqx_action',
    'data-action-link'=>site_url($CI->controller_url.'/index/details')
);
if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5']) && ($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>"Download"
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')

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
    <div class="col-xs-12" style="margin-bottom: 20px;">
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="id"><?php echo $CI->lang->line('ID'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="name"><?php echo $CI->lang->line('LABEL_TASK_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="department_name"><?php echo $CI->lang->line('LABEL_DEPARTMENT_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="interval_name"><?php echo $CI->lang->line('LABEL_INTERVAL_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="status"><?php echo $CI->lang->line('STATUS'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="remarks"><?php echo $CI->lang->line('LABEL_REMARKS'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="ordering"><?php echo $CI->lang->line('LABEL_ORDER'); ?></label>
        </div>
    </div>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        var url = "<?php echo base_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'name', type: 'string' },
                { name: 'department_name', type: 'string' },
                { name: 'interval_name', type: 'string' },
                { name: 'status', type: 'string' },
                { name: 'remarks', type: 'string' },
                { name: 'ordering', type: 'string' }
            ],
            id: 'id',
            url: url
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                source: dataAdapter,
                pageable: true,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                pagesize:50,
                pagesizeoptions: ['20', '50', '100', '200','300','500'],
                selectionmode: 'singlerow',
                altrows: true,
                autoheight: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('ID'); ?>', dataField: 'id',width:'50',cellsAlign:'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_TASK_NAME'); ?>', dataField: 'name',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_DEPARTMENT_NAME'); ?>', dataField: 'department_name',width:'300'},
                    { text: '<?php echo $CI->lang->line('LABEL_INTERVAL_NAME'); ?>', dataField: 'interval_name',width:'200'},
                    { text: '<?php echo $CI->lang->line('STATUS'); ?>', dataField: 'status',width:'200'},
                    { text: '<?php echo $CI->lang->line('LABEL_REMARKS'); ?>', dataField: 'remarks',width:'200'},
                    { text: '<?php echo $CI->lang->line('LABEL_ORDER'); ?>', dataField: 'ordering',width:'200'}
                ]
            });
    });
</script>
