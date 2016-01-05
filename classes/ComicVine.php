<?php

/**
 * Created by PhpStorm.
 * User: ico3
 * Date: 29/05/15
 * Time: 15:03
 */
class ComicVine extends Entity{
	private static $domain = "http://api.comicvine.com/";
	private static $urls = array(
		'search' => '/search',
	);

	//todo - impliment config for psotersizes and location etc: http://api.themoviedb.org/3/configuration?api_key=ce842a1d45f50cd3de2acc09a6ec771f

	public function __construct($data = false){
		if(!$data){
			global $settings;
			$data = array('api_key' => $settings['comicvine_api_key']);
		}

		return parent::__construct($data);
	}

	public function getUrl($type){
		if(!array_key_exists($type, self::$urls)){
			$type = 'search';
		}

		$url = self::$domain.self::$urls[ $type ]."?api_key=".$this->getApiKey();

		return $url;
	}

	/**
	 * Shortcut faction to pull data from themoviedb as an array
	 * @param $path
	 * @param array $data
	 * @return bool
	 */
	private function queryArray($path, $query = array(), $filter = array()){
		//set api key
		$query['api_key'] = $this->getApiKey();

		//always set as json
		$query['format'] = "json";

		if($filter){
			foreach($filter as $field => $value){
				$query['filter'] .= "$field:$value";

			}
		}

		//build as a url
		$url = self::$domain.$path.'?'.http_build_query($query);

		echo $url;

		//$file_contents = tor_get_contents($url);
		$enable_cache  = true;

		if($enable_cache){
			//cache url
			$cache = "/tmp/".urlencode($url);
			if(!file_exists($cache)){
				echo "rebuilding ache";
				$file_contents = file_get_contents($url);

				if(!$file_contents){
					echo "<h1>Temorarily blocked</h1>";

					return false;
				}

				file_put_contents($cache, $file_contents);
			}

			$file_contents = file_get_contents($cache);
		}


		if(!$file_contents){
			return false;
		}

		//parse results
		$data = json2array($file_contents);

		if($_GET['debug'] == 1){
			pre_R($data);
			exit;
		}

		return $data;

	}


	public function searchVolumes($search, $store=false){
		global $db;

		//human readable url encoded string
		$search = str_replace(" ", "+", $search);

		$query = array(
			'query'     => $search,
			'resources' => 'volume'
		);

		//get as json
		$data = $this->queryArray('search', $query);

		//store volumes in database
		if($store){
			foreach($data['results'] as $result){
				$result['comicvine_id'] = $result['id'];
				$result['thumb_url']    = $result['image']['thumb_url'];
				unset($result['id']);
				$db->insert('volumes', $result, true);
			}
		}

		return $data['results'];
	}

	public function listIssues($volume_id, $store=false){
		global $db;

		$filter = array(
			'volume' => $volume_id,
		);

		//get as json
		$data = $this->queryArray('issues', false, $filter);

		//store volumes in database
		if($store){
			foreach($data['results'] as $result){
				$api_detail_url = $result['api_detail_url'];

				//comicvine id is preceded by a reference id of some sort
				$issue_id = explode('/', $api_detail_url);
				$issue_id = $issue_id[ count($issue_id) - 2 ];

				$result['comicvine_id'] = $issue_id;
				$result['volume_id']    = $volume_id;
				$result['thumb_url']    = $result['image']['thumb_url'];
				unset($result['id']);

				$db->insert('issues', $result, true);
			}
		}

		return $data['results'];
	}

	public function getIssue($issue_id){
		//get as json
		$data = $this->queryArray('issue/'.$issue_id);


		return $data['results'];

	}

	public function getIssueReview($review_id, $full = false){
		//todo - use api to get this data, then full or just score can be returned

		$raw   = file_get_contents($review_id);
		$raw   = explode('<span class="average-score">', $raw);
		$raw   = explode(" ", $raw[1]);
		$score = current($raw);

		return $score;
	}

	/**
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 */
	public function getShows($type = 'all'){
		$url = $this->getUrl($type);

		$file_contents = file_get_contents($url);

		if(empty($file_contents)){
			return false;
		}

		$json = json2array($file_contents);

		//alternative image: poster_path
		foreach($json['results'] as $show){
			$show_details = array(
				'show_id' => $show['id'],
				'title'   => $show['name'],
				'image'   => $show['backdrop_path'],
				'country' => $show['origin_country'][0],
			);

			$shows[] = new Entity($show_details);
		}

		return $shows;
	}

	public function getAllShows(){
		return $this->getShows('all');
	}

	public function getCurrentShows(){
		return $this->getShows('current');
	}

	public function searchShows($search){
		$data = array('query' => $search);

		$search_results = $this->queryArray('/search/tv', $data);

		$shows = array();

		foreach($search_results['results'] as $show_details){
			$data    = array(
				'show_id' => $show_details['id'],
				'image'   => $this->getImage($show_details['poster_path']),
				'name'    => $show_details['name']
			);
			$shows[] = new Entity($data);
		}

		return $shows;
	}

	public function getShow($show_id){

		$url = self::$domain."/tv/$show_id?api_key=".$this->getApiKey();

		$file_contents = file_get_contents($url);

		if(!$file_contents){
			return false;
		}

		$show = json2array($file_contents);

		//todo - handle 404
		$air_date = new DateTime($show['last_air_date']);

		$num_seasons = 0;
		foreach($show['seasons'] as $season){
			if($season['season_number'] > $num_seasons){
				$num_seasons = $season['season_number'];
			}
		}

		$show_details = array(
			'show_id'     => $show['id'],
			'title'       => $show['name'],
			'image'       => $show['backdrop_path'],
			'country'     => $show['origin_country'][0],
			'air_day'     => $air_date->format('l'),
			'num_seasons' => $num_seasons,
		);

		return $show_details;
	}

	/**
	 * Show id needs to be the api id
	 * @param $show_id
	 * @return array
	 */
	public function getEpisodes($show_id, $type = 'all'){
		//get show info

		///tv/{id}
		$show_details = $this->queryArray("/tv/$show_id");

		//skip on failure
		if(!$show_details){
			return false;
		}

		if($type == 'latest'){
			$num_seasons = 0;
			foreach($show_details['seasons'] as $season){
				if($season['season_number'] > $num_seasons && $season['episode_count'] > 0){
					$num_seasons = $season['season_number'];
					$seasons     = array($season);
				}
			}
		}
		else{
			$seasons = $show_details['seasons'];
		}

		foreach($seasons as $season_info){
			$season       = $season_info['season_number'];
			$num_episodes = $season_info['episode_count'];

			if($season > 0){
				//get season info
				for($episode = 1; $episode <= $num_episodes; $episode ++){
					//get data
					///tv/{id}/season/{season_number}/episode/{episode_number}
					$episode_details = $this->queryArray("/tv/$show_id/season/$season/episode/$episode");

					//skip on failure
					if(!$episode_details){
						continue;
					}

					//convert to our format
					$data = array(
						'season'     => $season,
						'episode'    => $episode,
						'title'      => $episode_details['name'],
						'aired_date' => $episode_details['air_date'],
						'rating'     => $episode_details['rating'],
						'image'      => $episode_details['still_path'],
					);

					//$episodes[] = new Entity($data);
					$episodes[] = $data;

				}
			}
		}

		return $episodes;
	}


	public function getImage($image){
		//todo - get and cahe this data from http://api.themoviedb.org/3/configuration
		return "http://image.tmdb.org/t/p/w154/".$image;

	}

	/**
	 * Return api id
	 * @return int
	 */
	public static function getApiId(){
		//tdo get id from apis table where code is apitype
		return 2;
	}
}
