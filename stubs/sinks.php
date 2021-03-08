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

namespace Drupal\Core\Database\Query;

interface ConditionInterface {

    /**
     * @psalm-taint-sink sql $operator
     */
    public function condition($field, $value = NULL, $operator = '=');

    /**
     * @psalm-taint-sink sql $snippet
     */
    public function where($snippet, $args = []);

}

interface SelectInterface {

    /**
     * @psalm-taint-sink sql $condition
     */
    public function addJoin($type, $table, $alias = NULL, $condition = NULL, $arguments = []);

    /**
     * @psalm-taint-sink sql $expression
     */
    public function addExpression($type, $table, $alias = NULL, $condition = NULL, $arguments = []);

    /**
     * @psalm-taint-sink sql $condition
     */
    public function join($table, $alias = NULL, $condition = NULL, $arguments = []);

    /**
     * @psalm-taint-sink sql $condition
     */
    public function innerJoin($table, $alias = NULL, $condition = NULL, $arguments = []);

    /**
     * @psalm-taint-sink sql $condition
     */
    public function leftJoin($table, $alias = NULL, $condition = NULL, $arguments = []);

    /**
     * @psalm-taint-sink sql $snippet
     */
    public function having($snippet, $args = []);

    /**
     * @psalm-taint-sink sql $operator
     */
    public function havingCondition($field, $value = NULL, $operator = NULL);

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
