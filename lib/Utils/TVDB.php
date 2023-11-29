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
  * @param searchTerm $searchTerm you wish to search
  * @param include_year $include_year true or false - added 0.4.2
  * @param lang $lang language to search - added 0.6.0
  * @param show_index $show_index to select - added 1.0.0
  * @since 1.0.0
  * @return results as array
  */
  public function searchTvShow($searchTerm, $include_year, $lang = 'eng', $show_index = 0) {
    # https://developers.themoviedb.org/3/search/search-tv-shows

    $lang = Tools::convert_2_to_3($lang);

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
      'type' => 'series',
    );

    # add the year to the search
    if (isset($matches['year']) && $include_year){
      $params['year'] = $matches['year'];
    }

    $results = Tools::api_call($this->base_url . 'search', null, $this->token, $params);
    if ($results['status'] == 'success' && $results['links']['total_items'] > $show_index){
      $data = array(
        'source' => 'tvdb',
        'adult' => '',
        'id' => $results['data'][$show_index]['id'],
        'overview' => $results['data'][$show_index]['overview'],
        'name' => $results['data'][$show_index]['name'],
        'first_air_date' => $results['data'][$show_index]['first_air_time'],
        'img_path' => 'tvdb/image' . str_replace('https://artworks.thetvdb.com/banners/posters/', '/', $results['data'][$show_index]['image_url']),
        'total_results' => $results['links']['total_items'],
      );
      if (isset($results['data'][$show_index]['overviews'][$lang])){
        $data['overview'] = $results['data'][$show_index]['overviews'][$lang];
      }
      if (isset($results['data'][$show_index]['translations'][$lang])){
        $data['name'] = $results['data'][$show_index]['translations'][$lang];
      }
    }else{
      $data = null;
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



}
