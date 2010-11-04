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

    $wikiLink = self::parseWikiLink($pageName);

    if (!isset($wikiLink['url']))
    {
      return $match[0];
    }

    $attributes = array('class' => 'wikilink');

    if ('mobile_frontend' === sfConfig::get('sf_app'))
    {
      if (isset($wikiLink['enable_mobile']) && !$wikiLink['enable_mobile'])
      {
        return $match[0];
      }
    }
    elseif (isset($wikiLink['enable_pc']) && !$wikiLink['enable_pc'])
    {
      return $match[0];
    }

    if (isset($wikiLink['attributes']) && is_array($wikiLink['attributes']))
    {
      $attributes = array_merge($attributes, $wikiLink['attributes']);
    }

    $attributes['href'] = str_replace('%s', urlencode($wikiLink['pageName']), $wikiLink['url']);

    return content_tag('a', $linkText, $attributes);
  }

  static protected function parseWikiLink($wikiLink)
  {
    if (self::$wikiUrl === null)
    {
      self::loadWikiUrl();
    }

    $wikiName     = 'default';
    $pageName     = $wikiLink;

    if (preg_match('/^([^:]+):(.+)$/', $wikiLink, $pageNameMatch))
    {
      if (isset(self::$wikiUrl[$pageNameMatch[1]]))
      {
        $wikiName     = $pageNameMatch[1];
        $pageName = $pageNameMatch[2];
      }
    }

    $result = array(
      'wikiName' => $wikiName,
      'pageName' => $pageName,
    );

    if (!isset(self::$wikiUrl[$wikiName]))
    {
      return $result;
    }

    if (is_array(self::$wikiUrl[$wikiName]))
    {
      $result = array_merge($result, self::$wikiUrl[$wikiName]);
    }
    else
    {
      $result['url'] = self::$wikiUrl[$wikiName];
    }

    return $result;
  }

  static protected function loadWikiUrl()
  {
    $cache = sfContext::getInstance()->getConfigCache();
    $cache->registerConfigHandler(self::WIKI_URL_CONFIG_PATH, 'sfSimpleYamlConfigHandler');
    self::$wikiUrl = include($cache->checkConfig(self::WIKI_URL_CONFIG_PATH));
  }
}
