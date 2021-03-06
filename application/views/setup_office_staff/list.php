<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line('LABEL_CHANGE_SUBORDINATE'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_subordinate_employee')
    );
}
if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line('LABEL_CHANGE_COWORKER'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_coworker')
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
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="employee_id"><?php echo $CI->lang->line('LABEL_EMPLOYEE_ID'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="name"><?php echo $CI->lang->line('LABEL_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" value="group_name"><?php echo $CI->lang->line('LABEL_USER_GROUP'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="designation_name"><?php echo $CI->lang->line('LABEL_DESIGNATION_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="department_name"><?php echo $CI->lang->line('LABEL_DEPARTMENT_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="subordinate_number"><?php echo $CI->lang->line('LABEL_NUMBER_OF_SUBORDINATE'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="coworker_number"><?php echo $CI->lang->line('LABEL_NUMBER_OF_COWORKER'); ?></label>
        </div>
    </div>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        var url = "<?php echo base_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'employee_id', type: 'string' },
                { name: 'name', type: 'string' },
                { name: 'group_name', type: 'string' },
                { name: 'designation_name', type: 'string' },
                { name: 'department_name', type: 'string' },
                { name: 'subordinate_number', type: 'int' },
                { name: 'coworker_number', type: 'int' }
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
                    { text: '<?php echo $CI->lang->line('LABEL_EMPLOYEE_ID'); ?>', dataField: 'employee_id',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_NAME'); ?>', dataField: 'name'},
                    { text: '<?php echo $CI->lang->line('LABEL_USER_GROUP'); ?>', dataField: 'group_name',width:'200',filtertype:'list',hidden:true},
                    { text: '<?php echo $CI->lang->line('LABEL_DESIGNATION_NAME'); ?>', dataField: 'designation_name',width:'200'},
                    { text: '<?php echo $CI->lang->line('LABEL_DEPARTMENT_NAME'); ?>', dataField: 'department_name',width:'200'},
                    { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_SUBORDINATE'); ?>', dataField: 'subordinate_number',width:'160',cellsAlign:'right',filtertype:'list'},
                    { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_COWORKER'); ?>', dataField: 'coworker_number',width:'150',cellsAlign:'right',filtertype:'list'}
                ]
            });
    });
</script>
