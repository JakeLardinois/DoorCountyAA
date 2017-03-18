<?php
/*
Plugin Name: Jake's Hello World!
Version: 0.1
Description: My First Wordpress Plugin
Author: Jake Lardinois
Author URI: www.IntegralSWSolutions.com
Plugin URI: www.IntegralSWSolutions.com
Text Domain: Hello!
Domain Path: /languages
 */

add_shortcode('JakesHelloWorldShort', 'process_HelloWorld');
function process_HelloWorld() {
    $returnHTML = file_get_contents('jakes_hello_world.htm', true);
    $returnHTML = str_replace("##host##", "http://localhost:19320/", $returnHTML);
    $returnHTML = str_replace("##jake##", "Jake", $returnHTML);

    return $returnHTML;
}