<?php if ($can_do): ?>
	<a  href="#0" title='' class="btn btn-default btn-round btn-xs  do_action <?php if ($subscribed) echo 'on'; ?>"
		data-action="toggle"
		data-url-on="<?php echo $url_subscribe ?>"
		data-url-off="<?php echo $url_subscribe_del ?>"
		data-title-on='Hủy theo dõi<?php //echo lang("action_subscribe_del") ?>'
		data-title-off='Theo dõi<?php //echo lang("action_subscribe") ?>'
		data-text-on='Hủy theo dõi<?php //echo lang("action_subscribe_del") ?>'
		data-text-off='Theo dõi<?php // echo lang("action_subscribe") ?>'
		data-class-on="active"
		>
	</a>

<?php else: ?>
	<a class="btn btn-default btn-round btn-xs act-notify-modal" title='<?php echo lang("action_subscribe") ?>' href="javascript:void(0)"
	   data-content="<?php echo lang("notice_please_login_to_use_function") ?>">
		Theo dõi<?php //echo lang("action_subscribe") ?></a>
<?php endif; ?>
