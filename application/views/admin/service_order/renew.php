<script type="text/javascript">
(function($)
{
	$(document).ready(function()
	{
		var main = $('#form');
		$('input.btn-primary').click(function(){
			$(this).val('Hệ thống đang xử lý...');
		});
		$('.date_picker').datepicker({
			  format: 'dd-mm-yyyy',
			  autoclose: 1,
			 })/*.on('changeDate', function(ev){
			  $(this).datepicker('hide');
			 })*/
		
		// Form handle
		$('#form, .form_action').each(function()
		{
			var $this = $(this);
			$this.nstUI('formAction', {
				field_load: $this.attr('_field_load'),
				event_error: function(data)
				{
					// Reset captcha
					if (data['security_code'])
					{
						var captcha = $this.find('img[_captcha]').attr('id');
						if (captcha)
						{
							change_captcha(captcha);
						}
					}
				},
			});
		});
	});
})(jQuery);

</script>
<div class="portlet" style="width:800px;min-height:300px">
    <div class="portlet-heading bg-primary">
    	<div class="portlet-title">
    		<h4><i class="fa fa-list-ul"></i> 
    		<?php echo lang('renew')?>
    		</h4>
    	</div>
    	<div class="portlet-widgets">
    		<a href="#_b9689de8210fa98325be16500f99a92b" data-parent="#accordion" data-toggle="collapse"><i class="fa fa-chevron-down"></i></a>
    		<span class="divider"></span>
    
    	</div>
    	<div class="clearfix"></div>
    </div>
    <div id="_b9689de8210fa98325be16500f99a92b" class="panel-collapse collapse in">
        <div class="portlet-body">
            
        	<form class="form form-horizontal" id="form" method="post" action="<?php echo $action; ?>">
                    <?php    
                    echo macro('mr::form')->row( array(
                        'param' 	=> 'expire_from','type' => 'date',
                        'value' 	=> get_date($info->expire_from),
                        'req'       => 1
                    ));
                    echo macro('mr::form')->row( array(
                        'param' 	=> 'expire_to','type' => 'date',
                        'value' 	=> get_date($info->expire_to),
                        'req'       => 1
                    ));
                    ?>
        			<div class="form-actions">
        				<div class="form-group formSubmit">
        				    <label class="col-sm-3 control-label " for="_db2b1f51d95f1db6e523fc38e8835239">
        				    </label>
        				    
        					<div class="col-sm-9">
        						<input type="submit" value="<?php echo lang('button_update'); ?>" class="btn btn-primary" />
        					</div>
        				</div>
        			</div>
        			<div class="clearfix"></div>
        	</form>
        </div>
    </div>
</div>