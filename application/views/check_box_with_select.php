<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
foreach($items as $item)
{
    ?>
    <div class="checkbox">
        <label title="<?php echo $item['name']; ?>">
            <input type="checkbox" name="users[]" value="<?php echo $item['id']; ?>"><?php echo $item['name'].' - '.$item['employee_id'].' ('.$item['designation_name'].')'; ?>
        </label>
    </div>
<?php
}
?>

