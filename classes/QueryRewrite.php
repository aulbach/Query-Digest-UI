<?php


	class QueryRewrite{
		private $sql = null;
		private $type = 0;
		
		const UNKNOWN     = 0;
		const SELECT      = 1;
		const DELETE      = 2;
		const INSERT      = 3;
		const UPDATE      = 4;
		const ALTER       = 5;
		const DROP        = 6;
		const CREATE      = 7;
		const DELETEMULTI = 8;
		const UNION		  = 9;
		
	// Valid Table Regex
		const TABLEREF = '`?[A-Za-z0-9_]+`?(\.`?[A-Za-z0-9_]+`?)?';
		
		public function __construct($sql) {
			$this->sql = trim($sql);
			$this->figureOutType();
		}
		
		function figureOutType(){
			if (preg_match('/^SELECT\s/', $this->sql))
				$this->type = self::SELECT;
			elseif (preg_match('/^DELETE\s+FROM\s/', $this->sql))
				$this->type = self::DELETE;
			elseif (preg_match('/^DELETE\s+'.self::TABLEREF.'\s+FROM\s/', $this->sql))
				$this->type = self::DELETEMULTI;
			elseif (preg_match('/^INSERT\s+INTO\s/', $this->sql))
				$this->type = self::INSERT;
			elseif (preg_match('/^(.*)\s+UNION\s+(.*)$/', $this->sql))
				$this->type = self::UNION;
			else
				$this->type = self::UNKNOWN;
		}
		
		function toSelect() {
			switch ($this->type) {
				case self::SELECT:
				case self::UNION:
					return $this->sql;
				case self::DELETE:
					return preg_replace('/^DELETE\s+FROM\s/', 'SELECT 0 FROM ', $this->sql);
				case self::DELETEMULTI:
					return preg_replace('/^DELETE\s+'.self::TABLEREF.'\s+FROM\s/', 'SELECT 0 FROM ', $this->sql);
			}
			return null;
		}
		
		function asExplain() {
			switch ($this->type) {
				case self::SELECT:
				case self::UNION:
					$sql = $this->sql;
					break;
				case self::DELETE:
				case self::DELETEMULTI:
					$sql = $this->toSelect();
					break;
				default:
					return null;
			}
			return "EXPLAIN $sql";
		}
		
		function asExtendedExplain() {
			$sql = $this->asExplain();
			if (is_null($sql))
				return null;
			$sql = preg_replace('/^EXPLAIN /', 'EXPLAIN EXTENDED ', $sql);
			return $sql;
		}
	}
