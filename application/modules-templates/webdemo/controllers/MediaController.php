<?php
/**
 * CORE Conference Manager
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.terena.org/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to webmaster@terena.org so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2011 TERENA (http://www.terena.org)
 * @license    http://www.terena.org/license/new-bsd     New BSD License
 * @revision   $Id: MediaController.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */
require_once APPLICATION_PATH.'/modules/webdemo/controllers/AbstractController.php';

class Webdemo_MediaController extends Webdemo_AbstractController
{

	public function indexAction()
	{
		$this->_forward('announcements');
 		$this->_helper->getHelper('AjaxContext')
					  ->addActionContext('coverage', 'json')
					  ->initContext();
	}

	public function announcementsAction()
	{
		$this->view->stylesheet('media.css');
		$db = Zend_Db::factory(
			Zend_Registry::get('webConfig')->resources->multidb->webdb
		);

		$this->view->announcements = $db->fetchAll(
			"SELECT * FROM announcements WHERE active=true ORDER BY created_at DESC"
		);
	}

	/**
	 * Live streaming action
	 *
	 */
	public function streamAction()
	{
		$this->view->threeColumnLayout = true;
		$this->view->stylesheet('media.css');
		$this->view->headScript()->appendFile('/js/jwplayer.js');

		// uncomment for testing
		$datearray = array(
			'year' => 2011,
			'month' => 5,
			'day' => 18,
			'hour' => 11,
			'minute' => 1,
			'second' => 10);
		$zd = new Zend_Date($datearray);

		$schedule = new Core_Model_Schedule();
		$this->view->roomsessions = $schedule->getStreamData();
		$this->view->activeStream = $this->getRequest()->getParam('stream');

		// quality toggler
		$conference = Zend_Registry::get('conference');
		if ($quality = $this->_getParam('quality')) {
			setcookie('stream_quality', $quality, time() + (14 * 3600 * 24), '/', $conference['hostname']);
			$this->view->quality = $quality;
		} else {
			$this->view->quality = $this->getRequest()->getCookie('stream_quality', 'lo');
		}
	}

	/**
	 * Archived video stream
	 *
	 */
	public function archiveAction()
	{
		$this->view->threeColumnLayout = true;
		$this->view->stylesheet('media.css');
		$this->view->headScript()->appendFile('/js/jwplayer.js');

		// uncomment for testing
		$datearray = array(
			'year' => 2011,
			'month' => 5,
			'day' => 18,
			'hour' => 16,
			'minute' => 1,
			'second' => 10);
		$zd = new Zend_Date($datearray);

		$sessionModel = new Core_Model_Session();
		$this->view->archive = $sessionModel->getResource('sessionsview')->getSessionsBeforeDate()->group();
		$this->view->activeStream = $this->getRequest()->getParam('stream');

		// quality toggler
		$conference = Zend_Registry::get('conference');
		$this->view->quality = 'hi';
		if ($quality = $this->_getParam('quality')) {
			setcookie('stream_quality', $quality, time() + (14 * 3600 * 24), '/', $conference['hostname']);
			$this->view->quality = $quality;
		} else {
			$this->view->quality = $this->getRequest()->getCookie('stream_quality', 'lo');
		}

	}

	/**
	 * Interviews/other recorded videos
	 *
	 */
	public function videoAction()
	{
		$this->view->threeColumnLayout = true;
		$this->view->stylesheet('media.css');
		$this->view->stylesheet('video.css');
		$this->view->headScript()->appendFile('/js/jwplayer.js');

		$this->view->activeStream = $this->getRequest()->getParam('stream');

		$this->view->streams = array(
			'15sunday' => array(
				'name' => 'Sunday',
				'title' => 'Sunday, May 15, 2011',
				'description' => 'Sunday impression'
			),
			'16monday' =>	array(
				'name'	=>	'Monday',
				'title'	=>	'Monday, May 16, 2011',
				'description'	=>	'Monday impression'
			),
			'17tuesday'	=>	array(
				'name'	=>	'Tuesday',
				'title'	=>	'Tuesday, May 17, 2011',
				'description'	=>	'Tuesday impression'
			),
			'18wednesday'     => array(
				'name'  => 'Wednesday',
				'title' => 'Wednesday, May 18, 2011',
				'description' => 'Wednesday impression'
			),
			'19thursday' => array(
				'name'  => 'Thursday',
				'title' => 'Thursday, May 19, 2011',
				'description' => 'Thursday impression'
			),
			'TNC2011_Impressions' => array(
				'name'  => 'TNC2011 at a glance',
				'title' => 'TNC2011 at a glance',
				'description' => 'TNC2011 Impressions'
			)
		);
	}

	/**
	 * News from TERENA website
	 *
	 */
	public function newsAction()
	{
		$this->view->stylesheet('media.css');
		$db = Zend_Db::factory(
			Zend_Registry::get('webConfig')->resources->multidb->terena
		);

		$this->view->news = $db->fetchAll(
			"SELECT * FROM news WHERE active=true AND tnc=true AND inserted > '20101001' AND inserted < '20111007' and publish_date < now() ORDER BY publish_date DESC"
		);
		$lastItem = current($this->view->news);

		$this->view->selected = $db->fetchRow(
			"select * from news where news_id=".$this->getRequest()->getParam('id', $lastItem['news_id'])
		);
	}

	/**
	 * Photos page
	 *
	 */
	public function photosAction()
	{
		$this->view->stylesheet('media.css');
		$this->view->stylesheet('../js/slimbox/css/slimbox2.css');
		$this->view->javascript('slimbox/js/slimbox2.js');

		$allowed_days = array(
			'sunday' => 'sunday',
			'monday' => 'monday',
			'tuesday' => 'tuesday',
			'wednesday' => 'wednesday',
			'thursday' => 'thursday'
		);

		$this->view->days = $allowed_days;
		$this->view->day = $day = $this->getRequest()->getParam('day', 'wednesday');
		if (!in_array($day, $allowed_days)) { exit(); }

		$relpath = '/includes/tnc2011/gfx/photos/'.$day.'/';
		$path = '/pub/www/core_live/public'.$relpath;
		$diterator = new DirectoryFilterDots($path);
		$photos = array();
		try {
			// loop through directory and build photo array
			foreach ($diterator as $item) {
				$photos[$item->key()]['location'] = $relpath.$item->getFilename();
				$photos[$item->key()]['href'] = $relpath.str_replace('_thumb', '', $item->getFilename());
			}
		} catch (Exception $e) {
			exit();
		}
		$this->view->photos = $photos;
	}


	/**
	 * Coverage page
	 *
	 * @note Still in development
	 */
	public function coverageAction()
	{
		$this->view->threeColumnLayout = true;
		// add this to make sure json output does not get flooded with
		// notices from the YUI library
		error_reporting(0);

		// @todo: override setLayout() where it sets 'customlayout' property so I don't have
		// to remember adding it in the controller
		$this->_helper->layout->assign('customlayout', true);
		$this->_helper->layout->setLayout('tnc2011/coverage');
		$this->view->stylesheet('min-coverage.css');
		// @todo: move this to includes/tnc2011 !
		//$this->view->headLink()->appendStylesheet('/js/prettyPhoto/css/prettyPhoto.css');
		$this->view->headLink()->appendStylesheet('/js/jquery-ui/css/ui-lightness/jquery-ui.css');
		$this->view->headScript()->appendFile('/js/jquery-ui/js/jquery-ui.min.js');
		//$this->view->headScript()->appendFile('/js/prettyPhoto/js/jquery.prettyPhoto.js');
		$this->view->headScript()->appendFile('/js/coverage.js');

		$yql = new YQL();
		$results = $yql->getData();
		$markup = new htmlMarkup($results);

		if (!$this->getRequest()->isXmlHttpRequest()) {
			$this->view->results = $results;
			$this->view->markup = $markup;
		} else {
			$return = array();
			$return['blog'] = $markup->blog();
			$return['twitter'] = $markup->twitter();
			$return['flickr'] = $markup->flickr();
			$return['youtube'] = $markup->youtube();
			echo json_encode($return);
			exit();
		}
	}
}





/**
 * @todo Make this shit a service! No time now, so will probably never get done
 *
 */

class htmlMarkup {

	private $_results;

	public function __construct($results)
	{
		$this->_results = $results;
	}

	public function blog()
	{
		$html = '<ul id="blog">';
		for ($i = 0; $i < sizeof($this->_results['blog']); ++$i) {
			$r = $this->_results['blog'][$i];
			if (isset($r->creator)) {
				$author = $r->creator;
			}
			if (isset($r->author)) {
				$author = $r->author;
			}
			if ($i + 1 == sizeof($this->_results['blog'])) {
				$html .= '<li class="last">';
			} else {
				$html .= '<li>';
			}
			if (isset($r->content)) {
				// we are probably dealing with Wordpress
				$html .= '<div class="blog-image">';

				$url = (is_array($r->content)) ? $r->content[0]->url : $r->content->url;
				$authorPicture = substr($url, 0, strpos($url, '?'));

				$html .= '<img src="'.$authorPicture.'?s=48" alt="'.$r->creator.'" title="'.$r->creator.'" height="48" width="48" /></div>';
			} else {
				// we are probably dealing with Blogger
				$html .= '<div class="blog-image">';
				$html .= '<img src="/includes/tnc2011/gfx/socialmedia/rss.png" alt="'.$r->author.'" title="'.$r->author.'" height="48" width="48" /></div>';
			}
			$html .= '<div class="blog-text">';
			$html .= $author.' > '.'<a href="'.$r->link.'">'.$r->title.'</a>';
			$html .= '<div class="metadata">';

				$diff = $this->_time_passed(strtotime($r->pubDate), strtotime('now'));
				$units = 0;
				$created_at = array();
				foreach ($diff as $unit => $value) {
				   if ($value != 0 && $units < 2) {
						if ($value === 1) {
							#let's remove the plural "s"
							$unit = substr($unit, 0, -1);
						}
					   $created_at[] = $value.' '.$unit;
					   ++$units;
					}
				}
				$created_at = implode(', ', $created_at);
				$created_at .= ' ago';
				$html .= $created_at;
			$html .= '</div></div>';
			$html .= '<div class="clearer"></div>';
			$html .= '</li>';
		}
		$html .= '</ul>';
		#$nextPage = ($params) ? '?page='. ( substr(strrchr($params, "="), 1) + 1 ) : '?page=2' ;
		#$html .= '<span class="pager"><a class="blog-pager" href="'.$nextPage.'">Older Posts</a></span>';
		#if ($params) {
		#	$pageParam = substr(strrchr($params, "="), 1);
		#	if ($pageParam != 1) {
		#		$previousPage = '?page='. ( $pageParam - 1 );
		#		$html .= '<span class="pager"><a class="blog-pager" href="'.$previousPage.'">Newer Posts</a></span>';
		#	}
		#}
		return $html;
	}

	public function twitter()
	{
		$spammers = array('96324065', '120871936');
		$html = '<ul id="tweets">';
		if (property_exists($this->_results['twitter'], 'results')) {
			for ($i = 0; $i < sizeof($this->_results['twitter']->results); ++$i) {
				$r = $this->_results['twitter']->results[$i];
				if (!in_array($r->from_user_id, $spammers)) {
					if ($i + 1 == sizeof($this->_results['twitter']->results)) {
						$html .= '<li class="last">';
					} else {
						$html .= '<li>';
					}
					$html .= '<div class="tweet-avatar">';
					$html .= '<a href="http://www.twitter.com/'.$r->from_user.'"><img src="'.$r->profile_image_url.'" alt="" height="48" width="48" /></a>';
					$html .= '</div>';
					$html .= '<div class="tweet-text"><a href="http://twitter.com/'.$r->from_user.'">'.$r->from_user.' </a><span>'.$r->text;
					$html .= '</span><div class="metadata">';

					$diff = $this->_time_passed(strtotime($r->created_at), strtotime('now'));
					$units = 0;
					$created_at = array();
					foreach ($diff as $unit => $value) {
					   if ($value != 0 && $units < 2) {
							if ($value === 1) {
								#let's remove the plural "s"
								$unit = substr($unit, 0, -1);
							}
						   $created_at[] = $value.' '.$unit;
						   ++$units;
						}
					}
					$created_at = implode(', ', $created_at);
					$created_at .= ' ago';
					$html .= $created_at;
					$html .= '<span class="viewlink"><a href="http://twitter.com/'.$r->from_user.'/status/'.$r->id.'">View Tweet</a></span></div>';
					$html .= '</div>';
					$html .= '<div class="clearer"></div></li>';
				}
			}
		}
		$html .= '</ul>';
		#if (isset($this->_results['twitter']->next_page)) {
		#   $html .= '<span class="pager"><a class="tweets-pager" href="'.$this->_results['twitter']->next_page.'">older tweets</a></span>';
		#}
		#if (isset($this->_results['twitter']->previous_page)) {
		#   $html .= '<span class="pager"><a class="tweets-pager" href="'.$this->_results['twitter']->previous_page.'">newer tweets</a></span>';
		#}
		return $html;
	}

	public function flickr()
	{
		$html = '<ul id="flickr">';
		if (isset($this->_results['flickr'])) {
			foreach ($this->_results['flickr'] as $r) {
				$html .= '<li>';
				$html .= '<a title="" href="http://farm'.$r->farm.'.static.flickr.com/'.$r->server.'/'.$r->id.'_'.$r->secret.'.jpg" rel="prettyPhoto[flickr]">';
				$html .= '<img src="http://farm'.$r->farm.'.static.flickr.com/'.$r->server.'/'.$r->id.'_'.$r->secret.'_s.jpg" alt="flickrimage" title="flickrimage" height="75" width="75" />';
				$html .= '</a></li>';
			}
		}
		$html .= '</ul>';
		return $html;
	}

	public function morepics()
	{
		$html = '<ul id="morepics">';
		foreach ($this->_results['morepics']['picasa'] as $r) {
			$html .= '<li>';
			$html .= '<a title="'.$r->title.'" href="'.$r->link.'" rel="lightbox-morepics" target="_blank">';
			$html .= '<img src="'.$r->group->thumbnail[0]->url.'" alt="'.$r->group->credit.'" title="'.$r->group->credit.'" height="72" width="72" />';
			$html .= '</a></li>';
		}
		foreach ($this->_results['morepics']['martin'] as $r) {
			$html .= '<li>';
			$html .= '<a href="'.$r->href.'" target="_blank">';
			$html .= '<img src="http://bech.uni-c.dk/tnc2010/thumbnails/'.$r->img->src.'" alt="'.$r->img->src.'" title="" height="75" width="75" />';
			$html .= '</a></li>';
		}
		#$html .= '<li><a href="/gfx/photos/thursday/IMG_7410.jpg" title="" rel="prettyPhoto[flickr]"><img src="/gfx/photos/img_7410_75x75.jpg" height="75" width="75" /></a></li>';
		$html .= '</ul>';
		return $html;
	}

	public function youtube()
	{
		$html = '<ul id="youtube">';
		if (is_array($this->_results['youtube'])) {
			foreach ($this->_results['youtube'] as $r) {
				$html .= $this->_youtubeLiBuilder($r);
			}
		} else {
			$html .= $this->_youtubeLiBuilder($this->_results['youtube']);
		}
		$html .= '</ul>';
		return $html;
	}

	private function _youtubeLiBuilder($r)
	{
		$data = array(
			'author' => $r->author->name,
			'title' => $r->title->content,
			'thumbnail' => $r->group->thumbnail[0]->url,
			'link' => $r->link[0]->href,
			'id' => $r->id,
			'published' => $r->published
		);
		$html = '<li>';
		$html .= '<div class="youtube-image"><img src="'.$r->group->thumbnail[0]->url.'" alt="" height="90" width="120" /></div>';
		$html .= '<div class="youtube-text">';
		$html .= $r->author->name.' > '.'<a href="'.htmlentities($r->link[0]->href).'" rel="prettyPhoto">'.$r->title->content.'</a>';
		#$html .= '<p>'.$r->content->content.'</p>';
		$diff = $this->_time_passed(strtotime($r->published), strtotime('now'));
		$units = 0;
		$created_at = array();
		foreach ($diff as $unit => $value) {
		   if ($value != 0 && $units < 2) {
				if ($value === 1) {
					#let's remove the plural "s"
					$unit = substr($unit, 0, -1);
				}
			   $created_at[] = $value.' '.$unit;
			   ++$units;
			}
		}
		$created_at = implode(', ', $created_at);
		$created_at .= ' ago';
		$html .= '<div class="metadata">'.$created_at.'</div>';
		$html .= '</div><div class="clearer"></div>';
		$html .= '</li>';
		return $html;
	}

	/**
	 * @param integer $t1
	 * @param integer $t2
	 */
	private function _time_passed($t1, $t2) {
		if ($t1 > $t2) {
		  $time1 = $t2;
		  $time2 = $t1;
		} else {
		  $time1 = $t1;
		  $time2 = $t2;
		}
		$diff = array(
		  'years' => 0,
		  'months' => 0,
		  'weeks' => 0,
		  'days' => 0,
		  'hours' => 0,
		  'minutes' => 0,
		  'seconds' =>0
		);
		$units = array('years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds');
		foreach ($units as $unit) {
		  while (true) {
			 $next = strtotime("+1 $unit", $time1);
			 if ($next < $time2) {
				$time1 = $next;
				$diff[$unit]++;
			 } else {
				break;
			 }
		  }
		}
		return($diff);
	}

	private function _createNormalYoutubeMarkup($str) {
		$dom = new domDocument;
		$dom->loadHTML($str);
		$dom->preserveWhiteSpace = false;
		$tables = $dom->getElementsByTagName('table');

		$rows = $tables->item(0)->getElementsByTagName('tr');

		// get all columns
		$cols = $rows->item(0)->getElementsByTagName('td');
		$anchor = $cols->item(0)->getElementsByTagName('a');
		$a = $anchor->item(0);

		$link = $a->getAttribute('href');
		$image = $a->firstChild->getAttribute('src');

		$divs = $cols->item(1)->getElementsByTagName('div');
		$title = $divs->item(0)->nodeValue;
		$description = $divs->item(1)->nodeValue;

		// return
		return array('link' => $link, 'image' => $image, 'title' => $title, 'description' => $description);
	}

}


class YQL {

	private $_results;

	private $_queryMulti;

	private $_cached;

	private $_logger;

	// Yahoo application object
	private $_application;

	// Yahoo Consumer object
	const API_KEY = "dj0yJmk9ck1XbkRYYXNaaGkxJmQ9WVdrOVFtc3pTMEZPTnpBbWNHbzlNakEwTWpjeU1UazJNZy0tJnM9Y29uc3VtZXJzZWNyZXQmeD0wOA--";

	// Yahoo Consumer secret
	const SHARED_SECRET = "49a97a26444cee726f4489d3d71ac5ce6989e236";

	// Debug of Curl requests
	const DEBUG = true;

	public function __construct() {
		$this->_logger = Zend_Registry::get('log');
	}

	/**
	 * Logger convenience method
	 * @param string $msg
	 */
	private function _logger($msg, $extra = null)
	{
		if (self::DEBUG) {
			$this->_logger->info($msg);
		}
	}

	public function getData()
	{
		$frontendOptions = array(
			'lifetime' => 300,
			'automatic_serialization' => true
		);

		$backendOptions = array(
			'cache_dir' => '/tmp/'
		);

		$cachePerm = Zend_Cache::factory('Core',
			'File',
			array(
				'lifetime' => null
			),
			$backendOptions);

		$cache = Zend_Cache::factory('Core',
			'File',
			$frontendOptions,
			$backendOptions);

		$cache2 = Zend_Cache::factory('Core',
			'File',
			array(
				'lifetime' => null
			),
			$backendOptions);

		// no cached result found
		if (!$results = $cache->load('yqlresult')) {

			if (!$cache2->load('yql_in_progress')) {
				// set 'cookie'
				$cache2->save('1', 'yql_in_progress');
				$this->_logger('Cache miss > performing query');
				$results = $this->performQueryMultiple();
				if ($results) {
					// only proceed if results returns something
					$results = $this->_buildResultsArray($results);
					$cache->save($results, 'yqlresult');
					#$cachePerm->save($results, 'yqlresultperm');
					$this->_logger('Cache miss > saving YQL result cache');
					$this->_cached = false;
					// unset 'cookie' - do this ONLY when there query was successfull!
					$cache2->remove('yql_in_progress');
				} else {
					$this->_logger('Cache miss > Something went wrong performing YQL query, so reading from cache');
					$results = $cache->load('yqlresult', true);
				}
			} else {
				// cookie is set, so query is in progress; use cache
				$this->_logger('Cache miss > YQL query in progress, so using cache');
				$results = $cache->load('yqlresult', true);
			}

		} else {
			// cache hit
			$this->_logger('Cache hit > reading from cache');
			$this->_cached = true;
		}

		return $results;
	}

	public function getOlderData($table, $params)
	{
		$results = $this->performQuery($table, $params);
		return $results;
	}

	public function isCachedResult()
	{
		return $this->_cached;
	}

	private function _buildResultsArray($results)
	{
		$res = array();
		$res['flickr'] = (isset($results[0]->photo)) ? $results[0]->photo : null;
		$res['blog'] = (isset($results[1]->item)) ? $results[1]->item : null;
		$res['twitter'] = (isset($results[2]->json)) ? $results[2]->json : null;
		$res['youtube'] = (isset($results[3]->entry)) ? $results[3]->entry : null;
		return $res;
	}

	/**
	 * Perform Query (query.multi)
	 *
	 * @return array on success and false on failure
	 */
	public function performQueryMulti()
	{
		$this->_buildQueryMulti();

		try {
			$this->_setYqlApplication();
			$this->_logger($this->_queryMulti);
			$data = $this->_application->query($this->_queryMulti);
		} catch (Exception $e) {
			return false;
		}
		if (!$data) {
			return false;
		}
		if ($data->query->count == 0) {
			return false;
		}
		return $data->query->results->results;
	}

	/**
	 * This appears to have the lowest load on YQL
	 * so this is used instead of queryMulti()
	 *
	 */
	public function performQueryMultiple()
	{
		$this->_setYqlApplication();
		$return = array();

		// Flickr
		$query = 'select farm,id,owner,secret,server,title from flickr.photos.search(0, 20) where group_id="1646917@N24"';
		$data = $this->_application->query($query);
		$return[] = $data->query->results;

		// Blog RSS
		$query = 'select * from rss where url in ("http://myterena.wordpress.com/feed/", "http://lpfischer.wordpress.com/feed/", "https://rnd.feide.no/feed/", "http://blog.signal2noise.ie/cgi-bin/blosxom.pl/index.rss20", "http://ejbjib.blogspot.com/feeds/posts/default?alt=rss") | sort(field="pubDate", descending="true") | truncate(count=10)';
		$data = $this->_application->query($query);
		$return[] = $data->query->results;

		// Twitter (forced cache down to 1 minute, by using window of time for cachebust parameter)
		$query = 'select * from json where url="http://search.twitter.com/search.json?q=%23tnc2011&q=%23TNC2011Prague&lang=en&rnd='.date("YmdHi").'"';
		$data = $this->_application->query($query);
		$return[] = $data->query->results;

		// Youtube
		$query = 'select * from feed where url="http://gdata.youtube.com/feeds/api/videos?q=&category=tnc2011"';
		$data = $this->_application->query($query);
		$return[] = $data->query->results;

		return $return;
	}

	public function performQuery($table, $params)
	{
		$query = $this->_buildQuery($table, $params);
		$this->_setYqlApplication();

		$data = $this->_application->query($query);
		// add parameters to return object
		$data->query->results->_tnc_params = $params;
		return $data->query->results;
	}

	private function _setYqlApplication()
	{
		include_once("yahoo-sdk/lib/Yahoo.inc");
		$this->_application = new YahooApplication(self::API_KEY, self::SHARED_SECRET);
		#YahooLogger::setDebug(self::DEBUG);
		if ($this->_application === null) {
		   throw new Exception('could not authenticate');
		}
	}

	private function _buildQuery($table, $params)
	{
		switch ($table) {
			case 'twitter':
				$query = 'select * from json where url="http://search.twitter.com/search.json'.$params.'"';
			break;
			case 'blog':
				$page = substr(strrchr($params, "="), 1);
				$page = 10 * (int) $page;
				$query = 'select * from rss where url in ("http://blog.ecampus.no/feed/",
				"http://lpfischer.wordpress.com/feed/","http://tnc2010.wordpress.com/feed/",
				"http://blog.archred.com/feeds/posts/default?alt=rss")
				| sort(field="pubDate", descending="true") | truncate(count='.$page.') | tail(count=10)';
			break;
		}
		return $query;
	}

	private function _buildQueryMulti()
	{
		// Flickr
		$this->_queryMulti = 'select farm,id,owner,secret,server,title from flickr.photos.search(0, 25) where group_id="1374625@N24";';

		// Blog RSS
		$this->_queryMulti .= 'select * from rss where url in ("http://myterena.wordpress.com/feed/", "http://lpfischer.wordpress.com/feed/", "https://rnd.feide.no/feed/", "http://blog.signal2noise.ie/cgi-bin/blosxom.pl/index.rss20", "http://ejbjib.blogspot.com/feeds/posts/default?alt=rss") | sort(field="pubDate", descending="true") | truncate(count=10)';
		
		// Twitter (forced cache down to 1 minute, by using window of time for cachebust parameter)
		#$this->_queryMulti .= 'select * from json where url="http://search.twitter.com/search.json?q=%26tnc2010&rnd='.date("YmdHi").'";';
		$this->_queryMulti .= 'select * from json where url="http://search.twitter.com/search.json?q=%26tnc2011";';

		// Youtube (only this month)
		$this->_queryMulti .= 'select * from feed where url="http://gdata.youtube.com/feeds/api/videos?q=&category=tnc2010" | sort(field="published") | reverse();';

		$this->_queryMulti = "select * from query.multi where queries='".$this->_queryMulti."'";
	}

}
// get files with SPL
class DirectoryFilterDots extends FilterIterator
{
	/**
	 * @param string $path
	 */
	public function __construct($path)
	{
		parent::__construct(new DirectoryIterator($path));
	}

	public function accept()
	{
		$inner = $this->getInnerIterator();
		$fn = explode('.', $inner->getFilename());
		if (substr($fn[0], -6) == '_thumb') {
			return !$this->getInnerIterator()->isDir();
		}

	}
}