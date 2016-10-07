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
        $this->_DB = $this->_CI->load->database($this->dbGroup, TRUE);

		//Load DBForge
		$this->_dbforge();

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

    // DBFORGE INITIALIZATION
    public function _dbforge(){
        require_once(BASEPATH.'database/DB_forge.php');
        require_once(BASEPATH.'database/drivers/'.$this->_CI->db->dbdriver.'/'.$this->_CI->db->dbdriver.'_forge.php');
        $class = 'CI_DB_'.$this->_CI->db->dbdriver.'_forge';

        $this->_CI->dbforge = new $class($this->_DB);
    }

}

/* End of file jobs.php */
/* Location: ./application/libraries/jobs.php */
