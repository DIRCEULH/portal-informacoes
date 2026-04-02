<?php
/*
Plugin Name: Widget: Selo CLIMATEMPO
Description: Selo de Previsão do Tempo - CLIMATEMPO
Version: 0.1.2
Author: Climatempo
Author URI: http://www.climatempo.com.br
*/
define('WP_DEBUG', true);

/* INCLUINDO CLASSE WIDGET */
require(dirname(__FILE__) . '/class/WidgetSeloClimatempo.class.php');

function wsct_registra_scripts()
{
    /* REGISTRANDO SCRIPTS. */
    wp_register_script('functions', plugins_url('js/functions.js', __FILE__));
}

function wsct_aciona_scripts()
{
    /* LINKANDO SCRIPTS APOS REGISTRA-LOS */
    wp_enqueue_script('functions');
}

/* AO INICIARMOS O WIDGET, CHAMA FUNCAO DE REGISTRAR SCRIPT E DEPOIS LINKA-OS A PAGINA. */
add_action('admin_init', 'wsct_registra_scripts');
add_action('admin_enqueue_scripts', 'wsct_aciona_scripts');

/* AO INICIAR O WIDGET, VERIFICA SE ESTÁ REGISTRADO, SE NÃO O REGISTRA */
add_action('widgets_init', create_function('', 'return register_widget("WidgetSeloClimatempo");'));