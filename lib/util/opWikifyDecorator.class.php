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
    sfContext::getInstance()->getConfiguration()->loadHelpers('Tag');

    return preg_replace_callback('/\[\[([^\]\|]+)(?:\|([^\]]+))?\]\]/', array(__CLASS__, 'replaceWikiLink'), $value);
  }

  static protected function replaceWikiLink($match)
  {
    $pageName = $match[1];
    $linkText = count($match) == 3 ? $match[2] : $match[1];

    if (self::$wikiUrl === null)
    {
      self::loadWikiUrl();
    }

    $wikiName     = 'default';

    if (preg_match('/^([^:]+):(.+)$/', $pageName, $pageNameMatch))
    {
      if (isset(self::$wikiUrl[$pageNameMatch[1]]))
      {
        $wikiName     = $pageNameMatch[1];
        $pageName = $pageNameMatch[2];
      }
    }

    $pageName = urlencode($pageName);

    if (!isset(self::$wikiUrl[$wikiName]))
    {
      return $match[0];
    }

    $attributes = array('class' => 'wikilink');

    if (is_array(self::$wikiUrl[$wikiName]))
    {
      $urlInfo = self::$wikiUrl[$wikiName];

      if (isset(self::$wikiUrl['url']))
      {
        return $match[0];
      }

      if ('mobile_frontend' === sfConfig::get('sf_app'))
      {
        if (isset($urlInfo['enable_mobile']) && !$urlInfo['enable_mobile'])
        {
          return $match[0];
        }
      }
      elseif (isset($urlInfo['enable_pc']) && !$urlInfo['enable_pc'])
      {
        return $match[0];
      }

      if (isset($urlInfo['attributes']) && is_array($urlInfo['attributes']))
      {
        $attributes = array_merge($attributes, $urlInfo['attributes']);
      }

      $url = sprintf($urlInfo['url'], $pageName);
    }
    else
    {
      $url = sprintf(self::$wikiUrl[$wikiName], $pageName);
    }

    $attributes['href'] = $url;
    return content_tag('a', $linkText, $attributes);
  }

  static protected function loadWikiUrl()
  {
    $cache = sfContext::getInstance()->getConfigCache();
    $cache->registerConfigHandler(self::WIKI_URL_CONFIG_PATH, 'sfSimpleYamlConfigHandler');
    self::$wikiUrl = include($cache->checkConfig(self::WIKI_URL_CONFIG_PATH));
  }
}
