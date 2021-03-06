<?php if (count($list)): ?>

    <?php $_rd = random_string('unique'); ?>

    <script type="text/javascript">
        (function($)
        {
            $(document).ready(function()
            {

                var $main = $('.file_list_<?php echo $_rd; ?>');
                var sort = <?php echo 1//(int) $sort; ?>;
                $main.find('.do_action').nstUI('doAction',{
                    event_complete: function(data, settings)
                    {
                        load_ajax($main.parents('#file_list'));
                    }
                });
                // Hide it
                $main.find('.hideit').click(function()
                {
                    $(this).fadeOut();
                });
                // Lightbox
                $main.find('.lightbox').nstUI({
                    method:	'lightbox'
                });


                $('.product-images .owl-carousel').owlCarousel({
                    loop:true,
                    margin:0,
                    responsiveClass:true,
                    items: 1,
                    autoplay:false,
                    autoplayTimeout:5000,
                    autoplayHoverPause:true,
                    nav:true,
                    dots:true,
                    dotsData:true,
                    navText: ["",""],
                    smartSpeed:700,
                })
                /*// Hide it
                $('.product-images').on('click', '.item-video-icon', function () {
                    var $parent= $(this).closest('.item');
                    $(this).hide();
                    $parent.find('.file_image_item').hide();

                    $('<iframe>', {
                        src: '//www.youtube.com/embed/'+$(this).data('youtube')+'?rel=0&autoplay=1',
                        frameborder: 0,
                        scrolling: 'no'
                    }).appendTo( $parent.find('.item-video-player'));
                });*/
            });
        })(jQuery);
    </script>
    <style type="text/css">

        .file_image_item .file_image_actions {
            z-index: 10;
            position: absolute;
            top: 4px;
            right: 4px;
            padding: 5px;
            /*display: none;*/
            background: #333;
            border-radius: 2px;
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
        }

        .file_image_item .file_image_actions a {
            display: inline;
            margin-left: 5px;
        }

        .file_image_item .file_image_actions a:first-child {
            margin-left: 0;
        }

        .file_image_item:hover .file_image_actions {
            display: block;
        }

    </style>
    <div class="file_list_<?php echo $_rd; ?>">
            <div class="product-images">
                <div class="owl-carousel">
                    <?php foreach ($list as $row):
                        $type ='image';
                        $youtube_id='';
                        if($row->type =='youtube'){
                            $type = 'video';
                            $youtube_id=$row->data;
                        }
                        ?>
                        <div class="item "data-dot="<img src='<?php echo $row->_url_thumb; ?>'>">
                            <div class="file_image_item" data-item="<?php echo $row->id; ?>" >
                                <div class="file_image_img">
                                    <a href="<?php echo $row->_url; ?>?lightbox&rel=<?php echo $_rd; ?>"
                                       class="lightbox">
                                        <img class="img-slide" src="<?php echo $row->_url; ?>"/>
                                    </a>
                                </div>

                                <div class="file_image_actions">
                                    <a data-url="<?php echo $row->_url_del; ?>" data-action="confirm" class="do_action"
                                      title="<?php echo lang('delete'); ?>"
                                       data-notice="<?php echo lang('notice_confirm_delete'); ?>:<br><strong><?php echo $row->orig_name; ?></strong>"
                                        >
                                        <img src="<?php echo public_url('admin') ?>/images/icons/color/delete.png"/>
                                    </a>
                                </div>
                            </div>
                            <?php if($youtube_id): ?>
                                <div class="item-video" >
                                    <div class="item-video-icon"  <?php echo $youtube_id?' data-youtube="'.$youtube_id.'"':'' ?> ></div>
                                    <div class="item-video-player"></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
    </div>

<?php endif; ?>
