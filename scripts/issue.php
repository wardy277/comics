<?php
$issue_id = $_GET['issue_id'];

$comicvine = new ComicVine();

$issue = $comicvine->getIssue($issue_id);

$review = $issue['has_staff_review']['site_detail_url'];

$review_score = $comicvine->getIssueReview($review);

pre_r($review_score);