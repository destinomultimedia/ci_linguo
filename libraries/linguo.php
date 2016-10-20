<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Linguo CodeIgniter Library
 * Implements a jowb queue system for CodeIgniter.
 *
 * @package		Codeigniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author      David López Santos <meencantaesto@hotmail.com>
 */

class Linguo {

	// SETTINGS
	const DB_PREFIX = "linguo_";

	// Gamify Instance
    private static $instance;

    // CODEIGNITER CONFIGURATION
    private $_CI;
    private $dbGroup = 'default';
    private $viewsFolder = 'linguo/';
    private $exceptionFiles = array('index.html');
    private $canWriteFiles = false;
    private $masterLanguageId = false;

    // URI CONFIGURATION
    private $linguoURL = '';

    //THEME CONFIGURATION
    private $css_files = array('assets/css/bootstrap.css', 'assets/css/custom.css');
    private $js_files = array('assets/js/jquery-1.10.2.js', 'assets/js/bootstrap.min.js', 'assets/js/custom.js');

    // DATABASE CONFIG
    private $dbTables = array('languages', 'language_files', 'language_strings');
    private $dbStructure = array(
    	'languages' => array(
    		'pk' => "language_id",
    		'fields' => array(
                        'language_id' => array(
	                                     'type' => 'INT',
	                                     'constraint' => 11,
	                                     'unsigned' => TRUE,
	                                     'auto_increment' => TRUE
                                    	),
                        'name' => array(
                                         'type' => 'VARCHAR',
                                         'constraint' => 125
                                      	),
                        'description' => array(
                                         'type' => 'VARCHAR',
                                         'constraint' => 255
                                        ),
                       	'folder' => array(
                                         'type' => 'VARCHAR',
                                         'constraint' => 255
                                      	),
                        'is_master' => array(
                                         'type' => 'TINYINT',
                                         'constraint' => 1,
                                         'default' => 0
                                        ),
            )
    	),
    	'language_files' => array(
    		'pk' => "file_id",
    		'fields' => array(
                        'file_id' => array(
	                                     'type' => 'INT',
	                                     'constraint' => 11,
	                                     'unsigned' => TRUE,
	                                     'auto_increment' => TRUE
                                    	),
                        'language_id' => array(
                                         'type' => 'INT',
                                         'constraint' => 11,
                                        ),
                        'name' => array(
                                         'type' => 'VARCHAR',
                                         'constraint' => 125
                                        ),
                        'description' => array(
                                         'type' => 'VARCHAR',
                                         'constraint' => 255
                                        ),
                        'folder' => array(
                                         'type' => 'VARCHAR',
                                         'constraint' => 255
                                        ),
                        'path' => array(
                                         'type' => 'VARCHAR',
                                         'constraint' => 255
                                        )
            )
    	),
    	'language_strings' => array(
    		'pk' => "string_id",
    		'fields' => array(
                        'string_id' => array(
	                                     'type' => 'INT',
	                                     'constraint' => 11,
	                                     'unsigned' => TRUE,
	                                     'auto_increment' => TRUE
                                    	),
                        'file_id' => array(
                                         'type' => 'INT',
                                         'constraint' => 11,
                                        ),
                        'key' => array(
                                         'type' => 'VARCHAR',
                                         'constraint' => 255
                                      	),
                        'value' => array(
                                         'type' => 'TEXT'
                                      	)
            )
    	)
    );

	/**
	 * Constructor function
	 */
	public function __construct($options = array()) {
        //Load Custom Options
        foreach($options AS $item=>$value){
            $this->$item = $value;
        }

		//Get CodeIgniter instance
		$this->_CI =& get_instance();
        $this->_CI->load->helper('directory');
        $this->_DB = $this->_CI->load->database($this->dbGroup, TRUE);

		//Load DBForge
		$this->_dbforge();
        //Prepare database
        $this->_setUpDatabase();
        //Check languages
        $this->_setUpLanguages();

        //Check writing permissions
        if(is_writable(APPPATH.'language/')){
            $this->canWriteFiles = true;
        }

        //Check if we have Master Language
        $this->masterLanguageId = $this->_getMasterLanguageId();

        //Prepare base url
        $this->linguoURL = base_url().$this->_CI->router->fetch_directory().$this->_CI->router->fetch_class()."/".$this->_CI->router->fetch_method();
	}

    // DBFORGE INITIALIZATION
    public function _dbforge(){
        require_once(BASEPATH.'database/DB_forge.php');
        require_once(BASEPATH.'database/drivers/'.$this->_CI->db->dbdriver.'/'.$this->_CI->db->dbdriver.'_forge.php');
        $class = 'CI_DB_'.$this->_CI->db->dbdriver.'_forge';

        $this->_CI->dbforge = new $class($this->_DB);
    }

    //SET UP THE TABLES
    public function _setUpDatabase(){
        //Setting up the tables
        foreach($this->dbTables AS $tableName){
            //If table not exists
            if($this->_DB->table_exists(strtolower(self::DB_PREFIX).$tableName)===false){
                //Define fiedls
                $this->_CI->dbforge->add_field($this->dbStructure[$tableName]['fields']);
                //Add Key
                $this->_CI->dbforge->add_key($this->dbStructure[$tableName]['pk'], TRUE);
                //Create Table
                $this->_CI->dbforge->create_table(strtolower(self::DB_PREFIX).$tableName);
            }
        }
    }

    //CHECK EXISTING LANGUAGES
    public function _setUpLanguages(){
        $language_folders = directory_map(APPPATH.'language/', 1);

        foreach($language_folders AS $language){
            $language_path = APPPATH.'language/'.$language."/";

            if(is_dir($language_path)){
                $result = $this->_addLanguage($language);
            }
        }
    }

    //GET MASTER LANGUAGE ID
    public function _getMasterLanguageId(){
        //Select item
        $this->_DB->where('is_master', '1');
        $sel_language = $this->_DB->get(self::DB_PREFIX.'languages');

        if($sel_language->num_rows() != 1){
            return false;
        }
        else{
            $language = $sel_language->row_array();
            return $language['language_id'];
        }
    }

    //CHECK EXISTING LANGUAGE FILES
    public function _setUpLanguageFiles($language_id, $language_path){
        //Get directory items
        $language_files = directory_map($language_path);

        //Loop language files.
        foreach($language_files AS $folder => $language_file){
            //If not folder and not in exception files
            if(!is_array($language_file)){
                if(!in_array($language_file, $this->exceptionFiles)){
                    $this->_addLanguageFile($language_id, $language_path.$language_file);
                }
            }
            else{
                $this->_loopLanguageFiles($language_id, $language_path.$folder, $language_file);
            }
        }

        return;
    }

    //LOOP EXISTING LANGUAGE FILES
    public function _loopLanguageFiles($language_id, $path, $language_files){
        //Loop language files.
        foreach($language_files AS $folder => $language_file){
            //If not folder and not in exception files
            if(!is_array($language_file)){
                if(!in_array($language_file, $this->exceptionFiles)){
                    $this->_addLanguageFile($language_id, $path."/".$language_file);
                }
            }
            else{
                $this->_loopLanguageFiles($language_id, $path."/".$folder, $language_file);
            }
        }

        return;
    }

    //CHECK EXISTING LANGUAGE FILE STRINGS
    public function _setUpLanguageFileStrings($file_id, $file_path){
        //First, require file
        if(file_exists($file_path)){
            require($file_path);

            if(isset($lang)){
                //Get File Keys
                $language_file_keys = array_keys($lang);
                $language_db_keys = array_keys($this->getLanguageFileStrings($file_id));

                //Insert or Update file Keys
                foreach($language_file_keys AS $key){
                    $this->_addLanguageFileString($file_id, $key, $lang[$key]);
                }

                //Delete keys that are on DB and not on file
                $keys_to_delete = array_diff($language_db_keys, $language_file_keys);
                if(count($keys_to_delete)!=0){
                    $this->_DB->where('file_id', $file_id);
                    $this->_DB->where_in('key', $keys_to_delete);
                    $del_keys = $this->_DB->delete(self::DB_PREFIX.'language_strings');
                }
            }            
        }
        else{
            show_error("Language file not found.");
        }

        return;
    }

    //GET LANGUAGE
    public function _getLanguage($language_id){
        //Select item
        $this->_DB->where('language_id', $language_id);
        $sel_language = $this->_DB->get(self::DB_PREFIX.'languages');

        if($sel_language->num_rows() == 0){
            show_error("Language not found.");
        }
        else{
            $language = $sel_language->row_array();
            return $language;
        }
    }

    //ADD LANGUAGE
    public function _addLanguage($language){
        //Configure query and insert if not exists
        $fields = array();
        $fields['name'] = $language;
        $fields['folder'] = APPPATH.'language/'.$language."/";

        $this->_DB->select('language_id');
        $this->_DB->where('name', $language);
        $exist_language = $this->_DB->get(self::DB_PREFIX.'languages');

        if($exist_language->num_rows()==0){
            $fields['is_master'] = 0;
            $sql_query = $this->_DB->insert(self::DB_PREFIX.'languages', $fields);
        }
        else{
            $this->_DB->where('name', $language);
            $sql_query = $this->_DB->update(self::DB_PREFIX.'languages', $fields);   
        }

        if($sql_query){
            return true;
        }
        else{
            return false;
        }
    }

    //GET LANGUAGE FILE
    public function _getLanguageFile($file_id){
        //Select item
        $this->_DB->where('file_id', $file_id);
        $sel_language_file = $this->_DB->get(self::DB_PREFIX.'language_files');

        if($sel_language_file->num_rows() == 0){
            show_error("Language file not found.");
        }
        else{
            $language_file = $sel_language_file->row_array();
            return $language_file;
        }
    }

     public function _getString($string_id){
        //Select item
        $this->_DB->where('string_id', $string_id);
        $sel_string = $this->_DB->get(self::DB_PREFIX.'language_strings');

        if($sel_string->num_rows() == 0){
            show_error("String not found.");
        }
        else{
            $string = $sel_string->row_array();
            return $string;
        }
    }

    //ADD LANGUAGE FILE
    public function _addLanguageFile($language_id, $filename){
        //Configure query and insert if not exists
        $fields = array();
        $fields['language_id'] = $language_id;
        $fields['name'] = basename($filename);
        $fields['folder'] = str_replace(APPPATH.'language/', "", dirname($filename));
        $fields['path'] = $filename;

        //Check if file exists in database
        $this->_DB->select('file_id');
        $this->_DB->where('language_id', $language_id);
        $this->_DB->where('path', $filename);
        $exist_language = $this->_DB->get(self::DB_PREFIX.'language_files');

        if($exist_language->num_rows()==0){
            $fields['description'] = '';
            $sql_query = $this->_DB->insert(self::DB_PREFIX.'language_files', $fields);
        }
        else{
            $this->_DB->where('language_id', $language_id);
            $this->_DB->where('path', $filename);
            $sql_query = $this->_DB->update(self::DB_PREFIX.'language_files', $fields);   
        }

        if($sql_query){
            return true;
        }
        else{
            return false;
        }
    }

    //ADD LANGUAGE FILE STRING
    public function _addLanguageFileString($file_id, $key, $value){
        //Configure query and insert if not exists
        $fields = array();
        $fields['file_id'] = $file_id;
        $fields['key'] = $key;
        $fields['value'] = $value;

        $this->_DB->where('file_id', $file_id);
        $this->_DB->where('key', $key);
        $exists_string = $this->_DB->get(self::DB_PREFIX.'language_strings');

        if($exists_string->num_rows()==0){
            $sql_query = $this->_DB->insert(self::DB_PREFIX.'language_strings', $fields);
        }
        else{
            $this->_DB->where('file_id', $file_id);
            $this->_DB->where('key', $key);
            $sql_query = $this->_DB->update(self::DB_PREFIX.'language_strings', $fields);   
        }

        if($sql_query){
            return true;
        }
        else{
            return false;
        }
    }

    //WRITE LANGUAGE FILE
    public function _writeLanguageFile($file_id){
        //Get File Info
        $file = $this->_getLanguageFile($file_id);
        
        //Get File Strings
        $strings = $this->getLanguageFileStrings($file_id);

        //Open file to write
        $tgt_file = fopen($file['path'], 'w');
        fputs($tgt_file, "<?php\r\n\r\n");
        foreach($strings AS $key => $string){
            $key = $string['key'];
            $value = $string['value'];
            fputs($tgt_file, "\$lang['".$key."'] = '".$value."';\r\n");
        }
        fclose($tgt_file);

    }

    public function _get_css_data($css_extra_files = array()){
        $css_content = "";

        foreach($this->css_files AS $css_file){
            $css_content .= file_get_contents(APPPATH."views/".$this->viewsFolder.$css_file);
        }
        
        foreach($css_extra_files AS $css_extra_file){
            $css_content .= file_get_contents(APPPATH."views/".$this->viewsFolder.$css_extra_file);
        }

        return $css_content;
    }

    public function _get_js_data($js_extra_files = array()){
        $js_content = "";

        foreach($this->js_files AS $js_file){
            $js_content .= file_get_contents(APPPATH."views/".$this->viewsFolder.$js_file);
        }
        
        foreach($js_extra_files AS $js_extra_file){
            $js_content .= file_get_contents(APPPATH."views/".$this->viewsFolder.$js_extra_file);
        }

        return $js_content;
    }

    //SET MASTER
    public function setMaster($language_id){

        $this->_DB->where('language_id', $language_id);
        $upd_master = $this->_DB->update(self::DB_PREFIX.'languages', array('is_master' => '1'));

        $this->_DB->where('language_id <>', $language_id);
        $upd_master = $this->_DB->update(self::DB_PREFIX.'languages', array('is_master' => '0'));

        return;
    }

    //GET LANGUAGES
    public function getLanguages(){
        //Initialize output
        $output = array();

        $get_languages = $this->_DB->get(self::DB_PREFIX.'languages');

        foreach($get_languages->result_array() AS $language){
            $output[$language['language_id']] = $language;
        }

        return $output;
    }

    //GET LANGUAGE FILE ITEMS
    public function getLanguageFiles($language_id){
        //Initialize output
        $output = array();

        $this->_DB->where('language_id', $language_id);
        $this->_DB->order_by('folder, name ASC');
        $get_language_files = $this->_DB->get(self::DB_PREFIX.'language_files');

        foreach($get_language_files->result_array() AS $file){
            $output[$file['file_id']] = $file;
        }

        return $output;
    }

    //GET LANGUAGE FILE ITEMS
    public function getLanguageFileStrings($file_id){
        //Initialize output
        $output = array();

        $this->_DB->where('file_id', $file_id);
        $get_language_strings = $this->_DB->get(self::DB_PREFIX.'language_strings');

        foreach($get_language_strings->result_array() AS $string){
            $output[$string['key']] = $string;
        }

        return $output;
    }

    //CREATE LANGUAGE
    public function createLanguage($value, $clone){
        //Create file
        $result = mkdir(APPPATH.'language/'.$value, 0777, true);

        //If clone and have master language
        if($clone=='1' && $this->masterLanguageId!==false){
            //Get master language
            $master_language = $this->_getLanguage($this->masterLanguageId);
            //Get files from master language
            $master_language_files = $this->getLanguageFiles($this->masterLanguageId);

            foreach($master_language_files AS $file_id => $file_info){
                $source_file = $file_info['path'];
                $target_file = str_replace("/".$master_language['name']."/", "/".$value."/", $file_info['path']);

                //Check if folder exists
                if(!file_exists(dirname($target_file))){
                    mkdir(dirname($target_file), 0777, true);
                }

                //Copy file
                copy($source_file, $target_file);
            }
        }
    }

    //CREATE LANGUAGE FILE
    public function createLanguageFile($language_id, $filename, $clone){
        //$result = mkdir(APPPATH.'language/'.$value, 0777, true);
        $language = $this->_getLanguage($language_id);
        $filepath = $language['folder'].$filename;

        //Check if folder exists
        if(!file_exists(dirname($filepath))){
            mkdir(dirname($filepath), 0777, true);
        }

        //If clone and have master language
        if($clone=='1' && $this->masterLanguageId!==false){
            //Get master language
            $master_language = $this->_getLanguage($this->masterLanguageId);
            $originpath = str_replace("/".$language['name']."/", "/".$master_language['name']."/", $filepath);

            if(file_exists($originpath)){
                var_dump(copy($originpath, $filepath));
            }
            else{
                $result = fopen($filepath, 'w');                
                fclose($result);
            }
        }
        else{
            $result = fopen($filepath, 'w');
            fclose($result);
        }
       
    }

    //CREATE LANGUAGE STRING
    public function createString($file_id, $key, $value){
        //Configure query and insert if not exists
        $fields = array();
        $fields['file_id'] = $file_id;
        $fields['key'] = $key;
        $fields['value'] = addslashes($value);

        $create_string = $this->_DB->insert(self::DB_PREFIX.'language_strings', $fields);

        if($create_string){
            //Write language file. 
            $this->_writeLanguageFile($file_id);
        }
    }

    //UPDATE LANGUAGE STRING
    public function updateString($string_id, $value){
        $this->_DB->where('string_id', $string_id);
        $update_string = $this->_DB->update(self::DB_PREFIX.'language_strings', array('value'=> addslashes($value)));

        if($update_string){
            //Get String
            $string = $this->_getString($string_id);
            //Write language file. 
            $this->_writeLanguageFile($string['file_id']);
        }
    }


    //RENDER VIEW
    public function render($language_id, $file_id, $action){
        //Prepare view data
        $view_data = array();

        //If no action, is UI
        if($action==''){
            //Data Items
            $view_data['_CI'] = $this->_CI;
            $view_data['languages'] = $this->getLanguages();
            $view_data['language_id'] = $language_id;
            $view_data['file_id'] = $file_id;
            $view_data['can_write'] = $this->canWriteFiles;
            $view_data['master_language_id'] = $this->masterLanguageId;

            //UI Items
            $view_data['css_data'] = $this->_get_css_data();
            $view_data['js_data'] = $this->_get_js_data(array('assets/js/functions.js'));

            //Config Items
            $view_data['linguo_url'] = $this->linguoURL;
            $view_data['current_language'] = $language_id;
            $view_data['current_file'] = $file_id;
            $view_data['views_folder'] = $this->viewsFolder;
            $view_data['views_path'] = APPPATH."views/".$this->viewsFolder;


            //If no language_id set...
            if($language_id=='' && $file_id==''){
                //Main View
                $view_data['view_content'] = 'list_languages';    
            }
            else if($language_id!='' && $file_id==''){
                //Setup Language Files
                $language = $this->_getLanguage($language_id);
                $this->_setUpLanguageFiles($language_id, $language['folder']);
                //Data Items
                $view_data['files'] = $this->getLanguageFiles($language_id);
                $view_data['language'] = $language;
                //Main View
                $view_data['view_content'] = 'list_files'; 
            }
            else if($language_id!='' && $file_id!==''){
                //Setup Language Files
                $file = $this->_getLanguageFile($file_id);
                $this->_setUpLanguageFileStrings($file_id, $file['path']);
                //Data Items
                $view_data['strings'] = $this->getLanguageFileStrings($file_id);
                //Main View
                $view_data['view_content'] = 'list_strings'; 
            }
            else{
                show_error(404);
            }

            $this->_CI->load->view($this->viewsFolder.'loader', $view_data);
        }
        else{
            switch($action){
                case "set_master":
                    $this->setMaster($this->_CI->input->post('language_id'));
                    break;
                case "create_language":
                    $this->createLanguage($this->_CI->input->post('value'), $this->_CI->input->post('clone'));
                    break;
                case "create_file":
                    $this->createLanguageFile($this->_CI->input->post('language_id'), $this->_CI->input->post('value'), $this->_CI->input->post('clone'));
                    break;
                case "create_string":
                    $this->createString($this->_CI->input->post('file_id'), $this->_CI->input->post('key'), $this->_CI->input->post('value'));
                    break;
                case "update_string":
                    $this->updateString($this->_CI->input->post('string_id'), $this->_CI->input->post('value'));
                    break;
            }
        }
        
        return;
    }

}