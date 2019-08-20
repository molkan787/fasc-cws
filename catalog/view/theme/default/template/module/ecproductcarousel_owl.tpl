<?php
    $cols = isset($cols)?$cols:3;
    $span = 12/$cols;
    $id = rand(1,9);
    $itemsperpage = isset($itemsperpage)?$itemsperpage:1;
    $pages = array_chunk( $products, $itemsperpage);
    if(!empty($pages)){
        if($carousel_mousewhell) {
            $carousel_mousewhell = (count($pages) > 1)?1:0;
        }
?>
<div class="<?php echo isset($prefix)?$prefix:"";?> box ecproductcarousel" style="width:100%">
    <div class="box-heading"><span><?php echo isset($module_title)?$module_title:""; ?></span><?php if($category_link){ ?>&nbsp;<span><a href="<?php echo $category_link; ?>"><?php echo $category_title; ?></a></span><?php } ?></div>
    <div class="box-content">
        <div class="box-carousel slide">
                <?php if( isset($module_description)) { ?>
                <div class="box-description"><?php echo $module_description;?></div>
                <?php } ?>
                <div id="ecproductcarousel<?php echo $module;?>" class="owl-carousel"> 
                    <?php foreach ($products as  $k => $product ) {   ?>
                    <div class="item">
                          <div class="product-block">
                            <div class="product-inner">
                                <?php if ($product['thumb']) { ?>
                                <div class="image">
                                <?php if( $product['special'] ) {   ?>
                                        <?php if($show_sale_label) { ?>
                                        <div class="product-label-special label"><?php echo $text_sale; ?></div>
                                        <?php } ?>
                                        <?php if($show_discount){ ?>
                                        <div class="product-label-discount label"><?php echo $product['discount']; ?><span>%</span></div>
                                        <?php } ?>
                                    <?php } ?>
                                <?php if($view_number_bought){ ?>
                                    <div class="product-label-bought label"><span><?php echo (int)$product['bought']; ?>&nbsp;<?php echo $text_bought; ?></span></div>
                                    <?php } ?>
                                    <?php if($show_quickview){ ?>
                                    <div class="mask">
                                        <a class="quickview" href="<?php echo $base."index.php?route=ecproductcarousel/product&product_id=".$product['product_id'];?>"><?php echo $text_quickview; ?></a>
                                    </div>
                                    <?php } ?>
                                <?php if($lazy_load_image) { ?>
                                <a href="<?php echo $product['href']; ?>"><img class="owl-lazy lazyOwl" style="display:block;width:<?php echo $image_width;?>px;height:<?php echo $image_height;?>px" data-src="<?php echo $product['thumb']; ?>" src="" alt="<?php echo $product['name']; ?>" /></a>
                                <?php } else { ?>
                                <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
                                 <?php } ?>
                                 </div>
                                <?php } ?>
                                <?php if($show_product_name) { ?>
                                <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
                                <?php } ?>
                                <?php if($show_product_description) { ?>
                                <div class="description">
                                   <?php echo $product['description']; ?>
                                </div>
                                <?php } ?>
                                <?php if ($show_price && $product['price']) { ?>
                                <div class="price">
                                  <?php if (!$product['special']) { ?>
                                  <?php echo $product['price']; ?>
                                  <?php } else { ?>
                                  <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
                                  <?php } ?>
                                </div>
                                <?php } ?>
                                <?php if ($product['rating']) { ?>
                                <div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
                                <?php } ?>
                                <?php if($show_addtocart){ ?>
                                <div class="cart"><input type="button" value="<?php echo $button_cart; ?>" onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button" /></div>
                                <?php }else{ ?>
                                 <a href="<?php echo $product['href']; ?>" class="button"><?php echo $text_view_product;?></a>
                                <?php } ?>
                                <?php if($show_wishlist){ ?>
                                <div class="wishlist"><a class="icon-heart" onclick="addToWishList('<?php echo $product['product_id']; ?>');"  data-placement="top" data-toggle="tooltip" data-original-title="<?php echo $button_wishlist; ?>"><span><?php echo $button_wishlist; ?></span></a></div>
                                <?php } ?>
                                <?php if($show_compare){ ?>
                                <div class="compare"><a class="icon-retweet" onclick="addToCompare('<?php echo $product['product_id']; ?>');"  data-placement="top" data-toggle="tooltip" data-original-title="<?php echo $button_compare; ?>"><span><?php echo $button_compare; ?></span></a></div>
                                <?php } ?>
                            </div>
                        </div>      
                    </div>
                    <?php } ?>
                </div>

            </div>
        </div>
</div>
<script type="text/javascript">
    <?php if( $enable_async && !defined("_LOADED_EC_CAROUSEL") ) { ?>
    var eccarousels = new Array();
    var ecindex = 0;
    function runECCarousels(){
        if(eccarousels.length){
            for(i=0; i< eccarousels.length; i++){
                if($.isFunction(eccarousels[i])) {
                    eccarousels[i]();
                }
                
            }
        }
    }
    <?php } ?>
    <?php if( $enable_async ) { ?>

    eccarousels.push( function(){

    <?php } else { ?>
      function  loadProductCarousel<?php echo $module;?>(){
    <?php } ?>
        //  Scrolled by user interaction
            var owl = $('#ecproductcarousel<?php echo $module;?>');
            owl.owlCarousel({
                autoplay: <?php echo $carousel_auto == 1?'true':'false';?>,
                autoplayTimeout: <?php echo $duration?(int)$duration:'5000';?>,
                autoplayHoverPause: true,
                <?php if($lazy_load_image) { ?>
                lazyLoad:true,    
                <?php } ?>
                navigationText: ["<",">"],
                items: <?php echo $large_items?(int)$large_items:4; ?>,//4 items above 1000px browser width
                slideBy: <?php echo (isset($slide_by) && $slide_by)?(int)$slide_by:1; ?>,
                loop: <?php echo $loop == 1?'true':'false'; ?>,
                margin: <?php echo $margin_item?(int)$margin_item:'0'; ?>,
                navigation: <?php echo $show_nav == 1?'true':'false'; ?>,
                rtl: <?php echo $rtl == 1?'true':'false'; ?>,
                mouseDrag: <?php echo $mouse_drag == 1?'true':'false'; ?>,
                touchDrag: <?php echo $touch_drag == 1?'true':'false'; ?>,
                pagination: true,
                autoWidth: false,
                responsive: <?php echo $carousel_responsive == 1?'true':'false'; ?>,
                itemsDesktop : [1000,<?php echo $default_items?(int)$default_items:4; ?>], //5 items between 1000px and 901px
                itemsDesktopSmall : [900,<?php echo $portrait_items?(int)$portrait_items:3; ?>], // betweem 900px and 601px
                itemsTablet: [600, <?php echo $tablet_items?(int)$tablet_items:2; ?>], //2 items between 600 and 0
                itemsMobile : [460, <?php echo $mobile_items?(int)$mobile_items:1; ?>] // itemsMobile disabled - inherit from itemsTablet option
            });
            <?php if($carousel_mousewhell) { ?>
            owl.on('mousewheel', '.owl-stage', function (e) {
                if (e.deltaY>0) {
                    owl.trigger('next.owl');
                } else {
                    owl.trigger('prev.owl');
                }
                e.preventDefault();
            });
            <?php } ?>
            <?php if($show_quickview) { ?>
                $('#ecproductcarousel<?php echo $module;?> .quickview').colorbox({
                    width: "80%", 
                    height: "60%",
                    overlayClose: true,
                    opacity: 0.5,
                    title: "<?php echo $this->language->get("text_quickview_product"); ?>",
                    iframe: true
                });
            <?php } ?>
    <?php if( $enable_async ) { ?>       
    });
    ecindex++;
    <?php } else { ?>
    }
    <?php } ?>
</script>
<script type="text/javascript">
<?php if($enable_async) { ?>
    <?php if( !defined("_LOADED_EC_CAROUSEL") ) { ?>
    <?php define("_LOADED_EC_CAROUSEL", 1); ?>
//this function will work cross-browser for loading scripts asynchronously
    $(document).ready(function() {
        $.getScript("<?php echo isset($script)?$script:""; ?>", runECCarousels)
    });
    
    <?php } ?>
<?php }else{ ?>
    $(document).ready(function() {
        loadProductCarousel<?php echo $module;?>();
    });
<?php } ?>
</script>
<?php } ?>