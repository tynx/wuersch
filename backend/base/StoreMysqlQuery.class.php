<?php

class StoreMysqlQuery{
	private static $DEFAULT_LIMIT = 100;
	private static $PARAM_PREFIX = ':';

	private $db = null;
/*	private $table = null;
	private $whereColumns = null;
	private $updateColumns = null;
	private $combination = null;
	private $limit = null;
*/
	private $queryParts = array();

	public function __construct($db){
		$this->db = $db;
	}
	/*public function __construct($db, $table, $whereColumns, $combination, $updateColumns, $limit){
		$this->db = $db;
		$this->table = $table;
		$this->whereColumns = $whereColumns;
		$this->updateColumns = $updateColumns;
		if($combination !== null)
			$this->combination = strtoupper($combination);
		$this->limit = $limit;
		$this->queryParts = array();
	}*/

	public function buildTableName($table){
		return '`' . $this->db . '`.`' . $table . '`';
	}

	public function buildLimit($limit = null){
		if($limit !== null && is_numeric($limit))
			return 'LIMIT ' . $limit;
		return 'LIMIT ' . StoreMysqlQuery::$DEFAULT_LIMIT;
	}

	public function buildWhere($columns, $combination){
		if($columns === null || !is_array($columns))
			return '';
		$combination = strtoupper($combination);
		if($combination !== 'AND' && $combination !== 'OR')
			return '';
		$where = 'WHERE ';
		$first = true;
		foreach($columns as $key => $value){
			if(!$first)
				$where .= ' ' . $combination . ' ';
			$first = false;
			$where .= '`' . $key . '`=' . StoreMysqlQuery::$PARAM_PREFIX . 'w' . $key;
		}
		return $where;
	}

	public function buildUpdate($columns){
		if($columns === null || !is_array($columns))
			return '';
		$update = 'SET ';
		$first = true;
		foreach($columns as $key => $value){
			if(!$first)
				$update .= ', ';
			$first = false;
			$update .= '`' . $key . '`=' . StoreMysqlQuery::$PARAM_PREFIX . 'v' . $key;
		}
		return $update;
	}

	public function buildColumnList($columns){
		if($columns === null || !is_array($columns))
			return '';
		$columnList = '(';
		$first = true;
		foreach($columns as $key => $value){
			if(!$first)
				$columnList .= ', ';
			$first = false;
			$columnList .= '`' . $key . '`';
		}
		return $columnList . ')';
	}

	public function buildValueList($columns){
		if($columns === null || !is_array($columns))
			return '';
		$valueList = 'VALUES (';
		$first = true;
		foreach($columns as $key => $value){
			if(!$first)
				$valueList .= ', ';
			$first = false;
			$valueList .= StoreMysqlQuery::$PARAM_PREFIX . 'v' . $key . '';
		}
		return $valueList . ')';
	}


	public function getSelectSql($table, $whereColumns, $combination = 'AND'){
		$sql = 'SELECT * FROM ' . $this->buildTableName($table) . ' ';
		if($whereColumns !== null && is_array($whereColumns)){
			$sql .= $this->buildWhere($whereColumns, $combination) . ' ';
		}
		$sql .= $this->buildLimit();
		return $sql;
	}

	public function getInsertSql($table, $valueColumns){
		$sql = 'INSERT INTO ' . $this->buildTableName($table) . ' ';
		if($valueColumns !== null && is_array($valueColumns)){
			$sql .= $this->buildColumnList($valueColumns) . ' ';
			$sql .= $this->buildValueList($valueColumns) . ' ';
		}
		return $sql;
	}

	public function getUpdateSql($table, $valueColumns, $whereColumns, $combination = 'AND'){
		$sql = 'UPDATE ' . $this->buildTableName($table) . ' ';
		if($valueColumns !== null && is_array($valueColumns));{
			$sql .= $this->buildUpdate($valueColumns) . ' ';
		}
		if($whereColumns !== null && is_array($whereColumns)){
			$sql .= $this->buildWhere($whereColumns, $combination) . ' ';
		}
		return $sql;
	}

	public function getDeleteSql($table, $whereColumns, $combination = 'AND'){
		$sql = 'DELETE FROM ' . $this->buildTableName($table) . ' ';
		if($whereColumns !== null && is_array($whereColumns)){
			$sql .= $this->buildWhere($whereColumns, $combination) . ' ';
		}
		return $sql;
	}

/*	public function autoBuild(){
		if($this->table !== null){
			$this->buildTableName();
		}
		if($this->whereColumns !== null && is_array($this->whereColumns)){
			if($this->combination !== null){
				$this->buildWhere();
			}else{
				
				$this->buildColumnList();
				$this->buildValueList();
			}
			
		}
		$this->buildLimit();
	}

	public function getPart($key){
		if(isset($this->queryParts[$key]))
			return $this->queryParts[$key];
		return '';
	}*/
}

?>
