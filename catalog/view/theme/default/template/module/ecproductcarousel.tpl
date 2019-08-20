<?php
    $cols = isset($cols)?$cols:3;
    $span = 12/$cols; 
    $active = 'latest';
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
            <div class="list_carousel">
                <div class="carousel-controls">
                    <a id="ecnext<?php echo $module;?>" class="next" href="#">&gt;</a>
                    <a id="ecprev<?php echo $module;?>" class="prev" href="#">&lt;</a>
                    <div id="ecpager<?php echo $module;?>" class="pager"></div>
                </div>
                 <div class="clearfix"></div>
                <ul id="ecproductcarousel<?php echo $module;?>">
                    
                    <?php foreach ($pages as  $k => $tproducts ) {   ?>
                    <li class="item<?php if($k==0) {?>active<?php } ?>">
                      <?php if($tproducts){ ?>
                           <?php foreach( $tproducts as $i => $product ) {  $i=$i+1;?>
                                <?php if( $i%$cols == 1 || $cols == 1) { ?>
                                  <div class="row-fluid box-product">
                                <?php } ?>
                                      <div class="span<?php echo $span;?> product-block"><div class="product-inner">
                                        <?php if ($product['thumb']) { ?>
                                        <div class="image">
                                        <?php if( $product['special'] ) {   ?>
                                                <?php if($show_sale_label) { ?>
                                                <div class="product-label-special label"><?php echo $this->language->get( 'text_sale' ); ?></div>
                                                <?php } ?>
                                                <?php if($show_discount){ ?>
                                                <div class="product-label-discount label"><?php echo $product['discount']; ?><span>%</span></div>
                                                <?php } ?>
                                                
                                            <?php } ?>
                                        <?php if($view_number_bought){ ?>
                                                <div class="product-label-bought label"><span><?php echo (int)$product['bought']; ?>&nbsp;<?php echo $this->language->get( 'text_bought' ); ?></span></div>
                                                <?php } ?>
                                                <?php if($show_quickview){ ?>
                                                <div class="mask">
                                                    <a class="quickview" href="<?php echo $base."index.php?route=ecproductcarousel/product&product_id=".$product['product_id'];?>"><?php echo $this->language->get("text_quickview"); ?></a>
                                                </div>
                                        <?php } ?>
                                        <?php if($lazy_load_image) { ?>
                                        <a href="<?php echo $product['href']; ?>"><img class="lazy" style="display:block;width:<?php echo $image_width;?>px;height:<?php echo $image_height;?>px" data-src="<?php echo $product['thumb']; ?>" src="" alt="<?php echo $product['name']; ?>" /></a>
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
                                        <div class="cart"><input type="button" value="<?php echo $this->language->get("button_cart"); ?>" onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button" /></div>
                                        <?php }else{ ?>
                                         <a href="<?php echo $product['href']; ?>" class="button"><?php echo $this->language->get("text_view_product");?></a></div>
                                        <?php } ?>
                                        <?php if($show_wishlist){ ?>
                                        <div class="wishlist"><a class="icon-heart" onclick="addToWishList('<?php echo $product['product_id']; ?>');"  data-placement="top" data-toggle="tooltip" data-original-title="<?php echo $this->language->get("button_wishlist"); ?>"><span><?php echo $this->language->get("button_wishlist"); ?></span></a></div>
                                        <?php } ?>
                                        <?php if($show_compare){ ?>
                                        <div class="compare"><a class="icon-retweet" onclick="addToCompare('<?php echo $product['product_id']; ?>');"  data-placement="top" data-toggle="tooltip" data-original-title="<?php echo $this->language->get("button_compare"); ?>"><span><?php echo $this->language->get("button_compare"); ?></span></a></div>
                                        <?php } ?>
                                      </div></div>
                              
                              <?php if( $i%$cols == 0 || $i==count($tproducts) ) { ?>
                                 </div>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </li>
                    <?php } ?>
                </ul>
               

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    <?php if( $enable_async ) { ?>
        if(typeof(eccarousels) == "undefined") {
            var eccarousels = new Array();
            var ecindex = 0;
        }
         <?php if( !defined("_LOADED_EC_CAROUSEL") ) { ?>
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
    <?php } ?>
    
    <?php if( $enable_async ) { ?>

    eccarousels.push( function(){

    <?php } else { ?>
      function  loadProductCarousel<?php echo $module;?>(){
    <?php } ?>
        //  Scrolled by user interaction
                <?php if($lazy_load_image) { ?>
                $("#ecproductcarousel<?php echo $module;?> img.lazy").lazy({
                                    enableThrottle: true,
                                    throttle: 250,
                                    effect: "fadeIn",
                                    effectTime: 1500,
                                    bind: "event"
                                    });
                <?php } ?>
                $('#ecproductcarousel<?php echo $module;?>').carouFredSel({
                    auto: <?php echo $carousel_auto == 1?'true':'false';?>,
                    prev: '#ecprev<?php echo $module;?>',
                    next: '#ecnext<?php echo $module;?>',
                    pagination: "#ecpager<?php echo $module;?>",
                    mousewheel: <?php echo $carousel_mousewhell == 1?'true':'false';?>,
                    responsive: <?php echo $carousel_responsive == 1?'true':'false';?>,
                    width: '100%',
                    scroll: 1,
                    items: {
                        visible: {
                            min: 1,
                            max: 1
                        }
                    },
                    swipe: {
                        onMouse: true,
                        onTouch: true
                    }
                });
            <?php if($show_quickview) { ?>
                $('#ecproductcarousel<?php echo $module;?> .quickview').colorbox({
                    width: "50%", 
                    height: "550px",
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