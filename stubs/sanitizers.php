<?php

namespace Drupal\Component\Utility;

class Xss
{
  /**
   * @psalm-taint-escape html
   *
   * @param string $string
   *
   * @return string
   */
    public static function filter($string, array $html_tags = null)
    {
    }

  /**
   * @psalm-taint-escape html
   *
   * @param string $string
   *
   * @return string
   */
    public static function filterAdmin($string)
    {
    }
}

class Html
{
    /**
     * @psalm-taint-escape html
     *
     * @param string $identifier
     *
     * @return string
     *
     */
    public static function cleanCssIdentifier($identifier, array $filter)
    {
    }

    /**
     * @psalm-taint-escape html
     *
     * @param string $id
     *
     * @return string
     */
    public static function getId($id)
    {
    }

    /**
     * @psalm-taint-escape html
     *
     * @param string $text
     *
     * @return string
     */
    public static function escape($text)
    {
    }
}
