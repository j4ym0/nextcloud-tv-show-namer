<?php
namespace OCA\TVShowNamer\Controller;

use OCP\IRequest;
use OCP\IConfig;
use OCP\IInitialStateService;

use OCA\TVShowNamer\AppInfo\Application;
use OCA\TVShowNamer\Utils\Files;
use OCA\TVShowNamer\Utils\TMDB;

use OCP\AppFramework\Http\StreamResponse;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;
use OCP\Files\Node;
use OCP\Files\Folder;
use OCP\Files\IRootFolder;


class PageController extends Controller {
	private $userId;
	private $config;
	private $rootFolder;
	private $initialStateService;
	private $postdata;
	private $TMDB;

	public function __construct($AppName, IRequest $request,
                                IConfig $Config,
																IRootFolder $rootFolder,
																IInitialStateService $initialStateService,
								 							$UserId){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->config = $Config;
		$this->rootFolder = $rootFolder;
		$this->initialStateService = $initialStateService;
		$this->postdata = json_decode(file_get_contents("php://input"));
		$this->TMDB = new TMDB($this->config->getAppValue(Application::APP_ID, 'tmdb_api_key', ''));
	}

	/**
	 *      load home page of TVSN
	 */
	public function home() {

		return new JSONResponse(['status' => 200]);
	}
	/**
	 *          save settings changed
	 */
	public function saveSetting() {
		# init response
		$response = array('success' => false,
											'message' => '');

		#get the setting to save
		$setting = $this->postdata->setting;
		$data = $this->postdata->data;
		$this->config->setAppValue(Application::APP_ID, $setting, $data);

		if ($this->config->getAppValue(Application::APP_ID, $setting, '') == $data){
			$response['success'] = true;
		}else {
			$response['message'] = "Oops, Something when wrong.";
		}
		if ($setting == 'tmdb_api_key'){
			$response['message'] = "Updated your API Key";
		}
		if ($setting == 'file_name_structure'){
			$response['message'] = "Updated file naming structure";
		}
		#return the json to render on client
		return new JSONResponse($response);
	}
	/**
	 *          retreve list of show that can be renamed
	 */
	public function rename() {
		# init response
		$response = array('success' => false,
											'message' => '');
		# get the posted vars we need
		$file_id = $this->postdata->file_id;
		$file_path = $this->postdata->file_path;
		$new_file_name = $this->postdata->new_name;
		# init OC Files
		$userHome = $this->rootFolder->getUserFolder($this->userId);
		$file_path = str_replace('//', '/', $userHome->getPath() . '/' . $file_path);
		$file = $userHome->getById($file_id);
		$file = $file[0] ?? null;


		# extra security check to make sure the file is in the same folder as we think
		$internal_path = $file == null ? null : $file->getParent()->getPath();
		if($file != null && $internal_path == $file_path){
			try{
				# move the file and will through a error on fail
				$file->move($internal_path . '/' . $new_file_name);
				$response['success'] = true;
				$response['action'] = 'update';
				$response['element'] = 'file'.$file_id;
				$response['file'] = Files::getFile($file, $userHome);
			} catch (\OCP\Files\NotPermittedException $ex) {
				$response['message'] = 'Unable rename the file';
			} catch (\OCP\Lock\LockedException $ex) {
				$response['message'] = 'File is locked or in use';
			} catch (\OCP\Files\InvalidPathException $ex) {
				$response['message'] = 'File path invalid';
			}
		}else {
			$response['message'] = 'Unable to find file. Try to refresh';
		}
		#return the json to render on client
		return new JSONResponse($response);
	}


	/**
	 *          retreve list of show that can be renamed
	 */
	public function scan() {
		// start the response with defaults
		$response = array('success' => false,
											'message' => '');
		if ($this->config->getAppValue(Application::APP_ID, 'tmdb_api_key', '') == ''){
			$response['message'] = 'Please configure your API key in settings';
			return new JSONResponse($response);
		}
		// is there a show index
		$show_index = property_exists($this->postdata, 'show_index') ? $this->postdata->show_index+1 : 0;

		// get the folder path
		$path = $this->postdata->scan_folder;
		$userHome = $this->rootFolder->getUserFolder($this->userId);
		#check to make sure the folder exsist
		$path = Files::startsWith($path, $userHome->getPath()) ? Files::removeStart($path, $userHome->getPath()) : $path;
		if ($userHome->nodeExists($path)) {
			$folder_to_scan = $userHome->get($path);
			# store the folder info ready to eb returned to ui
			$response['id'] =  $folder_to_scan->getId();
			$response['name'] =  $folder_to_scan->getName();
			$response['path'] = $folder_to_scan->getPath();

			# check if folder was selected
			if ($folder_to_scan instanceof Folder) {
				#check if home folder was selected
				if ($folder_to_scan != $userHome) {
						# store the folder info ready to eb returned to ui
						$response['folder_id'] =  $folder_to_scan->getId();
						$response['name'] =  $folder_to_scan->getName();
#						$response['absolute_path'] = $folder_to_scan->getPath();
						$response['path'] = $path;

						$search = $this->TMDB->searchTvShow($response['name']);
						#check if there are enought results
						if ($search !== "" || $search['total_results'] == '0'){
							$response['show_info'] = $search['results'][$show_index];
							$response['show_index'] = $show_index;
							$response['files'] = Files::getFilesRecursive($path);

							# check we have some files
							if (is_null($response['files'])){
								$response['message'] = 'No files found';
							}else{
							#match the files to episodes
								Files::matchFilesToEpisodes($response, $this->TMDB);

								$response['success'] = true;
							}
						}else{
							$response['message'] = 'No results for "' . $response['name'] .'"';
						}
				}else{
					$response['message'] = 'Can not scan home folder, Select a folder';
				}
			}else{
				$response['message'] = 'Please select a folder';
			}
		}else{
			$response['message'] = 'Folder does not exsist';
		}

		#return the json to render on client
		return new JSONResponse($response);
	}

	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {

		$perams =['tmdb_api_key' => $this->config->getAppValue(Application::APP_ID, 'tmdb_api_key', '')];

		return new TemplateResponse(Application::APP_ID, 'index', $perams);
	}

	# just a place holder to get imags at the mo
	/**
		 * @PublicPage
		 * @NoCSRFRequired
		 */
   public function image($img){
		 $res = new StreamResponse(fopen("https://image.tmdb.org/t/p/w500/" . $img, 'r'));
		 $res->addHeader('Content-type', "image/jpeg; charset=utf-8");
		 return $res;

   }

}
