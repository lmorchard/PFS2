<?php
require_once dirname(__FILE__) . '/App.php';
/**
 * Main class for PFS2 application
 *
 * @package    Mozilla_PFS2
 * @subpackage libraries
 * @author     lorchard@mozilla.com
 */
class Mozilla_PFS2 extends Mozilla_App
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
                'pfs_id', 'id', 'name', 'description', 'latest_release_id', 
                'vendor', 'url', 'icon_url', 'license_url',
            ),
            'plugin_releases' => array(
                'has_vulnerability', 'vulnerability_url', 'vulnerability_description',
                'id', 'plugin_id', 'os_id', 'platform_id', 
                'guid', 'filename', 'version', 'xpi_location',
                'installer_location', 'installer_hash', 'installer_shows_ui', 
                'manual_installation_url', 'license_url', 'needs_restart', 'min', 
                'max', 'xpcomabi', 
                'modified',
            ),
            'platforms' => array(
                'app_id', 'app_release', 'app_version', 'locale'
            ),
            'oses' => array(
                'name' => 'os_name'
            ),
        );
        $from  = array('plugins','plugin_releases');
        $where = array(
            "`plugin_releases`.`plugin_id`=`plugins`.`id`"
        );
        $params = array();
        $join = array();
        $group_by = array(
            '`plugin_releases`.`id`',
        );

        // HACK: (kind of) Since '*' sorts last in the list, this is roughly a 
        // relevance sort for platforms and OS
        $order_by = array(
            "`plugin_releases`.`version` DESC", 
            "`oses`.`name` DESC", 
            "`platforms`.`locale` DESC", 
            "`platforms`.`app_id` DESC",
            "`platforms`.`app_version` DESC", 
            "`platforms`.`app_release` DESC"
        );

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

        /* 
         * Prepare the list of columns for the select statement, as 
         * well as a data structure and fields to be bound to the 
         * prepared statement for result fetching
         */
        $columns = array();
        $row     = array();
        $results = array();
        $names   = array();
        foreach ($select as $table => $col_names) {
            $row_table = array();
            foreach ($col_names as $idx=>$col_name) {
                if (!is_numeric($idx)) {
                    // Non-numeric index indicates a column name alias.
                    $names[] = $col_name;
                    $col_name = $idx;
                } else {
                    // Otherwise, the result name is the same as the column name.
                    $names[] = $col_name;
                }
                $columns[] = "`{$table}`.`{$col_name}`";
                $row_table[$col_name] = null;
                $results[] = &$row_table[$col_name];
            }
            $row[$table] = $row_table;
        }

        /*
         * Finally, assemble the SQL source.
         */
        $sql = join("\n", array(
            'SELECT ' . join(', ', $columns),
            'FROM ' . join(', ', array_map($bt_quote, $from)),
            join("\n", $join),
            'WHERE ' . join(' AND ', array_map($parens, $where)),
            'GROUP BY ' . join(', ', $group_by),
            'ORDER BY ' . join(', ', $order_by),
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

        /* 
         * Fetch all the rows and assemble the results.  
         *
         * Note that plain column names are used here, rather than 
         * fully-qualified table.column names.  This is so that column values 
         * from the plugins table are overwritten by non-empty columns from the 
         * plugin_releases table, offering a sort of inheritance relationship.
         */
        $rows = array();
        while ($stmt->fetch()) {
            $row = array();
            foreach ($names as $idx => $name) {
                
                // Skip DB IDs, but pfs_id is okay.
                if ('id' == $name) continue;
                if ('pfs_id' != $name && 
                    strpos($name, '_id') == strlen($name)-3) continue;
                
                // Omit empty columns.
                if (empty($results[$idx])) continue;

                if (in_array($name, array('created', 'modified'))) {
                    $row[$name] = gmdate('c', strtotime($results[$idx]));
                } else {
                    $row[$name] = $results[$idx];
                }
            }
            $rows[] = $row;
        }
        $stmt->close();

        /*
         * Prune the rows down to the single most relevant plugin record for
         * each pfs_id and plugin version pair.
         *
         * This might be better done in MySQL, but couldn't quite figure
         * out how to do it all in SQL.
         */
        $prune = array();
        foreach ($rows as $row) {
            // Collect rows using a key made up of pfs_id and plugin version
            $prune_id = join(':', array(
                @$row['pfs_id'], @$row['version']
            ));
            if (empty($prune[$prune_id])) {
                // Don't have a plugin for this key yet, so just store it.
                $prune[$prune_id] = $row;
            } else {
                // Replace the plugin we have, if this new one is more relevant.
                $curr_rel = $this->_calcRelevance($prune[$prune_id]);
                $new_rel = $this->_calcRelevance($row);
                if ($new_rel > $curr_rel) {
                    $prune[$prune_id] = $row;
                }
            }
        }

        $rows = array_values($prune);

        return $rows;
    }

    /**
     * Calculate result relevance, based on the number of OS / Platform columns 
     * that are not wildcards.
     */
    public function _calcRelevance($row) {
        $rel = 0;
        $cols = array('os_name', 'app_id', 'app_release', 'app_version', 'locale');
        foreach ($cols as $name) {
            if ('*' !== $row[$name]) $rel++;
        }
        return $rel;
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
                'min', 'max', 'xpcomabi', 
                'has_vulnerability', 'vulnerability_url', 'vulnerability_description',
                'modified'
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

        $platforms_insert = $this->db->prepareInsertOrUpdate(
            'platforms', array(
                'app_id', 'app_release', 'app_version', 'locale'
            )
        );

        $oses_insert = $this->db->prepareInsertOrUpdate(
            'oses', array(
                'name'
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

            $platform_id = $platforms_insert->execute_finish(array_merge(
                $platform_defaults, $release['platform']
            ));
            $release['platform_id'] = $platform_id;

            if (!empty($release['vulnerability_description']) ||
                    !empty($release['vulnerability_url'])) {
                $release['has_vulnerability'] = 1;
            }

            $pr_id = $plugin_release_insert->execute_finish($release);
            if ($pr_id) $release_ids[] = $pr_id;

        }

        return array($p_id, $release_ids);

    }

}
