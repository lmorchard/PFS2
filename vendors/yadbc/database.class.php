<?php
/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1/GPL 2.0/LGPL 2.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is yadbc
 *
 * The Initial Developer of the Original Code is
 * Mozilla Corporation.
 * Portions created by the Initial Developer are Copyright (C) 2008
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *   Wil Clouser <clouserw@mozilla.com> (Original Author)
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 * ***** END LICENSE BLOCK ***** */

class Database {

    /**
     * Convenience constants.  These are used to refer to the data sources.
     */
    const SOURCE_PRIMARY  = 1;
    const SOURCE_SHADOW   = 2;
    const SOURCE_MEMCACHE = 4;

    /**
     * The primary database connection information.
     *
     *   array(
     *     'hostname' => 'localhost',
     *     'port'     => 3306,
     *     'username' => '',
     *     'password' => '',
     *     'database' => ''
     *   )
     */
    public $primary_config  = array();
    private $primary_handle = null;

    /**
     * The primary database connection information.  Weight MUST add up to 100 across
     * all servers.
     *
     *   array(
     *     0 => array(
     *            'hostname' => 'localhost',
     *            'port'     => 3306,
     *            'username' => '',
     *            'password' => '',
     *            'database' => ''
     *            'weight'   => 100
     *           )
     *   )
     */
    public $shadow_config  = array();
    private $shadow_handle = null;

    /**
     * Lists all possible memcached servers to use.  The format is:
     * array(
     *   'hostname' => array(
     *      'port'           => '11211',
     *      'persistent'     => true,
     *      'weight'         => '1',
     *      'timeout'        => '1',
     *      'retry_interval' => 15
     *   )
     * )
     */
    public $memcache_config = array();
    private $memcaching     = null;

    public function __destruct() {
        if (is_object($this->primary_handle))
            $this->primary_handle->close();

        if (is_object($this->shadow_handle))
            $this->shadow_handle->close();

        if (is_object($this->memcaching))
            unset($this->memcaching);
    }

    /**
     * A convenience function to connect the primary db, shadow db, and memcache
     */
    public function connectAll() {
        $this->connect(array(self::SOURCE_PRIMARY, self::SOURCE_SHADOW, self::SOURCE_MEMCACHE));
    }

    /**
     * Connect data sources. 
     *
     * @param array sources to try to connect to, in the order to try.  
     * @throws Exception - on failure to connect to a database
     */
    public function connect(array $sources) {
        foreach ($sources as $source) {
            switch ($source) {
                case self::SOURCE_PRIMARY:
                    if (!empty($this->primary_config)) {
                        $this->primary_handle = new mysqli(
                            $this->primary_config['hostname'],
                            $this->primary_config['username'],
                            $this->primary_config['password'],
                            $this->primary_config['database'],
                            $this->primary_config['port']
                        );

                        $_primary_failed = (!is_object($this->primary_handle) || mysqli_connect_errno()) ? mysqli_connect_error() : false;
                    }
                    break;
                case self::SOURCE_SHADOW:
                    $_config = $this->chooseShadow();
                    if ($_config) {
                        $this->shadow_handle = new mysqli(
                            $_config['hostname'],
                            $_config['username'],
                            $_config['password'],
                            $_config['database'],
                            $_config['port']
                        );

                        $_shadow_failed = (!is_object($this->shadow_handle) || mysqli_connect_errno()) ? mysqli_connect_error() : false;
                    }
                    break;
                case self::SOURCE_MEMCACHE:
                    if (!class_exists('memcaching')) {
                        require_once dirname(__FILE__).'/memcaching.class.php';
                    }
                    $this->memcaching = new Memcaching($this->memcache_config);
                    break;
                default:
                    break;
            }
        }

        // Throw exceptions after we've tried to connect everything so if the primary
        // fails the supplemental sources will still be connected.  
        if (isset($_primary_failed) && $_primary_failed !== false) {
            throw new Exception("Failed to connect primary database: {$_primary_failed}", E_USER_ERROR);
        }
        if (isset($_shadow_failed) && $_shadow_failed !== false) {
            throw new Exception("Failed to connect shadow database: {$_shadow_failed}", E_USER_ERROR);
        }
    }

    /**
     * Pick a shadow database to connect to from $this->shadow_config according to
     * their specified weights.  The format for that array is documented at the 
     * variable declaration.
     *
     * @throws Exception - if the shadow_config array is not empty, but there were no
     *                      valid options to connect to
     * @return mixed a config array on success, false on failure
     */
    private function chooseShadow() {
        if (!empty($this->shadow_config)) {

            $database_keys = array();

            // Store shadow db keys in an array proportionate to their weight
            foreach ($this->shadow_config as $k => $shadow_database) {
                if ($shadow_database['weight'] > 0)
                    $database_keys = array_merge($database_keys, array_fill(0, $shadow_database['weight'] * 100, $k));
            }
            
            if (!empty($database_keys)) {
                // Select random database from weighted array
                return $this->shadow_config[$database_keys[array_rand($database_keys)]];
            }

            throw new Exception('Failed to retrieve a valid shadow database.', E_USER_ERROR);

        }
        return false;
    }

    /**
     * Disconnect data sources.  Disconnecting memcache will disconnect _all_
     * memcache servers.
     *
     * @param array sources to try to connect to, in the order to try.  
     */
    public function disconnect(array $sources) {
        foreach ($sources as $source) {
            switch ($source) {
                case self::SOURCE_PRIMARY:
                    if (is_object($this->primary_handle))
                        $this->primary_handle->close();
                    $this->primary_handle = null;
                    break;
                case self::SOURCE_SHADOW:
                    if (is_object($this->shadow_handle))
                        $this->shadow_handle->close();
                    $this->shadow_handle = null;
                    break;
                case self::SOURCE_MEMCACHE:
                    if (is_object($this->memcaching))
                        $this->memcaching->close();
                    $this->memcaching = null;
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Retrieve a connected database handle.  Useful if you want to do operations
     * directly on the database handle.
     *
     * @param int source to try to connect to, class constatns
     * @return mixed database handle on success, false on failure
     */
    public function getHandle($source) {
        switch ($source) {
            case self::SOURCE_PRIMARY:
                if (is_object($this->primary_handle))
                    return $this->primary_handle;
                return false;
            case self::SOURCE_SHADOW:
                if (is_object($this->shadow_handle))
                    return $this->shadow_handle;
                return false;
            case self::SOURCE_MEMCACHE:
                if (is_object($this->memcaching))
                    return $this->memcaching;
                return false;
            default:
                return false;
        }
    }

    /**
     * Escape a string for inserting into MySQL.
     *
     * @throws Exception - no db handle to escape string with
     * @param string to escape
     * @return escaped string
     */
    public function real_escape_string($string) {
        if (is_object($this->primary_handle)) {
            return $this->primary_handle->real_escape_string($string);
        }
        if (is_object($this->shadow_handle)) {
            return $this->shadow_handle->real_escape_string($string);
        }
        throw new Exception('Attempt to escape string, but no database available.');
    }

    /**
     * Run a query against a data source (and optionally memcache).  All connections
     * must be made before this function is called.  If a result is
     * not found in memcache and other sources are available, it will automatically
     * look in the other sources.  Also, if memcache is specified and a result is
     * found from another source, the result is automatically stored in memcache if
     * the query matches /^select/i
     *
     * @throws Exception  - for attempt to connect to unknown data source
     * @param string query to run
     * @param array sources to try to connect to, in the order to try.  
     * @return mixed a mysqli object on success, false on failure
     */
    public function query($query, array $sources=array(self::SOURCE_MEMCACHE, self::SOURCE_SHADOW, self::SOURCE_PRIMARY)) {

        // If the query doesn't start with select, we override any options and go
        // straight to the primary database
        if (0 !== strpos(strtolower(ltrim($query)), 'select')) {
            $sources = array(self::SOURCE_PRIMARY);
        }

        foreach ($sources as $source) {
            switch ($source) {
                case self::SOURCE_MEMCACHE:
                    if (is_object($this->memcaching)) {
                        $_return = $this->memcaching->get($query);
                        if ($_return === false) {
                            continue;
                        } else {
                            return $_return;
                        }
                    }
                    break;
                case self::SOURCE_SHADOW:
                    if (is_object($this->shadow_handle)) {
                        $_return = $this->shadow_handle->query($query);
                        // If we checked memcache, we'll put it in there for next time
                        if (   in_array(self::SOURCE_MEMCACHE, $sources) 
                            && is_object($this->memcaching)
                            && (0 === strpos(strtolower(ltrim($query)), 'select'))
                            ) {
                            $this->memcaching->set($query, $_return);
                        }
                        return $_return;
                    }
                    break;
                case self::SOURCE_PRIMARY:
                    if (is_object($this->primary_handle)) {
                        $_return = $this->primary_handle->query($query);
                        // If we checked memcache, we'll put it in there for next time
                        if (   in_array(self::SOURCE_MEMCACHE, $sources) 
                            && is_object($this->memcaching)
                            && (0 === strpos(strtolower(ltrim($query)), 'select'))
                            ) {
                            $this->memcaching->set($query, $_return);
                        }
                        return $_return;
                    }
                    break;
                default:
                    throw new Exception("Attempt to connect to non-existant data source: {$source}");
                    break;
            }
        }

        return false;
    }

}
?>
