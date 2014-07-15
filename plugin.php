<?php
/*********
Custom WordPress Functions File
created by: dnnsldr
Used this in a mu-plugins folder so that it is active regardless of the theme
 
Be sure to check 'MultiPostThumbnails' to change names and ID's
Be Sure to check 'plugin_action_links' so to change the name of the plugins that cannot be edited
replace string: 'yoursitename.com' with your url
replace string: 'yoursitename' with your company name
replace string: 'ddnsldr' with a custom name for functions
replace string: 'URL TO YOUR LOGO' with the url to your logo. Full url needed
replace string; 'YOUR EMAIL ADDRESS' with your email address
replace string: 'YOUR PHONE NUMBER' with your phone number
**********/
 
//remove hello dolley plugin
if(file_exists(WP_PLUGIN_DIR . '/hello.php')) unlink(WP_PLUGIN_DIR . '/hello.php');
 
//create custom update message in admin to replace 'Update WordPress' message
function dnnsldr_update_nag() {
    if ( is_multisite() && !current_user_can('update_core') )
        return false;
 
    global $pagenow;
 
    if ( 'update-core.php' == $pagenow )
        return;
 
    $cur = get_preferred_from_update_core();
 
    if ( ! isset( $cur->response ) || $cur->response != 'upgrade' )
        return false;
 
    if ( current_user_can('update_core') )
        $msg = sprintf( __('Your version of WordPress is ready for an update. Please <a href="http://yoursitename.com" target="_blank">contact yoursitename</a> to perform the required update.'), $cur->current, 'update-core.php' );
    else
        $msg = sprintf( __('Please <a href="http://yoursitename.com" target="_blank">contact yoursitename</a> for all your development and design needs.'), $cur->current );
 
    echo "<div class='update-nag'>$msg</div>";
}
add_action('admin_init',create_function('$a','remove_action("admin_notices","update_nag",3);'));
add_action('admin_init',create_function('$a','remove_filter("update_footer","core_update_footer");'));
add_action( 'admin_notices', 'dnnsldr_update_nag', 3 );
 
//grab first image form posts in case no featured image
function catch_that_image() {
    global $post, $posts;
     
    $first_img = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $first_img = $matches [1] [0];
 
    // no image found display default image instead
    if(empty($first_img)){
        $first_img = "images/post-thumbnail-banner.png";
    }
    //$first_img = str_replace('http://mywebistename.com', '', $first_img);
    return $first_img;
}
 
//remove inline width and height added to images to create responsive web elements
add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10 );
add_filter( 'image_send_to_editor', 'remove_thumbnail_dimensions', 10 );
// Removes attached image sizes as well
add_filter( 'the_content', 'remove_thumbnail_dimensions', 10 );
function remove_thumbnail_dimensions( $html ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
        return $html;
}
 
//add an metabox for support into the dashboard and remove default meta boxes
add_action('wp_dashboard_setup', 'mosaic_dashboard_widgets');
 
function dnnsldr_dashboard_widgets() {
    global $wp_meta_boxes;
     
    //Plugins - Popular, New and Recently updated Wordpress Plugins
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    //Wordpress Development Blog Feed
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    //Other Wordpress News Feed
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
    //Quick Doctor Form
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    //Recent Drafts List
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
    //add the new dashboard widget for Support
        wp_add_dashboard_widget('dnnsldr_help_widget', 'Theme Support', 'dnnsldr_dashboard_help');
}
 
function dnnsdlr_dashboard_help() {
    echo '<p><a href="http://yoursitename.com"><img src="URL TO YOUR LOGO" /></a></p><p style="font-size: 13px;padding-bottom: 5px;line-height: 22px;">Welcome!</p><p style="font-size: 13px;padding-bottom: 5px;line-height: 22px;">Contact yoursitename <a href="mailto:YOUR EMAIL ADDRESS">by email</a> or give us a call at YOUR PHONE NUMBER for all updates and support.</p>';
}
 
// REMOVE THE WORDPRESS UPDATE NOTIFICATION FOR ALL USERS EXCEPT SYSADMIN
global $user_login;
get_currentuserinfo();
if (!current_user_can('update_plugins')) { // checks to see if current user can update plugins
    add_action( 'init', create_function( '$a', "remove_action( 'init', 'wp_version_check' );" ), 2 );
     add_filter( 'pre_option_update_core', create_function( '$a', "return null;" ) );
}
 
//remove the plugin update count
add_action('admin_menu', 'remove_plugin_update_count');
function remove_plugin_update_count(){
    global $menu,$submenu;
    $menu[65][0] = 'Plugins';  
     $submenu['index.php'][10][0] = 'Updates';  
}
 
//remove the meta boxes from the post editor
add_action( 'add_meta_boxes', 'my_remove_post_meta_boxes' );
 
function my_remove_post_meta_boxes() {
 
    /* Publish meta box. */
    remove_meta_box( 'submitdiv', 'post', 'normal' );
    /* Comments meta box. */
    remove_meta_box( 'commentsdiv', 'post', 'normal' );
    /* Revisions meta box. */
    remove_meta_box( 'revisionsdiv', 'post', 'normal' );
    /* Author meta box. */
    remove_meta_box( 'authordiv', 'post', 'normal' );
    /* Slug meta box. */
    remove_meta_box( 'slugdiv', 'post', 'normal' );
    /* Post tags meta box. */
    remove_meta_box( 'tagsdiv-post_tag', 'post', 'side' );
    /* Category meta box. */
    remove_meta_box( 'categorydiv', 'post', 'side' );
    /* Excerpt meta box. */
    remove_meta_box( 'postexcerpt', 'post', 'normal' );
    /* Post format meta box. */
    remove_meta_box( 'formatdiv', 'post', 'normal' );
    /* Trackbacks meta box. */
    remove_meta_box( 'trackbacksdiv', 'post', 'normal' );
    /* Custom fields meta box. */
    remove_meta_box( 'postcustom', 'post', 'normal' );
    /* Comment status meta box. */
    remove_meta_box( 'commentstatusdiv', 'post', 'normal' );
    /* Featured image meta box. */
    remove_meta_box( 'postimagediv', 'post', 'side' );
    /* Page attributes meta box. */
    remove_meta_box( 'pageparentdiv', 'page', 'side' );
}
 
//remove the meta boxes from the page editor
add_action( 'add_meta_boxes', 'my_remove_post_meta_boxes' );
 
function my_remove_page_meta_boxes() {
 
    /* Publish meta box. */
    remove_meta_box( 'submitdiv', 'post', 'normal' );
    /* Comments meta box. */
    remove_meta_box( 'commentsdiv', 'post', 'normal' );
    /* Revisions meta box. */
    remove_meta_box( 'revisionsdiv', 'post', 'normal' );
    /* Author meta box. */
    remove_meta_box( 'authordiv', 'post', 'normal' );
    /* Slug meta box. */
    remove_meta_box( 'slugdiv', 'post', 'normal' );
    /* Post tags meta box. */
    remove_meta_box( 'tagsdiv-post_tag', 'post', 'side' );
    /* Category meta box. */
    remove_meta_box( 'categorydiv', 'post', 'side' );
    /* Excerpt meta box. */
    remove_meta_box( 'postexcerpt', 'post', 'normal' );
    /* Post format meta box. */
    remove_meta_box( 'formatdiv', 'post', 'normal' );
    /* Trackbacks meta box. */
    remove_meta_box( 'trackbacksdiv', 'post', 'normal' );
    /* Custom fields meta box. */
    remove_meta_box( 'postcustom', 'post', 'normal' );
    /* Comment status meta box. */
    remove_meta_box( 'commentstatusdiv', 'post', 'normal' );
    /* Featured image meta box. */
    remove_meta_box( 'postimagediv', 'post', 'side' );
    /* Page attributes meta box. */
    remove_meta_box( 'pageparentdiv', 'page', 'side' );
}
 
// CUSTOMIZE ADMIN MENU ORDER
function dnnsldr_menu_order($menu_ord) {
 
    if (!$menu_ord) return true;
        return array(
            'index.php', // this represents the dashboard link
            'edit.php', // this is the default POST admin menu
            'edit.php?post_type=YOUR_CUSTOM_POST_TYPE', //custom post type
            'edit.php?post_type=page', // this is the default page menu
        );
}
add_filter('dnnsldr_menu_order', 'dnnsldr_menu_order');
add_filter('menu_order', 'dnnsldr_menu_order');
 
// unregister all default WP Widgets
function dnnsldr_unregister_default_wp_widgets() {
    unregister_widget('WP_Widget_Pages');
        unregister_widget('WP_Widget_Calendar');
        unregister_widget('WP_Widget_Archives');
        unregister_widget('WP_Widget_Links');
        unregister_widget('WP_Widget_Meta');
        unregister_widget('WP_Widget_Search');
        unregister_widget('WP_Widget_Text');
        //unregister_widget('WP_Widget_Categories');
        //unregister_widget('WP_Widget_Recent_Posts');
        unregister_widget('WP_Widget_Recent_Comments');
        //unregister_widget('WP_Widget_RSS');
        unregister_widget('WP_Widget_Tag_Cloud');
}
add_action('widgets_init', 'dnnsldr_unregister_default_wp_widgets', 1);
 
//if using MultipostThumbnails Plugin
//multiple thumbnails for posts
if (class_exists('MultiPostThumbnails')) {
    $types = array('YOUR-CUSTOM-POST-TYPE');//this is your custom post type
        foreach($types as $type) {
        $thumb = new MultiPostThumbnails(array(
            'label' => 'YOUR LABEL',//add your custom Name here
                'id' => 'yourid',//add your custom ID
                'post_type' => $type
          )
            );
        }
}
add_image_size('post-secondary-image-thumbnail', 250, 150);//change the size here
//gets the thumbnail url
if (class_exists('MultiPostThumbnails')) {
    class CustomMultiPostThumbnails extends MultiPostThumbnails {
 
        public function __construct($args = array()) {
            parent::__construct($args);
        }
 
        public static function get_the_post_thumbnail_url($post_type, $thumb_id, $post_id = NULL, $size = 'post-thumbnail', $attr = '' , $link_to_original = false) {
            global $id;
            $post_id = (NULL === $post_id) ? $id : $post_id;
            $post_thumbnail_id = self::get_post_thumbnail_id($post_type, $thumb_id, $post_id);
            $size = apply_filters("{$post_type}_{$id}_thumbnail_size", $size);
         
            if ($post_thumbnail_id) {
                do_action("begin_fetch_multi_{$post_type}_thumbnail_html", $post_id, $post_thumbnail_id, $size); // for "Just In Time" filtering of all of wp_get_attachment_image()'s filters
                $html = wp_get_attachment_image( $post_thumbnail_id, $size, false, $attr );
                do_action("end_fetch_multi_{$post_type}_thumbnail_html", $post_id, $post_thumbnail_id, $size);
            } else {
                $html = '';
            }
         
            if ($link_to_original) {
                $html = sprintf('<a href="%s">%s</a>', wp_get_attachment_url($post_thumbnail_id), $html);
            }
         
            return wp_get_attachment_url($post_thumbnail_id);
            //return apply_filters("{$post_type}_{$id}_thumbnail_html", $html, $post_id, $post_thumbnail_id, $size, $attr);
        }
    }
}//thats the end of the custom class
//how to use the MultiPostThumbs with timthumb using above extended class
/*
<img class="gray" src="<?php bloginfo('template_url'); ?>/scripts/timthumb.php?src=<?php echo CustomMultiPostThumbnails::get_the_post_thumbnail_url('YOUR-CUSTOM-POST-TYPE', 'yourid', $post->ID); ?>&amp;w=235&amp;h=126&amp;zc=1&amp;q=95" alt="<?php the_title(); ?>" />
*/
 
/*set the excerpt to any length*/
function excerpt($num) {
    $limit = $num+1;
        $excerpt = explode(' ', get_the_excerpt(), $limit);
        array_pop($excerpt);
        $excerpt = implode(" ",$excerpt)."...<a class='readmore' href='" .get_permalink($post->ID) ." '>Read more</a>";
        echo $excerpt;
}
//now without the read more
function excerpt_noread($num) {
        $limit = $num+1;
        $excerpt = explode(' ', get_the_excerpt(), $limit);
        array_pop($excerpt);
        $excerpt = implode(' ',$excerpt).'...';
        echo '<p>'.$excerpt.'</p>';
}
 
 
/***************
This snippet is particularly useful if you have given a client plugin activation/deactivation privileges (allowing them to add new plugins themselves), but the site you have built requires some core plugins to function and should never be deactivated.
The code below will remove the ‘Deactivate’ links from whichever plugins you deem fundamental as well as removing the ‘Edit’ links from all plugins.
 
source: http://sltaylor.co.uk/blog/disabling-wordpress-plugin-deactivation-theme-changing/
***************/
 
add_filter( 'plugin_action_links', 'dnnsldr_lock_plugins', 10, 4 );
function dnnsldr_lock_plugins( $actions, $plugin_file, $plugin_data, $context ) {
    // Remove edit link for all
        if ( array_key_exists( 'edit', $actions ) )
            unset( $actions['edit'] );
        // Remove deactivate link for crucial plugins
        if ( array_key_exists( 'deactivate', $actions ) && in_array( $plugin_file, array(
        'plugin-name/plugin-name.php',//this is the name of the plugin folder name and the plugin php file
            'plugin-name/plugin-name.php'
        )))
        unset( $actions['deactivate'] );
        return $actions;
}
 
/**
* create a custom login form
**/
function dnnsldr_loginpage_logo_link($url) {
     // Return a url; in this case the homepage url of wordpress
     return get_bloginfo('wpurl');
}
function dnnsldr_loginpage_logo_title($message) {
     // Return title text for the logo to replace 'wordpress'; in this case, the blog name.
     return get_bloginfo('name');
}
function dnnsldr_loginpage_head() {
     /* Add a stylesheet to the login page; add your styling in here, for example to change the logo use something like:
     #login h1 a {
          background:url(images/logo.jpg) no-repeat top;
     }
     */
     $stylesheet_uri = get_bloginfo('template_url')."/css/login.css";
     echo '<link rel="stylesheet" href="'.$stylesheet_uri.'" type="text/css" media="screen" />';
}
// Hook in for the custom login
add_filter("login_headerurl","dnnsldr_loginpage_logo_link");
add_filter("login_headertitle","dnnsldr_loginpage_logo_title");
add_action("login_head","dnnsldr_loginpage_head");
 
//Change the Logo on the admin page
//This one’s an old trick, but a good one nonetheless. You can change the logo for the login page and the one in the top left located at the WordPress Admin area pages.
function custom_logo() {
    echo '<style type="text/css">
            #header-logo { background-image: url('.get_bloginfo('template_directory').'/images/admin_logo.png) !important; }
        </style>';
}
 
add_action('admin_head', 'custom_logo');
 
 
//remove the links admin menu item
add_action( 'admin_menu', 'my_admin_menu' );
function my_admin_menu() {
    remove_menu_page('link-manager.php');
}
 
//add credits to the footer in the admin
add_filter( 'admin_footer_text', 'my_admin_footer_text' );
function my_admin_footer_text( $default_text ) {
    return '<span id="footer-thankyou">Website managed by <a href="http://www.yoursitname.com">yoursitenamename</a><span> | Powered by <a href="http://www.wordpress.org">WordPress</a>';
}
 
//Change 'posts' to 'articles'
// hook the translation filters
add_filter(  'gettext',  'change_post_to_article'  );
add_filter(  'ngettext',  'change_post_to_article'  );
function change_post_to_article( $translated ) {
    $translated = str_ireplace(  'Post',  'Article',  $translated );  // ireplace is PHP5 only
    return $translated;
}
 
//remove the admin bar
add_filter( 'show_admin_bar', '__return_false' );
 
 
//add a help dropdown menu in the admin
add_action('load-page-new.php','dnnsldr_help_page');
add_action('load-page.php','dnnsldr_help_page');
function custom_help_page() {
    add_filter('contextual_help','dnnsldr_page_help');
}
function dnnsldr_page_help($help) {
    // echo $help; // Uncomment if you just want to append your custom Help text to the default Help text
    echo "<h5>Custom Help text</h5>";
    echo "<p>Contact yoursitename <a href="mailto:YOUR EMAIL ADDRESS">by email</a> or give us a call at YOUR PHONE NUMBER for all updates and support.</p>";
}
 
//This is how you would remove submenu items under the top-level navigation (for example, the "Theme" link under "Appearance"):
function remove_submenus() {
    global $submenu;
    unset($submenu['index.php'][10]); // Removes 'Updates'.
    unset($submenu['themes.php'][5]); // Removes 'Themes'.
    unset($submenu['options-general.php'][15]); // Removes 'Writing'.
    unset($submenu['options-general.php'][25]); // Removes 'Discussion'.
    unset($submenu['edit.php'][16]); // Removes 'Tags'. 
}
 
add_action('admin_menu', 'remove_submenus');
 
//Remove the Editor Submenu Item
function remove_editor_menu() {
    remove_action('admin_menu', '_add_themes_utility_last', 101);
}
 
add_action('_admin_menu', 'remove_editor_menu', 1);
 
 
//remove the theme editor, this goes in the wp-config file
define('DISALLOW_FILE_EDIT',true);
//You can also add this to your wp-config.php to disable or limit revisions
define('WP_POST_REVISIONS', false ); //disable completely
define('WP_POST_REVISIONS', 5 ); //limit to 5 per post
 
 
//move the text editor in pages, posts, or custom post types
add_action('admin_init','admin_init_hook');
function admin_init_hook()
{
    function blank(){}
  
    foreach (array('page','post','custom_type') as $type)
    {
        add_meta_box('custom_editor', 'Content', 'blank', $type, 'normal', 'low');
    }
}
  
add_action('admin_head','admin_head_hook');
function admin_head_hook()
{
    ?><style type="text/css">
        #postdiv.postarea, #postdivrich.postarea { margin:0; }
        #post-status-info { line-height:1.4em; font-size:13px; }
        #custom_editor .inside { margin:2px 6px 6px 6px; }
        #ed_toolbar { display:none; }
        #postdiv #ed_toolbar, #postdivrich #ed_toolbar { display:block; }
    </style><?php
}
  
add_action('admin_footer','admin_footer_hook');
function admin_footer_hook()
{
    ?><script type="text/javascript">
        jQuery('#postdiv, #postdivrich').prependTo('#custom_editor .inside');
    </script><?php
}
 
 
 
//that's it. were all done
?>
