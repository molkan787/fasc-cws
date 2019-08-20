<?php echo $header;?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="$('#action').val('save_stay');$('#form').submit();" class="button"><?php echo $button_save_stay; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <input type="hidden" name="action" id="action" value=""/>

          <div class="vtabs">
            <?php $module_row = 1; ?>
            <?php foreach ($modules as $module) { ?>
            <a href="#tab-module-<?php echo $module_row; ?>" id="module-<?php echo $module_row; ?>"><?php echo $tab_block . ' ' . $module_row; ?>&nbsp;<img src="view/image/delete.png" alt="" onclick="$('.vtabs a:first').trigger('click'); $('#module-<?php echo $module_row; ?>').remove(); $('#tab-module-<?php echo $module_row; ?>').remove(); return false;" /></a>
            <?php $module_row++; ?>
            <?php } ?>
            <span id="module-add"><?php echo $button_add_new_block; ?>&nbsp;<img src="view/image/add.png" alt="" onclick="addModule();" /></span> 
          </div>
          <?php $module_row = 1; ?>
          <?php foreach ($modules as $module) { ?>
          <div id="tab-module-<?php echo $module_row; ?>" class="vtabs-content">
            <div id="language-<?php echo $module_row; ?>" class="htabs">
                     <?php foreach ($languages as $language) { ?>
                        <a href="#tab-language-<?php echo $language['language_id']; ?>"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
                        <?php } ?>
            </div>

             <?php foreach ($languages as $language) { ?>
                      <div id="tab-language-<?php echo $language['language_id']; ?>">
                        <table class="form">
                          <tr>
                            <td><?php echo $this->language->get("entry_module_title"); ?></td>   
                            <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][title][<?php echo $language['language_id']; ?>]" value="<?php echo isset($module['title'][$language['language_id']])?$module['title'][$language['language_id']]:''; ?>" size="40" /></td> 
                          </tr>
                          
                          <tr>
                            <td><?php echo $this->language->get("entry_module_message"); ?></td>  
                            <td><textarea name="ecproductcarousel_module[<?php echo $module_row; ?>][description][<?php echo $language['language_id']; ?>]" id="description-<?php echo $module_row; ?>-<?php echo $language['language_id']; ?>"><?php echo isset($module['description'][$language['language_id']]) ? $module['description'][$language['language_id']] : ''; ?></textarea></td> 
                          </tr>
                          
                        </table>
                      </div>
            <?php } ?>
            <table class="form">
              <tr>
                <td><?php echo $entry_layout; ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][layout_id]">
                    <?php if ($module['layout_id'] == 0) { ?>
                    <option value="0" selected="selected"><?php echo $text_alllayout; ?></option>
                    <?php } else { ?>
                    <option value="0"><?php echo $text_alllayout; ?></option>
                    <?php } ?>
                    <?php foreach ($layouts as $layout) { ?>
                    <?php if ($layout['layout_id'] == $module['layout_id']) { ?>
                    <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
        <tr>
          <td><?php echo $entry_store; ?></td>
                <td><div class="scrollbox">
                    <?php $class = 'even'; ?>
                    <div class="<?php echo $class; ?>">
                      <?php if (isset($module['store_id']) && in_array(0, $module['store_id'])) { ?>
                      <input type="checkbox" name="ecproductcarousel_module[<?php echo $module_row; ?>][store_id][]" value="0" checked="checked" />
                      <?php } else { ?>
                      <input type="checkbox" name="ecproductcarousel_module[<?php echo $module_row; ?>][store_id][]" value="0" />
                      <?php } ?>
            <?php echo $text_default; ?>
                    </div>
                    <?php foreach ($stores as $store) { ?>
                    <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                    <div class="<?php echo $class; ?>">
                      <?php if (isset($module['store_id']) && in_array($store['store_id'], $module['store_id'])) { ?>
                      <input type="checkbox" name="ecproductcarousel_module[<?php echo $module_row; ?>][store_id][]" value="<?php echo $store['store_id']; ?>" checked="checked" />
                      <?php echo $store['name']; ?>
                      <?php } else { ?>
                      <input type="checkbox" name="ecproductcarousel_module[<?php echo $module_row; ?>][store_id][]" value="<?php echo $store['store_id']; ?>" />
                      <?php echo $store['name']; ?>
                      <?php } ?>
                    </div>
                    <?php } ?>
                  </div></td>
              </tr>
             <tr>
                        <td><?php echo $entry_position; ?></td>
                        <td>
                          <?php 
                          $custom_position = (isset($module['custom_position']) && !empty($module['custom_position']))?$module['custom_position']:'';
                          $tmp_positions = $positions;
                          if(!empty($custom_position)){
                            $tmp_positions[] = $custom_position;
                          }
                          
                          ?>
                          <select name="ecproductcarousel_module[<?php echo $module_row; ?>][position]">
                                     <?php foreach( $tmp_positions as $pos ) { ?>
                                              <?php if ($module['position'] == $pos) { ?>
                                              <option value="<?php echo $pos;?>" selected="selected"><?php echo $this->language->get('text_'.$pos); ?></option>
                                              <?php } else { ?>
                                              <option value="<?php echo $pos;?>"><?php echo $this->language->get('text_'.$pos); ?></option>
                                              <?php } ?>
                                              <?php } ?> 
                                            </select></td>
                </tr>
               <tr>
                        <td><?php echo $entry_custom_position; ?></td>
                        <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][custom_position]" value="<?php echo $custom_position; ?>" size="30" /></td>
              </tr>
              <tr>
                <td><?php echo $entry_status; ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][status]">
                    <?php if ($module['status']) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?php echo $entry_sort_order; ?></td>
                <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $module['sort_order']; ?>" size="3" /></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_product_image_width_height"); ?></td>
                <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][image_width]" value="<?php echo isset($module['image_width'])?$module['image_width']:200; ?>" size="10" /> - <input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][image_height]" value="<?php echo isset($module['image_height'])?$module['image_height']:200; ?>" size="10" /></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_source_from"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][source_from]" onchange="showGroupFields(<?php echo $module_row; ?>,$(this).val())">
                    <?php foreach($source_from as $key=>$val) { ?>
                      <?php if(isset($module['source_from']) && $key == $module['source_from']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr id="featured<?php echo $module_row;?>" class="group_fields<?php echo $module_row;?>" style="display:none">
                <td colspan="2">
                  <table class="form">
                      <tr>
                        <td><?php echo $this->language->get("entry_featured_product"); ?></td>
                        <td><input type="text" name="product<?php echo $module_row;?>" value="" placeholder="<?php echo $this->language->get("text_input_product_name");?>" size="50"/></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td><div id="featured-product<?php echo $module_row;?>" class="scrollbox">
                            <?php $class = 'odd'; ?>
                            <?php if(isset($module['products'])) { ?>
                              <?php foreach ($module['products'] as $product) { ?>
                              <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                              <div id="featured-product<?php echo $module_row;?>-<?php echo $product['product_id']; ?>" class="<?php echo $class; ?>"><?php echo $product['name']; ?> <img src="view/image/delete.png" alt="" />
                                <input type="hidden" value="<?php echo $product['product_id']; ?>" />
                              </div>
                              <?php } ?>
                            <?php } ?>
                          </div>
                          <input type="hidden" name="ecproductcarousel_module[<?php echo $module_row;?>][featured_product]" id="featured_product<?php echo $module_row;?>" value="<?php echo isset($module['featured_product'])?$module['featured_product']:''; ?>" /></td>
                      </tr>
                    </table>
                </td>
              </tr>
              <tr class="group_fields<?php echo $module_row;?>">
                <td><?php echo $this->language->get("entry_category_id"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][category_id]" size="10">
                      <option value=""><?php echo $this->language->get("text_choose_a_category");?></option>
                      <?php foreach ($categories as $category) { ?>
                      <?php if(isset($module['category_id']) && $category['category_id'] == $module['category_id']){ ?>
                        <option value="<?php echo $category['category_id']; ?>" selected="selected"><?php echo $category['name']; ?></option>
                      <?php }else{ ?>
                        <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                        <?php } ?>
                        <?php if (isset($category['children']) && $category['children']) { ?>
                          <?php foreach ($category['children'] as $child) { ?>
                          <?php if(isset($module['category_id']) && $category['category_id'] == isset($module['category_id'])){ ?>
                            <option value="<?php echo $child['category_id']; ?>" selected="selected"> - <?php echo $child['name']; ?></option>
                          <?php }else{ ?>
                            <option value="<?php echo $child['category_id']; ?>"> - <?php echo $child['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                        <?php } ?>
                     
                      <?php } ?>
                    </select></td>
              </tr>
              <tr class="group_fields<?php echo $module_row;?>">
                <td><?php echo $this->language->get("entry_filter_sub_category"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][filter_sub_category]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['filter_sub_category']) && $key == $module['filter_sub_category']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr class="group_fields<?php echo $module_row;?>">
                <td><?php echo $this->language->get("entry_order_status_id"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][order_status_id][]" size="10" multiple="multiple">
                  <?php
                    if(!empty($order_status)){
                      $module['order_status_id'] = isset($module['order_status_id'])?$module['order_status_id']:array(5);
                      foreach($order_status as $key=>$val){
                        if(isset($module['order_status_id']) && in_array($val['order_status_id'], $module['order_status_id'])){
                        ?>
                        <option value="<?php echo $val['order_status_id']; ?>" selected="selected"><?php echo $val['name'];?></option>
                        <?php
                        }else{
                           ?>
                        <option value="<?php echo $val['order_status_id']; ?>"><?php echo $val['name'];?></option>
                        <?php
                        }
                      }
                    }
                  ?>
                </select></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_limit"); ?></td>
                <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][limit]" value="<?php echo isset($module['limit'])?$module['limit']:9; ?>" size="3" /></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo $this->language->get("entry_carousel_setting");?></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_carousel_width"); ?></td>
                <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][carousel_width]" value="<?php echo isset($module['carousel_width'])?$module['carousel_width']:'100%'; ?>" size="10" /></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_carousel_height"); ?></td>
                <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][carousel_height]" value="<?php echo isset($module['carousel_height'])?$module['carousel_height']:'auto'; ?>" size="10" /></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_mode"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][carousel_type]" id="group_selector_<?php echo $module_row; ?>" onchange="change_mode(<?php echo $module_row; ?>)">
                   <?php
                     foreach($modes as $key=>$val) {
                       if(isset($module['carousel_type']) && $key == $module['carousel_type']) {
                        ?>
                        <option value="<?php echo $key;?>" selected="selected"><?php echo $val ?></option>
                        <?php
                       } else {
                        ?>
                        <option value="<?php echo $key;?>"><?php echo $val ?></option>
                        <?php
                       }
                     }
                   ?>
                  </select>
                  <script type="text/javascript">
                  $(document).ready(function(){
                     change_mode(<?php echo $module_row; ?>);
                  })
                  
                  </script>
                </td>
               </tr>
               <tr id="group_field_mode_default_<?php echo $module_row; ?>" class="group_field_<?php echo $module_row; ?>" style="display:none">
                  <td colspan="2">
                    <table class="form">
                      <tr>
                        <td><?php echo $this->language->get("entry_item_width_height"); ?></td>
                        <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][carousel_item_width]" value="<?php echo isset($module['carousel_item_width'])?$module['carousel_item_width']:400; ?>" size="3" /> - <input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][carousel_item_height]" value="<?php echo isset($module['carousel_item_height'])?$module['carousel_item_height']:400; ?>" size="3" /></td>
                      </tr>
                     
                      <tr>
                        <td><?php echo $this->language->get("entry_itemsperpage_cols"); ?></td>
                        <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][itemsperpage]" value="<?php echo isset($module['itemsperpage'])?$module['itemsperpage']:6; ?>" size="3" /> - <input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][cols]" value="<?php echo isset($module['cols'])?$module['cols']:3; ?>" size="3" /></td>
                      </tr>
                      <tr>
                      <td><?php echo $this->language->get("entry_scroll_effect"); ?></td>
                      <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][scroll_effect]">
                          <?php foreach($effects as $key) { ?>
                            <?php if(isset($module['scroll_effect']) && $key == $module['scroll_effect']){ ?>
                            <option value="<?php echo $key; ?>" selected="selected"><?php echo $key; ?></option>
                            <?php }else{ ?>
                            <option value="<?php echo $key; ?>"><?php echo $key; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select></td>
                    </tr>
                    </table>
                  </td>
                </tr>
                <tr id="group_field_mode_owl_<?php echo $module_row; ?>" class="group_field_<?php echo $module_row; ?>" style="display:none">
                  <td colspan="2">
                    <table class="form">
                        <tr>
                          <td><?php echo $this->language->get("entry_enable_rtl"); ?></td>
                          <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][rtl]">
                              <?php foreach($yesno as $key=>$val) { ?>
                                <?php if(isset($module['rtl']) && $key == $module['rtl']){ ?>
                                <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                                <?php }else{ ?>
                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select></td>
                        </tr>
                        <tr>
                          <td><?php echo $this->language->get("entry_enable_navigation"); ?></td>
                          <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][show_nav]">
                              <?php foreach($yesno as $key=>$val) { ?>
                                <?php if(isset($module['show_nav']) && $key == $module['show_nav']){ ?>
                                <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                                <?php }else{ ?>
                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select></td>
                        </tr>
                         <tr>
                          <td><?php echo $this->language->get("entry_loop_carousel"); ?></td>
                          <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][loop]">
                              <?php foreach($yesno as $key=>$val) { ?>
                                <?php if(isset($module['loop']) && $key == $module['loop']){ ?>
                                <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                                <?php }else{ ?>
                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select></td>
                        </tr>
                        <tr>
                          <td><?php echo $this->language->get("entry_mouse_drag"); ?></td>
                          <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][mouse_drag]">
                              <?php foreach($yesno as $key=>$val) { ?>
                                <?php if(isset($module['mouse_drag']) && $key == $module['mouse_drag']){ ?>
                                <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                                <?php }else{ ?>
                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select></td>
                        </tr>
                        <tr>
                          <td><?php echo $this->language->get("entry_touch_drag"); ?></td>
                          <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][touch_drag]">
                              <?php foreach($yesno as $key=>$val) { ?>
                                <?php if(isset($module['touch_drag']) && $key == $module['touch_drag']){ ?>
                                <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                                <?php }else{ ?>
                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select></td>
                        </tr>
                        <tr>
                          <td><?php echo $this->language->get("entry_carousel_slide_by"); ?></td>
                          <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][slide_by]" value="<?php echo isset($module['slide_by'])?$module['slide_by']:'1'; ?>" size="10" /></td>
                        </tr>
                        <tr>
                          <td><?php echo $this->language->get("entry_carousel_margin"); ?></td>
                          <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][margin_item]" value="<?php echo isset($module['margin_item'])?$module['margin_item']:'0'; ?>" size="10" /></td>
                        </tr>
                         <tr>
                            <td><?php echo $this->language->get("entry_default_display_items"); ?></td>
                            <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][default_items]" value="<?php echo isset($module['default_items'])?$module['default_items']:4; ?>" size="10" /></td>
                          </tr>
                          <tr>
                            <td><?php echo $this->language->get("entry_mobile_display_items"); ?></td>
                            <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][mobile_items]" value="<?php echo isset($module['mobile_items'])?$module['mobile_items']:1; ?>" size="10" /></td>
                          </tr>
                          <tr>
                            <td><?php echo $this->language->get("entry_tablet_display_items"); ?></td>
                            <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][tablet_items]" value="<?php echo isset($module['tablet_items'])?$module['tablet_items']:2; ?>" size="10" /></td>
                          </tr>
                          <tr>
                            <td><?php echo $this->language->get("entry_portrait_display_items"); ?></td>
                            <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][portrait_items]" value="<?php echo isset($module['portrait_items'])?$module['portrait_items']:3; ?>" size="10" /></td>
                          </tr>
                          <tr>
                            <td><?php echo $this->language->get("entry_large_display_items"); ?></td>
                            <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][large_items]" value="<?php echo isset($module['large_items'])?$module['large_items']:5; ?>" size="10" /></td>
                          </tr>
                    </table>
                  </td>
                </tr>
                
              <tr>
                <td><?php echo $this->language->get("entry_carousel_auto"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][carousel_auto]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['carousel_auto']) && $key == $module['carousel_auto']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
              
               <tr>
                <td><?php echo $this->language->get("entry_duration"); ?></td>
                <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][duration]" value="<?php echo isset($module['duration'])?$module['duration']:1000; ?>" size="10" /></td>
              </tr>

              <tr>
                <td><?php echo $this->language->get("entry_carousel_responsive"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][carousel_responsive]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['carousel_responsive']) && $key == $module['carousel_responsive']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_carousel_mousewhell"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][carousel_mousewhell]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['carousel_mousewhell']) && $key == $module['carousel_mousewhell']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
             
              <tr>
                <td colspan="2">
                   <?php echo $this->language->get("entry_display_setting"); ?>
                </td>
              </tr>
               <tr>
                <td><?php echo $this->language->get("entry_limit_chars"); ?></td>
                <td><input type="text" name="ecproductcarousel_module[<?php echo $module_row; ?>][limit_chars]" value="<?php echo isset($module['limit'])?$module['limit_chars']:58; ?>" size="3" /></td>
              </tr>
               <tr>
                <td><?php echo $this->language->get("entry_strip_tags"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][strip_tags]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['strip_tags']) && $key == $module['strip_tags']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_show_product_name"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][show_product_name]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['show_product_name']) && $key == $module['show_product_name']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_show_addtocart"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][show_addtocart]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['show_addtocart']) && $key == $module['show_addtocart']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
               <tr>
                <td><?php echo $this->language->get("entry_show_price"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][show_price]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['show_price']) && $key == $module['show_price']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_show_sale_label"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][show_sale_label]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['show_sale_label']) && $key == $module['show_sale_label']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_show_category_name"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][show_category_name]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['show_category_name']) && $key == $module['show_category_name']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
               <tr>
                <td><?php echo $this->language->get("entry_show_discount"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][show_discount]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['show_discount']) && $key == $module['show_discount']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_show_wishlist"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][show_wishlist]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['show_wishlist']) && $key == $module['show_wishlist']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
               <tr>
                <td><?php echo $this->language->get("entry_show_compare"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][show_compare]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['show_compare']) && $key == $module['show_compare']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
               <tr>
                <td><?php echo $this->language->get("entry_show_number_bought"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][view_number_bought]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['view_number_bought']) && $key == $module['view_number_bought']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_enable_async"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][enable_async]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['enable_async']) && $key == $module['enable_async']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_enable_lazyload"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][lazy_load_image]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['lazy_load_image']) && $key == $module['lazy_load_image']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?php echo $this->language->get("entry_show_quickview"); ?></td>
                <td><select name="ecproductcarousel_module[<?php echo $module_row; ?>][show_quickview]">
                    <?php foreach($yesno as $key=>$val) { ?>
                      <?php if(isset($module['show_quickview']) && $key == $module['show_quickview']){ ?>
                      <option value="<?php echo $key; ?>" selected="selected"><?php echo $val; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
            </table>
          </div>
          <?php $module_row++; ?>
          <?php } ?>
      </form>
    </div>
  </div>
</div>
<style style="text/css">
  .hidden{display: none;}
</style>
<script type="text/javascript"><!--

function change_mode(module_index) {
  var select_value = $("#group_selector_"+module_index).val();

  if($("#group_field_mode_"+select_value+"_"+module_index)) {
    $(".group_field_"+module_index).hide();
    $("#group_field_mode_"+select_value+"_"+module_index).show();
  }
}
function showGroupFields(module_id, source_from){

  switch(source_from){
    case "featured":
      $(".group_fields"+module_id).hide();
      $("#featured"+ module_id).removeClass("hidden");
      $("#featured"+module_id).show();
    break;
    case "alsobought":
      $(".group_fields"+module_id).show();
      $("#featured"+ module_id).addClass("hidden");
      $("#featured"+ module_id).hide();
    break;
    case "related":
      $(".group_fields"+module_id).hide();
    break;
    default:
      $(".group_fields"+module_id).show();
      $("#featured"+ module_id).addClass("hidden");
      $("#featured"+ module_id).hide();
    break;

  }
}

 function productAutocomplete(module_id){

  $('input[name=\'product'+module_id+'\']').autocomplete({
    delay: 500,
    source: function(request, response) {
      $.ajax({
        url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
        dataType: 'json',
        success: function(json) {   
          response($.map(json, function(item) {
            return {
              label: item.name,
              value: item.product_id
            }
          }));
        }
      });
    }, 
    select: function(event, ui) {
      $('#featured-product'+module_id + ui.item.value).remove();
      
      $('#featured-product'+module_id).append('<div id="featured-product'+module_id + ui.item.value + '">' + ui.item.label + '<img src="view/image/delete.png" alt="" /><input type="hidden" value="' + ui.item.value + '" /></div>');

      $('#featured-product'+module_id+' div:odd').attr('class', 'odd');
      $('#featured-product'+module_id+' div:even').attr('class', 'even');
      
      data = $.map($('#featured-product'+module_id+' input'), function(element){
        return $(element).attr('value');
      });
              
      $('#featured_product'+module_id).attr('value', data.join());
            
      return false;
    },
    focus: function(event, ui) {
          return false;
      }
  });

  $('#featured-product'+module_id+' div img').live('click', function() {
    $(this).parent().remove();
    
    $('#featured-product'+module_id+' div:odd').attr('class', 'odd');
    $('#featured-product'+module_id+' div:even').attr('class', 'even');

    data = $.map($('#featured-product'+module_id+' input'), function(element){
      return $(element).attr('value');
    });
            
    $('#featured_product'+module_id).attr('value', data.join()); 
  });
}
//--></script> 
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script> 
<script type="text/javascript"><!--
<?php $module_row = 1; ?>
<?php foreach ($modules as $module) { ?>
  <?php foreach ($languages as $language) { ?>
  CKEDITOR.replace('description-<?php echo $module_row; ?>-<?php echo $language['language_id']; ?>', {
    filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
    filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
    filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
    filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
    filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
    filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
  });
  <?php } ?>
  <?php $module_row++; ?>
<?php } ?>
//--></script>
<script type="text/javascript"><!--
  function showGroup(group_name){

  }
//--></script>
<script type="text/javascript"><!--
var module_row = <?php echo $module_row; ?>;
function addModule() {
  html ='<div id="tab-module-'+module_row+'" class="vtabs-content"> ';
  html +='<div id="language-'+module_row+'" class="htabs">';
                     <?php foreach ($languages as $language) { ?>
  html +='                      <a href="#tab-language-<?php echo $language['language_id']; ?>"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>';
                        <?php } ?>
  html +='          </div>';
            
             <?php foreach ($languages as $language) { ?>
  html +='                    <div id="tab-language-<?php echo $language['language_id']; ?>">';
  html +='                      <table class="form">';
  html +='                        <tr>';
  html +='                          <td><?php echo $this->language->get("entry_module_title"); ?></td>   ';
  html +='                          <td><input type="text" name="ecproductcarousel_module['+module_row+'][title][<?php echo $language['language_id']; ?>]" value="" size="40" /></td> ';
  html +='                       </tr>';
                          
  html +='                        <tr>';
  html +='                          <td><?php echo $this->language->get("entry_module_message"); ?></td>  ';
  html +='                          <td><textarea name="ecproductcarousel_module['+module_row+'][description][<?php echo $language['language_id']; ?>]" id="description-'+module_row+'-<?php echo $language['language_id']; ?>"></textarea></td> ';
  html +='                        </tr>';
                          
  html +='                      </table>';
  html +='                    </div>';
            <?php } ?>
  html +='         <table class="form">';
  html +='          <tr>';
  html +='            <td><?php echo $entry_layout; ?></td>';
  html +='            <td><select name="ecproductcarousel_module['+module_row+'][layout_id]">';
  html +='                <option value="0"><?php echo $text_alllayout; ?></option>';
                  <?php foreach ($layouts as $layout) { ?>
  html +='                <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>';
                  <?php } ?>
  html +='              </select></td>';
  html +='          </tr>';
  html +='    <tr>';
  html +='      <td><?php echo $entry_store; ?></td>';
  html +='            <td><div class="scrollbox">';
                  <?php $class = 'even'; ?>
  html +='                <div class="<?php echo $class; ?>">';
  html +='                  <input type="checkbox" name="ecproductcarousel_module['+module_row+'][store_id][]" value="0" checked="checked"/>';
  html +='        <?php echo $text_default; ?>';
  html +='                </div>';
                  <?php foreach ($stores as $store) { ?>
                  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
  html +='                <div class="<?php echo $class; ?>">';
  html +='                  <input type="checkbox" name="ecproductcarousel_module['+module_row+'][store_id][]" value="<?php echo $store['store_id']; ?>" />';
  html +='                  <?php echo $store['name']; ?>';
  html +='                </div>';
                  <?php } ?>
  html +='              </div></td>';
  html +='    </tr>';
  html +='          <tr>';
  html +='            <td><?php echo $entry_position; ?></td>';
  html +='            <td><select name="ecproductcarousel_module['+module_row+'][position]">';
                           <?php foreach( $positions as $pos ) { ?>
  html +='                                  <option value="<?php echo $pos;?>"><?php echo $this->language->get('text_'.$pos); ?></option>';
                                    <?php } ?> 
  html +='                                </select></td>';
  html +='          </tr>';
  html +=' <tr>';
  html +='                      <td><?php echo $entry_custom_position; ?></td>';
  html +='                      <td><input type="text" name="ecslideshow2_module['+module_row+'][custom_position]" value="" size="30" /></td>';
  html +='                    </tr>';
  html +='          <tr>';
  html +='            <td><?php echo $entry_status; ?></td>';
  html +='            <td><select name="ecproductcarousel_module['+module_row+'][status]">';
  html +='                <option value="1"><?php echo $text_enabled; ?></option>';
  html +='                <option value="0"><?php echo $text_disabled; ?></option>';

  html +='              </select></td>';
  html +='          </tr>';
  html +='          <tr>';
  html +='            <td><?php echo $entry_sort_order; ?></td>';
  html +='            <td><input type="text" name="ecproductcarousel_module['+module_row+'][sort_order]" value="" size="3" /></td>';
  html +='          </tr>';
  html +='<tr>';
  html +='              <td><?php echo $this->language->get("entry_product_image_width_height"); ?></td>';
  html +='              <td><input type="text" name="ecproductcarousel_module['+module_row+'][image_width]" value="200" size="10" /> - <input type="text" name="ecproductcarousel_module['+module_row+'][image_height]" value="200" size="10" /></td>';
  html +='            </tr>';
  html +='<tr>';
  html +='              <td><?php echo $this->language->get("entry_source_from"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module['+module_row+'][source_from]" onchange="showGroupFields('+module_row+', $(this).val())">';
                    <?php foreach($source_from as $key=>$val) { ?>

  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
                    <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +=' <tr id="featured' + module_row + '" class="group_fields' + module_row + '" style="display:none">';
  html +='              <td colspan="2">';
  html +='                <table class="form">';
  html +='                    <tr>';
  html +='                      <td><?php echo $this->language->get("entry_featured_product"); ?></td>';
  html +='                      <td><input type="text" name="product' + module_row + '" value="" placeholder="<?php echo $this->language->get("text_input_product_name");?>" size="50"/></td>';
  html +='                    </tr>';
  html +='                    <tr>';
  html +='                      <td>&nbsp;</td>';
  html +='                      <td><div id="featured-product' + module_row + '" class="scrollbox"></div>';
  html +='                        <input type="hidden" name="ecproductcarousel_module[' + module_row + '][featured_product]" id="featured_product' + module_row + '" value="" /></td>';
  html +='                    </tr>';
  html +='                  </table>';
  html +='              </td>';
  html +='            </tr>';
  html +=' <tr class="group_fields' + module_row + '">';
  html +='              <td><?php echo $this->language->get("entry_category_id"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][category_id]" size="10">';
  html +='                    <option value=""><?php echo $this->language->get("text_choose_a_category");?></option>';
                      <?php foreach ($categories as $category) { ?>

  html +='                      <option value="<?php echo $category['category_id']; ?>"><?php echo str_replace("'","\'", $category['name']); ?></option>';
                        <?php if (isset($category['children']) && $category['children']) { ?>
                          <?php foreach ($category['children'] as $child) { ?>

  html +='                          <option value="<?php echo $child['category_id']; ?>"> - <?php echo str_replace("'","\'", $child['name']); ?></option>';
                          <?php } ?>
                         <?php } ?>
                        <?php } ?>

  html +='                  </select></td>';
  html +='            </tr>';
  html +='            <tr class="group_fields' + module_row + '">';
  html +='              <td><?php echo $this->language->get("entry_filter_sub_category"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][filter_sub_category]">';
                    <?php foreach($yesno as $key=>$val) { ?>
  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
                      <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='<tr class="group_fields' + module_row + '">';
  html +='              <td><?php echo $this->language->get("entry_order_status_id"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][order_status_id][]" size="10" multiple="multiple">';
                  <?php
                    if(!empty($order_status)){
                      $order_status_id = 5;
                      foreach($order_status as $key=>$val){
                        if($val['order_status_id'] == $order_status_id){
                        ?>
  html +='                      <option value="<?php echo $val['order_status_id']; ?>" selected="selected"><?php echo $val['name'];?></option>';
                        <?php
                        }else{
                           ?>
  html +='                      <option value="<?php echo $val['order_status_id']; ?>"><?php echo $val['name'];?></option>';
                        <?php
                        }
                      }
                    }
                  ?>
  html +='              </select></td>';
  html +='            </tr>';
  html +='            <tr>';
  html +='              <td><?php echo $this->language->get("entry_limit"); ?></td>';
  html +='              <td><input type="text" name="ecproductcarousel_module[' + module_row + '][limit]" value="9" size="3" /></td>';
  html +='            </tr>';
  html +=' <tr>';
  html +='              <td colspan="2"><?php echo $this->language->get("entry_carousel_setting");?></td>';
  html +='            </tr>';
  html +='<tr>';
  html +='              <td><?php echo $this->language->get("entry_carousel_width"); ?></td>';
  html +='              <td><input type="text" name="ecproductcarousel_module[' + module_row + '][carousel_width]" value="100%" size="10" /></td>';
  html +='            </tr>';
  html +='<tr>';
  html +='              <td><?php echo $this->language->get("entry_carousel_height"); ?></td>';
  html +='              <td><input type="text" name="ecproductcarousel_module[' + module_row + '][carousel_height]" value="auto" size="10" /></td>';
  html +='            </tr>';
  
  html +='<tr>';
  html +='              <td><?php echo $this->language->get("entry_mode"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module['+module_row+'][carousel_type]" id="group_selector_'+ module_row+'" onchange="change_mode('+module_row+')">';
                   <?php
                     foreach($modes as $key=>$val) {
                        ?>
  html +='                      <option value="<?php echo $key;?>"><?php echo $val ?></option>';
                        <?php
                     }
                   ?>
  html +='                </select>';
  html +='              </td>';
  html +='             </tr>';
  html +='             <tr id="group_field_mode_default_'+module_row+'" class="group_field_'+module_row+'" style="display:none">';
  html +='                <td colspan="2">';
  html +='                  <table class="form">';
  html +='                    <tr>';
  html +='                      <td><?php echo $this->language->get("entry_item_width_height"); ?></td>';
  html +='                      <td><input type="text" name="ecproductcarousel_module['+module_row+'][carousel_item_width]" value="400" size="3" /> - <input type="text" name="ecproductcarousel_module['+module_row+'][carousel_item_height]" value="400" size="3" /></td>';
  html +='                    </tr>';
                     
  html +='                    <tr>';
  html +='                      <td><?php echo $this->language->get("entry_itemsperpage_cols"); ?></td>';
  html +='                      <td><input type="text" name="ecproductcarousel_module['+module_row+'][itemsperpage]" value="6" size="3" /> - <input type="text" name="ecproductcarousel_module['+module_row+'][cols]" value="3" size="3" /></td>';
  html +='                    </tr>';
  html +='                    <tr>';
  html +='                    <td><?php echo $this->language->get("entry_scroll_effect"); ?></td>';
  html +='                    <td><select name="ecproductcarousel_module['+module_row+'][scroll_effect]">';
                          <?php foreach($effects as $key) { ?>
  html +='                           <option value="<?php echo $key; ?>"><?php echo $key; ?></option>';
                          <?php } ?>
  html +='                       </select></td>';
  html +='                   </tr>';
  html +='                   </table>';
  html +='                 </td>';
  html +='               </tr>';
  html +='               <tr id="group_field_mode_owl_'+module_row+'" class="group_field_'+ module_row +'" style="display:none">';
  html +='                 <td colspan="2">';
  html +='                   <table class="form">';
  html +='                       <tr>';
  html +='                         <td><?php echo $this->language->get("entry_enable_rtl"); ?></td>';
  html +='                         <td><select name="ecproductcarousel_module['+module_row+'][rtl]">';
                              <?php foreach($yesno as $key=>$val) { ?>
  html +='                              <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
                              <?php } ?>
  html +='                          </select></td>';
  html +='                      </tr>';
  html +='                      <tr>';
  html +='                        <td><?php echo $this->language->get("entry_enable_navigation"); ?></td>';
  html +='                        <td><select name="ecproductcarousel_module['+module_row + '][show_nav]">';
                              <?php foreach($yesno as $key=>$val) { ?>
  html +='                              <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
                              <?php } ?>
  html +='                          </select></td>';
  html +='                      </tr>';
  html +='                       <tr>';
  html +='                        <td><?php echo $this->language->get("entry_loop_carousel"); ?></td>;'
  html +='                        <td><select name="ecproductcarousel_module['+module_row+'][loop]">';
                              <?php foreach($yesno as $key=>$val) { ?>
  html +='                              <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
                              <?php } ?>
  html +='                          </select></td>';
  html +='                      </tr>';
  html +='                      <tr>';
  html +='                        <td><?php echo $this->language->get("entry_mouse_drag"); ?></td>';
  html +='                        <td><select name="ecproductcarousel_module['+module_row+ '][mouse_drag]">';
                              <?php foreach($yesno as $key=>$val) { ?>
  html +='                              <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
                              <?php } ?>
  html +='                          </select></td>';
  html +='                      </tr>';
  html +='                      <tr>';
  html +='                        <td><?php echo $this->language->get("entry_touch_drag"); ?></td>';
  html +='                        <td><select name="ecproductcarousel_module['+module_row + '][touch_drag]">';
                              <?php foreach($yesno as $key=>$val) { ?>
  html +='                              <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
                              <?php } ?>
  html +='                          </select></td>';
  html +='                      </tr>';
  html +='<tr>';
  html +='                        <td><?php echo $this->language->get("entry_carousel_slide_by"); ?></td>';
  html +='                        <td><input type="text" name="ecproductcarousel_module['+module_row + '][slide_by]" value="1" size="10" /></td>';
  html +='                      </tr>';
  html +='<tr>';
  html +='                        <td><?php echo $this->language->get("entry_carousel_margin"); ?></td>';
  html +='                        <td><input type="text" name="ecproductcarousel_module['+module_row + '][margin_item]" value="0" size="10" /></td>';
  html +='                      </tr>';
  html +='                       <tr>';
  html +='                          <td><?php echo $this->language->get("entry_default_display_items"); ?></td>';
  html +='                          <td><input type="text" name="ecproductcarousel_module['+module_row+'][default_items]" value="4" size="10" /></td>';
  html +='                        </tr>';
  html +='                        <tr>';
  html +='                          <td><?php echo $this->language->get("entry_mobile_display_items"); ?></td>';
  html +='                          <td><input type="text" name="ecproductcarousel_module['+module_row+'][mobile_items]" value="1" size="10" /></td>';
  html +='                        </tr>';
  html +='                        <tr>';
  html +='                          <td><?php echo $this->language->get("entry_tablet_display_items"); ?></td>';
  html +='                          <td><input type="text" name="ecproductcarousel_module['+module_row+'][tablet_items]" value="2" size="10" /></td>';
  html +='                        </tr>';
  html +='                        <tr>';
  html +='                          <td><?php echo $this->language->get("entry_portrait_display_items"); ?></td>';
  html +='                          <td><input type="text" name="ecproductcarousel_module['+module_row+'][portrait_items]" value="3" size="10" /></td>';
  html +='                        </tr>';
  html +='                        <tr>';
  html +='                          <td><?php echo $this->language->get("entry_large_display_items"); ?></td>';
  html +='                          <td><input type="text" name="ecproductcarousel_module['+module_row+'][large_items]" value="5" size="10" /></td>';
  html +='                        </tr>';
  html +='                  </table>';
  html +='                </td>';
  html +='              </tr>';

  html +='            <tr>';
  html +='              <td><?php echo $this->language->get("entry_carousel_auto"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][carousel_auto]">';
                    <?php foreach($yesno as $key=>$val) { ?>
  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
                    <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +=' <tr>';
  html +='              <td><?php echo $this->language->get("entry_scroll_effect"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][scroll_effect]">';
                    <?php foreach($effects as $key) { ?>
                      <?php if($key == 'scroll'){ ?>
  html +='                   <option value="<?php echo $key; ?>" selected="selected"><?php echo $key; ?></option>';
                      <?php }else{ ?>
  html +='                    <option value="<?php echo $key; ?>"><?php echo $key; ?></option>';
                      <?php } ?>
                    <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='             <tr>';
  html +='              <td><?php echo $this->language->get("entry_duration"); ?></td>';
  html +='              <td><input type="text" name="ecproductcarousel_module[' + module_row + '][duration]" value="1000" size="10" /></td>';
  html +='            </tr>';

  html +='            <tr>';
  html +='              <td><?php echo $this->language->get("entry_carousel_responsive"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][carousel_responsive]">';
                    <?php foreach($yesno as $key=>$val) { ?>
  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
                    <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='            <tr>';
  html +='              <td><?php echo $this->language->get("entry_carousel_mousewhell"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][carousel_mousewhell]">';
                    <?php foreach($yesno as $key=>$val) { ?>
  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
                    <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='<tr>';
  html +='              <td colspan="2">';
  html +='                 <?php echo $this->language->get("entry_display_setting"); ?>';
  html +='              </td>';
  html +='            </tr>';
  html +='             <tr>';
  html +='              <td><?php echo $this->language->get("entry_limit_chars"); ?></td>';
  html +='              <td><input type="text" name="ecproductcarousel_module[' + module_row + '][limit_chars]" value="<?php echo isset($module['limit'])?$module['limit_chars']:58; ?>" size="3" /></td>';
  html +='            </tr>';
  html +='             <tr>';
  html +='              <td><?php echo $this->language->get("entry_strip_tags"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][strip_tags]">';
                    <?php foreach($yesno as $key=>$val) { ?>
  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
                    <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='            <tr>';
  html +='              <td><?php echo $this->language->get("entry_show_product_name"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][show_product_name]">';
                    <?php foreach($yesno as $key=>$val) { ?>
  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';

                    <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='            <tr>';
  html +='              <td><?php echo $this->language->get("entry_show_addtocart"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][show_addtocart]">';
                    <?php foreach($yesno as $key=>$val) { ?>
  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
                    <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='             <tr>';
  html +='              <td><?php echo $this->language->get("entry_show_price"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][show_price]">';
                    <?php foreach($yesno as $key=>$val) { ?>
  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
                 <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='            <tr>';
  html +='              <td><?php echo $this->language->get("entry_show_sale_label"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][show_sale_label]">';
                    <?php foreach($yesno as $key=>$val) { ?>
  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';

                    <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='            <tr>';
  html +='              <td><?php echo $this->language->get("entry_show_category_name"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][show_category_name]">';
                    <?php foreach($yesno as $key=>$val) { ?>
  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';

                    <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='             <tr>';
  html +='              <td><?php echo $this->language->get("entry_show_discount"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][show_discount]">';
                    <?php foreach($yesno as $key=>$val) { ?>
  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';

                    <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='            <tr>';
  html +='              <td><?php echo $this->language->get("entry_show_wishlist"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][show_wishlist]">';
                    <?php foreach($yesno as $key=>$val) { ?>

  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';

                    <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='             <tr>';
  html +='              <td><?php echo $this->language->get("entry_show_compare"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][show_compare]">';
                    <?php foreach($yesno as $key=>$val) { ?>

  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';

                    <?php } ?>
  html +='                </select></td>';
   html +='             <tr>';
  html +='              <td><?php echo $this->language->get("entry_show_number_bought"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][view_number_bought]">';
                    <?php foreach($yesno as $key=>$val) { ?>

  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';

                    <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='            </tr>';
  html +='            <tr>';
  html +='              <td><?php echo $this->language->get("entry_enable_async"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][enable_async]">';
                    <?php foreach($yesno as $key=>$val) { ?>
  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';

                   <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='            <tr>';
  html +='              <td><?php echo $this->language->get("entry_enable_lazyload"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][lazy_load_image]">';
                    <?php foreach($yesno as $key=>$val) { ?>

  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';

                   <?php } ?>
  html +='                </select></td>';
  html +='            </tr>';
  html +='            <tr>';
  html +='              <td><?php echo $this->language->get("entry_show_quicview"); ?></td>';
  html +='              <td><select name="ecproductcarousel_module[' + module_row + '][show_quicview]">';
                    <?php foreach($yesno as $key=>$val) { ?>

  html +='                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>';
                    <?php } ?>
  html +='               </select></td>';
  html +='            </tr>';
  html +='        </table>';
  html +='      </div>';
  
  $('#form').append(html);
  
  $('#module-add').before('<a href="#tab-module-' + module_row + '" id="module-' + module_row + '"><?php echo $tab_block; ?> ' + module_row + '&nbsp;<img src="view/image/delete.png" alt="" onclick="$(\'.vtabs a:first\').trigger(\'click\'); $(\'#module-' + module_row + '\').remove(); $(\'#tab-module-' + module_row + '\').remove(); return false;" /></a>');
  
  $('#language-' + module_row + ' a').tabs();
  change_mode( module_row );
  $('.vtabs a').tabs();
 <?php foreach ($languages as $language) { ?>
  CKEDITOR.replace('description-' + module_row + '-<?php echo $language['language_id']; ?>', {
    filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
    filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
    filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
    filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
    filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
    filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
  });
  <?php } ?>
  $('#module-' + module_row).trigger('click');
  productAutocomplete(module_row);
  showGroupFields(module_row, "latest");
  module_row++;
}
//--></script> 
<script type="text/javascript"><!--
$('.vtabs a').tabs();
//--></script>
<script type="text/javascript"><!--
<?php $module_row = 1; ?>
<?php foreach ($modules as $module) { ?>
$('#language-<?php echo $module_row; ?> a').tabs();
productAutocomplete(<?php echo $module_row ?>);
showGroupFields(<?php echo $module_row ?>, "<?php echo isset($module['source_from'])?$module['source_from']:'latest'; ?>");
<?php $module_row++; ?>
<?php } ?> 
//--></script>
<?php echo $footer; ?>