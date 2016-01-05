<?php
$volume = $_GET['volume'];

$comicvine = new ComicVine();

$store = $_GET['store']?true:false;

$results     = $comicvine->listIssues($volume, $store);
$result_html = array();

foreach($results as $result){
	$api_detail_url = $result['api_detail_url'];
	$issue_id       = explode('/', $api_detail_url);
	$issue_id       = $issue_id[ count($issue_id) - 2 ];

	$url = "/issue.php?issue_id=".$issue_id;

	//build title
	$title = "#".$result['issue_number']." ".$result['name']." (".$result['id'].")";

	/* too heavy
	//get issue data
	$issue = $comicvine->getIssue($issue_id);
	//get review score
	$review = $issue['has_staff_review']['site_detail_url'];
	if($review){
		$score = $comicvine->getIssueReview($review);

		$title .= "<span class='right'>score: $score</span>";
	}
	*/

	//build content
	$content = "<div class='row'>
					<a href='$url' class='col-md-4' target='_blank'><img src='{$result['image']['thumb_url']}' /></a>
					<div class='col-md-8'>".tString::wordLength($result['description'], 40)."</div>
				</div>";

	$result_html[ $result['issue_number'] ] .= Bootstrap::panel($title, $content, false, 'col-md-3');

}

//newest first
krsort($result_html);

?>

<div class="row">
	<?php

	foreach($result_html as $html){
		echo $html;
	}

	?>

</div>