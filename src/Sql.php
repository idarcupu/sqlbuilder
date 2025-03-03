<?php

namespace Idaravel\SqlBuilder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Sql {

  protected $query;
  protected $table;
  protected $alias;

  public function __construct(){
    $this->table = null;
    $this->alias = null;
    $this->query = null;
  }

  public static function __callStatic($name, $arguments){
    $instance = new self();
    $instance->table = Str::snake($name);
    $instance->query = DB::table($instance->table);
    return $instance;
  }

  public function alias($alias){
    $this->alias = $alias;
    $this->query = DB::table("{$this->table} as {$this->alias}");
    return $this;
  }

  public function all($conditions = []){
    if (!empty($conditions) && is_array($conditions)) {
      foreach ($conditions as $column => $value) {
        $this->query->where($column, $value);
      }
    }
    return $this->query->get();
  }

  public function one($conditions = []){
    if (!empty($conditions) && is_array($conditions)) {
      foreach ($conditions as $column => $value) {
        $this->query->where($column, $value);
      }
    }
    return $this->query->first();
  }

  public function __call($method, $arguments){
    if(method_exists($this->query, $method)){
      return call_user_func_array([$this->query, $method], $arguments);
    }
    throw new \BadMethodCallException("Method {$method} does not exist.");
  }
}
