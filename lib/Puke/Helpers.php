<?php

namespace Puke;

class Helpers
{

  private static $aliases = array(
                    'dl' => '_dl',
                    'time' => '_time',
                    'header' => '_header',
                  );

  private static $valid = array(
                    'a', 'abbr', 'address', 'article', 'aside', 'audio', 'b', 'bdi', 'bdo', 'blockquote', 'body', 'button',
                    'canvas', 'caption', 'cite', 'code', 'colgroup', 'datalist', 'dd', 'del', 'details', 'dfn', 'div', 'dl', 'dt', 'em',
                    'fieldset', 'figcaption', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hgroup',
                    'html', 'i', 'iframe', 'ins', 'kbd', 'label', 'legend', 'li', 'map', 'mark', 'menu', 'meter', 'nav', 'noscript', 'object',
                    'ol', 'optgroup', 'option', 'output', 'p', 'pre', 'progress', 'q', 'rp', 'rt', 'ruby', 's', 'samp', 'script', 'section',
                    'select', 'small', 'span', 'strong', 'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot',
                    'th', 'thead', 'time', 'title', 'tr', 'u', 'ul', 'video',
                  );

  private static $close = array('area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'param', 'source', 'track', 'wbr');

  private static $doctypes = array(
                    'default' => '<!DOCTYPE html>',
                    '5' => '<!DOCTYPE html>',
                    'xml' => '<?xml version="1.0" encoding="utf-8" ?>',
                    'transitional' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
                    'strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
                    'frameset' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
                    '1.1' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
                    'basic' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">',
                    'mobile' => '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">',
                    'ce' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "ce-html-1.0-transitional.dtd">',
                  );


  public static function is_closed($tag)
  {
    return in_array($tag, static::$close);
  }

  public static function allowed()
  {
    $out = array();
    $set = array_merge(static::$valid, static::$close);

    foreach ($set as $tag) {
      $out[isset(static::$aliases[$tag]) ? static::$aliases[$tag] : $tag] = $tag;
    }

    return $out;
  }

  public static function doctype($type = 'default')
  {
    isset(static::$doctypes[$type]) OR $type = 'default';
    return static::$doctypes[$type];
  }

  public static function escape($text)
  {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', FALSE);
  }

  public static function source(\Closure $lambda)
  {
    $fn    = new \ReflectionFunction($lambda);

    $file  = $fn->getFileName();
    $begin = $fn->getStartLine();
    $end   = $fn->getEndLine();

    $code  = explode("\n", file_get_contents($file));
    $tmp   = join("\n", array_slice($code, $begin - 1, $end - $begin + 1));
    $tmp   = preg_replace('/^.*?function[^{}]+?(?=\{)/', '', $tmp);


    $inc = 0;
    $out = '';
    $len = strlen($tmp);

    for ($i = 0; $i < $len; $i += 1) {
      $char = substr($tmp, $i, 1);
      switch ($char) {
        case '{';
          $inc += 1;
          $out .= $char;
        break;
        case '}';
          $inc -= 1;
          $out .= $char;
        break;
        default;
          $out .= $char;
        break;
      }

      if ( ! $inc) {
        break;
      }
    }

    $out = substr($out, 1, -1);

    return $out;
  }

  public static function reduce(array $set) {
    $out = array();

    foreach ($set as $key => $val) {
      if (is_bool($val)) {
        $val && $out[$key] = $key;
      } elseif (is_array($val)) {
        foreach (static::reduce($val) as $k => $v) {
          $out["$key-$k"] = $v;
        }
      } else {
        $out[$key] = $val;
      }
    }

    return $out;
  }

}
