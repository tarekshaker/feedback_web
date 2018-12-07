<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('get_children'))
{
	function get_children ($arr , $parent )
	{
		$my_return = array () ;
		foreach ( $arr as $e )
		{
			if ( $e['parent_tree_id'] == $parent)
			{
				$my_return[] = $e ;
			}
		}
		return $my_return;
	}
}
if ( ! function_exists('push_children'))
{
	function push_children ( $original_arr , $arr , $parent )
	{
		$my_return = $arr;
		foreach ( $original_arr as $e )
		{
			if ( $e['parent_tree_id'] == $parent)
			{
				array_push ( $my_return , $e);
			}
		}
		return $my_return;
	}
}
if ( ! function_exists('get_root'))
{
	function get_root ( $arr )
	{

		foreach ( $arr as $e )
		{
			if ( $e['parent_tree_id'] == NULL || trim($e['parent_tree_id']) == '' )
			{
				return  $e;
			}
		}
		return array () ;
	}
}
if ( ! function_exists('tree_or'))
{
	function tree_or ( $arr  )
	{
		foreach ( $arr as $a )
		{
			if ( $a == true  )
			{
				return true ;
			}
		}
		return false ;
	}
}

if ( ! function_exists('search_tree'))
{
	function search_tree ( $arr , $current , $id )
	{
		
		if ( $id == $current ){
			return true ; 
		}else 
		{
			$children = get_children($arr , $current);
			if ( empty ( $children ) ) 
			{
				return false ; 
			}
			$new_arr = array () ; 
			foreach ($children as $c ) 
			{
				$new_arr[] = search_tree($arr , $c['id'] , $id) ; 
			} 
			return tree_or  ( $new_arr ) ; 
		}
	}
}
if ( ! function_exists('search_tree_array'))
{
	function search_tree_array ( $arr , $selected , $current )
	{

		if ( in_array ($current, $selected) ){
			return true ;
		}else
		{
			$children = get_children($arr , $current);
			if ( empty ( $children ) )
			{
				return false ;
			}
			$new_arr = array () ;
			foreach ($children as $c )
			{
				$new_arr[] = search_tree_array($arr , $selected , $c['id']) ;
			}
			return tree_or  ( $new_arr ) ;
		}
	}
}
if ( ! function_exists('get_tree_item_by_id'))
{
	function get_tree_item_by_id ( $arr , $id )
	{

		foreach ( $arr as $e )
		{
			if ( $e['id'] == $id )
			{
				return  $e;
			}
		}
		return array () ;
	}
}
if ( ! function_exists('draw_tree_in_data'))
{
	function draw_tree_in_data ( $node  , $arr)
	{
		echo "{'text': '".$node['tree_name']."'";
		$children =get_children ( $arr , $node['id']) ;

		if ( !empty ( $children))
		{
			$i = 0 ;
			echo ",'children' : [";
			$len = count ( $children ) ;
			for  ($i = 0 ; $i < $len ;$i ++  ) {
				if ( $i == $len -1)
				{
					echo draw_tree_in_data ( $children[$i] , $arr ) ;
				}else
				{
					echo draw_tree_in_data ( $children[$i] , $arr ).',' ;
				}
			}
			echo "]";
		}
		echo "}";
	}

}

if ( ! function_exists('draw_tree_in_list'))
{
	function draw_tree_in_list ( $node  , $arr)
	{
		$children =get_children ( $arr , $node['id']) ;
		echo "<li id='tree_{$node['id']}'>".$node['tree_name'];


		if ( !empty ( $children))
		{
			$i = 0 ;
			echo "<ul>";
			$len = count ( $children ) ;
			for  ($i = 0 ; $i < $len ;$i ++  ) {

					echo draw_tree_in_list ( $children[$i] , $arr ) ;

			}
			echo "</ul>";
		}
		echo "</li>";
	}

}

if ( ! function_exists('draw_tree_in_list_h'))
{
	function draw_tree_in_list_h ( $node  , $arr)
	{
		$children =get_children ( $arr , $node['id']) ;

		if(!empty($children)){
			echo "<li  class='parentdd' id='{$node['id']}'>".$node['tree_name'];
		}else{
			echo "<li  id='{$node['id']}'>".$node['tree_name'];
		}

		if ( !empty ( $children))
		{
			$i = 0 ;
			echo "<ul>";
			$len = count ( $children ) ;
			for  ($i = 0 ; $i < $len ;$i ++  ) {

					echo draw_tree_in_list_h ( $children[$i] , $arr ) ;

			}
			echo "</ul>";
		}
		echo "</li>";
	}

}
if ( ! function_exists('draw_tree_in_list_checkbox'))
{
	function draw_tree_in_list_checkbox ( $node  , $arr)
	{
		$children =get_children ( $arr , $node['id']) ;
		echo "<li id='tree_{$node['id']}'>".$node['tree_name'];
		echo "<input type='hidden' name='tree_checked[]' value='{$node['id']}' />";

		if ( !empty ( $children))
		{
			$i = 0 ;
			echo "<ul>";
			$len = count ( $children ) ;
			for  ($i = 0 ; $i < $len ;$i ++  ) {

				echo draw_tree_in_list_checkbox ( $children[$i] , $arr ) ;

			}
			echo "</ul>";
		}
		echo "</li>";
	}

}

