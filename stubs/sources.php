<?php

namespace Drupal\Core\Field;

interface FieldItemListInterface {

  /**
   * @psalm-taint-source input
   */
  public function __get($property_name) : string;

}

namespace Drupal\Core\Entity;

use Drupal\Core\Field\FieldItemListInterface;

interface FieldableEntityInterface {

    /**
     * @return FieldItemListInterface
     */
    public function __get($property_name);
  
    /**
     * @return FieldItemListInterface
     */
    public function get($property_name);

}

// @todo Figure out why this doesn't work without stub.

namespace Drupal\node\Entity;

use Drupal\Core\Field\FieldItemListInterface;

class Node {

    /**
     * @return FieldItemListInterface
     */
    public function __get($property_name) {}
  
    /**
     * @return FieldItemListInterface
     */
    public function get($property_name) {}

}
