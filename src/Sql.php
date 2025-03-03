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
      $query = clone $this->query;

      if(!empty($conditions) && is_array($conditions)){
        foreach ($conditions as $column => $value){
          if($this->alias && !str_contains($column, '.')){
            $column = "{$this->alias}.{$column}";
          }
          $query->where($column, $value);
        }
      }
      return $query->get();
    }

    public function one($conditions = []){
      $query = clone $this->query;

      if(!empty($conditions) && is_array($conditions)){
        foreach ($conditions as $column => $value){
          if($this->alias && !str_contains($column, '.')){
            $column = "{$this->alias}.{$column}";
          }
          $query->where($column, $value);
        }
      }
      return $query->first();
    }

    public function __call($method, $arguments){
      if(method_exists($this->query, $method)){
        $this->query = call_user_func_array([$this->query, $method], $arguments);
        return $this;
      }

      throw new \BadMethodCallException("Method {$method} does not exist.");
    }
}
