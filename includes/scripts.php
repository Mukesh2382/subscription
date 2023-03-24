<?php

if( !function_exists('sm_plugin_scripts')) {
    function sm_plugin_scripts() {

        //Plugin Frontend CSS
        wp_enqueue_style('style-css', plugin_dir_url( __DIR__ ). 'assets/css/style.css');

    }
    add_action('admin_enqueue_scripts', 'sm_plugin_scripts');
}