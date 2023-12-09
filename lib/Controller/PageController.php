<?php
namespace OCA\TVShowNamer\Controller;

use OCP\IRequest;
use OCP\IConfig;
use OCP\IInitialStateService;
use OCP\IUserSession;
use OCP\IL10N;

use OCA\TVShowNamer\AppInfo\Application;
use OCA\TVShowNamer\Utils\Files;
use OCA\TVShowNamer\Utils\TMDB;
use OCA\TVShowNamer\Utils\TVDB;

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
	private $TVDB;
	public $file_name_structure;
	private $tmdb_apiKey;
	private $app_file_name_structure;
	private $app_hide_matching;
	private $hide_matching;
	private $enable_tvdb;
	private $enable_tmdb;
	private $tvdb_active;
	private $tmdb_active;
	private $active_datasource;
	public static $file_name_structure_default = '{{Season_Name}} S{{Season_Number_Padded}}E{{Episode_Number_Padded}} - {{Episode_Name}}';
	public static $preferred_language_default = 'en';
	private $l;

	public function __construct(	$AppName,
									IRequest $request,
									IConfig $Config,
									IUserSession $userSession,
									IRootFolder $rootFolder,
									IInitialStateService $initialStateService,
									IL10N $l){
		parent::__construct($AppName, $request);
		if ($userSession->isLoggedIn()){
			$this->userId = ($userSession->getUser())->getUID();
			$this->config = $Config;
			$this->rootFolder = $rootFolder;
			$this->initialStateService = $initialStateService;
			$this->l = $l;
			$this->postdata = json_decode(file_get_contents("php://input"));

			$tvdb_token = $this->config->getAppValue(Application::APP_ID, 'tvdb_token', '');

			$this->app_file_name_structure = $this->config->getAppValue(Application::APP_ID, 'file_name_structure', '');
			$this->app_hide_matching = $this->config->getAppValue(Application::APP_ID, 'hide_matching', '');

			$this->tmdb_apiKey = $this->config->getUserValue($this->userId, Application::APP_ID, 'tmdb_api_key', '');
			$this->enable_tvdb = $this->config->getUserValue($this->userId, Application::APP_ID, 'enable_tvdb', 'checked');
			$this->enable_tmdb = $this->config->getUserValue($this->userId, Application::APP_ID, 'enable_tmdb', 'checked');

			$this->active_datasource = $this->config->getUserValue($this->userId, Application::APP_ID, 'active_datasource', 'tmdb');
			$this->tvdb_active = $this->active_datasource == 'tvdb' ? 'active' : '';
			$this->tmdb_active = $this->active_datasource == 'tmdb' ? 'active' : '';

			$this->TMDB = new TMDB($this->tmdb_apiKey == '' ? Application::get_tmdb_api_key() : $this->tmdb_apiKey);
			$this->TVDB = new TVDB(Application::get_tvdb_api_key(), $tvdb_token);

			if ($tvdb_token != $this->TVDB->token){
				$this->config->setAppValue(Application::APP_ID, 'tvdb_token', $this->TVDB->token);
			}

			$this->hide_matching = $this->config->getUserValue($this->userId, Application::APP_ID, 'hide_matching', '');
			$this->file_name_structure = $this->config->getUserValue($this->userId, Application::APP_ID, 'file_name_structure', '');
			if ($this->file_name_structure == ''){
				$this->file_name_structure = self::$file_name_structure_default;
				$this->config->setUserValue($this->userId, Application::APP_ID, 'file_name_structure', self::$file_name_structure_default);
			}
			$this->preferred_language = $this->config->getUserValue($this->userId, Application::APP_ID, 'preferred_language', '');
			if ($this->preferred_language == ''){
				$this->preferred_language = self::$preferred_language_default;
				$this->config->setUserValue($this->userId, Application::APP_ID, 'preferred_language', self::$preferred_language_default);
			}
		}
	}

	/**
	*				Init IL10N get language
	**/
	public function getLanguageCode() {
		return $this->l->getLanguageCode();
	}

	/**
	*				load home page of TVSN
	*
	* @NoAdminRequired
  * @NoCSRFRequired
	*/
	public function home() {
		return new JSONResponse(['status' => 200]);
	}

	/**
	*				Save settings changed in settings panel
	*
	* @NoAdminRequired
  * @NoCSRFRequired
	*/
	public function saveSetting() {
		# init response
		$response = array('success' => false,
											'message' => '');

		#get the setting to save
		$setting = $this->postdata->setting;
		$data = $this->postdata->data;
		$this->config->setUserValue($this->userId, Application::APP_ID, $setting, $data);

		if ($this->config->getUserValue($this->userId, Application::APP_ID, $setting, '') == $data){
			$response['success'] = true;
		}else {
			$response['message'] = $this->l->t("Oops, something went wrong.");
		}
		switch ($setting){
			case 'tmdb_api_key':
				$response['message'] = $this->l->t("Updated your API Key");
				break;
			case 'file_name_structure':
				$response['message'] = $this->l->t("Updated file naming structure");
				break;
			case 'preferred_language':
				$response['message'] = $this->l->t("Updated your preferred language");
				break;
			case 'hide_matching':
				$response['message'] = $this->l->t("Updated your preference");
				break;
			case 'enable_tvdb':
				if ($data == "checked"){
					$response['message'] = $this->l->t("Enabled") . " The TV DB " . $this->l->t("Datasource");
				}else{
					$response['message'] = $this->l->t("Disabled") . " The TV DB " . $this->l->t("Datasource");
				}
				break;
			case 'enable_tmdb':
				if ($data == "checked"){
					$response['message'] = $this->l->t("Enabled") . " The Movie DB " . $this->l->t("Datasource");
				}else{
					$response['message'] = $this->l->t("Disabled") . " The Movie DB " . $this->l->t("Datasource");
				}
				break;
			case 'active_datasource':
				if ($data == "tvdb"){
					$response['message'] = $this->l->t("Switching data source to") . " The TV DB";
				}else{
					$response['message'] = $this->l->t("Switching data source to") . " The Movie DB";
				}
				break;
		}
		# return the json to render on client
		return new JSONResponse($response);
	}

	/**
	*				Retrieve list of show that can be renamed
	*
	* @NoAdminRequired
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
				$response['message'] = $this->l->t("Unable rename the file");
			} catch (\OCP\Lock\LockedException $ex) {
				$response['message'] = $this->l->t("File is locked or in use");
			} catch (\OCP\Files\InvalidPathException $ex) {
				$response['message'] = $this->l->t("File path invalid");
			}
		}else {
			$response['message'] = $this->l->t("Unable to find file. Try to refresh");
		}

		#return the json to render on client
		return new JSONResponse($response);
	}


	/**
	*				retrieve list of show that can be renamed
	*
	* @NoAdminRequired
	*/
	public function scan() {
		// start the response with defaults
		$response = array('success' => false,
											'message' => '');

		// old check for api key can be removed
		//if ($this->config->getUserValue($this->userId, Application::APP_ID, 'tmdb_api_key', '') == ''){
		//	$response['message'] = $this->l->t("Please configure your API key in settings");
		//	return new JSONResponse($response);
		//}

		// is there a show index
		$show_index = property_exists($this->postdata, 'show_index') ? $this->postdata->show_index+1 : 0;

		// is there a selected datasource
		$datasource = property_exists($this->postdata, 'datasource') ? $this->postdata->datasource : $this->active_datasource;
		$DS = $this->TVDB;
		if ($datasource == 'tvdb'){
			if ($this->enable_tvdb == 'checked'){
				$DS = $this->TVDB;
			}else{
				$DS = $this->TMDB;
			}
		}else{
			if ($this->enable_tmdb == 'checked'){
				$DS = $this->TMDB;
			}else{
				$DS = $this->TVDB;
			}	
		}

		// get the folder path
		$path = $this->postdata->scan_folder;
		$userHome = $this->rootFolder->getUserFolder($this->userId);

		#check to make sure the folder exists
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

						$search = $DS->searchTvShow(Files::removeAfter($response['name'], "#"), $show_index == 0 ? true : false, $this->preferred_language, $show_index);
						#check if there are enough results
						if ($search !== "" && (string)$search['total_results'] != '0'){
							$response['show_info'] = $search;
							$response['show_index'] = $show_index;
							$response['files'] = Files::getFilesRecursive($path);

							# check we have some files
							if (is_null($response['files'])){
								$response['message'] = $this->l->t("No files found");
							}else{
							#match the files to episodes
								Files::matchFilesToEpisodes($response, $DS, $this->file_name_structure, $this->preferred_language);

								$response['success'] = true;
							}
						}else{
							$response['message'] = $this->l->t('No results for "%1$s"',  [$response['name']]);
						}
				}else{
					$response['message'] = $this->l->t("Cannot scan home folder. Select a folder");
				}
			}else{
				$response['message'] = $this->l->t("Please select a folder");
			}
		}else{
			$response['message'] = $this->l->t("Folder does not exist");
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

		$message = '';

		$appApiKey = $this->config->getAppValue(Application::APP_ID, 'tmdb_api_key', '');

		# Migrate old settings to new
		if ($appApiKey != '' && $this->tmdb_apiKey == ''){
			$this->config->setUserValue($this->userId, Application::APP_ID, 'tmdb_api_key', $appApiKey);
			$this->config->setUserValue($this->userId, Application::APP_ID, 'file_name_structure', $this->app_file_name_structure);
			$this->config->setUserValue($this->userId, Application::APP_ID, 'hide_matching', $this->app_hide_matching);

			$this->file_name_structure = $this->config->getUserValue($this->userId, Application::APP_ID, 'file_name_structure', '');
			$this->hide_matching = $this->config->getUserValue($this->userId, Application::APP_ID, 'hide_matching', '');

			$this->config->deleteAppValue(Application::APP_ID, 'tmdb_api_key');
			$this->config->deleteAppValue(Application::APP_ID, 'file_name_structure');
			$this->config->deleteAppValue(Application::APP_ID, 'hide_matching');
		}

		$perams =['tmdb_api_key' => $this->config->getUserValue($this->userId, Application::APP_ID, 'tmdb_api_key', ''),
							'file_name_structure' => $this->file_name_structure,
							'hide_matching' => $this->hide_matching,
							'enable_tvdb' => $this->enable_tvdb,
							'enable_tmdb' => $this->enable_tmdb,
							'tvdb_active' => $this->tvdb_active,
							'tmdb_active' => $this->tmdb_active,
							'preferred_language' => $this->preferred_language,
							'info_message' => $message];

		return new TemplateResponse(Application::APP_ID, 'index', $perams);
	}

	# just a place holder to get images at the min
	/**
	* @PublicPage
	* @NoCSRFRequired
	*/
	public function image($src){
		$img = $_SERVER["QUERY_STRING"];
		if ($src == 'tmdb'){
			$res = new StreamResponse(fopen("https://image.tmdb.org/t/p/w500/" . $img, 'r'));
		}
		if ($src == 'tvdb'){
			$res = new StreamResponse(fopen("https://artworks.thetvdb.com/" . $img, 'r'));
		}
		$res->addHeader('Content-type', "image/jpeg; charset=utf-8");
		return $res;
	}

}
