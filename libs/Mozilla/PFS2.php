<?php
/**
 * Main class for PFS2 application
 *
 * @package    Mozilla_PFS2
 * @subpackage libraries
 * @author     lorchard@mozilla.com
 */
class Mozilla_PFS2
{
    public static $release_fields = array(
        'guid', 'version', 'xpi_location',
        'installer_location', 'installer_hash', 'installer_shows_ui',
        'manual_installation_url', 'license_url', 'needs_restart',
        'min', 'max', 'xpcomabi', 'os_name', 'platform_key'
    );

    public static $mime_fields = array(
        'name', 'description', 'suffixes'
    );
    
    public static $platform_fields = array(
        'app_id', 'app_release', 'app_version', 'locale'
    );

    /**
     * Run as a web application.
     */
    public function run()
    {
        $params = $this->getParams(array(
            'mimetype' => '',
            'clientOS' => '',
            'appID' => '',
            'appVersion' => '',
            'appRelease' => '',
            'chromeLocale' => '',
            'callback' => ''
        ));

        $params['mimetype'] = explode(' ', $params['mimetype']);

        $callback = $params['callback'];
        unset($params['callback']);

        $out = $this->lookup($params);

        if ($callback) {
            header('Content-Type: text/javascript');
            // Whitelist the callback to alphanumeric and a few mostly harmless
            // characters, none of which can be used to form HTML or escape a JSONP call
            // wrapper.
            $callback = preg_replace(
                '/[^0-9a-zA-Z\(\)\[\]\,\.\_\-\+\=\/\|\\\~\?\!\#\$\^\*\: \'\"]/', '', 
                $callback
            );
            echo "$callback(";
        } else {
            header('Content-Type: application/json');
        }

        echo json_encode($out);

        if ($callback) echo ')';
    }

    /**
     * Perform DB lookup based on criteria from parameters
     */
    public function lookup($criteria)
    {
        $criteria = array_merge(array(
            'mimetype' => '',
            'clientOS' => '',
            'appID' => '',
            'appVersion' => '',
            'appRelease' => '',
            'chromeLocale' => ''
        ), $criteria);

        if (empty($criteria['mimetype']))
            return NULL;

        $dbh = $this->db->getHandle(Database::SOURCE_SHADOW);

        $parens = create_function('$a', 'return "(".$a.")";');
        $bt_quote = create_function('$a', 'return "`".$a."`";');

        // Establish the tables and columns to be selected.
        $select = array(
            'plugins' => array( 
                'id', 'name', 'description', 'latest_release_id', 
                'vendor', 'url', 'icon_url', 'license_url',
            ),
            'plugin_releases' => array(
                'id', 'plugin_id', 'os_id', 'platform_id', 
                'guid', 'filename', 'version', 'xpi_location',
                'installer_location', 'installer_hash', 'installer_shows_ui', 
                'manual_installation_url', 'license_url', 'needs_restart', 'min', 
                'max', 'xpcomabi', 'created', 'modified',
            ),
        );
        $from  = array_keys($select);
        $where = array(
            "`plugin_releases`.`plugin_id`=`plugins`.`id`"
        );
        $params = array();
        $join = array();

        /*
         * Add client OS criteria to the SQL
         */
        $client_oses = $this->db->escape(
            $this->normalizeClientOS(@$criteria['clientOS'])
        );
        $join[] = join(' ', array(
            "JOIN (`oses`)",
            "ON (`oses`.`id` = `plugin_releases`.`os_id`)"
        ));
        $where[] = "`oses`.`name` in (" . join(',', $client_oses) . ")";

        /*
         * Add a search for platform to SQL
         */
        $join[] = join(' ', array(
            "JOIN (`platforms`)",
            "ON (`platforms`.`id` = `plugin_releases`.`platform_id`)"
        ));
        $where[] = join(' AND ', array(
            "`platforms`.`app_id` in (?,'*')",
            "`platforms`.`app_version` in (?,'*')",
            "`platforms`.`app_release` in (?,'*')",
            "`platforms`.`locale` in (?,'*')",
        ));
        $params = array_merge($params, array(
            @$criteria['appID'],
            @$criteria['appVersion'],
            @$criteria['appRelease'],
            @$criteria['chromeLocale'],
        ));

        /*
         * Add a search for mimetype to SQL
         */
        if (!empty($criteria['mimetype'])) {

            $mimetypes = $criteria['mimetype'];
            if (!is_array($mimetypes)) {
                $mimetypes = array($mimetypes);
            }

            $join[] = join(' ', array(
                "JOIN (`plugins_mimes`,`mimes`)",
                "ON (" . join(' AND ', array (
                    "`mimes`.`id` = `plugins_mimes`.`mime_id`",
                    "`plugins_mimes`.`plugin_id` = `plugin_releases`.`plugin_id`",
                )) . ")"
            ));
            $where[] = "`mimes`.`name` in (" . str_repeat('?,', count($mimetypes)-1) . "?)";
            $params = array_merge($params, $mimetypes);

        }

        /* 
         * Prepare the list of columns for the select statement, as 
         * well as a data structure and fields to be bound to the 
         * prepared statement for result fetching
         */
        $columns = array();
        $row     = array();
        $results = array();
        foreach ($select as $table => $col_names) {
            $row_table = array();
            foreach ($col_names as $col_name) {
                $cols[] = "`{$table}`.`{$col_name}`";
                $row_table[$col_name] = null;
                $results[] = &$row_table[$col_name];
            }
            $row[$table] = $row_table;
        }

        /*
         * Finally, assemble the SQL source.
         */
        $sql = join("\n", array(
            'SELECT ' . join(', ', $cols),
            'FROM ' . join(', ', array_map($bt_quote, $from)),
            join("\n", $join),
            'WHERE ' . join(' AND ', array_map($parens, $where))
        ));

        /*
         * Create the prepared statement based on the assembled SQL.
         */
        if (!($stmt = $dbh->prepare($sql))) 
            throw new Exception($dbh->error);
        
        /*
         * Bind the input params, execute, and bind the result vars.
         */
        array_unshift($params, str_repeat('s', count($params)));
        call_user_func_array(array($stmt, 'bind_param'), $params);
        $rv = $stmt->execute();
        call_user_func_array(array($stmt, 'bind_result'), $results);

        /**
         * Build a map of indexed columns to names.
         */
        $cols = array();
        foreach ($select as $table=>$col_names) {
            foreach ($col_names as $name) {
                $cols[] = $name;
            }
        }

        /* 
         * Fetch all the rows and assemble the results.
         */
        $rows = array();
        while ($stmt->fetch()) {
            $row = array();
            foreach ($cols as $idx => $name) {
                if ('id' == $name) continue;
                if (empty($results[$idx])) continue;
                $row[$name] = $results[$idx];
            }
            $rows[] = $row;
        }

        $stmt->close();

        return $rows;
    }

    /**
     * Normalize a client OS name into a list of names for fuzzy
     * matching in the DB.
     *
     * @param   string Raw name of client OS
     * @returns array  List of normalized matches
     */
    public function normalizeClientOS($client_os)
    {
        $list = array();

        $client_os = trim(strtolower($client_os));
        if (!empty($client_os)) 
            $list[] = $client_os;

        if (preg_match('/^windows nt 6\.0/', $client_os)) {
            $list[] = 'windows vista';
        }
        if (preg_match('/^win/', $client_os)) {
            $list[] = 'win';
        }
        if (preg_match('/^(ppc|intel) mac os x/', $client_os)) {
            $list[] = 'mac os x';
            $list[] = 'mac';
        }
        if (preg_match('/^linux/', $client_os)) {
            $list[] = 'linux';
        }
        if (preg_match('/^linux.+i\d86/i', $client_os)) {
            $list[] = 'linux x86';
        }
        
        if (preg_match('/^sunos/i', $client_os)) {
            $list[] = 'sunos';
            if (preg_match('/sun4u/i', $client_os)) {
                $list[] = 'sunos sun4u';
            }
        }

        $list[] = '*';

        return $list;
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
     * Constructor, initialize the application environment.
     */
    public function __construct($config_data_or_fn=null)
    {
        $this->loadConfig($config_data_or_fn);
        $this->setupAutoLoader();
        $this->setupDatabase();
    }

    /**
     * Construct and return an instance of this class.
     *
     * @return Mozilla_PFS2
     */
    public static function factory()
    {
        return new self();
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

    /**
     * Load plugin into the database.
     */
    public function loadPlugin($plugin)
    {
        if (is_string($plugin)) {
            $plugin = json_decode($plugin, true);
        }

        $plugin_insert = $this->db->prepareInsertOrUpdate(
            'plugins', array(
                'pfs_id', 'name', 'description', 'vendor', 
                'url', 'icon_url', 'license_url'
            )
        );

        $plugin_release_insert = $this->db->prepareInsertOrUpdate(
            'plugin_releases', array(
                'plugin_id', 'os_id', 'platform_id', 
                'guid', 'version', 'xpi_location',
                'installer_location', 'installer_hash', 'installer_shows_ui',
                'manual_installation_url', 'license_url', 'needs_restart',
                'min', 'max', 'xpcomabi', 'created', 'modified'
            )
        );

        $plugins_mimes_insert = $this->db->prepareInsertOrUpdate(
            'plugins_mimes', array( 
                'plugin_id', 'mime_id' 
            )
        );

        $mimes_insert = $this->db->prepareInsertOrUpdate(
            'mimes', array(
                'name', 'description', 'suffixes'
            )
        );

        $os_lookup = $this->db->prepareStatement(
            'SELECT id FROM oses WHERE name=?', array('name')
        );

        $platform_lookup = $this->db->prepareStatement("
            SELECT id FROM platforms 
            WHERE app_id=? AND app_release=? AND app_version=? AND locale=?
            ",
            Mozilla_PFS2::$platform_fields
        );

        $meta_defaults = array(
            'installer_shows_ui' => 'true',
            'needs_restart' => 'true',
            'created' => gmdate('c'),
            'modified' => gmdate('c')
        );
        $release_defaults = array(
        );
        $platform_defaults = array(
            'app_id' => '*',
            'app_release' => '*',
            'app_version' => '*',
            'locale' => '*'
        );

        $meta = array_merge($meta_defaults, $plugin['meta']);

        $p_id = $plugin_insert->execute_finish($meta);

        if (!empty($plugin['mimes'])) foreach ($plugin['mimes'] as $mime) {
            $mime_id = $mimes_insert->execute_finish($mime);
            $plugins_mimes_insert->execute_finish(array(
                'plugin_id' => $p_id, 
                'mime_id' => $mime_id
            ));
        }

        $release_ids = array();
        if (!empty($plugin['releases'])) foreach ($plugin['releases'] as $release) {

            $release = array_merge($release_defaults, $meta, $release);
            $release['plugin_id'] = $p_id;

            $os_id = $os_lookup->fetch_col(array(
                'name'=>$release['os_name']
            ));
            $release['os_id'] = $os_id;

            $platform_id = $platform_lookup->fetch_col(array_merge(
                $platform_defaults, $release['platform']
            ));
            $release['platform_id'] = $platform_id;

            $pr_id = $plugin_release_insert->execute_finish($release);
            if ($pr_id) $release_ids[] = $pr_id;

        }

        return array($p_id, $release_ids);

    }

}
