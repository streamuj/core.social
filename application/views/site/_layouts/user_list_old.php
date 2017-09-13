<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"   xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<?php widget('site')->head(["css"=>"page_user"]); ?>
	<?php //widget('site')->head(); ?>
</head>
<body  >
<div class="wrapper">
	<?php echo $header; ?>
	<!-- MAIN -->
	<div id="main">
		<div class="container">
			<?php t('view')->load('tpl::user_list/_common/top') ?>
			<?php echo widget('user')->filter([], "top") ?>
			<?php //echo $content_top; ?>
			<?php echo $content; ?>
			<?php //echo $content_bottom; ?>
		</div>
	</div>
	<?php echo $footer; ?>
</div>
<?php view('tpl::_widget/site/js') ?>
</body>
</html>

