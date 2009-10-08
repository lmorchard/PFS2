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
    // {{{ Class constants
    
    public static $status_codes = array(
        'unknown'    => 0,
        'latest'     => 10,
        'outdated'   => 20,
        'vulnerable' => 30,
    );

    // }}}

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

        // Check to see if any of the other params are empty, causing a
        // shortcircuit straight to empty results.
        $req_empty = false;
        foreach ($criteria as $name => $value) {
            // All params are required at present
            if (empty($value)) {
                $req_empty = true; break;
            }
        }
        if ($req_empty) {
            // Missing required criteria, so punt.
            // TODO: Respond with an error someday?
            return array();
        }

        if (!is_array($criteria['mimetype'])) {
            $criteria['mimetype'] = explode(' ', $criteria['mimetype']);
        }

        $dbh = $this->db->getHandle(Database::SOURCE_SHADOW);

        $parens = create_function('$a', 'return "(".$a.")";');
        $bt_quote = create_function('$a', 'return "`".$a."`";');

        // Establish the tables and columns to be selected.
        $select = array(
            'plugins' => array( 
                'pfs_id', 'name', 'description', 'latest_release_id', 
                'vendor', 'url', 'icon_url', 'license_url',
            ),
            'plugin_releases' => array(
                'name', 'description', 'vendor',
                'vulnerability_url', 'vulnerability_description',
                'status_code', 'guid', 'filename', 'version', 'xpi_location',
                'installer_location', 'installer_hash', 'installer_shows_ui', 
                'icon_url', 'url',
                'manual_installation_url', 'license_url', 'needs_restart', 
                'min', 'max', 'xpcomabi', 
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
            "`platforms`.`app_release` DESC",
        );

        /*
         * Add client OS criteria to the SQL
         */
        $criteria['clientOS'] = $this->normalizeClientOS(@$criteria['clientOS']);
        $client_oses = $this->db->escape($criteria['clientOS']);
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
        $codes_to_status = array_flip(self::$status_codes);
        $rows = array();
        while ($stmt->fetch()) {
            $row = array();
            foreach ($names as $idx => $name) {

                if ('status_code' == $name) {
                    $row['status'] = isset($codes_to_status[$results[$idx]]) ?
                        $codes_to_status[$results[$idx]] : 'unknown';
                    continue;
                }

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
         * Group the releases by pfs_id, and reduce releases down to the
         * single most relevant record per version.  That is, records with 
         * non-wildcard matches are preferred over wildcard matches for OS / 
         * platform / etc.
         *
         * This might be better done in the database, but couldn't quite figure
         * out how to do it all in SQL.
         */
        $data = array();
        foreach ($rows as $row) {

            // Grab the pfs_id and version from the row
            $pfs_id  = $row['pfs_id'];
            $version = isset($row['version']) ? $row['version'] : '0.0.0';

            // Initialize the structure for a plugin, if this is the first 
            // release seen.
            if (!isset($data[$pfs_id]['releases'])) {
                $data[$pfs_id] = array(
                    'aliases'  => array(), 
                    'releases' => array()
                );
            }

            // Calculate relevance for current row
            $row['relevance'] = $this->calcRelevance($row, $criteria);

            // Decide whether to store or ignore this release...
            if (empty($data[$pfs_id]['releases'][$version])) {
                // Don't have a release for this version yet, so store it.
                $data[$pfs_id]['releases'][$version] = $row;
            } else {
                // Replace the release we have, if this new one is more relevant.
                $curr_rel = $data[$pfs_id]['releases'][$version]['relevance'];
                if ($row['relevance'] > $curr_rel) {
                    $data[$pfs_id]['releases'][$version] = $row;
                }
            }
        }

        /*
         * Collect aliases for the pfs_id's found in releases.
         */
        if (!empty($data)) {

            $params = array_keys($data);

            // Build SQL to find aliases for known pfs_id's
            $sql = join(' ', array(
                'SELECT ',
                '   `plugins`.`pfs_id` as pfs_id,', 
                '   `plugin_aliases`.`alias` as alias,',
                '   `is_regex` as is_regex',
                'FROM plugin_aliases',
                'JOIN (`plugins`) ON (`plugin_aliases`.`plugin_id` = `plugins`.`id`)',
                'WHERE `plugins`.`pfs_id`',
                'IN (' . str_repeat('?,', count($params)-1) . '?)'
            ));
            $aliases_lookup = 
                $this->db->prepareStatement($sql, array('pfs_id', 'alias'));

            // Execute the SQL with known pfs_ids
            array_unshift($params, str_repeat('s', count($params)));
            call_user_func_array(array($aliases_lookup->stmt, 'bind_param'), $params);
            $aliases_lookup->stmt->execute();
            $aliases_lookup->stmt->bind_result($pfs_id, $alias, $is_regex);

            // Gather the aliases into the output.
            while ($aliases_lookup->stmt->fetch()) {
                $data[$pfs_id]['aliases'][($is_regex) ? 'regex' : 'literal'][] = $alias;
            }

        }

        /*
         * Perform some final massaging of the data for easier digestion in a 
         * JS client.  Trade pfs_ids for numeric indices; separate releases into
         * the latest release and a list of others.
         */
        $flat = array();
        foreach ($data as $pfs_id => $plugin) {

            $rs = array(
                'latest' => null,
                'others' => array()
            );

            // Comb through releases to find most relevant latest, collect the 
            // rest under 'others'
            foreach ($plugin['releases'] as $version => $r) {
                if ('latest' == $r['status']) {
                   if (!$rs['latest'] || $r['relevance'] > $rs['latest']['relevance']) {
                        $rs['latest'] = $r;
                    }
                } else {
                    $rs['others'][] = $r;
                }
            }

            $plugin['releases'] = $rs;
            $flat[] = $plugin;

        }

        return $flat;
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
        if (preg_match('/^ppc mac os x/', $client_os)) {
            $list[] = 'ppc mac os x';
        }
        if (preg_match('/^intel mac os x/', $client_os)) {
            $list[] = 'intel mac os x';
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
     * Calculate result relevance based on the criteria values.
     */
    public function calcRelevance($row, $criteria) {
        $rel = 0;
        
        // First, bump relevance where the match was not a wildcard
        $cols = array('os_name', 'app_id', 'app_release', 'app_version', 'locale');
        foreach ($cols as $name) {
            if ('*' !== $row[$name]) $rel++;
        }

        // Next, bump the relevance by how close to the top of the list of 
        // normalized client OS alternatives the row's value matches.
        if (!empty($row['os_name']) && !empty($criteria['clientOS'])) {
            foreach ($criteria['clientOS'] as $idx => $c_name) {
                if ($c_name === $row['os_name']) {
                    $rel += (count($criteria['clientOS']) - $idx);
                    break;
                }
            }
        }

        // TODO: Support lists of locales in the same way as OS

        return $rel;
    }

    /**
     * Load plugin into the database by either updating existing info, or 
     * deleting and replacing it.
     *
     * @param array|string plugin data as an array or JSON string
     * @param boolean      delete the plugin by pfs_id before updating
     */
    public function loadPlugin($plugin, $delete_first=TRUE)
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
                'name', 'description', 'vendor',
                'status_code', 'guid', 'version', 
                'xpi_location',
                'installer_location', 'installer_hash', 'installer_shows_ui',
                'icon_url', 'url',
                'manual_installation_url', 'license_url', 'needs_restart',
                'min', 'max', 'xpcomabi', 
                'vulnerability_url', 'vulnerability_description',
                'modified'
            )
        );

        $plugins_mimes_insert = $this->db->prepareInsertOrUpdate(
            'plugins_mimes', array('plugin_id', 'mime_id')
        );

        $mimes_insert = $this->db->prepareInsertOrUpdate(
            'mimes', array('name', 'description', 'suffixes')
        );

        $platforms_insert = $this->db->prepareInsertOrUpdate(
            'platforms', array('app_id', 'app_release', 'app_version', 'locale')
        );

        $oses_insert = $this->db->prepareInsertOrUpdate(
            'oses', array('name')
        );

        $os_lookup = $this->db->prepareStatement(
            'SELECT id FROM oses WHERE name=?', array('name')
        );

        $meta_defaults = array(
            'status_code' => self::$status_codes['latest'],
            'installer_shows_ui' => 'true',
            'needs_restart' => 'true',
            'created' => gmdate('c'),
            'modified' => gmdate('c')
        );
        $release_defaults = array(
            'os_name' => '*'
        );
        $platform_defaults = array(
            'app_id' => '*',
            'app_release' => '*',
            'app_version' => '*',
            'locale' => '*'
        );

        // Set up aliases accumulator
        $aliases = array(
            'literal' => isset($plugin['aliases']['literal']) ? 
                $plugin['aliases']['literal'] : array(),
            'regex' => isset($plugin['aliases']['regex']) ? 
                $plugin['aliases']['regex'] : array(),
        );

        $meta = array_merge($meta_defaults, $plugin['meta']);

        // If necessary, delete the plugin before inserting updates.
        if ($delete_first) {
            $this->db->prepareStatement(
                "DELETE FROM plugins WHERE pfs_id=?", array('pfs_id')
            )->execute_finish(array(
                'pfs_id' => $meta['pfs_id']
            ));
        }

        $p_id = $plugin_insert->execute_finish($meta);

        if (!empty($plugin['mimes'])) foreach ($plugin['mimes'] as $mime) {
            if (is_string($mime)) {
                $mime = array( 'name' => $mime );
            }
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

            $os_id = $oses_insert->execute_finish(array(
                'name'=>$release['os_name']
            ));
            $release['os_id'] = $os_id;

            $platform_id = $platforms_insert->execute_finish(array_merge(
                $platform_defaults, $release['platform']
            ));
            $release['platform_id'] = $platform_id;

            if (!empty($release['status']) && 
                    !empty(self::$status_codes[$release['status']])) {
                $release['status_code'] = self::$status_codes[$release['status']];
            }

            if (!empty($release['vulnerability_description']) ||
                    !empty($release['vulnerability_url'])) {
                $release['status_code'] = self::$status_codes['vulnerable'];
            }

            $pr_id = $plugin_release_insert->execute_finish($release);
            if ($pr_id) $release_ids[] = $pr_id;

            $aliases['literal'][] = $release['name'];

        }

        // Update the list of known aliases for this plugin.
        $plugin_aliases_insert = $this->db->prepareInsertOrUpdate(
            'plugin_aliases', array('plugin_id', 'alias', 'is_regex')
        );

        foreach (array('literal', 'regex') as $kind) {
            $is_regex = ('regex' == $kind) ? 1 : 0;
            $a = array_unique($aliases[$kind]);
            foreach ($a as $alias) {
                $plugin_aliases_insert->execute_finish(array(
                    'plugin_id' => $p_id, 
                    'alias' => $alias,
                    'is_regex' => $is_regex
                ));
            }
        }

        // Mark all other non-vulnerable releases as outdated.
        $params = array_merge(
            array(
                self::$status_codes['outdated'], 
                self::$status_codes['vulnerable'], 
                $p_id
            ), 
            $release_ids
        );
        $sql = join("\n", array(
            'UPDATE `plugin_releases`',
            'SET `status_code`=?',
            'WHERE `plugin_releases`.`status_code` <> ? AND',
            '      `plugin_releases`.`plugin_id` = ? AND',
            '      `plugin_releases`.`id` ',
            '           NOT IN ('.str_repeat('?,', count($params)-4).'?)',
        ));
        $update = $this->db->prepareStatement($sql, null);

        array_unshift($params, str_repeat('s', count($params)));
        call_user_func_array(array($update->stmt, 'bind_param'), $params);
        $update->stmt->execute();
        $update->finish();

        return array($p_id, $release_ids);
    }

}
