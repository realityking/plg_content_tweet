<?php
/**
 * @copyright	Copyright (C) 2011 Rouven Weßling. All rights reserved.
 * @license		GNU General Public License version 2 or later; see license.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('joomla.environment.uri');

/**
 * Tweet plugin.
 *
 */
class plgContentTweet extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onContentBeforeDisplay($context, &$row, &$params, $page=0)
	{
		if ($this->params->def('position', 0) == 0 && $context == 'com_content.article') {
			if ($this->params->def('loadcss', 0) == 0) {
				$loadcss = true;
			} else {
				$loadcss = false;
			}

			return self::renderButton($loadcss, $row);
		}
	}
	
	public function onContentAfterDisplay($context, &$row, &$params, $page=0)
	{
		if ($this->params->def('position', 0) == 1 && $context == 'com_content.article') {
			if ($this->params->def('loadcss', 0) == 0) {
				$loadcss = true;
			} else {
				$loadcss = false;
			}

			return self::renderButton($loadcss, $row);
		}
	}
	
	public static function renderButton($loadcss, $item)
	{
		static $loaded = false;
		
		$doc = JFactory::getDocument();
		$lang = JFactory::getLanguage();
		$tag = explode('-', $lang->getTag());
		$tag = $tag[0];
		JHtml::_('behavior.framework');

		if (!$loaded) {
			$doc->addScriptDeclaration('
			window.addEvent("domready", function() {
				var twbts = $$(".tweet-button > a");
				twbts.each(function(item) {
					item.addEvent("click", function(e) {
						e.stop();
						var w = 550, h = 450;
						var wh = screen.height;
						var ww = screen.width;

						var left = Math.round((ww/2)-(w/2));
						if (wh>h) {
							var top = Math.round((wh/2)-(h/2));
						}
						window.open(this.href, "twitter_tweet", "status=0,toolbar=0,location=0,menubar=0,directories=0,resizable=0,scrollbars=0,width="+w+",height="+h+",left="+left+",top="+top);
					});
				})
			});');

			if ($loadcss) {
				$doc->addStyleSheet(JURI::base().'media/plg_content_tweet/css/tweet.css');
			}
			$loaded = true;
		}

		$uri = JURI::root().substr(JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid)), 1);
		$urisuffix = "&amp;text=".htmlspecialchars(urlencode($item->title));
		if (in_array($tag, array('de', 'es', 'fr', 'ja', 'ko'))) {
			$urisuffix .= "&amp;lang=".$tag;
		}

		$html = '';
		$attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=450,height=550';
		$html .= '<div class="tweet-button"><a rel="nofollow" href="http://twitter.com/share?url=';
		$html .= htmlspecialchars(urlencode($uri)).$urisuffix;
		$html .= '" target="_blank" title="'.JText::sprintf('PLG_CONTENT_TWEET_SHARE_ON_TWITTER', $uri).'">'.JText::_('PLG_CONTENT_TWEET_TWEET').'</a></div>';

		return $html;
	}
}
