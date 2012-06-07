<?php
/**
 * @package EBay Plugins
 */

function widget_ebay_store_register() {
  function widget_ebay_store($args) {
      $params = get_option(EBAY_STORE_PLUGIN_VAR_NAME, array());
      if(is_string($params))
        $params = unserialize(base64_decode($params));
    
      if(!is_array($params) || empty($params)){
        echo __("EBay Store is not properly configured");
        return;
      }
      if(trim($params['storeName']) == ""){
        echo __("EBay Store is not properly configured, missing store name");
        return;
      }
      $content = '<div id="ebay_store">';

      if(isset($params['intro']) && trim($params['intro']) != "")
        $content .= "<p>".str_replace(array("\r\n", "\n"),"<br>",$params['intro'])."</p>";
   
      $content .=   ebay_store_generate_javascript($params);
      $content .= '</div>';
      echo $content;
  }

  function ebay_store_generate_javascript($params) {
    $callUrl = ebay_store_get_proxy_url();

    $resp = array(
        'requestType'                       => 'EBayStore',
        'storeName'                         => urlencode($params['storeName']),
        'keywords'                          => isset($params['keywords'])   ? $params['keywords']   : '',
        'paginationInput.entriesPerPage'    => isset($params['maxEntries']) ? $params['maxEntries'] : '3',
        'sortOrder'                         => isset($params['sortOrder'])  ? $params['sortOrder']  : 'BestMatch',
        'GLOBAL-ID'                         => isset($params['global_id'])  ? $params['global_id'] : 'EBAY-US',
        'openlink'                          => isset($params['openlink'])   ? $params['openlink']  : '_blank',
        'proxy_display_language'            => isset($params['proxy_display_language']) ? $params['proxy_display_language'] : 'en',
    );

    $category_id = isset($params['categoryId'])  ? $params['categoryId']   : '';
    if(trim($category_id) !== "")
      $resp['categoryId'] = $category_id;

    $first = true;
    foreach ($resp as $key=>$param){
          if($first){
             $first = false;
             $callUrl .= $key . '=' . $param;
          } else
             $callUrl .= '&' . $key . '=' . $param;
      }

      return '<script type="text/javascript" src="'.$callUrl.'"></script>';
  }

  function widget_ebay_store_control(){
      $content = "";
      $content .= __('Please configure your widget from');
      $content .= ': <a href="plugins.php?page=ebay-store-config">';
      $content .= __("here");
      $content .= '</a>';

      echo $content;
  }

  function widget_ebay_store_include_css(){
      echo '<style type="text/css">'.file_get_contents(EBAY_STORE_PLUGIN_URL."front.css").'</style>';
  }

  if(function_exists('register_sidebar_widget') ){
    if(function_exists('wp_register_sidebar_widget')){
      wp_register_sidebar_widget( 'ebay_store', 'EBay Store', 'widget_ebay_store', null, 'ebay_store');
      wp_register_widget_control( 'ebay_store', 'EBay Store', 'widget_ebay_store_control', null, 75, 'ebay_store');
    }elseif(function_exists('register_sidebar_widget')){
      register_sidebar_widget('EBay Store', 'widget_ebay_store', null, 'ebay_store');
      register_widget_control('EBay Store', 'widget_ebay_store_control', null, 75, 'ebay_store');
    }
  }
  
  if(is_active_widget('widget_ebay_store'))
    add_action('wp_head', 'widget_ebay_store_include_css');

}

add_action('init', 'widget_ebay_store_register');

