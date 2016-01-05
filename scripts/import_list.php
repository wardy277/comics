<?php
$filename = dirname(dirname(__FILE__))."/lists/dcyou.list";

//setup comic api
$comicvine = new ComicVine();

foreach(file($filename) as $comic){
	$comic = trim($comic);
	echo "<br />Search for $comic";

	//search for comics on list
	$results = $comicvine->searchVolumes($comic);
	$result_html = array();

	$found = false;

	foreach($results as $result){
		$comic_id = $result['id'];
		$name = $result['name'];

		if($name == $comic){
			//found what we are looking for
			$found = true;
			$results     = $comicvine->listIssues($comic_id);
			break;
		}
	}

	if(!$found){
		echo " - not found";
	}

	if($i++ > 10){
		echo "<br />";
		sleep(1);
		$i=0;
	}
}

