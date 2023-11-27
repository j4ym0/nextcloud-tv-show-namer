<?php
namespace OCA\TVShowNamer\Utils;

use OCA\TVShowNamer\Utils\Tools;

class TVDB {

  public $token = "";
  private $key = "";
  public $api_msg = "";
  private $base_url = "https://api4.thetvdb.com/v4/";
  private $cache = array();

  public function __construct($api_key, $token = null){
    $this->key = $key;
    if ($token == null){
      $data = array(
        'apikey' => $api_key,
        'pin' => '',
      );
  
      $token_data = Tools::api_call($this->base_url . 'login', null, null, null, $data, 'json');
      if ($token_data['status'] == 'success'){
        $this->token = $token_data['data']['token'];
      }else{
        $this->api_msg = $token_data['message'];
      }
    }else{
      $this->token = $token;
    }
  }

  /**
  * search the tv database for TV show
  *
  * @param searchTurm $searchTurm you wish to search
  * @param include_year $include_year ture or false - added 0.4.2
  * @param lang $lang language to search - added 0.6.0
  * @param show_index $show_index to select - added 1.0.0
  * @since 0.0.1
  * @return results as array
  */
  public function searchTvShow($searchTurm, $include_year, $lang = 'en', $show_index = 0) {
    # https://developers.themoviedb.org/3/search/search-tv-shows

    # try to filter out the year and present it to thetmdb.com
    preg_match('/^(?P<seriesname>.*?)[ \._\-]{0,3}(?P<year>19|20[0-9][0-9])?$/',
                $searchTurm,
                $matches);

    # update the search turm
    if (isset($matches['seriesname'])){
      $searchTurm = $matches['seriesname'];
    }
    $perams = array(
      'query' => $searchTurm,
      'language' => $lang,
    );

    # add the year to the search
    if (isset($matches['year']) && $include_year){
      $perams['first_air_date_year'] = $matches['year'];
    }

    $results = $this->api_Fetch('/search/tv', $perams);
    if ($show_index >= $results['total_results']){
      $data = null;
    }else{
      $data = array(
        'source' => 'tmdb',
        'adult' => $results['results'][$show_index]['adult'],
        'id' => $results['results'][$show_index]['id'],
        'overview' => $results['results'][$show_index]['overview'],
        'name' => $results['results'][$show_index]['name'],
        'first_air_date' => $results['results'][$show_index]['first_air_date'],
        'img_path' => 'tmdb/image' . $results['results'][$show_index]['poster_path'],
        'total_results' => $results['total_results'],
      );
    }
    return $data;
  }

  /**
  * get a list of the episodes for a show season
  * @param show $show id for the episode list
  * @param season $season number
  * @param episode $episode number
  * @param lang $lang language to search - added 0.6.0
  * @since 0.1.3
  * @return results as array
  */
  public function getTvShowEpisodes($show, $season, $episode, $lang = 'en') {
    # https://developers.themoviedb.org/3/tv-seasons/get-tv-season-details
    $perams = array(
      'language' => $lang,
    );
    #check cache for results - save recalling the api
    if (!array_key_exists($show.'/'.$season.'/0', $this->cache)){
      $data = $this->api_Fetch('/tv/' . $show . '/season/' . $season, $perams);
      $this->cache[$show.'/'.$season.'/'.$episode] = json_encode($data);
      return $data;
    }else{
      return json_decode($this->cache[$show.'/'.$season.'/0']);
    }
  }

  /**
  * get a details of a episodes for a show season
  * @param show $show id for the episode list
  * @param season $season number
  * @param episode $episode number
  * @param lang $lang language to search - added 0.6.0
  * @since 0.0.1
  * @return results as array
  */
  public function getTvShowEpisode($show, $season, $episode, $lang = 'en') {
    # https://developers.themoviedb.org/3/tv-episodes/get-tv-episode-details
    $perams = array(
      'language' => $lang,
    );
    #check cache for results - save recalling the api
    if (!array_key_exists($show.'/'.$season.'/'.$episode, $this->cache)){
      $data = $this->api_Fetch('/tv/' . $show . '/season/' . $season . '/episode/' . $episode, $perams);
      $this->cache[$show.'/'.$season.'/'.$episode] = json_encode($data);
      return $data;
    }else{
      return json_decode($show.'/'.$this->cache[$season.'/'.$episode]);
    }
  }



}
