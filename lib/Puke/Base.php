<?php

namespace Puke;

class Base
{

  public static function parse($test)
  {
    if ($test instanceof \Closure) {
      $src = \Puke\Helpers::source($test);
    } else {
      $src = (string) $test;

      if (@eval("function(){$test};") === FALSE) {
        throw new \Exception("Syntax error on `$test`");
      }
    }

    $src = static::compile($src);

    return $src;
  }

  public static function render(\Closure $lambda, array $locals = array())
  {
    $src = static::parse($lambda);
    $out = @eval($src);

    return $out($locals);
  }

  private static function compile($view)
  {
    static $regex = '/^(\s*)(.+?)\s*function\s*\(\s*\)\s*\{/m',
           $repl = '\\1$_ = get_defined_vars(); \\2 function () use ($_) { extract($_); unset($_);';

    $id  = '__' . md5($view);
    $tpl = preg_replace($regex, $repl, $view);
    $php = <<<CODE
namespace $id;

function __() { static \$__ck; return \$__ck ?: \$__ck = new \\Puke\\Buffer; }
function doctype(\$use = 'default') { __()->append(\\Puke\\Helpers::doctype(\$use)); }
function text() { __()->append(join('', func_get_args())); }
function h(\$s) { return \\Puke\\Helpers::escape(\$s); }

foreach (\\Puke\\Helpers::allowed() as \$fn => \$tag) {
  eval("namespace $id; function \$fn(\\\$attrs, \\\$content = '') { \\$id\\__()->tag('\$tag', \\\$attrs, \\\$content); }");
}

return function (\$_ = array()) { extract(\$_); unset(\$_);
$tpl
return \\$id\\__()->get();
};
CODE;

    return $php;
  }

}
