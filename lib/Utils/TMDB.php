<?php
namespace OCA\TVShowNamer\Utils;



class TMDB {

  private $api_key = "";
  private $base_url = "https://api.themoviedb.org/3/";
  private $cache = array();

  public function __construct($api_key){
    $this->api_key = $api_key;
  }

  /**
  * search the movie database for TV show
  * @param searchTurm $searchTurm you wish to search
  * @since 0.0.1
  * @return results as array
  */
  public function searchTvShow($searchTurm) {
    # https://developers.themoviedb.org/3/search/search-tv-shows
    $perams = array(
      'query' => $searchTurm,
    );
    return $this->api_Fetch('/search/tv', $perams);
  }

  /**
  * get a list of the episodes for a show season
  * @param show $show id for the episode list
  * @param season $season number
  * @param episode $episode number
  * @since 0.1.3
  * @return results as array
  */
  public function getTvShowEpisodes($show, $season, $episode) {
    # https://developers.themoviedb.org/3/tv-seasons/get-tv-season-details
    $perams = array(
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
  * get a detailes of a episodes for a show season
  * @param show $show id for the episode list
  * @param season $season number
  * @param episode $episode number
  * @since 0.0.1
  * @return results as array
  */
  public function getTvShowEpisode($show, $season, $episode) {
    # https://developers.themoviedb.org/3/tv-episodes/get-tv-episode-details
    $perams = array(
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



  /**
  * fehch the data from the TMDB api
  * @param path $url of the api to query
  * @param peramiters $perams in a key => value format, will be phrased in func
  * @since 0.0.1
  * @return results as json
  */

  public function api_Fetch($path, $perams = null) {
    # remove first / if there
    if (substr($path, 0, 1) === '/'){
      $path = substr($path, 1);
    }

    $querystring = '?api_key=' . $this->api_key;
    if ($perams != null){
      foreach ($perams as $key => $value){
        $querystring .= '&' . $key . '=' . urlencode($value);
      }
    }
    $json = file_get_contents($this->base_url . $path . $querystring);
    return json_decode($json, true);
  }

}
