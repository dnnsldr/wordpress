<?php
 
/*  =Add Shortcode
------------------------------------------------------------------------*/
/**
ShortCode Tutorial
my_shortcodename Method
*/
  
function Add_my_shortcodename( $atts, $content = null ) {
  extract( shortcode_atts( array (
    'href' => 'http://www.tutorialchip.com/'
  ), $atts ) );
  return '<a href="'.$href.'">'.$content.'</a>';
}
 
/**
Hook into WordPress
*/
  
add_shortcode( 'my_shortcodename', 'Add_my_shortcodename' );
 
<?
 
/*  =ADD Shortcode Button to TinyMCE Editor in WordPress
------------------------------------------------------------------------*/
 
/**
Hook into WordPress
*/
  
add_action('init', 'my_shortcodename_button');
 
/**
Create Our Initialization Function
*/
  
function my_shortcodename_button() {
  
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
     return;
   }
  
   //if ( get_user_option('rich_editing') == 'true' ) {  **uncomment if you only want this in the visual editor*/
     add_filter( 'mce_external_plugins', 'add_plugin' );
     add_filter( 'mce_buttons', 'register_button' );
   //} **uncomment if you want this only in the visual editor
  
}
 
/**
Register Button
*/
  
function register_button( $buttons ) {
 array_push( $buttons, "|", "my_shortcodename" );
 return $buttons;
}
 
/**
Register TinyMCE Plugin
*/
  
function add_plugin( $plugin_array ) {
   $plugin_array['my_shortcodename'] = get_bloginfo( 'template_url' ) . '/script/my_shortcodes.js';
   return $plugin_array;
}
 
 
 
 
 
 
 
//thats the end of the button
?>
 
 
<?php
/*  =Now move this to your my_shorcodes.js
------------------------------------------------------------------------*/
 
// JavaScript Document
(function() {
    tinymce.create('tinymce.plugins.my_shortcodename', {
        init : function(ed, url) {
            ed.addButton('my_shortcodename', {
                title : 'My Shortcode Name',
                image : url+'/my_shortcodename.png', //you need to make this image
                onclick : function() {
                 //use this if the shortcode has content
                     ed.selection.setContent('[my_shortcodename]' + ed.selection.getContent() + '[/my_shortcodename]');
                     //use below if it is a closed shortcode ex. no content or options for the shortcode
                     //ed.selection.setContent('[socialShare]');
  
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('my_shortcodename', tinymce.plugins.my_shortcodename);
})();
 
 
 
?>
