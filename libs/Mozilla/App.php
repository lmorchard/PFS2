<?php
/**
 * App support class
 *
 * @package    Mozilla_PFS2
 * @subpackage libraries
 * @author     lorchard@mozilla.com
 */
class Mozilla_App
{
    /**
     * Main application driver
     */
    public function run()
    {
    }

    /**
     * Constructor, initialize the application environment.
     */
    public function __construct($config_data_or_fn=null)
    {
        $this->loadConfig($config_data_or_fn);
        $this->setupAutoLoader();
        $this->setupDatabase();
    }

    /**
     * Get parameters with a set of supplied defaults.
     *
     * @param  array Set of expected parameters and defaults
     * @return array
     */
    public function getParams($defaults)
    {
        $params = array();
        foreach ($defaults as $name=>$default) {
            if (!empty($_POST[$name])) {
                $params[$name] = $_POST[$name];
            } elseif (!empty($_GET[$name])) {
                $params[$name] = $_GET[$name];
            } else {
                $params[$name] = $default;
            }
        }
        return $params;
    }

    /**
     * Set up the include path and register the autoloader.
     */
    public function setupAutoLoader()
    {
        // Set up an include path that covers libs and vendors
        set_include_path(join(PATH_SEPARATOR, array_merge(
            array(APPPATH.'/libs'),
            glob(APPPATH.'/vendors/*', GLOB_ONLYDIR),
            array(get_include_path())
        )));

        // Set up the class namespace autoloader.
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Class autoloader, transforms underscores to path separators.
     *
     * @param string Class name
     * @return boolean Class was loaded.
     */
    public function autoload($class)
    {
        $file = str_replace('_', '/', $class);
        require_once $file . '.php';
        return TRUE;
    }

    /**
     * Get a config setting, with default.
     *
     * @param  string Config setting key
     * @param  mixed  Default value if key not set
     * @return mixed
     */
    public function getConfig($name, $default=null)
    {
        return isset($this->config[$name]) ?
            $this->config[$name] : $default;
    }

    /**
     * Load a config file.
     *
     * @param mixed Name of a config file or an array of config settings
     */
    public function loadConfig($config_data_or_fn=null) 
    {
        if (null == $config_data_or_fn) {
            $config_data_or_fn = APPPATH . '/conf/config.php';
        }
        if (is_array($config_data_or_fn)) {
            $this->config = $config_data_or_fn;
        } else if ($config_data_or_fn) {
            $config = array();
            require $config_data_or_fn;
            $this->config = $config;
        }
        return $this->config;
    }

    /**
     * Connect to the database specified in the config.
     */
    public function setupDatabase()
    {
        $this->db = new Mozilla_PFS2_Database();
        $this->db->primary_config  = $this->getConfig('primary_database');
        $this->db->shadow_config   = $this->getConfig('shadow_databases');
        $this->db->memcache_config = $this->getConfig('memcache_config');
        $this->db->connectAll();
    }

    /**
     * Load the database with the schema file specified by 'db_schema' in 
     * config, or APPPATH/conf/schema.sql by default.
     */
    public function resetDatabase()
    {
        $fn = $this->getConfig('db_schema', APPPATH . '/conf/schema.sql');
        $sql = file_get_contents($fn);
        $parts = explode(';', $sql);

        foreach ($parts as $part) {
            $part = trim($part);
            if (!$part) continue;
            try {
                $this->db->query($part.';');
            } catch (Exception $e) {
                echo "$part\n";
                throw $e;
            }
        }
    }

}
