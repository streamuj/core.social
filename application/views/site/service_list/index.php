<?php if ($pages_config['total_rows'] > 0): ?>
    <?php
    $style_display = '';
    if (isset($category->common_data->display) && $category->common_data->display)
        $style_display = $category->common_data->display;

    ?>
    <?php widget('service')->display_list($list, $style_display); ?>
    <?php widget('service')->display_pagination($pages_config); ?>
<?php else: ?>
    <div class="clearfix mt20"></div>
    <div class="well">
        <?php echo lang('have_no_list') ?>
    </div>
<?php endif; ?>