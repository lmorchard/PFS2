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

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/config.php';
require_once dirname(__FILE__).'/../database.class.php';
 
class DatabaseTest extends PHPUnit_Framework_TestCase
{
    protected $primary_database;
    protected $shadow_databases;
    protected $memcache_config;

    protected $db;

    public function setUp() {

        $this->db = new Database();

        global $primary_database, $shadow_databases, $memcache_config;

        $this->db->primary_config = $primary_database;
        $this->db->shadow_config = $shadow_databases;
        $this->db->memcache_config  = $memcache_config;
    }

    public function reset() {

        unset($this->db);

        $this->setUp();
    }

    public function testConnectPrimary() {

        $this->reset();

        // Attempt to connect with valid data
        $this->db->connectPrimary();
        $this->db->disconnectPrimary();

        // Attempt to connect with an invalid username
        $this->setExpectedException('Exception');
        $this->db->primary_config['username'] = 'nobody';
        $this->db->connectPrimary();
        $this->db->disconnectPrimary();
    }

    public function testConnectShadow() {

        $this->reset();

        // Attempt to connect with valid data
        $this->db->connectShadow();
        $this->db->disconnectShadow();

        // Attempt to connect with an invalid username
        $this->setExpectedException('Exception');
        $this->db->shadow_config[0]['username'] = 'nobody';
        $this->db->connectShadow();
    }

    public function testConnectMemcache() {

        $this->reset();

        // Attempt to connect with valid data
        $this->db->connectMemcache();
        $this->db->disconnectMemcache();

        // Attempt to connect with an invalid username
        // NOTE: This is currently not throwing an exception.  Even given a totally
        // bogus port memcache's addServer() isn't failing.
        $this->setExpectedException('Exception');
        $this->db->memcache_config['localhost']['port'] = 9999;
        $this->db->connectMemcache();
    }

    public function testQuery() {

        $this->reset();

        // Connect nothing, ask for exiting data
        $this->assertFalse($this->db->query("SELECT * FROM locales LIMIT 1"));

        // Connect only shadow, ask for non-existant data.
        $this->db->connectShadow();
        $_result = $this->db->query("SELECT * FROM locales WHERE id='fake'");
        $this->assertEquals($_result->num_rows, 0);
        $this->db->disconnectShadow();

        // Connect only primary, ask for non-existant data.
        $this->db->connectPrimary();
        $_result = $this->db->query("SELECT * FROM locales WHERE id='fake'");
        $this->assertEquals($_result->num_rows, 0);
        $this->db->disconnectPrimary();

        // Connect only memcache, ask for non-existant data.
        $this->db->connectMemcache();
        $this->assertFalse($this->db->query("I don't exist."));
        $this->db->disconnectMemcache();


        // Connect only shadow, ask for existing data.
        $this->db->connectShadow();
        $_result = $this->db->query("SELECT * FROM locales LIMIT 1");
        $this->assertEquals($_result->num_rows, 1);
        $this->db->disconnectShadow();

        // Connect only primary, ask for existing data.
        $this->db->connectPrimary();
        $_result = $this->db->query("SELECT * FROM locales LIMIT 1");
        $this->assertEquals($_result->num_rows, 1);
        $this->db->disconnectPrimary();

        // Connect memcache+primary, ask for existing data, verify memcache == primary
        $this->db->connectPrimary();
        $this->db->connectMemcache();
        $_result_primary = $this->db->query("SELECT * FROM locales LIMIT 1");
        $this->db->disconnectPrimary();
        $_result_memcache = $this->db->query("SELECT * FROM locales LIMIT 1");
        $this->assertEquals($_result_primary, $_result_memcache);
        $this->db->disconnectMemcache();

        // Attempt to query from bogus source
        $this->setExpectedException('Exception');
        $this->db->query('SELECT * FROM locales LIMIT 1', array('bogus'));

        // Connect memcache+shadow+primary, ask _only memcache_ for existing data that 
        // is _not_ in memcache but is in the database
        $this->db->connectAll();
        $this->assertFailure($this->db->query("SELECT * FROM locales LIMIT 2", array(Database::DATABASE_MEMCACHE)));

    }


}
?>
