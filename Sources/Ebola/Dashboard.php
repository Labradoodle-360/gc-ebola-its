<?php

/*
 * Ebola Effort
 *
 * Author: Matthew P. Kerle
 * Date Created: October 9th, 2014
 * From: Greenville College
 */

if (!defined('SMF'))
	die('No direct access...');

function DashboardMain()
{
	global $context, $txt, $scripturl, $sourcedir;

	isAllowedTo('ebola_view_dashboard');

	loadLanguage('Ebola/Primary');
	loadTemplate('Ebola/Dashboard');

	$context['linktree'][] = array(
		'url' => $scripturl . '?action=ebola-tools',
		'name' => $txt['ebola']['menu']['main']
	);

	$context['page_title'] = $txt['ebola']['main'] . ' | ';

	if (!empty($_GET['area']) && $_GET['area'] == 'quizzes')
	{
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=ebola-tools;area=quizzes',
			'name' => $txt['ebola']['linktree']['quizzes']
		);

		require_once($sourcedir . '/Ebola/Quizzes.php');
		loadEbolaQuizzes();
	}
	else
	{
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=ebola-tools;area=dashboard',
			'name' => $txt['ebola']['linktree']['dashboard']
		);

		$context['page_title'] .= $txt['ebola']['page_titles']['dashboard'];
	}
}