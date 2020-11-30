<?php declare(strict_types=1);
namespace bPack\ORM;

use \bPack\Protocol;
use \PDO;

class ModelCollection implements Protocol\ModelCollection, \Countable, \Iterator {
    // Protocol\Model
    protected  $model;

    // query to build condition
    // array
    protected $where = array();
    // array
    protected $orderBy = array();
    // array
    protected $select = array("*");
    // int
    protected $limit = 100;
    // int
    protected $offset = 0;

    // store executed result
    // array
    protected $resultset = null;

    // iterator pos
    // int
    protected $position = 0;

    public function __construct(Protocol\Model $model, array $whereCond = array()) {
        $this->model = $model;
        $this->where = $whereCond;
    }

    protected function buildWhere(array &$sql) {
        if(sizeof($this->where) > 0) {
            $sql[] = "WHERE " . implode(" ", array_map(
                function($n) {
                    if(!is_array($n)) return $n;
                    $n[2] = ":" . $n[0];
                    return implode(" ", $n);
                }, $this->where
            ));
        }
    }

    public function getBindingData():array {
        $bindingData = [];

        foreach($this->where as $n) {
            if(!is_array($n)) continue;
            $bindingData[":" . $n[0]] = $n[2];
        }

        return $bindingData;
    }

    protected function doSelect() {
        $sql = [];

        $sql[] = "SELECT";
        $sql[] = implode(",", $this->select);
        $sql[] = "FROM " . $this->model->getTablename();
        $this->buildWhere($sql);

        if(sizeof($this->orderBy) > 0) {
            $sql[] = "ORDER BY " . implode(", ", array_map(
                function($n) {
                    return "$n " . $this->orderBy[$n];
                },
                array_keys($this->orderBy)
            ));
        }

        $sql[] = "LIMIT " . $this->limit;
        $sql[] = "OFFSET " . $this->offset;

        $executed_sql = implode(" ", $sql);

        // send to DB
        $dbConn = $this->model->getConnection();
        $stmt = $dbConn->prepare($executed_sql);

        //
        $result = $stmt->execute( $this->getBindingData() );

        if(!$result) {
            throw new \Exception("[Database Model] Cannot query database.");
        }

        // TODO if large batch, we should use streaming to get data one by one
        $resultset = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->resultset = array_map(function($res) {
            return new ModelEntity($this->model, $res);
        }, $resultset);
    }

    public function first():?Protocol\ModelEntity {
        $this->limit(1)->doSelect();
        return $this->resultset[0] ?? null;
    }

    public function destroy():bool {
        $sql = [];

        $sql[] = "DELETE FROM";
        $sql[] = $this->model->getTablename();
        $this->buildWhere($sql);

        $executed_sql = implode(" ", $sql);

        $dbConn = $this->model->getConnection();
        $stmt = $dbConn->prepare($executed_sql);
        return $stmt->execute( $this->getBindingData() );
    }

    public function update(array $updatedData):bool {

        if(sizeof($updatedData) == 0) {
            return false;
        }

        $sql = [];
        $sql[] = "UPDATE";
        $sql[] = $this->model->getTablename();
        $sql[] = "SET";

        $updataExpr = [];
        $bindingData = [];
        foreach($updatedData as $k => $v) {
            $updataExpr[] = "{$k} = :{$k}_update_field";
            $bindingData[":" . $k . "_update_field"] = $v;
        }
        $sql[] = implode(", ", $updataExpr);

        $this->buildWhere($sql);

        $executed_sql = implode(" ", $sql);

        $dbConn = $this->model->getConnection();
        $stmt = $dbConn->prepare($executed_sql);
        return $stmt->execute( array_merge($this->getBindingData(), $bindingData) );
    }

    public function limit(int $limitCount):Protocol\ModelCollection {
        $this->limit = $limitCount;
        return $this;
    }

    public function offest(int $offsetValue):Protocol\ModelCollection {
        $this->offset = $offsetValue;
        return $this;
    }

    public function orderBy(array $orderByExpression):Protocol\ModelCollection {
        $this->orderBy = $orderByExpression;
        return $this;
    }

    public function select(string ...$columns):Protocol\ModelCollection {
        $this->select = $columns;
        return $this;
    }

    public function pluck(string $column):array {
        $this->select($column)->doSelect();

        return array_map(function($item) use ($column) {
            return $item[$column];
        }, $this->resultset);
    }

    // ** countable
    public function count():int {
        return sizeof($this->resultset);
    }

    // iterator
    public function rewind() {
        $this->position = 0;

        if(is_null($this->resultset)) {
            $this->doSelect();
        }
    }

    public function current() {
        return $this->resultset[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->resultset[$this->position]);
    }

    // var_dump
    public function __debugInfo() {
        if(is_null($this->resultset)) {
            $this->doSelect();
        }

        return $this->resultset;
    }
}
