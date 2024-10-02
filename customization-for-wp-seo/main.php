<?php
/*
Plugin Name: Customization for WP SEO
Version: 1.0.1
Plugin URI: https://noorsplugin.com/customization-for-wp-seo-wordpress-plugin/
Author: naa986
Author URI: https://noorsplugin.com/
Description: Customization for the Yoast SEO WordPress plugin. 
Text Domain: customization-for-wp-seo
Domain Path: /languages
*/

if(!defined('ABSPATH')) exit;

class CUSTOMIZATION_FOR_WP_SEO
{
    var $plugin_version = '1.0.1';
    var $plugin_url;
    var $plugin_path;
    function __construct()
    {
        define('CUSTOMIZATION_FOR_WP_SEO_VERSION', $this->plugin_version);
        define('CUSTOMIZATION_FOR_WP_SEO_SITE_URL',site_url());
        define('CUSTOMIZATION_FOR_WP_SEO_URL', $this->plugin_url());
        define('CUSTOMIZATION_FOR_WP_SEO_PATH', $this->plugin_path());
        $this->plugin_includes();
    }
    function plugin_includes()
    {
        if(is_admin())
        {
            add_filter('plugin_action_links', array($this,'add_plugin_action_links'), 10, 2 );
        }
        add_action('plugins_loaded', array($this, 'plugins_loaded_handler'));
        add_action('admin_menu', array($this, 'add_options_menu'));
        add_filter('wpseo_json_ld_output', array($this, 'wpseo_json_ld_output'));
        add_filter('wpseo_frontend_presenters', array($this, 'wpseo_frontend_presenters'));
    }
    function plugin_url()
    {
        if($this->plugin_url) return $this->plugin_url;
        return $this->plugin_url = plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
    }
    function plugin_path(){ 	
        if ( $this->plugin_path ) return $this->plugin_path;		
        return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
    }
    function add_plugin_action_links($links, $file)
    {
        if ( $file == plugin_basename( dirname( __FILE__ ) . '/main.php' ) )
        {
            $links[] = '<a href="options-general.php?page=customization-for-wp-seo-settings">'.__('Settings', 'customization-for-wp-seo').'</a>';
        }
        return $links;
    }

    function plugins_loaded_handler()
    {
        load_plugin_textdomain('customization-for-wp-seo', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/'); 
    }

    function add_options_menu()
    {
        if(is_admin())
        {
            add_options_page(__('Customization for WP SEO', 'customization-for-wp-seo'), __('Customization for WP SEO', 'customization-for-wp-seo'), 'manage_options', 'customization-for-wp-seo-settings', array($this, 'display_options_page'));
        }
    }

    function display_options_page()
    {    
        $url = "https://noorsplugin.com/customization-for-wp-seo-wordpress-plugin/";
        $link_text = sprintf(__('Please visit the <a target="_blank" href="%s">Customization for WP SEO</a> documentation page for setup instructions.', 'customization-for-wp-seo'), esc_url($url));          
        $allowed_html_tags = array(
            'a' => array(
                'href' => array(),
                'target' => array()
            )
        );
        echo '<div class="wrap"><h2>Customization for WP SEO - v'.CUSTOMIZATION_FOR_WP_SEO_VERSION.'</h2>';
        echo '<div class="update-nag">'.wp_kses($link_text, $allowed_html_tags).'</div>';
        $this->general_settings();
        echo '</div>'; 
    }

    function general_settings() {
        if (isset($_POST['customization_for_wp_seo_update_settings'])) {
            $nonce = sanitize_text_field($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'customization_for_wp_seo_general_settings')) {
                wp_die(__('Error! Nonce Security Check Failed! please save the general settings again.', 'customization-for-wp-seo'));
            }
            $disable_schema_output = '';
            if(isset($_POST['cfws_disable_schema_output']) && !empty($_POST['cfws_disable_schema_output'])){
                $disable_schema_output = sanitize_text_field($_POST['cfws_disable_schema_output']);
            }
            $remove_og_locale = '';
            if(isset($_POST['cfws_remove_og_locale']) && !empty($_POST['cfws_remove_og_locale'])){
                $remove_og_locale = sanitize_text_field($_POST['cfws_remove_og_locale']);
            }
            $remove_og_article_published_time = '';
            if(isset($_POST['cfws_remove_og_article_published_time']) && !empty($_POST['cfws_remove_og_article_published_time'])){
                $remove_og_article_published_time = sanitize_text_field($_POST['cfws_remove_og_article_published_time']);
            }
            $remove_og_article_modified_time = '';
            if(isset($_POST['cfws_remove_og_article_modified_time']) && !empty($_POST['cfws_remove_og_article_modified_time'])){
                $remove_og_article_modified_time = sanitize_text_field($_POST['cfws_remove_og_article_modified_time']);
            }            
            $options = array();
            $options['disable_schema_output'] = $disable_schema_output;
            $options['remove_og_locale'] = $remove_og_locale;
            $options['remove_og_article_published_time'] = $remove_og_article_published_time;
            $options['remove_og_article_modified_time'] = $remove_og_article_modified_time;
            customization_for_wp_seo_update_option($options);
            echo '<div id="message" class="updated fade"><p><strong>';
            echo __('Settings Saved', 'customization-for-wp-seo').'!';
            echo '</strong></p></div>';
        }
        $options = customization_for_wp_seo_get_option();

        ?>

        <form method="post" action="">
            <?php wp_nonce_field('customization_for_wp_seo_general_settings'); ?>

            <table class="form-table">

                <tbody>

                    <tr valign="top">
                        <th scope="row"><label for="cfws_disable_schema_output"><?php _e('Disable Schema Output', 'customization-for-wp-seo');?></label></th>
                        <td><input name="cfws_disable_schema_output" type="checkbox" id="cfws_disable_schema_output" <?php if(isset($options['disable_schema_output']) && !empty($options['disable_schema_output'])) echo ' checked="checked"'; ?> value="1">
                            <p class="description"><?php _e("Check this option to disable all Yoast SEO's schema output", 'customization-for-wp-seo');?></p></td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><label for="cfws_remove_og_locale"><?php _e('Remove Open Graph Locale', 'customization-for-wp-seo');?></label></th>
                        <td><input name="cfws_remove_og_locale" type="checkbox" id="cfws_remove_og_locale" <?php if(isset($options['remove_og_locale']) && !empty($options['remove_og_locale'])) echo ' checked="checked"'; ?> value="1">
                            <p class="description"><?php _e("Check this option to remove og:locale meta from Yoast SEO's Open Graph output", 'customization-for-wp-seo');?></p></td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><label for="cfws_remove_og_article_published_time"><?php _e('Remove Open Graph Article Published Time', 'customization-for-wp-seo');?></label></th>
                        <td><input name="cfws_remove_og_article_published_time" type="checkbox" id="cfws_remove_og_article_published_time" <?php if(isset($options['remove_og_article_published_time']) && !empty($options['remove_og_article_published_time'])) echo ' checked="checked"'; ?> value="1">
                            <p class="description"><?php _e("Check this option to remove article:published_time meta from Yoast SEO's Open Graph output", 'customization-for-wp-seo');?></p></td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><label for="cfws_remove_og_article_modified_time"><?php _e('Remove Open Graph Article Modified Time', 'customization-for-wp-seo');?></label></th>
                        <td><input name="cfws_remove_og_article_modified_time" type="checkbox" id="cfws_remove_og_article_modified_time" <?php if(isset($options['remove_og_article_modified_time']) && !empty($options['remove_og_article_modified_time'])) echo ' checked="checked"'; ?> value="1">
                            <p class="description"><?php _e("Check this option to remove article:modified_time meta from Yoast SEO's Open Graph output", 'customization-for-wp-seo');?></p></td>
                    </tr>
                    
                </tbody>

            </table>

            <p class="submit"><input type="submit" name="customization_for_wp_seo_update_settings" id="customization_for_wp_seo_update_settings" class="button button-primary" value="<?php _e('Save Changes', 'customization-for-wp-seo');?>"></p></form>

        <?php
    }
    
    function wpseo_json_ld_output($data)
    {
        $options = customization_for_wp_seo_get_option();
        if(isset($options['disable_schema_output']) && !empty($options['disable_schema_output'])){
            return false;
        }
        return $data;
    }
    
    function wpseo_frontend_presenters($presenters) 
    {
        $options = customization_for_wp_seo_get_option();
        foreach($presenters as $i => $val){
            if(isset($options['remove_og_locale']) && !empty($options['remove_og_locale'])){
                if($val instanceof Yoast\WP\SEO\Presenters\Open_Graph\Locale_Presenter){
                    unset($presenters[$i]);
                }
            }
            if(isset($options['remove_og_article_published_time']) && !empty($options['remove_og_article_published_time'])){
                if($val instanceof Yoast\WP\SEO\Presenters\Open_Graph\Article_Published_Time_Presenter){
                    unset($presenters[$i]);
                }
            }
            if(isset($options['remove_og_article_modified_time']) && !empty($options['remove_og_article_modified_time'])){
                if($val instanceof Yoast\WP\SEO\Presenters\Open_Graph\Article_Modified_Time_Presenter){
                    unset($presenters[$i]);
                }
            }
        }
        return $presenters;
    }
}

$GLOBALS['customization_for_wp_seo'] = new CUSTOMIZATION_FOR_WP_SEO();

function customization_for_wp_seo_get_option(){
    $options = get_option('customization_for_wp_seo_options');
    if(!is_array($options)){
        $options = customization_for_wp_seo_get_empty_options_array();
    }
    return $options;
}

function customization_for_wp_seo_update_option($new_options){
    $empty_options = customization_for_wp_seo_get_empty_options_array();
    $options = customization_for_wp_seo_get_option();
    if(is_array($options)){
        $current_options = array_merge($empty_options, $options);
        $updated_options = array_merge($current_options, $new_options);
        update_option('customization_for_wp_seo_options', $updated_options);
    }
    else{
        $updated_options = array_merge($empty_options, $new_options);
        update_option('customization_for_wp_seo_options', $updated_options);
    }
}

function customization_for_wp_seo_get_empty_options_array(){
    $options = array();
    $options['disable_schema_output'] = '';
    $options['remove_og_locale'] = '';
    $options['remove_og_article_published_time'] = '';
    $options['remove_og_article_modified_time'] = '';
    return $options;
}
