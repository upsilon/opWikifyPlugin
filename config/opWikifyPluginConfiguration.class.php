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
 * opWikifyPluginConfiguration
 *
 * @package     opWikifyPlugin
 * @author      Kimura Youichi <kim.upsilon@gmail.com>
 */

class opWikifyPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->dispatcher->connect('op_decoration.filter_html', array('opWikifyDecorator', 'listenToPostDecorateString'));
  }
}
