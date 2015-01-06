<?php
add_action('admin_init', 'ebay_store_admin_init');
add_action('admin_menu', 'ebay_store_add_sub_menu_page');

function ebay_store_admin_init() {
	wp_register_style('ebay_store_back.css', EBAY_STORE_PLUGIN_URL . 'back.css');
	wp_enqueue_style('ebay_store_back.css');
}

function ebay_store_admin_configuration() {
  $page_content = "";
  $page_content .= '<div class="ebay_store">';
  $page_content .=  '<h2>'.__("EBay  Store - Configuration.").'</h2>';

  $data = array();
  if(isset($_POST['submit']) && isset($_POST['ebay_configuration'])){
    $data = $_POST['ebay_configuration'];
    update_option(EBAY_STORE_PLUGIN_VAR_NAME, base64_encode(serialize($data)));
    $page_content .= '<div class="announce">'.__("Successfully updated").'</div>';
  } else {
    $data = get_option(EBAY_STORE_PLUGIN_VAR_NAME, array());
    if(is_string($data))
      $data = unserialize(base64_decode($data));
  }

  $page_content .=  ebay_store_get_form($data);
  $page_content .= "</div>";

  echo $page_content;
}

function ebay_store_add_sub_menu_page(){
  if ( function_exists('add_submenu_page') )
    add_submenu_page('plugins.php', __('EBay Store Configuration'), __('EBay Store'), 'manage_options', 'ebay-store-config', 'ebay_store_admin_configuration');
}

function ebay_store_get_form($form_values = array()){
    // Prevent invalid $_POST .
    if(!is_array($form_values))
      exit(__("Invalid form values"));

    $ebay_proxy_language_list = array(
        "en" => "en - GB",
        "es" => "es - ES",
        "fr" => "fr - FR",
        "it" => "it - IT",
        "de" => "de - DE",
    );
    
    $ebay_global_id = array(
      "EBAY-US"   => "USA",
      "EBAY-ENCA" => "Canada",
      "EBAY-GB"   => "United Kingdom",
      "EBAY-AU"   => "Australia",
      "EBAY-AT"   => "Austria",
      "EBAY-FR"   => "France",
      "EBAY-DE"   => "Germany",
      "EBAY-IT"   => "Italy",
      "EBAY-NL"   => "Netherlands",
      "EBAY-ES"   => "Spain",
      "EBAY-CH"   => "Switzerland",
      "EBAY-IE"   => "Ireland",
      "EBAY-FRBE" => "Belgium-fr",
      "EBAY-NLBE" => "Belgium-nl",
    );
    
        $ebay_displaydate = array(
          "true"          => "yes",
          "false"           => "no"
    );
    
    $ebay_floatorder = array(
          "floating"          => "floating",
          "Regular"           => "regular"
    );
    
    
    
    $ebay_sort_order = array(
      "BestMatch"                 => "BestMatch",
      "CurrentPriceHighest"       => "CurrentPriceHighest",
      "EndTimeSoonest"            => "EndTimeSoonest",
      "PricePlusShippingHighest"  => "PricePlusShippingHighest",
      "PricePlusShippingLowest"   => "PricePlusShippingLowest",
      "StartTimeNewest"           => "StartTimeNewest",
      "BidCountMost"              => "BidCountMost",
      "BidCountFewest"            => "BidCountFewest",
    );

    $ebay_open_link = array(
      "_blank"          => "new window",
      "_self"           => "same window",
    );

    $ret = "";
    $ret .= '<form class="ebay_store" method="post">';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("StoreName<BR />").'</label>';
    $ret .= ' <input type="text" value="%%%storeName%%%" name="ebay_configuration[storeName]">';
    $ret .= ' <div class="description">'.__("Your Store Name on Ebay (case sentisitve)").'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Ebay country source").'</label>';
    $ret .=   ebay_store_generateSelectFromArray($ebay_global_id, 'ebay_configuration[global_id]', isset($form_values['global_id']) ? $form_values['global_id'] : "");
    $ret .=   '<div class="description">'.__("Ebay country source").'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Maximum items<BR />").'</label>';
    $ret .=   '<input type="text" value="%%%maxEntries%%%" name="ebay_configuration[maxEntries]">';
    $ret .=   '<div class="description">'.__("Maximum items limited to 100").'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Enter your intro text <BR /><em>* optional</em>").'</label>';
    $ret .=   '<textarea name="ebay_configuration[intro]">'."%%%intro%%%".'</textarea>';
    $ret .=   '<div class="description">'.__("Enter your intro text").'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Sort order").'</label>';
    $ret .=   ebay_store_generateSelectFromArray($ebay_sort_order, 'ebay_configuration[itemSort]', isset($form_values['sortOrder']) ? $form_values['sortOrder'] : "");
    $ret .=   '<div class="description">'.__("Sort Order").'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Open ebay link").'</label>';
    $ret .=   ebay_store_generateSelectFromArray($ebay_open_link, 'ebay_configuration[openlink]', isset($form_values['openlink']) ? $form_values['openlink'] : "");
    $ret .=   '<div class="description">&nbsp;</div>';
    $ret .= '</div>';
    
      
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("<b>floating auctions</b> (responsive rows and columns on full available page) or <b>regular column version</b>").'</label>';
    $ret .=   ebay_seller_generateSelectFromArray($ebay_floatorder, 'ebay_configuration[floatorder]', isset($form_values['floatorder']) ? $form_values['floatorder'] : "");
    $ret .=   '<div class="description">&nbsp;</div>';
    $ret .= '</div>';
    
     
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("<b>Display date on floating version</b>").'</label>';
    $ret .=   ebay_seller_generateSelectFromArray($ebay_displaydate, 'ebay_configuration[displaydate]', isset($form_values['displaydate']) ? $form_values['displaydate'] : "");
    $ret .=   '<div class="description">&nbsp;</div>';
    $ret .= '</div>';
    
     $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Keyword<BR />").'</label>';
    $ret .=   '<input type="text" value="%%%keywords%%%" name="ebay_configuration[keywords]">';
    $ret .=   '<div class="description">'.__("optional").'</div>';
    $ret .= '</div>';
    
    
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Display language").'</label>';
    $ret .=   ebay_store_generateSelectFromArray($ebay_proxy_language_list, 'ebay_configuration[proxy_display_language]', isset($form_values['proxy_display_language']) ? $form_values['proxy_display_language'] : "");
    
    $ret .= '</div>';
    $ret .= '<div class="clear"></div>';
    $ret .= '<input type="submit" name="submit" value="Save"/>';
    $ret .= '</form>';

    $ret = str_replace("%%%storeName%%%", isset($form_values['storeName']) ? $form_values['storeName'] : "", $ret);
    $ret = str_replace("%%%maxEntries%%%", isset($form_values['maxEntries']) ? $form_values['maxEntries'] : "3", $ret);
    $ret = str_replace("%%%intro%%%", isset($form_values['intro']) ? $form_values['intro'] : "", $ret);
    $ret = str_replace("%%%keywords%%%", isset($form_values['keywords']) ? $form_values['keywords'] : "", $ret);


    return $ret;
}

function ebay_store_generateSelectFromArray($options , $select_name , $selected_option = null){
    $return = "";
    $return .= '<select id="'.$select_name.'" name="'.$select_name.'">';
    foreach($options as $value=>$name){
        $return .= '<option value="'.$value.'"';

        if($value == $selected_option)
            $return .= 'selected="selected"';

        $return .= '>'.$name.'</option>';
    }
    $return .= '</select>';

    return $return;
}
