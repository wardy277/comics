<?php

/**
 * Created by PhpStorm.
 * User: chris
 * Date: 03/01/16
 * Time: 20:53
 */
class tString{

	public static function wordLength($string, $num){

		$array = explode(" ", strip_tags($string));

		$snippit = array_slice($array, 0, $num);

		$output = implode(" ", $snippit);

		if(count($array) > $num){
			$output .= "...";
		}

		return $output;
	}

}