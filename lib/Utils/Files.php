<?php
namespace OCA\TVShowNamer\Utils;

use OC\Files\Filesystem;
use OCP\Files\FileInfo;
use OC\Files\Node\File;

use OCA\TVShowNamer\Utils\TMDB;

class Files {

  /**
  * retern all files in the folder and do it recursive
  * @param results $results use the & to preserve the results
  * @param path $path to scan
  * @param type $type array to filter the tiles returned
  * @since 0.0.1
  * @return results of search
  */

  public function getFilesRecursive($path , & $results = [], $type = []) {
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

  public function getFile(File $file) {
    $results[] = array(
          'file_id' => $file->getId(),
          'name' => $file->getName(),
          'file' => $path . '/' . $file->getName(),
          'path' => $path,
          'mimetype' => $file->getMimePart(),
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

  public function matchFilesToEpisodes(&$collection, &$TMDB) {
    # loop though all the files in the collection
    for($i = 0; $i < count($collection['files']); ++$i) {
      #decode the file name
      $name = $collection['files'][$i]['name'];
      $path = $collection['files'][$i]['path'];
      # phrase the filename
      # yeh, just a simple regex ATM
      preg_match('/^(?P<seriesname>.+?)?[ \._\-]?[Ss](?P<seasonnumber>[0-9]+)[\.\- ]?[Ee]p?(?P<episodenumberstart>[0-9]+)[\.\- ]?(?P<episodename>.+?)?\.(?P<extention>[^.]{3})$/',
                  $name,
                  $matches);
      # add info to filelist
      $collection['files'][$i]['seriesname'] = $matches['seriesname'];
      $collection['files'][$i]['seasonnumber'] = $matches['seasonnumber'];
      $collection['files'][$i]['episodenumberstart'] = $matches['episodenumberstart'];
      $collection['files'][$i]['episodename'] = $matches['episodename'];
      $collection['files'][$i]['ext'] = $matches['extention'];
      $collection['files'][$i]['new_name'] = '';

      # get the episode list from tmdb
      $epilist = $TMDB->getTvShowEpisodes($collection['show_info']['id'], $matches['seasonnumber']);
      $episode_number = -1;
      for($e = 0; $e < count($epilist['episodes']); ++$e) {
        if ((int)$epilist['episodes'][$e]['episode_number'] == (int)$matches['episodenumberstart']){
          $episode_number = $e;
          break;
        }
      }
      # ok we have the episode index
      if ($episode_number > -1){
        $collection['files'][$i]['new_name'] = self::filePathEncode($collection['show_info']['name'], $matches['seasonnumber'], $matches['episodenumberstart'], $epilist['episodes'][$episode_number]['name'], $matches['extention']);
      }
    }


  }

  /**
  * build a filename from the info avalable
  * @param title $title of the show
  * @param season $season_number of the file
  * @param episode $episode_number of the file
  * @param name $name of the episode
  * @param ext $extention of the file
  * @since 0.0.1
  * @return file named a directed
  */
  public function filePathEncode($title, $season_number, $episode_number, $episode_name, $file_ext){
    $season_number_padded = substr('0'.$season_number, -2);
    $episode_number_padded = substr('0'.$episode_number, -2);
    $title = self::sanitizeString($title,'');
    $episode_name = self::sanitizeString($episode_name,'');
    return 'S'. $season_number_padded . 'E' . $episode_number_padded . ' - ' . $episode_name . '.' . $file_ext;
  }

  /**
  * sanatize the string for renameing as a filename
  * @param str $string to be sanitized
  * @param replace $replace charicter to replace the iligal char with
  * @since 0.0.1
  * @return string sanitized
  */
  public function sanitizeString($string, $replace = ''){
    return preg_replace('/[\/\\?*<>"\':;\|]/', $replace, $string);
  }
  /**
  * Function to check string startingwith given substring
  * @param string $string to be check
  * @param startString $startString the start of the string to check
  * @since 0.0.1
  * @return Bool
  */
  function startsWith($string, $startString){
      $len = strlen($startString);
      return (substr($string, 0, $len) === $startString);
  }
  /**
  * Function to remove the firs part of a string
  * @param string $string to be check
  * @param startString $startString the start of the string to remove
  * @since 0.0.1
  * @return string
  */
  function removeStart($string, $startString){
      $len = is_string($startString) ? strlen($startString) : $startString;
      return (substr($string, $len));
  }
}
