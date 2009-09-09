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
 * The Original Code is addons.mozilla.org site.
 *
 * The Initial Developer of the Original Code is
 * Mozilla Corporation.
 * Portions created by the Initial Developer are Copyright (C) 2007
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *   Frederic Wenzel <fwenzel@mozilla.com> (Original Author)
 *   Wil Clouser <clouserw@mozilla.com>
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

/**
 * This model is an interface to Memcache.
 * It's called Memcaching to not interfere with the actual Memcache class.
 */
class Memcaching {

    /**
     * Did we find a valid memcache server?
     */
    public $memcache_connected = false;

    /**
     * Will be prefixed to the keys.  Can be overridden with a MEMCACHE_PREFIX define
     */
    public $memcache_prefix = '';

    /**
     * How long to store elements in memcache in seconds.  Can be overridden with
     * a MEMCACHE_TIMEOUT define
     */
    public $memcache_timeout = 60;

    /**
     * Holds a copy of the configuration
     */
    private $memcache_config;

    /**
     * Holds the actual Memcache object
     */
    private $cache;

    /**
     * Constructor
     *
     * @param array configuration.  Format is specified in the config file.
     * @throws Exception - Memcache class non-existant
     * @throws Exception - Unable to connect to any memcache servers
     */
    public function __construct(array $config) {

        $this->memcache_config = $config;

        if (class_exists('Memcache'))
            $this->cache = new Memcache();
        else
            throw new Exception("Attempt to load memcache but the class doesn't exist.", E_USER_ERROR);

        // If a prefix is defined in a config and it hasn't been overridden already
        if (defined('MEMCACHE_PREFIX') && empty($this->memcache_prefix))
            $this->memcache_prefix = MEMCACHE_PREFIX;

        // If a timeout is defined in a config and it hasn't been overridden already
        if (defined('MEMCACHE_TIMEOUT') && empty($this->memcache_timeout))
            $this->memcache_timeout = MEMCACHE_TIMEOUT;

        if (is_array($this->memcache_config)) {
            foreach ($this->memcache_config as $host=>$options) {
                if ($this->cache->addServer($host, $options['port'], $options['persistent'], $options['weight'], $options['timeout'], $options['retry_interval'])) {
                    $this->memcache_connected = true;
                }
            }
        }

        if (!$this->memcache_connected)
            throw new Exception("Unable to connect to any memcache servers.", E_USER_WARNING);
    }

    /**
     * Destructor
     */
    public function __destruct() {
        if (!$this->memcache_connected) return false;
        $this->cache->close();
    }

    /**
     * Memcache seems to have problems with long key names.  Using this function we
     * create unique key names that are relatively short
     *
     * @param string the string we'll be hashing
     * @param mixed something else that would uniquely identify this information.
     * For example, the URL or class information.  The important thing is that this is
     * always the same for this hash.  This parameter must be serializable.
     * @return string the hash key
     */
    public function buildKey($string, $additional_identification = null) {

        if (!is_null($additional_identification)) {
            $string .= serialize($additional_identification);
        }

        $_key = md5($string);

        return $this->memcache_prefix.$_key;
    }

    /**
     * Get an item from the cache, if it exists.  This will automatically convert the
     * query to a key before checking memcache.
     *
     * @param string whatever you're looking for
     * @param mixed additional info.  More details in self::buildKey
     * @return mixed item if found, else false
     */
    public function get($string, $additional_identification = null) {
        if (!$this->memcache_connected) return false;
        $_key = $this->buildKey($string, $additional_identification);
        return $this->getFromKey($_key);
    }

    /**
     * Get an item from the cache, if it exists.  This assumes you're passing in a
     * key already and will do no conversions.
     *
     * @param string key to look for in memcache
     * @return mixed item if found, else false
     */
    public function getFromKey($key) {
        if (!$this->memcache_connected) return false;
        return $this->cache->get($key);
    }

    /**
     * Store an item in the cache. Replaces an existing item.
     * @return bool success
     */
    public function set($string, $var, $flag = null, $expire = null) {
        if (!$this->memcache_connected) return false;
        $expire = is_null($expire) ? $this->memcache_timeout : $expire;
        $_key = $this->buildKey($string);
        return $this->cache->set($_key, $var, $flag, $expire);
    }

    /**
     * Store an item in the cache. Replaces an existing item.
     * @return bool success
     */
    public function setWithKey($key, $var, $flag = null, $expire = null) {
        if (!$this->memcache_connected) return false;
        $expire = is_null($expire) ? $this->memcache_timeout : $expire;
        return $this->cache->set($key, $var, $flag, $expire);
    }
    
    /**
     * Store an item in the cache. Returns false if the key is
     * already present in the cache.
     * @return bool success
     */
    public function add($key, $var, $flag = null, $expire = null) {
        if (!$this->memcache_connected) return false;
        $expire = is_null($expire) ? $this->memcache_timeout : $expire;
        return $this->cache->add($key, $var, $flag, $expire);
    }

    /**
     * Store an item in the cache. Returns false if the key did
     * NOT exist in the cache before.
     * @return bool success
     */
    public function replace($key, $var, $flag = null, $expire = null) {
        if (!$this->memcache_connected) return false;
        $expire = is_null($expire) ? $this->memcache_timeout : $expire;
        return $this->cache->replace($key, $var, $flag, $expire);
    }

    /**
     * Close the connection to _ALL_ cache servers
     * @return bool success
     */
    public function close() {
        if (!$this->memcache_connected) return false;
        return $this->cache->close();
    }

    /**
     * Delete something off the cache
     * @return bool success
     */
    public function delete($key, $timeout = null) {
        if (!$this->memcache_connected) return false;
        return $this->cache->delete($key, $timeout);
    }

    /**
     * Flush the cache
     * @return bool success
     */
    public function flush() {
        if (!$this->memcache_connected) return false;
        return $this->cache->flush();
    }

    /**
     * Get server statistics.
     * return array
     */
    public function getExtendedStats() {
        if (!$this->memcache_connected) return false;
        return $this->cache->getExtendedStats();
    }
}
?>
