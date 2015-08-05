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
 * @revision   $Id: formdefaults.php 104 2013-04-08 11:58:49Z gijtenbeek@terena.org $
 */
return array(
	'formdefaults' => array(
		'review' => array(
			'suitability_conf' =>  array(
				'0' => '',
				'1' => '1 - Not interesting, innovative and relevant',
				'2' => '2 - Not specially interesting, innovative and relevant',
				'3' => '3 - Borderline',
				'4' => '4 - Reasonably interesting, innovative and relevant',
				'5' => '5 - Very interesting, innovative and relevant'
			),
			'quality' => array(
				'0' => '',
				'1' => '1 - Unstructured, unaware of previous work',
				'2' => '2 - Not well structured',
				'3' => '3 - Borderline',
				'4' => '4 - Can be improved',
				'5' => '5 - Well structured, develops previous work'
			),
			'importance' => array(
				'0' => '',
				'1' => '1 - No result',
				'2' => '2 - Minor contribution/yet another paper',
				'3' => '3 - Marginal',
				'4' => '4 - Useful result',
				'5' => '5 - Important result'
			),
			'rating' => array(
				'0' => '',
				'1' => '1 - Reject - out of scope',
				'2' => '2 - Reject - with comments',
				'3' => '3 - Accept with suggested changes',
				'4' => '4 - Accept with minor changes',
				'5' => '5 - Accept as is (very high quality)'

			),
			'self_assessment' => array(
				'0' => '',
				'1' => '1 - Wrong reviewer',
				'2' => '2 - Unsure, would like second opinion',
				'3' => '3 - A little unsure',
				'4' => '4 - Sure',
				'5' => '5 - Very sure'
			)
		),

		'location' => array(
			'types' => array(
				'0' => '---',
				'1' => 'room',
				'2' => 'external'
			)
		),

		'poster' => array(
			'categories' => array(
				'0' => '---',
				'1' => 'normal',
				'2' => 'student'
			)
		),		

		'submit' => array(
			'status' => array(
				'0' => '---',
				'1' => 'yes',
				'2' => 'no',
				'3' => 'maybe'
			),
			'target_audience' => array(
				'gen' => 'General audience',
				'mod' => 'Moderately technical audience',
				'high' => 'Highly technical audience'
			),
			'publish_paper' => array(
				'yes' => 'yes',
				'no' => 'no'
			),
			'topic' => array(
				'nren' => 'NREN 3.0',
				'openscience' => 'OPEN SCIENCE, BIG DATA',
				'solutions' => 'CREATING SOLUTIONS',
				'infra' => 'CREATING INFRASTRUCTURES'
			)
		),

		'feedback' => array(
			'rating' => array(
				'0' => 'N/A',
				'5' => 'Excellent',
				'4' => 'Good',
				'3' => 'Neutral',
				'2' => 'Poor',
				'1' => 'Very poor'
			)
		)
	)
);