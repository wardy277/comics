<?php
$filename = dirname(dirname(__FILE__))."/lists/dcyou.list";

//setup comic api
$comicvine = new ComicVine();

foreach(file($filename) as $comic){
	$comic = trim($comic);
	echo "<br />Search for $comic";

	if($comic != 'Bizarro'){
		//continue;
	}

	//search for comics on list
	$results = $comicvine->searchVolumes($comic);
	$result_html = array();

	$found = false;

	foreach($results as $result){
		$comic_id = $result['id'];
		$name = $result['name'];


		if($name == $comic){
			echo "<br /> - found $name == $comic";
			//found what we are looking for
			$found = true;

			//get isues as this stores in DB
			$comicvine->listIssues($comic_id, true);
			//break;
		}
	}

	if(!$found){
		echo " - <strong>not found</strong>";
	}

	if($i++ > 10){
		echo "<br />";
		sleep(1);
		$i=0;
	}
}

