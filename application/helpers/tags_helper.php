<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('get_children_tags'))
{
	function get_children_tags ($arr , $parent )
	{
		$my_return = array () ;
		foreach ( $arr as $e )
		{
			if ( $e['parent_tag_id'] == $parent)
			{
				$my_return[] = $e ;
			}
		}
		return $my_return;
	}
}
if ( ! function_exists('push_children_tags'))
{
	function push_children_tags ( $original_arr , $arr , $parent )
	{
		$my_return = $arr;
		foreach ( $original_arr as $e )
		{
			if ( $e['parent_tag_id'] == $parent)
			{
				array_push ( $my_return , $e);
			}
		}
		return $my_return;
	}
}
if ( ! function_exists('get_root_tags'))
{
	function get_root_tags ( $arr )
	{

		foreach ( $arr as $e )
		{
			if ( $e['parent_tag_id'] == NULL || trim($e['parent_tag_id']) == '' )
			{
				return  $e;
			}
		}
		return array () ;
	}
}
if ( ! function_exists('draw_tags_in_data'))
{
	function draw_tags_in_data ( $node  , $arr)
	{
		echo "{'text': '".$node['tag_name']."'";
		$children =get_children_tags ( $arr , $node['id']) ;

		if ( !empty ( $children))
		{
			$i = 0 ;
			echo ",'children' : [";
			$len = count ( $children ) ;
			for  ($i = 0 ; $i < $len ;$i ++  ) {
				if ( $i == $len -1)
				{
					echo draw_tags_in_data ( $children[$i] , $arr ) ;
				}else
				{
					echo draw_tags_in_data ( $children[$i] , $arr ).',' ;
				}
			}
			echo "]";
		}
		echo "}";
	}

}

if ( ! function_exists('draw_tags_in_list'))
{
	function draw_tags_in_list ( $node  , $arr)
	{
		$children =get_children_tags ( $arr , $node['id']) ;
		echo "<li id='tags_{$node['id']}'>".$node['tag_name'];


		if ( !empty ( $children))
		{
			$i = 0 ;
			echo "<ul>";
			$len = count ( $children ) ;
			for  ($i = 0 ; $i < $len ;$i ++  ) {

					echo draw_tags_in_list ( $children[$i] , $arr ) ;

			}
			echo "</ul>";
		}
		echo "</li>";
	}

}


if ( ! function_exists('draw_tags_in_list_h'))
{
	function draw_tags_in_list_h ( $node  , $arr)
	{
		$children =get_children_tags ( $arr , $node['id']) ;
		if(!empty($children)){
			echo "<li  class='parentdd' id='{$node['id']}'>".$node['tag_name'];
		}else{
			echo "<li  id='{$node['id']}'>".$node['tag_name'];
		}

		if ( !empty ( $children))
		{
			$i = 0 ;
			echo "<ul>";
			$len = count ( $children ) ;
			for  ($i = 0 ; $i < $len ;$i ++  ) {

					echo draw_tags_in_list_h ( $children[$i] , $arr ) ;

			}
			echo "</ul>";
		}
		echo "</li>";
	}

}
