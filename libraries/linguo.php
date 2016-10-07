<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Gamify CodeIgniter Library
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
            $result = $this->_addLanguage($language_slug);

            if($result){
                $this->_setUpLanguageFiles(APPPATH.'language/'.$language_slug."/");
            }
        }
    }

    //CHECK EXISTING LANGUAGE FILES
    public function _setUpLanguageFiles($language_path){
        $language_files = directory_map($language_path);

        var_dump($language_files);
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

}

/* End of file jobs.php */
/* Location: ./application/libraries/jobs.php */
