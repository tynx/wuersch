<?php

class Store {
	private static $pdo = null;
	private static $query = null;

	private $statementResult = null;

	public function __construct() {
		if (Store::$pdo === null) {
			$this->_init();
		}
	}

	private function _init() {
		Store::$query = new StoreMysqlQuery(Config::PDO_DATABASE);
		$host = 'host=' . Config::PDO_HOST;
		$db = 'dbname=' . Config::PDO_DATABASE;
		$charset = 'charset=' . Config::PDO_CHARSET;
		$user = Config::PDO_USER;
		Store::$pdo = new PDO(
			'mysql:' . implode(';', array($host, $db, $charset) ),
			Config::PDO_USER,
			Config::PDO_PASSWORD
		);
	}

	private function _buildWhereColumns($id) {
		$whereColumns = array();
		if (!is_numeric($id) && strlen($id) === 32) {
			$whereColumns['id_md5'] = $id;
		} else {
			$whereColumns['id'] = (int)$id;
		}
		return $whereColumns;
	}

	private function execute($sql, $whereColumns = null, $valueColumns = null, $fetch = false, $fetchClass = null) {
		if (substr($sql, -1) !== ';') {
			$sql .= ';';
		}
		$statement = Store::$pdo->prepare($sql);
		if ($whereColumns !== null && is_array($whereColumns)) {
			foreach ($whereColumns as $key => $value) {
				$statement->bindValue(':w' . $key, $value);
			}
		}
		if ($valueColumns !== null && is_array($valueColumns)) {
			foreach ($valueColumns as $key => $value) {
				$statement->bindValue(':v' . $key, $value);
			}
		}
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

	public function getByColumns($table, $whereColumns, $combination = 'AND') {
		$sql = Store::$query->getSelectSql($table, $whereColumns, $combination);
		if (class_exists(ucfirst($table), false)) {
			$class = ucfirst($table);
		}
		if ($this->execute($sql, $whereColumns, null, true, $class)) {
			return $this->statementResult;
		}
		return null;
	}

	public function insert($table, $valueColumns) {
		if (!is_array($valueColumns)) {
			return -1;
		}
		$sql = Store::$query->getInsertSql($table, $valueColumns);
		if ($this->execute($sql, null, $valueColumns)) {
			return $this->statementResult;
		}
		return -1;
	}


	public function updateByColumns($table, $valueColumns, $whereColumns = null, $combination = 'AND') {
		if (!is_array($valueColumns) && !is_array($valueColumns)) {
			return false;
		}
		$sql = Store::$query->getUpdateSql($table, $valueColumns, $whereColumns, $combination);
		if ($this->execute($sql, $whereColumns, $valueColumns)) {
			return $this->statementResult;
		}
		return false;
	}

	public function deleteByColumns($table, $whereColumns, $combination = 'AND') {
		$sql = Store::$query->getDeleteSql($table, $whereColumns, $combination);
		if ($this->execute($sql, $whereColumns, null)) {
			return $this->statementResult;
		}
		return null;
	}

	public function getById($table, $id) {
		$result = $this->getByColumns($table, $this->_buildWhereColumns($id));
		if (is_array($result) && count($result) === 1) {
			return $result[0];
		}
		return null;
	}

	public function updateById($table, $id, $valueColumns) {
		return $this->updateByColumns($table, $valueColumns, $this->_buildWhereColumns($id));
	}

	public function deleteById($table, $id) {
		return $this->deleteByColumns($table, $this->_buildWhereColumns($id));
	}
}
