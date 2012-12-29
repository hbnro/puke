<?php

$loader = require dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

$tpl = function () {
  doctype();
  html(function () {
    head(function () {
      title('Hello World');
    });
    body(function () {
      ul(array('id' => 'postals'), function () {
        foreach ($postals as $name => $one) {
          li(function () {
            figure(function () {
              h3($one['title']);
              $list = json_encode(range(rand(0, 9), rand(11, 19)));
              p(array('data' => array('role' => 'none', 'set' => $list)), h($one['desc']));
              img(array('src' => 'http://placehold.it/50'));
              figcaption(function () {
                div(array('class' => 'actions'), function() {
                  button(array('class' => 'add'), 'Agregar');
                  button(array('class' => 'rm'), 'Quitar');
                });
              });
            });
          });
        }
      });
    });
  });
};


$data = array(
  'lol' => array('title' => 'Fuck yeah!', 'desc' => '<FOO/>'),
  'baz' => array('title' => 'BUZZ', 'desc' => 'OK'),
);

echo Puke\Base::render($tpl, array('postals' => $data));


// fancy move!
function tpl(\Closure $lambda)
{
  return function ($locals) use ($lambda) {
    return Puke\Base::render($lambda, $locals);
  };
}

$set = array_slice(get_declared_classes(), -5);
$view = tpl(function () {
  ul(function () {
    foreach ($classes as $one) {
      li($one);
    }
  });
});

echo $view(array('classes' => $set));
