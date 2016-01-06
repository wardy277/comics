<?php
include(dirname(__FILE__)."/../settings.php");

//setup comic api
$comicvine = new ComicVine();

$base_url = "http://www.comicbookresources.com/comic-review/";

//get not scored where there are issues, ordered by newest issue
$sql = "SELECT v.name, i.id, i.issue_number, i.site_detail_url
			FROM volumes v
			JOIN issues i ON i.volume_id = v.comicvine_id
			WHERE score IS NULL OR score = -1
			ORDER BY i.date_last_updated DESC";

foreach($db->getArray($sql) as $row){

	$comic = str_replace(' ', '-', strtolower($row['name']));
	$issue = $row['issue_number'];

	$search = "$comic-$issue-dc-comics";

	$url = $base_url.$search;

	echo "\n$url";
	$raw = file_get_contents($url);

	//e.g <meta itemprop="rating" content="3.0">
	$raw   = explode('<meta itemprop="rating" content="', $raw);
	$raw   = explode('"', $raw[1]);
	$review_score = current($raw);

	if(empty($review_score)){
		//not yet reviewed flag for later on
		$review_score = -1;
	}

	$db->update('issues', array('score'=>$review_score),  array('id'=>$row['id']));

	echo ": $review_score";

}