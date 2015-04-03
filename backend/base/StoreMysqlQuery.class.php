<?php

/**
 * This class builds mysql queries. The motivation is to keep it as
 * generic as possible, so other implementations (like sqlite) could be
 * implemented. The current status is to believed basically as
 * interface, meaning all the needed feature are covered regardless
 * the implementation. Of course NoSQL-stuff would not be compatible
 * 100% and would need a lot of adaption. But definetaly not scope of
 * this project.
 * @author Tim Luginbühl (tynx)
 */
class StoreMysqlQuery {

	/**
	 * The default limit for security/performance reasons pretty low.
	 */
	const DEFAULT_LIMIT = 100;

	/**
	 * pdo-param preffix. for binding params
	 */
	const PARAM_PREFIX = ':';

	/**
	 * the name of the db which is added to the query.
	 */
	private $db = null;


	public function __construct($db) {
		$this->db = $db;
	}

	public function buildTableName($table) {
		return '`' . $this->db . '`.`' . $table . '`';
	}

	public function buildLimit($limit = null) {
		if ($limit !== null && is_numeric($limit)) {
			return 'LIMIT ' . $limit;
		}
		return 'LIMIT ' . StoreMysqlQuery::DEFAULT_LIMIT;
	}

	public function buildWhere($columns, $combination) {
		if ($columns === null || !is_array($columns)) {
			return '';
		}
		$combination = strtoupper($combination);
		if ($combination !== 'AND' && $combination !== 'OR') {
			return '';
		}
		$where = 'WHERE ';
		$first = true;
		foreach ($columns as $key => $value) {
			if (!$first) {
				$where .= ' ' . $combination . ' ';
			}
			$first = false;
			$where .= '`' . $key . '`=' . StoreMysqlQuery::PARAM_PREFIX . 'w' . $key;
		}
		return $where;
	}

	public function buildUpdate($columns) {
		if ($columns === null || !is_array($columns)) {
			return '';
		}
		$update = 'SET ';
		$first = true;
		foreach ($columns as $key => $value) {
			if (!$first) {
				$update .= ', ';
			}
			$first = false;
			$update .= '`' . $key . '`=' . StoreMysqlQuery::PARAM_PREFIX . 'v' . $key;
		}
		return $update;
	}

	public function buildColumnList($columns) {
		if ($columns === null || !is_array($columns)) {
			return '';
		}
		$columnList = '(';
		$first = true;
		foreach ($columns as $key => $value) {
			if (!$first) {
				$columnList .= ', ';
			}
			$first = false;
			$columnList .= '`' . $key . '`';
		}
		return $columnList . ')';
	}

	public function buildValueList($columns) {
		if ($columns === null || !is_array($columns)) {
			return '';
		}
		$valueList = 'VALUES (';
		$first = true;
		foreach ($columns as $key => $value) {
			if (!$first) {
				$valueList .= ', ';
			}
			$first = false;
			$valueList .= StoreMysqlQuery::PARAM_PREFIX . 'v' . $key . '';
		}
		return $valueList . ')';
	}

	public function getSelectSql($table, $whereColumns, $combination = 'AND') {
		$sql = 'SELECT * FROM ' . $this->buildTableName($table) . ' ';
		if ($whereColumns !== null && is_array($whereColumns)) {
			$sql .= $this->buildWhere($whereColumns, $combination) . ' ';
		}
		$sql .= $this->buildLimit();
		return $sql . ';';
	}

	public function getInsertSql($table, $valueColumns) {
		$sql = 'INSERT INTO ' . $this->buildTableName($table) . ' ';
		if ($valueColumns !== null && is_array($valueColumns)) {
			$sql .= $this->buildColumnList($valueColumns) . ' ';
			$sql .= $this->buildValueList($valueColumns) . ' ';
		}
		return $sql . ';';
	}

	public function getUpdateSql($table, $valueColumns, $whereColumns, $combination = 'AND') {
		$sql = 'UPDATE ' . $this->buildTableName($table) . ' ';
		if ($valueColumns !== null && is_array($valueColumns)) {
			$sql .= $this->buildUpdate($valueColumns) . ' ';
		}
		if ($whereColumns !== null && is_array($whereColumns)) {
			$sql .= $this->buildWhere($whereColumns, $combination) . ' ';
		}
		return $sql . ';';
	}

	public function getDeleteSql($table, $whereColumns, $combination = 'AND') {
		$sql = 'DELETE FROM ' . $this->buildTableName($table) . ' ';
		if ($whereColumns !== null && is_array($whereColumns)) {
			$sql .= $this->buildWhere($whereColumns, $combination) . ' ';
		}
		return $sql . ';';
	}
}
