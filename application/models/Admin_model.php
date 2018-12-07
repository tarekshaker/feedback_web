<?php
class Admin_model  extends CI_Model  {

	function __construct()
    {
        parent::__construct();
    }
    function authenticate ( $username , $password )
    {
    	$password = md5 ( $password ) ;
    	$return = $this->db->get_where ( 'admin_users' , array (
    			'username' => $username ,
    			'password' => $password
    	))->row_array();
    	if (empty ($return ) )
    	{
    		return false ;
    	}else
    	{
    		return $return ;
    	}
    }

    function check_user_exist_by_user_name ( $username )
    {
    	$return = $this->db->get_where ( 'admin_users' , array (
    			'email' => $username ,
    	))->row_array();
    	if (empty ($return ) )
    	{
    		return false ;
    	}else
    	{
    		return $return ;
    	}
    }
}
