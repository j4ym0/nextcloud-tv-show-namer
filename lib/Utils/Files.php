<?php
namespace OCA\TVShowNamer\Utils;

use OC\Files\Filesystem;
use OCP\Files\FileInfo;
use OC\Files\Node\File;
use OC\Files\Node\Folder;

use OCA\TVShowNamer\Utils\TMDB;
use OCA\TVShowNamer\Controller\PageController;

class Files {

  /**
  * retern all files in the folder and do it recursive
  * @param results $results use the & to preserve the results
  * @param path $path to scan
  * @param type $type array to filter the tiles returned
  * @since 0.0.1
  * @return results of search
  */

  public static function getFilesRecursive($path , & $results = [], $type = []) {
    $files = Filesystem::getDirectoryContent($path);
    foreach($files as $file) {
      if ($file->getType() === FileInfo::TYPE_FOLDER) {
        self::getFilesRecursive($path . '/' . $file->getName(), $results, $type);
     } elseif ($type === [] | in_array($file->getMimePart(), $type)) {
        $results[] = array(
          'file_id' => $file->getId(),
          'name' => $file->getName(),
          'file' => $path . '/' . $file->getName(),
          'path' => $path,
          'mimetype' => $file->getMimePart(),
        );
      }
    }

    return $results;
  }

  /**
  * retern file info
  * @param path $path of the file
  * @since 0.0.1
  * @return results of search
  */

  public static function getFile(File $file, $userHome) {
    $path = $file->getParent()->getPath();
    #remove the front folder of the user
    $path = Files::startsWith($path, $userHome->getPath()) ? Files::removeStart($path, $userHome->getPath()) : $path;
    $results[] = array(
          'file_id' => $file->getId(),
          'name' => $file->getName(),
          'file' => $path . '/' . $file->getName(),
          'path' => $path,
          'mimetype' => $file->getMimePart(),
          'ext' => $file->getExtension(),
          'new_name' => $file->getName(),
        );

    return $results;
  }

  /**
  * match the file names to the episode names from TMDB, uses a static var to update the files array
  * @param Data $collection of the show info and files must include the ['show_info', 'files']
  * @param TMDB $tmdb instance
  * @since 0.0.1
  * @return none
  */

  public static function matchFilesToEpisodes(&$collection, &$TMDB, $file_name_structure = null) {
    #check for empty-Collection
    if (is_null($collection['files'])){return false;}
    # loop though all the files in the collection
    for($i = 0; $i < count($collection['files']); ++$i) {
      #decode the file name
      $name = $collection['files'][$i]['name'];
      $path = $collection['files'][$i]['path'];
      # phrase the filename
      # yeh, just a simple regex ATM
      if (preg_match('/^(?P<seriesname>.+?)?[ \._\-]?(?:[Ss]|(?=[0-9]+x))(?:eason|eries)?[\.\-_ ]?(?P<seasonnumber>[0-9]+)[\.\-_ x]?(?:[EexX][Pp]?(?:isode)?[\.\- ]?|[\.\-_ ]+)(?P<episodenumberstart>[0-9]+)[\.\- ]?(?P<episodename>.+?)?\.(?P<extention>[^.]{3})$/',
                  $name,
                  $matches)){
        # add info to filelist
        $collection['files'][$i]['seriesname'] = $matches['seriesname'];
        $collection['files'][$i]['seasonnumber'] = $matches['seasonnumber'];
        $collection['files'][$i]['episodenumberstart'] = $matches['episodenumberstart'];
        $collection['files'][$i]['episodename'] = $matches['episodename'];
        $collection['files'][$i]['ext'] = $matches['extention'];
        $collection['files'][$i]['new_name'] = '';

        # get the episode list from tmdb
        $epilist = $TMDB->getTvShowEpisodes($collection['show_info']['id'], $matches['seasonnumber'], $matches['episodename']);
        $episode_number = -1;
        if (!is_null($epilist['episodes'])){
          for($e = 0; $e < count($epilist['episodes']); ++$e) {
            if ((int)$epilist['episodes'][$e]['episode_number'] == (int)$matches['episodenumberstart']){
              $episode_number = $e;
              break;
            }
          }
        }
        # ok we have the episode index
        if ($episode_number > -1){
          #set the episode name
          $collection['files'][$i]['new_name'] = self::filePathEncode($collection['show_info']['name'], substr($collection['show_info']['first_air_date'],0,4), $matches['seasonnumber'], $matches['episodenumberstart'], $epilist['episodes'][$episode_number]['name'], $matches['extention'], $file_name_structure);
        }
      }else{
        $collection['files'][$i]['seriesname'] = '';
        $collection['files'][$i]['seasonnumber'] = '0';
        $collection['files'][$i]['episodenumberstart'] = '0';
        $collection['files'][$i]['episodename'] = '';
        $collection['files'][$i]['ext'] = '';
        $collection['files'][$i]['new_name'] = '';
      }
    }


  }

  /**
  * build a filename from the info avalable
  * @param title $title of the show
  * @param year $season_year of the file
  * @param season $season_number of the file
  * @param episode $episode_number of the file
  * @param name $name of the episode
  * @param ext $extention of the file
  * @param file_structure $file_structure how the file should be named - Default in PageController
  * @since 0.5.0
  * @version 2
  * @return file named a directed
  */
  public static function filePathEncode($season_name, $season_year, $season_number, $episode_number, $episode_name, $file_ext, $file_structure = null){
    #set default from PageController
    if (null === $file_structure) {
      $file_structure = PageController::$file_name_structure_default;
    }
    $season_number_padded = substr('0'.$season_number, -2);
    $episode_number_padded = substr('0'.$episode_number, -2);
    $season_name = self::sanitizeString($season_name,'');
    $episode_name = self::sanitizeString($episode_name,'');

    #build the episode nome from Settings
    $array = array('{{Series_Name}}' => $season_name,
                  '{{Season_Name}}' => $season_name,
                  '{{Series_Year}}' => $season_year,
                  '{{Season_Year}}' => $season_year,
                  '{{Series_Number}}' => $season_number,
                  '{{Season_Number}}' => $season_number,
                  '{{Series_Number_Padded}}' => $season_number_padded,
                  '{{Season_Number_Padded}}' => $season_number_padded,
                  '{{Episode_Number}}' => $episode_number,
                  '{{Episode_Number_Padded}}' => $episode_number_padded,
                  '{{Episode_Name}}' => $episode_name
    );
    $named_file = self::sanitizeString(str_ireplace(array_keys($array), array_values($array), $file_structure),'') . '.' . $file_ext;

    return $named_file;
  }

  /**
  * sanatize the string for renameing as a filename
  * @param str $string to be sanitized
  * @param replace $replace charicter to replace the iligal char with
  * @since 0.0.1
  * @return string sanitized
  */
  public static function sanitizeString($string, $replace = ''){
    return preg_replace('/[\/\\\?*<>"\':;\|]/', $replace, $string);
  }

  /**
  * Function to check string startingwith given substring
  * @param string $string to be check
  * @param startString $startString the start of the string to check
  * @since 0.0.1
  * @return bool
  */
  static function startsWith($string, $startString){
      $len = strlen($startString);
      return (substr($string, 0, $len) === $startString);
  }

  /**
  * Function to remove the first part of a string
  * @param string $string to be check
  * @param startString $startString the start of the string to remove
  * @since 0.0.1
  * @return string
  */
  static function removeStart($string, $startString){
      $len = is_string($startString) ? strlen($startString) : $startString;
      return (substr($string, $len));
  }

  /**
  * Function to remove everyting after a string
  * @param string $string to be check
  * @param trimAt $startAt where to start the trim
  * @since 0.1.3
  * @return string
  */
  static function removeAfter($string, $startAt){
    if (strpos($string, $startAt) > 0)
      return substr($string, 0, strpos($string, $startAt));
    return $string;
  }
}
