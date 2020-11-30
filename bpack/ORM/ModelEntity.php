<?php declare(strict_types=1);
namespace bPack\ORM;

use \bPack\Protocol;
use \ArrayAccess;
use bPack\Protocol\HookTrait;

class ModelEntity implements Protocol\ModelEntity, ArrayAccess {
    // ?int
    protected $_id = null;
    // Protocol\Model
    protected $model;
    // array
    protected $data;
    // array
    protected $rowData;
    // array
	protected $outSchemaData = array();

	use HookTrait;

	// for hooktrait
	public function getHooks(): array
	{
		return [
			"beforeSave",
			"afterDataFeed"
		];
	}

	// for magic method
    public function __get(string $key) {
        return $this->offsetGet($key);
    }

    public function __set(string $key, $value) {
        $this->offsetSet($key, $value);
    }

    public function __construct(
        Protocol\Model $model,
        array $newData = array()
    ) {
		$this->model = $model;
		$model->registerEntityHook($this);

        // first we should get clean entity based on schema
        $this->data = $this->model->getSchema();
        $this->rowData = $this->model->getSchema();

        // only process those fields in schema
        foreach($newData as $k => $v) {
            if(!array_key_exists($k, $this->data) ) continue;
            $this->data[$k] = $v;
            $this->rowData[$k] = $v;
		}

		// hook
		$this->runHook("afterDataFeed");

        // if we get input id
        if(isset($newData["id"])) {
            $this->_id = $newData["id"];
            unset($this->data["id"]);
        }
    }

    // ArrayAccess interface
    public function offsetExists($offset) : bool {
        if(isset($this->data[$offset])) {
            return true;
        }

        return isset($this->outSchemaData[$offset]);
    }

    public function id() {
        return $this->_id;
    }

    public function offsetGet($offset) {
        if($offset == "id") {
            return $this->_id;
        }

        if(isset($this->data[$offset])) {
            return $this->data[$offset];
        }

        if(isset($this->outSchemaData[$offset])) {
            return $this->outSchemaData[$offset];
        }

        throw new \Exception("[Model Entity] can not get undefineded data");
    }

    public function offsetSet($offset, $value) {
        if(array_key_exists($offset, $this->data)) {
            return $this->data[$offset] = $value;
        }

        $this->outSchemaData[$offset] = $value;
    }

    public function offsetUnset($offset) {
        if(isset($this->outOfSchema[$offset])) {
            unset($this->outOfSchema[$offset]);
        }
    }

    //
	public function save():bool {
		$this->runHook("beforeSave");

        if(is_null($this->_id) ) {
            return $this->doCreate();
        }

        return $this->doUpdate();
    }

    protected function doCreate():bool {
        $sql = "INSERT INTO %s (%s) VALUES (%s);";

        $fields = implode(", ", array_keys($this->data)) . ",updated_at,created_at";

        $value_placeholders = implode(", ", array_map(
                function($i) {
                    return ":{$i}";
                },
                array_keys($this->data)
            )
        ) . ", :updated_at, :created_at";

        $exeucte_sql = sprintf($sql, $this->model->getTablename(), $fields, $value_placeholders);

        $dbConn = $this->model->getConnection();
        $stmt = $dbConn->prepare($exeucte_sql);

        // build binding data
        $bindingData = [];
        foreach($this->data as $k => $v) $bindingData[":" . $k] = $v;

        return $stmt->execute(array_merge(
            $bindingData,
            [
                ":updated_at" => date("Y-m-d H:i:s"),
                ":created_at" => date("Y-m-d H:i:s"),
            ]
        ));
    }

    protected function doUpdate():bool {
        if($this->_id === null) {
            return false;
        }

        $updatedData = [];
        foreach($this->data as $k => $v) {
            if($v != $this->rowData[$k]) {
               $updatedData[$k] = $v;
            }
        }

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

        $sql[] = "WHERE id = " . $this->_id;

        $executed_sql = implode(" ", $sql);

        $dbConn = $this->model->getConnection();
        $stmt = $dbConn->prepare($executed_sql);

        return $stmt->execute($bindingData);
    }

    public function update(array $newData):bool {
        foreach($newData as $k => $v) {
            if(!isset($this->data[$k]) ) continue;
            $this->data[$k] = $v;
        }

        return $this->doUpdate();
    }

    public function destroy():bool {
        if($this->_id === null ) {
            return false;
        }

        $sql = 'DELETE FROM %s WHERE id = %d;';
        $exeucte_sql = sprintf($sql, $this->model->getTablename(), $this->_id);

        $result = $this->model->getConnection()->exec($exeucte_sql);

        if($result === false ) {
            return false;
        }

        return true;
    }

    public function unwrap(): array {
        return $this->data;
    }

    // var_dump
    public function __debugInfo() {
        return $this->data;
    }
}
