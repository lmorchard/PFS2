<?php
/**
 * Extensions to Mozilla YADBC
 *
 * @package    Mozilla_PFS2
 * @subpackage libraries
 * @author     lorchard@mozilla.com
 */
require_once 'database.class.php';
require_once 'memcaching.class.php';

/**
 * Extension wrapper for Mozilla YADBC
 */
class Mozilla_PFS2_Database extends Database
{

    /**
     * Simple string or array escaping using the connection's 
     * real_escape_string method.
     *
     * @param mixed  Array of strings or single string
     * @return mixed Same as input parameter, but escaped
     */
    public function escape($val) {
        if (is_array($val)) {
            $out = array();
            foreach ($val as $v)
                $out[] = $this->escape($v);
            return $out;
        } else {
            $dbh = $this->getHandle(Database::SOURCE_PRIMARY);
            return "'".$dbh->real_escape_string($val)."'";
        }
    }

    /**
     * Prepare an INSERT statement for the given named table and list 
     * of field names.
     *
     * @param string Name of DB table for insert
     * @param array  List of field names for bind parameters
     * @return Mozilla_PFS2_Database_Statement
     */
    public function prepareInsert($table, $field_names)
    {
        return new Mozilla_PFS2_Database_Statement(
            $this, "
                INSERT INTO {$table} 
                (" . join(',', $field_names) . ")
                VALUES
                (" . str_repeat('?,', count($field_names)-1) . "?)
            ",
            $field_names
        );
    }

    /**
     * Prepare an INSERT / UPDATE statement for the given named table 
     * and list of field names.
     *
     * @param string Name of DB table for insert
     * @param array  List of field names for bind parameters
     * @return Mozilla_PFS2_Database_Statement
     */
    public function prepareInsertOrUpdate($table, $field_names)
    {
        return new Mozilla_PFS2_Database_Statement(
            $this, join(' ', array(
                "INSERT INTO {$table}",
                "(" . join(',', $field_names) . ")",
                "VALUES",
                "(" . str_repeat('?,', count($field_names)-1) . "?)",
                "ON DUPLICATE KEY UPDATE",
                "id=LAST_INSERT_ID(id),",
                join(', ', array_map(
                    create_function('$a', 'return "$a=?";'),
                    $field_names
                ))
            )),
            array_merge($field_names, $field_names)
        );
    }

    /**
     * Return a DB statement for the given SQL and field names.
     *
     * @param string SQL to be prepared as a statement
     * @param array  List of field names for bind parameters
     * @return Mozilla_PFS2_Database_Statement
     */
    public function prepareStatement($sql, $field_names=null)
    {
        $stmt = new Mozilla_PFS2_Database_Statement($this, $sql, $field_names);
        return $stmt;
    }

}

/**
 * Utility wrapper around mysqli statements.
 */
class Mozilla_PFS2_Database_Statement 
{
    public $db;
    public $stmt;
    public $sql;
    public $param_names;

    /**
     * Construct the wrapper.
     *
     * @param Mozilla_PFS2_Database Parent database object
     * @param string SQL source for prepared statement
     * @param array  List of field names for bind parameters
     */
    public function __construct($db, $sql, $field_names=null)
    {
        $this->db = $db;
        $this->sql = $sql;
        $this->field_names = $field_names;
        $this->reset();
    }

    /**
     * Execute the prepared statement, using values in the given array 
     * to bind the named parameters.
     *
     * @param array  Parameters to be bound to statement before execution
     * @return mixed Last insert ID, or false if failure.
     */
    public function execute($fields) 
    {
        $types = str_repeat('s', count($this->field_names));
        $args  = array( $types );
        foreach ($this->field_names as $field){
            $args[] = (isset($fields[$field])) ?  $fields[$field] : '';
        }
        call_user_func_array(array($this->stmt, 'bind_param'), $args);
        return $this->stmt->execute() ? $this->stmt->insert_id : FALSE;
    }

    /**
     * Close and reset the statement, making it available for reuse.
     */
    public function finish()
    {
        $this->stmt->close();
        $this->reset();
    }

    /**
     * Reset the statement for reuse.
     */
    public function reset()
    {
        $dbh = $this->db->getHandle(Database::SOURCE_PRIMARY);
        $this->stmt = $dbh->prepare($this->sql);
        if (!$this->stmt)
            throw new Exception($dbh->error);
        return $this->stmt;
    }

    /**
     * Execute the statement with the given fields, and immediately 
     * finish.
     */
    public function execute_finish($fields=null)
    {
        $rv = $this->execute($fields);
        $this->finish();
        return $rv;
    }

    /**
     * Fetch and return the value of the first column of the first row 
     * resulting from executing the statement with the given fields.
     *
     * @param array  Parameters to be bound to statement before execution
     * @return string
     */
    public function fetch_col($fields)
    {
        $val = null;
        $this->execute($fields);
        $this->stmt->bind_result($val);
        $this->stmt->fetch();
        $this->finish();
        return $val;
    }

}
