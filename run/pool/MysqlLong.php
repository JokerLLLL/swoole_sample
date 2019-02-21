<?php
/** 长连接池的封装
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/16
 * Time: 17:06
 */

class MysqlLong
{
        public $sql;
        public $head;
        public $table;
        public $where;
        public $select;
        public $values;

        public $limit;
        public $offset;
        public $orderBy;
        /*
         * sql请求
         */
        public static function query($sql,$time_out = -1)
        {
            return MysqlPool::getInstance()->query($sql,$time_out);
        }

        public static function find()
        {
            $model = new self();
            $model->head = 'SLECET';
            return $model;
        }

        public static function insert()
        {
            $model = new self();
            $model->head = 'INSERT';
            return $model;
        }

        public static function update()
        {
            $model = new self();
            $model->head = 'UPDATE';
            return $model;
        }

        public static function delete()
        {
            $model = new self();
            $model->head = 'UPDATE';
            return $model;
        }

        public function table($table)
        {
            $this->tabel = $table;
            return $this;
        }

        public function values($values)
        {
            $this->values = $values;
            return $this;
        }
        public function where($where)
        {
            $this->where = $where;
            return $this;
        }

        public function select($select)
        {
            $this->select = $select;
            return $this;
        }

        public function done()
        {
            return $this->query($this->buildSql());
        }

        public function buildSql()
        {
            switch ($this->head){
                case 'SELECT':
                    $this->buildSelect();
                    break;
                case 'INSERT':
                    $this->buildInsert();
                    break;
                case 'UPDATE':

                    break;
                case 'DELETE':

                    break;
            }
            return $this->sql;
        }

        public function buildSelect()
        {
            $this->sql = 'SELECT ';
            if(empty($this->select)){
                $this->sql .= '*';
            }elseif(is_array($this->select)){
                $this->sql .= join(',',$this->select);
            }else{
                $this->sql .= $this->select;
            }
            $this ->sql .= ' FROM '.$this->table.' WHERE '.$this->buildWhere();
            if(isset($this->limit)){
                $this->sql .= ' LIMIT '.$this->limit;
            }
            if(isset($this->offset)){
                $this->sql .= ' OFFSET '.$this->limit;
            }
            if(isset($this->orderBy)){
                $this->sql .= $this->orderBy;
            }
        }


        public function buildWhere()
        {
        }

        public function buildInsert()
        {
            $this->sql = 'INSERT INTO '.$this->table.' VALUES('.$this->buildValues().')';
        }

        public function buildValues()
        {
            $str = '';
            foreach ($this->values as $v){
                if($v === null){
                    $str .= ',NULL';
                }elseif (is_numeric($v)) {
                    $str .= ','.strval($v);
                }else{
                    $str .= ",'$v'";
                }
            }
            return substr($str,1);
        }
}