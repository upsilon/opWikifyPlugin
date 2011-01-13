<?php

include_once dirname(__FILE__).'/../../bootstrap/unit.php';

sfContext::createInstance(ProjectConfiguration::getApplicationConfiguration('pc_frontend', 'test', true));
include_once sfConfig::get('sf_lib_dir').'/vendor/symfony/lib/helper/HelperHelper.php';
use_helper('opUtil');

$t = new lime_test(10, new lime_output_color());

$base = sfConfig::get('op_base_url');

$t->comment('opWikifyDecorator::replaceWikiLink()');

$t->is(
  op_decoration('[[aaa]]'),
  '<a class="wikilink" target="_blank" href="http://example.com/wiki/aaa">aaa</a>',
  'decorator converts a wikilink to an <a> tag'
);

$t->is(
  op_decoration('[[bbb|ccc]]'),
  '<a class="wikilink" target="_blank" href="http://example.com/wiki/bbb">ccc</a>'
);

$t->is(
  op_decoration('[[bbb|ccc|ddd]]'),
  '<a class="wikilink" target="_blank" href="http://example.com/wiki/bbb">ccc|ddd</a>'
);

$t->is(
  op_decoration('[[abc:def]]'),
  '<a class="wikilink" target="_blank" href="http://example.com/wiki/abc%3Adef">abc:def</a>'
);

$t->is(
  op_decoration('[[abc:def:ghi]]'),
  '<a class="wikilink" target="_blank" href="http://example.com/wiki/abc%3Adef%3Aghi">abc:def:ghi</a>'
);

$t->is(
  op_decoration('[[wp:メインページ]]'),
  '<a class="wikilink" target="_blank" href="http://ja.wikipedia.org/wiki/'.urlencode('メインページ').'">wp:メインページ</a>'
);

$t->is(
  op_decoration('[[wp:メインページ|Wikipedia]]'),
  '<a class="wikilink" target="_blank" href="http://ja.wikipedia.org/wiki/'.urlencode('メインページ').'">Wikipedia</a>'
);

$t->is(
  op_decoration('[[wp:MediaWiki:Common.css]]'),
  '<a class="wikilink" target="_blank" href="http://ja.wikipedia.org/wiki/MediaWiki%3ACommon.css">wp:MediaWiki:Common.css</a>'
);

$t->is(
  op_decoration('[[member:1]]'),
  '<a class="wikilink" href="'.$base.'member/1">member:1</a>'
);

$t->is(
  op_decoration('[[member:1|OpenPNE君]]'),
  '<a class="wikilink" href="'.$base.'member/1">OpenPNE君</a>'
);

