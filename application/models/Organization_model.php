<?php
class Organization_model  extends CI_Model  {

	function __construct()
    {
        parent::__construct();
    }

    function checkPermission ($userID, $module){
        $this->db->select($module);
        $return = $this->db->get_where ('organizationEmployees',array (
        'id' => $userID))->row_array();
        if (empty ($return ) ) {
    		return false ;
    	}else {
    		return $return ;
    	}

    }

    function authenticate ( $email , $password,$table)
    {
    	$password = md5 ( $password ) ;
    	$return = array();
        if($table == 'organizations'){
            $return = $this->db->get_where ( 'organizations' , array (
        			'email' => $email ,
        			'password' => $password
        	))->row_array();
        }else{
            $this->db->select('organizationEmployees.*,organizations.DayToExpire,organizations.organization_name');
            $this->db->from('organizationEmployees');
            $this->db->join('organizations', 'organizations.id = organizationEmployees.organization_id', 'left');
            $this->db->where(array ('organizationEmployees.email' => $email,'organizationEmployees.password' => $password));
            $return = $this->db->get()->row_array();
        }

//        echo $this->db->last_query();
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
    	$return = $this->db->get_where ( 'organizations' , array (
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


    function checkAdmin ($userID){

        $this->db->select('type');
        $return = $this->db->get_where ('organizationEmployees',array (
            'id' => $userID))->row_array();

        if (empty ($return ) )
        {
            return false ;
        }else
        {
            return $return ;
        }
    }



}
