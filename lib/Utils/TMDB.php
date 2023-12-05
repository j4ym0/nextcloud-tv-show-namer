<?php
namespace OCA\TVShowNamer\Utils;

use OCA\TVShowNamer\Utils\Tools;

class TMDB {

  private $api_key = "";
  private $base_url = "https://api.themoviedb.org/3";
  private $cache = array();

  public function __construct($api_key){
    $this->api_key = $api_key;
  }

  /**
  * search the movie database for TV show
  *
  * @param searchTerm $searchTerm you wish to search
  * @param include_year $include_year true or false - added 0.4.2
  * @param lang $lang language to search - added 0.6.0
  * @param show_index $show_index to select - added 1.0.0
  * @since 0.0.1
  * @return results as array
  */
  public function searchTvShow($searchTerm, $include_year, $lang = 'en', $show_index = 0) {
    # https://developers.themoviedb.org/3/search/search-tv-shows

    # try to filter out the year and present it to thetmdb.com
    preg_match('/^(?P<series_name>.*?)[ \._\-]{0,3}(?P<year>19|20[0-9][0-9])?$/',
                $searchTerm,
                $matches);

    # update the search term
    if (isset($matches['series_name'])){
      $searchTerm = $matches['series_name'];
    }
    $params = array(
      'query' => $searchTerm,
      'language' => $lang,
    );

    # add the year to the search
    if (isset($matches['year']) && $include_year){
      $params['first_air_date_year'] = $matches['year'];
    }

    $results = $this->api_Fetch('/search/tv', $params);
    if (!$results['success'] && array_key_exists('success', $results)){
      $data = array(
        'source' => 'tmdb',
        'status_message' => $results['status_message'],
      );
    }elseif ($show_index >= $results['total_results']){
      $data = null;
    }else{
      $data = array(
        'source' => 'tmdb',
        'adult' => $results['results'][$show_index]['adult'],
        'id' => $results['results'][$show_index]['id'],
        'link' => 'https://www.themoviedb.org/tv/' . $results['results'][$show_index]['id'],
        'overview' => $results['results'][$show_index]['overview'],
        'name' => $results['results'][$show_index]['name'],
        'first_air_date' => $results['results'][$show_index]['first_air_date'],
        'img_path' => 'tmdb/image?' . $results['results'][$show_index]['poster_path'],
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
    $params = array(
      'language' => $lang,
    );
    #check cache for results - save recalling the api
    if (!array_key_exists($show.'/'.$season.'/0', $this->cache)){
      $data = $this->api_Fetch('/tv/' . $show . '/season/' . $season, $params);
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
    $params = array(
      'language' => $lang,
    );
    #check cache for results - save recalling the api
    if (!array_key_exists($show.'/'.$season.'/'.$episode, $this->cache)){
      $data = $this->api_Fetch('/tv/' . $show . '/season/' . $season . '/episode/' . $episode, $params);
      $this->cache[$show.'/'.$season.'/'.$episode] = json_encode($data);
      return $data;
    }else{
      return json_decode($show.'/'.$this->cache[$season.'/'.$episode]);
    }
  }



  /**
  * fetch the data from the TMDB api
  * @param path $url of the api to query
  * @param parameters $params in a key => value format, will be phrased in func
  * @since 0.0.1
  * @return results as json
  */

  public function api_Fetch($path, $params = null) {
    # Moved to tools
    if (strlen($this->api_key) > 40){
      return Tools::api_call($this->base_url . $path, null, $this->api_key, $params);
    }else{
      return Tools::api_call($this->base_url . $path, $this->api_key, null, $params);
    }
  }
}
