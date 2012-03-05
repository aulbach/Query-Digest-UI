<?php

    class QueryRewrite{
		public $sql 	  	= null;
		public $type 	  	= 0;
		
		const UNKNOWN     	= 0;
		const SELECT      	= 1;
		const DELETE      	= 2;
		const INSERT      	= 3;
		const UPDATE      	= 4;
		const ALTER       	= 5;
		const DROP        	= 6;
		const CREATE      	= 7;
		const DELETEMULTI 	= 8;
		const UNION       	= 9;
		const INSERTSELECT	= 10;
		
// Valid Table Regex
		const TABLEREF      = '`?[A-Za-z0-9_]+`?(\.`?[A-Za-z0-9_]+`?)?';
// Comment Regexs
		const COMMENTS_C    = '/\s*\/\*.*?\*\/\s*/';
		const COMMENTS_HASH = '/#.*$/';
		const COMMENTS_SQL  = '/--\s+.*$/';
                
        public function __construct($sql = null) {
			$this->setQuery($sql);
        }
		
		public function setQuery($sql = null) {
			$this->type = self::UNKNOWN;
			$this->sql  = $sql;
            if (is_null($this->sql))
                return;
        // Remove comments
            $this->sql  = preg_replace(self::COMMENTS_C,    '', $this->sql);
            $this->sql  = preg_replace(self::COMMENTS_HASH, '', $this->sql);
            $this->sql  = preg_replace(self::COMMENTS_SQL,  '', $this->sql);
        // Remove whitespace
			$this->sql  = trim($this->sql);
            $this->sql  = str_replace("\n", " ", $this->sql);
			$this->figureOutType();    
		}

		private function figureOutType(){
			if (preg_match('/^SELECT\s/i', $this->sql))
				$this->type = self::SELECT;
			elseif (preg_match('/^DELETE\s+FROM\s/i', $this->sql))
				$this->type = self::DELETE;
			elseif (preg_match('/^DELETE\s+'.self::TABLEREF.'\s+FROM\s/i', $this->sql))
				$this->type = self::DELETEMULTI;
			elseif (preg_match('/^INSERT\s+INTO\s'.self::TABLEREF.'[\s\(]*SELECT\s+/i', $this->sql))
				$this->type = self::INSERTSELECT;
			elseif (preg_match('/^INSERT\s+INTO\s/i', $this->sql))
				$this->type = self::INSERT;
			elseif (preg_match('/^(.*)\s+UNION\s+(.*)$/i', $this->sql))
				$this->type = self::UNION;
			elseif (preg_match('/^UPDATE\s/i', $this->sql))
				$this->type = self::UPDATE;
			elseif (preg_match('/^ALTER\s/i', $this->sql))
				$this->type = self::ALTER;
			elseif (preg_match('/^CREATE\s/i', $this->sql))
				$this->type = self::CREATE;
			elseif (preg_match('/^DROP\s/i', $this->sql))
				$this->type = self::DROP;
			else
				$this->type = self::UNKNOWN;
		}
		
		public function getType() {
			return $this->type;
		}
		
		public function toSelect() {
			switch ($this->type) {
				case self::SELECT:
				case self::UNION:
					return $this->sql;
				case self::DELETE:
					return preg_replace('/^DELETE\s+FROM\s/i', 'SELECT 0 FROM ', $this->sql);
				case self::DELETEMULTI:
					return preg_replace('/^DELETE\s+'.self::TABLEREF.'\s+FROM\s/i', 'SELECT 0 FROM ', $this->sql);
				case self::UPDATE:
					preg_match('/^UPDATE\s+(.*)\s+SET\s+(.*)\s+WHERE\s+(.*)$/i', $this->sql, $subpatterns);
					return "SELECT {$subpatterns[2]} FROM {$subpatterns[1]} WHERE {$subpatterns[3]}";
				case self::INSERTSELECT:
					if (preg_match('/\(\s*(SELECT\s+.*)\)/', $this->sql, $subpatterns))
						return trim("{$subpatterns[1]}");
					else
						preg_match('/^INSERT\s+INTO\s+(SELECT.*)/i', $subpatterns);
						return trim("{$subpatterns[1]}");
			}
			return null;
		}
                
		public function asExplain() {
			$sql = $this->toSelect();
			if (is_null($sql))
				return null;
			return "EXPLAIN $sql";
		}
                
		public function asExtendedExplain() {
			$sql = $this->toSelect();
			if (is_null($sql))
				return null;
			return "EXPLAIN EXTENDED $sql";
		}
	}
