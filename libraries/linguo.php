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
                        'slug' => array(
                                         'type' => 'VARCHAR',
                                         'constraint' => 125
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
                        'slug' => array(
                                         'type' => 'VARCHAR',
                                         'constraint' => 125
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

        //Prepare base url
        $this->linguoURL = base_url().$this->_CI->router->fetch_class()."/".$this->_CI->router->fetch_method();
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

        foreach($language_folders AS $language_slug){
            $language_path = APPPATH.'language/'.$language_slug."/";

            if(is_dir($language_path)){
                $result = $this->_addLanguage($language_slug);

                if($result){
                    $this->_setUpLanguageFiles(APPPATH.'language/'.$language_slug."/");
                }    
            }
        }
    }

    //CHECK EXISTING LANGUAGE FILES
    public function _setUpLanguageFiles($language_path){
        $language_files = directory_map($language_path);
    }

    //ADD LANGUAGE
    public function _addLanguage($language_slug){
        //Configure query and insert if not exists
        $fields = array();
        $fields['slug'] = $language_slug;
        $fields['folder'] = APPPATH.'language/'.$language_slug."/";

        $this->_DB->select('language_id');
        $this->_DB->where('slug', $language_slug);
        $exist_language = $this->_DB->get(self::DB_PREFIX.'languages');

        if($exist_language->num_rows()==0){
            $fields['is_master'] = 0;
            $sql_query = $this->_DB->insert(self::DB_PREFIX.'languages', $fields);
        }
        else{
            $this->_DB->where('slug', $language_slug);
            $sql_query = $this->_DB->update(self::DB_PREFIX.'languages', $fields);   
        }

        if($sql_query){
            return true;
        }
        else{
            return false;
        }
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


    //RENDER VIEW
    public function render($language_id, $file_id){
        //Prepare view data
        $view_data = array();
        //Data Items
        $view_data['_CI'] = $this->_CI;
        $view_data['languages'] = $this->getLanguages();
        //Config Items
        $view_data['linguo_url'] = $this->linguoURL;
        $view_data['current_language'] = $language_id;
        $view_data['current_file'] = $file_id;
        $view_data['views_folder'] = $this->viewsFolder;
        $view_data['views_path'] = APPPATH."views/".$this->viewsFolder;
        //UI Elements (CSS adn JS)
        $view_data['css_data'] = $this->_get_css_data();
        $view_data['js_data'] = $this->_get_js_data();
        //Main View
        $view_data['view_content'] = 'list_languages';
        

        $this->_CI->load->view($this->viewsFolder.'loader', $view_data);
        return;
    }

}