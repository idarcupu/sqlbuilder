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

  public function join($table, $firstColumn, $operator = null, $secondColumn = null, $type = 'inner'){
    if($this->alias && is_string($firstColumn) && !str_contains($firstColumn, '.')){
      $firstColumn = "{$this->alias}.{$firstColumn}";
    }

    if($operator !== null && $secondColumn !== null && is_string($secondColumn) && !str_contains($secondColumn, '.')){
      $secondColumn = Str::snake($table) . ".{$secondColumn}";
    }

    $this->query->join(Str::snake($table), $firstColumn, $operator, $secondColumn, $type);
    return $this;
  }

  public function where($column, $operator = null, $value = null){
    if($this->alias && is_string($column) && !str_contains($column, '.')){
      $column = "{$this->alias}.{$column}";
    }

    if(func_num_args() === 2){
      $this->query->where($column, $operator);
    } elseif(func_num_args() === 3){
      $this->query->where($column, $operator, $value);
    }
    return $this;
  }

  public function select(...$columns){
    $formattedColumns = [];
    foreach ($columns as $column) {
      if ($this->alias && is_string($column) && !str_contains($column, '.')) {
        $formattedColumns[] = "{$this->alias}.{$column}";
      } else {
        $formattedColumns[] = $column;
      }
    }
    $this->query->select($formattedColumns);
    return $this;
  }

  public function all($conditions = []){
    if (!empty($conditions) && is_array($conditions)) {
      foreach ($conditions as $column => $value) {
        $this->where($column, $value);
      }
    }
    return $this->query->get();
  }

  public function one($conditions = []){
    if (!empty($conditions) && is_array($conditions)) {
      foreach ($conditions as $column => $value) {
        $this->where($column, $value);
      }
    }
    return $this->query->first();
  }
}
