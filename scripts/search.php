<?php

$search = $_GET['search'];

$comicvine = new ComicVine();

$results = $comicvine->searchVolumes($search);
$result_html = array();

foreach($results as $result){
	$external_url = str_replace('/api/', '/', $result['site_detail_url']);
	$url = "/issues.php?volume=".$result['id'];

	$title = $result['name']." (".$result['id'].")";

	$date  = new DateTime($result['date_last_updated']);
	$index = $date->getTimestamp();

	$content = "<div class='row'>
					<a href='$url' class='col-md-4' target='_blank'><img src='{$result['image']['thumb_url']}' /></a>
					<div class='col-md-8'>".tString::wordLength($result['description'], 40)."</div>
				</div>";

	$result_html[ $index ] .= Bootstrap::panel($title, $content, false, 'col-md-3');
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
