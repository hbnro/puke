I did mean coupé
================

Many years ago CoffeeKup was born, we have lots of template engines now.

Well, I made this just for the "why not?" and inspired by the Markaby pattern, actually less sexy on PHP.

Of course, shall be installed as dependency using the Composer.

Basic usage
-----------

Display your favorite books.

    <?php

    require 'vendor/autoload.php';

    $data = [
      [
        'author' => 'Hernando de Soto',
        'title' => 'The Mystery of Capitalism'
      ],
      [
        'author' => 'Henry Hazlitt',
        'title' => 'Economics in One Lesson'
      ],
      [
        'author' => 'Milton Friedman',
        'title' => 'Free to Choose'
      ],
    ];

    # painless
    $view = function () {
      if ($books) {
        table(function () {
          tr(function () {
            th('Author');
            th('Title');
          });
          foreach ($books as $key => $val) {
            tr(function () {
              td($val['author']);
              td($val['title']);
            });
          }
        });
      } else {
        p('There are no books to display.');
      }
    };

    # or using a clean render() helper...
    echo Puke\Base::render($view, ['books' => $data]);

Note that a simple echo will not work as normally, use the `text()` helper inside
the template to achieve the same result.

Eval is evil but seriously here he is the king.

Finally
-------

There is not much to say, if you want contribute you're welcome buddy.
