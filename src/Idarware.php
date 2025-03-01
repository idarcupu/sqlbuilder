<?php

namespace Idaravel\SqlBuilder;

use Closure;
use Illuminate\Http\Request;

class Idarware {

  protected $sql;

  public function __construct(Sql $sql){
    $this->sql = $sql;
  }

  public function handle($request, Closure $next){
    return $next($request);
  }
}
