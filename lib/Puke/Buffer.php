<?php

namespace Puke;

class Buffer
{

  private $tabs = 0;
  private $output = array();


  public function indent()
  {
    $this->append(str_repeat('  ', $this->tabs));
  }

  public function get()
  {
    return join('', $this->output);
  }

  public function append($content)
  {
    $this->output []= $content;
  }

  public function attrs(array $args)
  {
    $out = '';

    foreach (\Puke\Helpers::reduce($args) as $k => $v) {
      $out .= $k === $v ? " $k" : " $k=\"" . \Puke\Helpers::escape($v) . "\"";
    }

    return $out;
  }

  public function tag($name, $attrs = array(), $content = '')
  {
    if ($attrs instanceof \Closure) {
      $content = $attrs;
      $attrs = array();
    } elseif ( ! is_array($attrs)) {
      $content = (string) $attrs;
      $attrs = array();
    }

    $suffix = '';

    $this->append("\n");

    $this->indent();
    $this->append("<$name");
    $this->append($this->attrs($attrs));

    if (\Puke\Helpers::is_closed($name)) {
      $this->append(' />');
    } else {
      $this->append('>');

      if ($content  instanceof \Closure) {
        $this->tabs++;
        ob_start() && $content();

        $content = ob_get_clean();
        $content && $this->append($content);

        $this->indent();
        $this->append("\n");
        $this->tabs--;

        $this->indent();
      } elseif (is_scalar($content)) {
        $this->append($content);
      }

      $this->append("</$name>");
    }
  }
}
