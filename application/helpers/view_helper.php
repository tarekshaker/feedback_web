<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('admin_view'))
{
    function admin_view($view = null , $data = null)
    {
    	$CI = get_instance();
    	

    	$CI->load->view ( 'admin/includes/header' , $data );
    	$CI->load->view ( $view , $data );
    	$CI->load->view ( 'admin/includes/footer' , $data );
    }
}
