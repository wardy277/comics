<?php

/**
 * Created by PhpStorm.
 * User:
 * Date: 03/01/16
 * Time: 20:37
 */
class Bootstrap{

	static function panel($title, $content, $class=false, $wrapping_class=false){
		if($wrapping_class){
			$pre = "<div class='$wrapping_class'>";
			$suffix = "</div>";
		}

		return $pre.'
				<div class="panel panel-default '.$class.'">
					<div class="panel-heading">
						<h3 class="panel-title">'.$title.'</h3>
					</div>
					<div class="panel-body">
						'.$content.'
					</div>
				</div>
			   '.$suffix;
	}

}