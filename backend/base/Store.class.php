<?php

/**
 * This class provides an easy way to talk to a persistent store. For
 * this project only mysql is currently supported.
 * @author Tim Luginbühl (tynx)
 */
class Store {

	/**
	 * The pdo object itself.
	 */
	private static $pdo = null;

	/**
	 * The query object. Currently only mysql is supported.
	 */
	private static $query = null;

	/**
	 * The results are temporary stored in here
	 */
	private $statementResult = null;

	/**
	 * The very first the object gets instanziated the pdo will be setup
	 * and otherwise we have nothing to do.
	 */
	public function __construct() {
		if (Store::$pdo === null) {
			Store::init();
		}
	}

	/**
	 * This method initializes the pdo-object. This method should only
	 * be called once per runtime, as the pdo-object is static and all
	 * queries should go trough the same object. 
	 */
	private static function init() {
		Store::$query = new StoreMysqlQuery(Config::PDO_DATABASE);
		$host = 'host=' . Config::PDO_HOST;
		$db = 'dbname=' . Config::PDO_DATABASE;
		$charset = 'charset=' . Config::PDO_CHARSET;
		$user = Config::PDO_USER;
		Store::$pdo = new PDO(
			'mysql:' . implode(';', array($host, $db, $charset)),
			Config::PDO_USER,
			Config::PDO_PASSWORD
		);
	}

	/**
	 * This is a little helper method which builds the according
	 * whereColumns-array if only an ID is provided. It does check if
	 * the provided ID is a public one (id_md5) or an integer-based one
	 * and does build the condition accordingly.
	 * @param id the id to build whereColumns from, may be integer or 
	 * md5 (public ID)
	 * @return whereColumns in form of assoc-array 
	 */
	private function _buildWhereColumns($id) {
		$whereColumns = array();
		if (!is_numeric($id) && strlen($id) === 32) {
			$whereColumns['id_md5'] = $id;
		} else {
			$whereColumns['id'] = (int)$id;
		}
		return $whereColumns;
	}

	/**
	 * This methods binds the provided params to the pdo-statement.
	 * valueColumns have prefix :v (as in (V)alues) and whereColumns
	 * have prefix :w (as in (W)here).
	 */
	private function _bindParams($statement, $whereColumns, $valueColumns) {
		if ($whereColumns !== null && is_array($whereColumns)) {
			foreach ($whereColumns as $key => $value) {
				$statement->bindValue(
					StoreMysqlQuery::PARAM_PREFIX . 'w' . $key,
					$value
				);
			}
		}
		if ($valueColumns !== null && is_array($valueColumns)) {
			foreach ($valueColumns as $key => $value) {
				$statement->bindValue(
					StoreMysqlQuery::PARAM_PREFIX . 'v' . $key,
					$value
				);
			}
		}
	}

	private function _execute($sql, $whereColumns = null, $valueColumns = null, $fetch = false, $fetchClass = null) {
		$statement = Store::$pdo->prepare($sql);
		$this->_bindParams($statement, $whereColumns, $valueColumns);
		if (!$statement->execute()) {
			return false;
		}
		if (!$fetch && $statement->rowCount() > 1) {
			$this->statementResult = true;
		} elseif (!$fetch) {
			$this->statementResult = (int)Store::$pdo->lastInsertId();
			if ($this->statementResult === 0 && $statement->rowCount() === 1) {
				$this->statementResult = true;
			} elseif ($this->statementResult === 0 && $statement->rowCount() === 0) {
				$this->statementResult = false;
			}
		} elseif ($fetchClass === null) {
			$this->statementResult = $statement->fetchAll(PDO::FETCH_ASSOC);
		} else {
			$this->statementResult = $statement->fetchAll(PDO::FETCH_CLASS, $fetchClass);
		}
		return true;
	}

	/**
	 * Fetches rows from a table based on given condition. ! There is
	 * automatically a LIMIT added for safety reasons. See constant in
	 * StoreMysqlQuery !
	 * @param table the name of the table to SELECT from
	 * @param whereColumns the conditions
	 * @param combination should the whereColumns concated with AND or
	 * OR
	 * @return the fetched rows in the according domain-object or if not
	 * existent as assoc-array
	 */
	public function getByColumns($table, $whereColumns, $combination = 'AND') {
		$sql = Store::$query->getSelectSql($table, $whereColumns, $combination);
		if (class_exists(ucfirst($table), false)) {
			$class = ucfirst($table);
		}
		if ($this->_execute($sql, $whereColumns, null, true, $class)) {
			return $this->statementResult;
		}
		return null;
	}

	/**
	 * Inserts a row into a table
	 * @param table the table to insert to
	 * @param valueColumns the values to be set
	 * @return -1 on failure otherwise inserted ID
	 */
	public function insert($table, $valueColumns) {
		if (!is_array($valueColumns)) {
			return -1;
		}
		$sql = Store::$query->getInsertSql($table, $valueColumns);
		if ($this->_execute($sql, null, $valueColumns)) {
			return $this->statementResult;
		}
		return -1;
	}

	/**
	 * This updates rows in a table based on the provided whereColumns
	 * (conditions).
	 * @param table the name of the table to update in
	 * @param valueColumns the to be set values
	 * @param whereColumns the values used for the condition
	 * @param combination should the whereColumns OR or AND concated
	 * @return true if one or more rows were affected
	 */
	public function updateByColumns(
		$table,
		$valueColumns,
		$whereColumns = null,
		$combination = 'AND'
	) {
		if (!is_array($valueColumns) && !is_array($valueColumns)) {
			return false;
		}
		$sql = Store::$query->getUpdateSql($table, $valueColumns, $whereColumns, $combination);
		if ($this->_execute($sql, $whereColumns, $valueColumns)) {
			return $this->statementResult;
		}
		return false;
	}

	/**
	 * This deletes from a table based on the provided whereColumns
	 * (conditions).
	 * @param table the name of the table to update in
	 * @param whereColumns (key,value)-array of the columns to build
	 * condition of.
	 * @param combination whetever the whereColumns should be concated
	 * with AND or OR.
	 * @return true if one or more columns where affected.
	 */
	public function deleteByColumns($table, $whereColumns, $combination = 'AND') {
		$sql = Store::$query->getDeleteSql($table, $whereColumns, $combination);
		if ($this->_execute($sql, $whereColumns, null)) {
			return $this->statementResult;
		}
		return false;
	}

	/**
	 * Returns an entry based on the provided id
	 * @param table the name of the table to select from
	 * @param id the id of the entry to fetch
	 * @return the according object of the entry. if no domain-model
	 * found it retuns an assoc-arrays
	 */
	public function getById($table, $id) {
		$result = $this->getByColumns($table, $this->_buildWhereColumns($id));
		if (is_array($result) && count($result) === 1) {
			return $result[0];
		}
		return null;
	}

	/**
	 * This updates a row based on the provided id and new values.
	 * @param table the name of the table do update in
	 * @param id the id of the entry to update
	 * @param valueColumns (key,value)-array of the columns to udpate
	 * @return true if one or more rows were affected
	 */
	public function updateById($table, $id, $valueColumns) {
		return $this->updateByColumns($table, $valueColumns, $this->_buildWhereColumns($id));
	}

	/**
	 * This deletes an entry in the according table based on the
	 * provided id.
	 * @param table the name of the table to delete from
	 * @param id the id of the entry to delete
	 * @return returns true if one or more rows were affected
	 */
	public function deleteById($table, $id) {
		return $this->deleteByColumns($table, $this->_buildWhereColumns($id));
	}
}
