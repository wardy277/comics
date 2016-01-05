<?php
include(dirname(__FILE__)."/../settings.php");

//get not scored where there are issues, ordered by newest issue
$sql = "SELECT v.name, i.id, i.issue_number, i.site_detail_url, score
			FROM volumes v
			JOIN issues i ON i.volume_id = v.comicvine_id
			WHERE score IS NULL
			ORDER BY i.date_last_updated";

foreach($db->getArray($sql) as $row){
	echo "\n{$row['name']} - {$row['issue_number']}' ";
	$review = $row['site_detail_url'];

	//shoudl already have sure details url so neo ned to excessively scrape
	//$issue = $comicvine->getIssue($issue_id);
	//$review = $issue['has_staff_review']['site_detail_url'];

	//$review_score = $comicvine->getIssueReview($review);

	//dangerous as can get blacked. try and pretend to be a human
	echo " ".(rand(50, 300)/100);
}

//done - be nice to a terminal
echo "\n\n";