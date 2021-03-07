<?php

namespace Drupal\Core\Database;

abstract class Connection {

    /**
     * @psalm-taint-sink sql $query
     */
    public function query($query, array $args = [], $options = []) {}

    /**
     * @psalm-taint-sink sql $conjunction
     */
    public function condition($conjunction) {}

}

namespace Drupal\Component\Render;

class FormattableMarkup {

    /**
      * @psalm-taint-sink html $string
      */
    public function __construct($string, array $arguments) {}

}

namespace Drupal\Core\StringTranslation;

class TranslatableMarkup {

    /**
     * @psalm-taint-sink html $string
     */
    public function __construct($string, array $arguments = [], array $options = [], $string_translation = NULL) {}

}

namespace Drupal\Core\StringTranslation;

class PluralTranslatableMarkup {

    /**
     * @psalm-taint-sink html $singular
     * @psalm-taint-sink html $plural
     */
    public function __construct($count, $singular, $plural, array $args = [], array $options = [], $string_translation = NULL) {}

}

namespace Drupal\Component\Render;

trait MarkupTrait {

    /**
     * @psalm-taint-sink html $string
     */
    public static function create($string) {}

}
