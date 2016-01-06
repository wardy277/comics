<?php
include(dirname(__FILE__)."/../settings.php");

$page_size = 150;
$max_offset = 2600;

$max_page = $max_offset/$page_size;

$page = 0;

//cache comics
$comic_cache = array();
$sql = "SELECT v.name, i.id, i.issue_number
		FROM volumes v
		JOIN issues i ON i.volume_id = v.comicvine_id
		WHERE score IS NULL OR score = -1";
foreach($db->getArray($sql) as $row){
	$comic_cache[$row['name']][$row['issue_number']] = $row['id'];
}

while($page <= $max_page){
	$offset = $page * $page_size;
	$url = "http://www.comicbookresources.com/?page=review_srch&by=pubid&for=6&offset=$offset&sort=dateadded+desc&v=l&max_per_page=$page_size";

	//echo $url;

	$cache = "/tmp/".urlencode($url);
	if(!file_exists($cache)){
		echo "rebuilding ache";
		$file_contents = file_get_contents($url);

		if(!$file_contents){
			echo "<h1>Temorarily blocked</h1>";
			exit;
		}

		file_put_contents($cache, $file_contents);
	}

	$file_contents = file_get_contents($cache);

	//e.g <tr class="list-comic">
		//<td class="list-comic-details">
			//<h3><a href="/comic-review/justice-league-47-dc-comics">Justice League #47</a></h3>
        //<td class="list-comic-rating">
			/*
			 <ul class="grid-reviews-rating">

                        <li><img src="/assets/images/star-full-sm.png"></li>

                        <li><img src="/assets/images/star-full-sm.png"></li>

                        <li><img src="/assets/images/star-full-sm.png"></li>

                        <li><img src="/assets/images/star-full-sm.png"></li>

                        <li><img src="/assets/images/star-empty-sm.png"></li>

                    </ul>
			 */
	$comic_html   = explode('<tr class="list-comic">', $file_contents);
	array_shift($comic_html);

	foreach($comic_html as $html){
		//get title
		$array = explode('<td class="list-comic-details">', $html);
		$array = explode('<h3>', $array[1]);
		$array = explode('</h3>', $array[1]);

		$array = explode("#", strip_tags($array[0]));

		$title = trim($array[0]);
		$issue_number = $array[1];


		//get score
		$array = explode('<ul class="grid-reviews-rating">', $html);
		$array = explode('</ul>', $array[1]);
		$score_raw = $array[0];

		$full = substr_count($score_raw, 'full');
		$half = substr_count($score_raw, 'half');

		//work out score (including the .5s)
		$score = $full + ($half/2);

		if(!isset($comic_cache[$title][$issue_number])){
			$title = str_replace(" and ", "/", $title);
		}

		echo "\n'$title' $issue_number: $score";

		//search for comic
		if(isset($comic_cache[$title][$issue_number])){
			echo "found";
			$db->update('issues', array('score'=>$score),  array('id'=>$comic_cache[$title][$issue_number]));
		}
		else{
			echo "missing";
		}


	}

	$page++;
	echo "\n";
}

