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

	/**
	 * The constructor just sets up the db-name. Not that it IS
	 * mandatory.
	 * @param db the name of the database
	 */
	public function __construct($db) {
		$this->db = $db;
	}

	/**
	 * This method builds the tablename accordingly escaped and with the
	 * db-name.
	 * @param table the name of the table
	 * @return the created string of the according mysql-table
	 */
	public function buildTableName($table) {
		return '`' . $this->db . '`.`' . $table . '`';
	}

	/**
	 * This method builds a limit queryPart. This is built so we won't
	 * select millions of rows in case of actual success. security and
	 * performance...
	 * @param limit (optional) if provided you can overwrite the default
	 * limit
	 * @return the query part of the limit
	 */
	public function buildLimit($limit = null) {
		if ($limit !== null && is_numeric($limit)) {
			return 'LIMIT ' . $limit;
		}
		return 'LIMIT ' . StoreMysqlQuery::DEFAULT_LIMIT;
	}

	/**
	 * This methods builds the "WHERE" part of the query. It does so
	 * by escaping the name properly and placing params for the pdo.
	 * @param columns (key,value)-array for the where
	 * @param combination whetever the columns should be AND or OR
	 * concated
	 * @return the final WHERE-part of the query
	 */
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

	/**
	 * This method builds the update part of the query (eg set
	 * column=>value). Column names are escaped. the values are just
	 * params for the pdo.
	 * @param columns the columns to update
	 * @return the final "SET" part of the query.
	 */
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

	/**
	 * This method builds a columnList, meaning only the names of the
	 * columns. this is useful for insert-queries.
	 * @param columns the columns (with the names)
	 * @return the final list of the column-names
	 */
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

	/**
	 * This methods builds (in comparison to buildColumnList()) the list
	 * of values. It does so by only setting params for pdo. Useful
	 * for insert-queries.
	 * @param columns the values to build
	 * @return the values params for pdo in sql-form
	 */
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

	/**
	 * This method builds a complete select query with the help of the
	 * other methods.
	 * @param table the tablename to select from
	 * @param whereColumns condition for the select
	 * @param combination whetever the whereColumns should be AND or OR
	 * concated
	 * @return the full SELECT query
	 */
	public function getSelectSql($table, $whereColumns, $combination = 'AND') {
		$sql = 'SELECT * FROM ' . $this->buildTableName($table) . ' ';
		if ($whereColumns !== null && is_array($whereColumns)) {
			$sql .= $this->buildWhere($whereColumns, $combination) . ' ';
		}
		$sql .= $this->buildLimit();
		return $sql . ';';
	}

	/**
	 * This method builds a complete insert query.
	 * @param table the tablename to insert into
	 * @param valueC0lumns the columns and values to insert.
	 * @return the full INSERT query
	 */
	public function getInsertSql($table, $valueColumns) {
		$sql = 'INSERT INTO ' . $this->buildTableName($table) . ' ';
		if ($valueColumns !== null && is_array($valueColumns)) {
			$sql .= $this->buildColumnList($valueColumns) . ' ';
			$sql .= $this->buildValueList($valueColumns) . ' ';
		}
		return $sql . ';';
	}

	/**
	 * This method builds a complete update-query
	 * @param table the tablename to update in
	 * @param valueColumns the to be set columns
	 * @param whereColumns the conditions
	 * @param combination whetever the where should be AND or OR
	 * concated.
	 * @return the full UPDATE query
	 */
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

	/**
	 * This method builds a complete delete query.
	 * @param table the tablename to delete from
	 * @param whereColumns the conditions
	 * @param combination whetever the where should be AND or OR
	 * concated.
	 * @return the fulll DELETE query
	 */
	public function getDeleteSql($table, $whereColumns, $combination = 'AND') {
		$sql = 'DELETE FROM ' . $this->buildTableName($table) . ' ';
		if ($whereColumns !== null && is_array($whereColumns)) {
			$sql .= $this->buildWhere($whereColumns, $combination) . ' ';
		}
		return $sql . ';';
	}
}
