<?php

/**
 * opWikifyPlugin
 *
 * This source file is subject to the Apache License version 2.0
 * that is bundled with this package in the file LICENSE.
 *
 * @copyright   2010 Kimura Youichi <kim.upsilon@gmail.com>
 * @license     Apache License 2.0
 */

/**
 * opWikifyDecorator
 *
 * @package     opWikifyPlugin
 * @author      Kimura Youichi <kim.upsilon@gmail.com>
 */

class opWikifyDecorator
{
  const WIKI_URL_CONFIG_PATH = 'config/interwiki.yml';

  static protected $wikiUrl = null;

  static public function listenToPostDecorateString(sfEvent $event, $value)
  {
    return preg_replace_callback('/\[\[([^\]\|]+)(?:\|([^\]]+))?\]\]/', array(__CLASS__, 'replaceWikiLink'), $value);
  }

  static protected function replaceWikiLink($match)
  {
    $pageName = $match[1];
    $linkText = count($match) == 3 ? $match[2] : $match[1];

    return sprintf('<a class="wikilink" href="%s">%s</a>', self::genWikiUrl($pageName), $linkText);
  }

  static protected function genWikiUrl($pageName)
  {
    if (self::$wikiUrl === null)
    {
      self::loadWikiUrl();
    }

    $wikiName = 'default';

    if (preg_match('/^([^:]+):(.+)$/', $pageName, $match))
    {
      if (isset(self::$wikiUrl[$match[1]]))
      {
        $wikiName = $match[1];
        $pageName = $match[2];
      }
    }

    $pageName = urlencode($pageName);

    return sprintf(self::$wikiUrl[$wikiName], $pageName);
  }

  static protected function loadWikiUrl()
  {
    $cache = sfContext::getInstance()->getConfigCache();
    $cache->registerConfigHandler(self::WIKI_URL_CONFIG_PATH, 'sfSimpleYamlConfigHandler');
    self::$wikiUrl = include($cache->checkConfig(self::WIKI_URL_CONFIG_PATH));
  }
}
