<?php

/**
 * @psalm-taint-sink sql $query
 */
function foo_bar($query) {}

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
