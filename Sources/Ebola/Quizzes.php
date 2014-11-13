<?php

/*
 * Ebola Effort
 *
 * Author: Matthew P. Kerle
 * Date Created: October 9th, 2014
 * From: Greenville College
 */

//-- Matthew was here...example commit.

if (!defined('SMF'))
	die('No direct access...');

function loadEbolaQuizzes()
{
	global $context, $sourcedir;

	isAllowedTo('ebola_view_quizzes');

	require_once($sourcedir . '/Ebola/Subs-Quizzes.php');

	$context['ebola']['quizzes'] = quizzes_retrieveQuizzes();

	loadLanguage('Ebola/Quizzes');
	loadTemplate('Ebola/Quizzes');

	//-- http://www.jankoatwarpspeed.com/css-message-boxes-for-different-message-types/
	$context['html_headers'] .= '
	<style type="text/css">
	.info, .success, .warning, .error, .validation {
		border: 1px solid;
		margin: 10px 0px;
		padding:15px 10px 15px 50px;
		background-repeat: no-repeat;
		background-position: 10px center;
	}
	.info {
		color: #00529B;
		background-color: #BDE5F8;
	}
	.success {
		color: #4F8A10;
		background-color: #DFF2BF;
	}
	.warning {
		color: #9F6000;
		background-color: #FEEFB3;
	}
	.error {
		color: #D8000C;
		background-color: #FFBABA;
	}
	.error strong {
		color: #D8000C;
	}
	.error ul li {
		margin-left: 20px;
		list-style-type: disc;
	}
	.form_field {
		margin-bottom: 8px;
		padding-bottom: 8px;
		border-bottom: 1px dotted #999;
	}
	.form_field .floatleft {
		width: 50%;
		margin-top: 4px;
	}
	.form_field .floatright {
		width: 50%;
	}
	.form_field .button_submit {
		margin-left: 0px !important;
		float: left !important;
	}
	.hidden {
		display: none;
	}
	input, textarea, select {
		padding: 6px;
	}
	.element_text {
		margin-left: 2px;
		color: #555;
	}
	</style>';

	$subActions = array(
		'view' => 'quizzes_loadSpecificQuiz',
		'add-question' => 'quizzes_addQuestionHandler',
		'add-category' => 'quizzes_addCategoryHandler',
		'export' => 'quizzes_exportQuizHandler',
	);

	if (!empty($_REQUEST['sa']) && array_key_exists($_REQUEST['sa'], $subActions) && !empty($_REQUEST['quiz']) && array_key_exists($_REQUEST['quiz'], $context['ebola']['quizzes']))
		call_user_func($subActions[$_REQUEST['sa']]);
	else
		quizzes_loadQuizzes();

}

function quizzes_loadQuizzes()
{
	global $context, $txt, $sourcedir, $scripturl, $modSettings;

	$context['sub_template'] = 'view_quizzes';

    $context['page_title'] .= $txt['ebola']['page_titles']['quizzes'];

	require_once($sourcedir . '/Subs-List.php');
	$listOptions = array(
		'id' => 'quizzes',
		'items_per_page' => $modSettings['defaultMaxMessages'],
		'default_sort_col' => 'quiz_name',
		'get_items' => array(
			'function' => 'quizzes_listQuizzes',
			'params' => array(),
		),
		'get_count' => array(
			'function' => 'quizzes_countQuizzes',
			'params' => array(),
		),
		'columns' => array(
			'quiz_name' => array(
				'header' => array(
					'value' => 'Quiz Name',
				),
				'data' => array(
					'db' => 'quiz_name',
					'class' => 'lefttext',
				),
				'sort' => array(
					'default' => 'itq.quiz_name',
					'reverse' => 'itq.quiz_name DESC',
				),
			),
			'categories' => array(
				'header' => array(
					'value' => 'Categories',
					'class' => 'lefttext',
				),
				'data' => array(
					'db' => 'categories',
					'class' => 'lefttext',
				),
			),
			'total_questions' => array(
				'header' => array(
					'value' => 'Total Questions',
					'class' => 'centertext',
				),
				'data' => array(
					'db' => 'total_questions',
					'class' => 'centertext',
				),
				'sort' => array(
					'default' => 'itq.total_questions',
					'reverse' => 'itq.total_questions DESC',
				),
			),
		),
	);

	if (!allowedTo('ebola_add_modify_quizzes'))
	{
		$listOptions['columns'] = array_merge($listOptions['columns'], array(
			'actions' => array(
				'header' => array(
					'value' => 'Actions',
					'class' => 'centertext',
				),
				'data' => array(
					'db' => 'actions',
					'class' => 'centertext',
					'style' => 'min-width: 70px;',
				),
			),)
		);
	}

	$listOptions['no_items_label'] = 'Sorry, but it appears that there are no quizzes.';
	if (allowedTo('ebola_add_modify_quizzes'))
		$listOptions['no_items_label'] .= ' Click <a href="' . $scripturl . '?action=ebola-tools;area=quizzes;sa=add" target="_self">here</a> to create one!';

	$listOptions['base_href'] = $scripturl . '?action=ebola-tools;area=quizzes';

	createList($listOptions);

	$txt['add_quiz'] = 'Add Quiz';

	$context['add_quiz_permission'] = allowedTo('ebola_add_modify_quizzes');

	$context['add_quiz_buttons'] = array(
		'add-quiz' => array(
			'text' => 'add_quiz',
			'url' => $scripturl . '?action=ebola-tools;area=quizzes;sa=add',
			//'active' => true,
			'test' => 'add_quiz_permission',
		),
	);
}

function quizzes_loadSpecificQuiz()
{
	global $context, $txt, $sourcedir, $scripturl, $modSettings;

	$quiz_id = $_REQUEST['quiz'];
	$this_quiz = $context['ebola']['quizzes'][$quiz_id];

	$context['page_title'] .= $this_quiz['quiz_name'];

	$context['linktree'][] = array(
		'url' => $scripturl . '?action=ebola-tools;area=quizzes;sa=view;quiz=' . $quiz_id,
		'name' => $this_quiz['quiz_name']
	);

	$context['sub_template'] = 'view_specific_quiz';

	require_once($sourcedir . '/Subs-List.php');

	$listOptions = array(
		'id' => 'specific_quiz',
		'items_per_page' => $modSettings['defaultMaxMessages'],
		'default_sort_col' => 'category_name',
		'get_items' => array(
			'function' => 'quizzes_listSpecificQuiz',
			'params' => array(
				array('quiz_id' => $quiz_id)
			),
		),
		'get_count' => array(
			'function' => 'quizzes_countSpecificQuiz',
			'params' => array(), //-- Populated below, from get_items->params[].
		),
		'columns' => array(
			'category_name' => array(
				'header' => array(
					'value' => 'Category',
				),
				'data' => array(
					'db' => 'category_name',
					'class' => 'lefttext',
				),
				'sort' => array(
					'default' => 'qc.category_name',
					'reverse' => 'qc.category_name DESC',
				),
			),
			'question_type' => array(
				'header' => array(
					'value' => 'Type',
				),
				'data' => array(
					'db' => 'question_type',
					'class' => 'lefttext',
				),
				'sort' => array(
					'default' => 'qq.question_type',
					'reverse' => 'qq.question_type DESC',
				),
			),
			'question_text' => array(
				'header' => array(
					'value' => 'Question',
				),
				'data' => array(
					'db' => 'question_text',
					'class' => 'lefttext',
				),
				'sort' => array(
					'default' => 'qq.question_text',
					'reverse' => 'qq.question_text DESC',
				),
			),
			'question_prompt' => array(
				'header' => array(
					'value' => 'Prompt',
				),
				'data' => array(
					'db' => 'question_prompt',
					'class' => 'lefttext',
				),
				'sort' => array(
					'default' => 'qq.question_prompt',
					'reverse' => 'qq.question_prompt DESC',
				),
			),
			'multiple_choice_a' => array(
				'header' => array(
					'value' => 'A',
				),
				'data' => array(
					'db' => 'multiple_choice_a',
					'class' => 'lefttext',
				),
				'sort' => array(
					'default' => 'qa.multiple_choice_a',
					'reverse' => 'qa.multiple_choice_a DESC',
				),
			),
			'multiple_choice_b' => array(
				'header' => array(
					'value' => 'B',
				),
				'data' => array(
					'db' => 'multiple_choice_b',
					'class' => 'lefttext',
				),
				'sort' => array(
					'default' => 'qa.multiple_choice_b',
					'reverse' => 'qa.multiple_choice_b DESC',
				),
			),
			'multiple_choice_c' => array(
				'header' => array(
					'value' => 'C',
				),
				'data' => array(
					'db' => 'multiple_choice_c',
					'class' => 'lefttext',
				),
				'sort' => array(
					'default' => 'qa.multiple_choice_c',
					'reverse' => 'qa.multiple_choice_c DESC',
				),
			),
			'multiple_choice_d' => array(
				'header' => array(
					'value' => 'D',
				),
				'data' => array(
					'db' => 'multiple_choice_d',
					'class' => 'lefttext',
				),
				'sort' => array(
					'default' => 'qa.multiple_choice_d',
					'reverse' => 'qa.multiple_choice_d DESC',
				),
			),
			'true_false_answer' => array(
				'header' => array(
					'value' => 'True / False',
					'class' => 'centertext',
				),
				'data' => array(
					'db' => 'true_false_answer',
					'class' => 'centertext',
				),
				'sort' => array(
					'default' => 'qa.true_false_answer',
					'reverse' => 'qa.true_false_answer DESC',
				),
			),
			'date_added' => array(
				'header' => array(
					'value' => 'Date Added',
					'class' => 'centertext',
				),
				'data' => array(
					'db' => 'date_added',
					'class' => 'centertext',
				),
				'sort' => array(
					'default' => 'qq.date_added',
					'reverse' => 'qq.date_added DESC',
				),
			),
			'revisions' => array(
				'header' => array(
					'value' => 'Revisions',
					'class' => 'centertext',
				),
				'data' => array(
					'db' => 'revisions',
					'class' => 'centertext',
				),
				'sort' => array(
					'default' => 'qq.revisions',
					'reverse' => 'qq.revisions DESC',
				),
			),
			'source' => array(
				'header' => array(
					'value' => 'Source',
					'class' => 'centertext',
				),
				'data' => array(
					'db' => 'source',
					'class' => 'centertext',
				),
				'sort' => array(
					'default' => 'qq.source',
					'reverse' => 'qq.source DESC',
				),
			),
			'contributor' => array(
				'header' => array(
					'value' => 'Contributor',
					'class' => 'centertext',
				),
				'data' => array(
					'db' => 'contributor',
					'class' => 'centertext',
				),
				'sort' => array(
					'default' => 'full_name',
					'reverse' => 'full_name DESC',
				),
			),
			'actions' => array(
				'header' => array(
					'value' => 'Actions',
					'class' => 'centertext',
				),
				'data' => array(
					'db' => 'actions',
					'class' => 'centertext',
					'style' => 'min-width: 70px;',
				),
			),
		),
	);

	//-- Remove those nasty filters.
	if (isset($_REQUEST['remove_filters']) || (isset($_SESSION['ebola']['last_log_time']) && ($_SESSION['ebola']['last_log_time'] + 60*60) < time()))
	{
		unset($_REQUEST['filter_questions'], $_REQUEST['filter_question_type_1'], $_REQUEST['filter_question_type_2'], $_REQUEST['filter_categories']);
		unset($_REQUEST['filter_include_fields_date_added'], $_REQUEST['filter_include_fields_prompt'], $_REQUEST['filter_include_fields_revisions'], $_REQUEST['filter_include_fields_source'], $_REQUEST['filter_include_fields_contributor']);
		unset($_SESSION['ebola']['user_settings']);
		unset($_SESSION['ebola']['last_log_time']);
	}

	if (!empty($_SESSION['ebola']['user_settings']['filter_questions']) && empty($_REQUEST['filter_questions']))
	{
		$_REQUEST['filter_questions'] = $_SESSION['ebola']['user_settings']['filter_questions'];

		if (!empty($_SESSION['ebola']['user_settings']['filter_question_type_1']) && empty($_REQUEST['filter_question_type_1']))
			$_REQUEST['filter_question_type_1'] = 1;
		if (!empty($_SESSION['ebola']['user_settings']['filter_question_type_2']) && empty($_REQUEST['filter_question_type_2']))
			$_REQUEST['filter_question_type_2'] = 1;

		if (!empty($_SESSION['ebola']['user_settings']['filter_categories']) && empty($_REQUEST['filter_categories']))
			$_REQUEST['filter_categories'] = $_SESSION['ebola']['user_settings']['filter_categories'];

		if (!empty($_SESSION['ebola']['user_settings']['filter_include_fields_date_added']) && empty($_REQUEST['filter_include_fields_date_added']))
			$_REQUEST['filter_include_fields_date_added'] = 1;
		if (!empty($_SESSION['ebola']['user_settings']['filter_include_fields_prompt']) && empty($_REQUEST['filter_include_fields_prompt']))
			$_REQUEST['filter_include_fields_prompt'] = 1;
		if (!empty($_SESSION['ebola']['user_settings']['filter_include_fields_revisions']) && empty($_REQUEST['filter_include_fields_revisions']))
			$_REQUEST['filter_include_fields_revisions'] = 1;
		if (!empty($_SESSION['ebola']['user_settings']['filter_include_fields_source']) && empty($_REQUEST['filter_include_fields_source']))
			$_REQUEST['filter_include_fields_source'] = 1;
		if (!empty($_SESSION['ebola']['user_settings']['filter_include_fields_contributor']) && empty($_REQUEST['filter_include_fields_contributor']))
			$_REQUEST['filter_include_fields_contributor'] = 1;
	}

	if (!empty($_REQUEST['filter_questions']))
	{
		$_SESSION['ebola']['user_settings']['filter_questions'] = 1;
		$_SESSION['ebola']['last_log_time'] = time();

		//-- Filter question types...
		if (empty($_REQUEST['filter_question_type_1']) && !empty($_REQUEST['filter_question_type_2']))
		{
			$_SESSION['ebola']['user_settings']['filter_question_type_2'] = 1;
			$listOptions['get_items']['params'][0]['filter_question_type'] = 2;
			unset($listOptions['columns']['question_type'], $listOptions['columns']['multiple_choice_a'], $listOptions['columns']['multiple_choice_b'], $listOptions['columns']['multiple_choice_c'], $listOptions['columns']['multiple_choice_d']);
		}
		if (empty($_REQUEST['filter_question_type_2']) && !empty($_REQUEST['filter_question_type_1']))
		{
			$_SESSION['ebola']['user_settings']['filter_question_type_1'] = 1;
			$listOptions['get_items']['params'][0]['filter_question_type'] = 1;
			unset($listOptions['columns']['question_type'], $listOptions['columns']['true_false_answer']);
		}

		//-- Filter question categories...
		if (!empty($_REQUEST['filter_categories']))
		{
			$_SESSION['ebola']['user_settings']['filter_categories'] = $_REQUEST['filter_categories'];

			//-- Quick $_GET translation.
			if (!empty($_GET['filter_categories']))
				$_REQUEST['filter_categories'] = explode(',', $_GET['filter_categories']);

			$listOptions['get_items']['params'][0]['filter_categories'] = implode(',', $_REQUEST['filter_categories']);
		}
	}

	//-- Should we include any additional fields?
	if (empty($_REQUEST['filter_include_fields_date_added']))
		unset($listOptions['columns']['date_added']);
	else
		$_SESSION['ebola']['user_settings']['filter_include_fields_date_added'] = 1;
	if (empty($_REQUEST['filter_include_fields_prompt']))
		unset($listOptions['columns']['question_prompt']);
	else
		$_SESSION['ebola']['user_settings']['filter_include_fields_prompt'] = 1;
	if (empty($_REQUEST['filter_include_fields_revisions']))
		unset($listOptions['columns']['revisions']);
	else
		$_SESSION['ebola']['user_settings']['filter_include_fields_revisions'] = 1;
	if (empty($_REQUEST['filter_include_fields_source']))
		unset($listOptions['columns']['source']);
	else
		$_SESSION['ebola']['user_settings']['filter_include_fields_source'] = 1;
	if (empty($_REQUEST['filter_include_fields_contributor']))
		unset($listOptions['columns']['contributor']);
	else
		$_SESSION['ebola']['user_settings']['filter_include_fields_contributor'] = 1;

	//-- Same params, less hassle.
	$listOptions['get_count']['params'] = $listOptions['get_items']['params'];

	$listOptions['no_items_label'] = 'Sorry, but it appears that there are no questions in this quiz.';
	if (allowedTo('ebola_add_modify_questions'))
		$listOptions['no_items_label'] .= ' Click <a href="' . $scripturl . '?action=ebola-tools;area=quizzes;sa=add-question;quiz=' . $quiz_id . '" target="_self">here</a> to add one!';

	$listOptions['base_href'] = $scripturl . '?action=ebola-tools;area=quizzes;sa=view;quiz=' . $quiz_id;

	createList($listOptions);

	$txt['add_question'] = 'Add Question';
    $txt['add_category'] = 'Add Category';
	$txt['export_quiz'] = 'Export Quiz';

	$context['add_question_permission'] = allowedTo('ebola_add_modify_questions') && !empty($this_quiz['categories']);
	$context['add_category_permission'] = allowedTo('ebola_add_modify_categories');
	$context['export_quizzes_permission'] = allowedTo('ebola_export_quizzes') && !empty($this_quiz['total_questions']);

	$context['add_question_buttons'] = array(
		'add-question' => array(
			'text' => 'add_question',
			'url' => $scripturl . '?action=ebola-tools;area=quizzes;sa=add-question;quiz=' . $quiz_id,
			'test' => 'add_question_permission',
		),
		'add-category' => array(
			'text' => 'add_category',
			'url' => $scripturl . '?action=ebola-tools;area=quizzes;sa=add-category;quiz=' . $quiz_id,
			'test' => 'add_category_permission',
		),
		'export' => array(
			'text' => 'export_quiz',
			'url' => $scripturl . '?action=ebola-tools;area=quizzes;sa=export;quiz=' . $quiz_id,
			//'active' => true,
			'test' => 'export_quizzes_permission',
		),
	);
}

function quizzes_addQuestionHandler()
{
	global $context, $scripturl, $txt;
	global $quiz_id, $this_quiz, $smcFunc;

	isAllowedTo('ebola_add_modify_questions');

	$quiz_id = $_REQUEST['quiz'];
	$this_quiz = $context['ebola']['quizzes'][$quiz_id];

	if (empty($this_quiz['categories']))
		redirectexit('action=ebola-tools;area=quizzes;sa=add-category;quiz=' . $quiz_id . ';error=no_categories');

	$context['page_title'] .= 'Add Question | ' . $this_quiz['quiz_name'];

	$context['linktree'][] = array(
		'url' => $scripturl . '?action=ebola-tools;area=quizzes;sa=view;quiz=' . $quiz_id,
		'name' => $this_quiz['quiz_name'],
	);

	$context['linktree'][] = array(
		'url' => $scripturl . '?action=ebola-tools;area=quizzes;sa=add-question;quiz=' . $quiz_id,
		'name' => 'Add Question',
	);

	$context['sub_template'] = 'add_quiz_question';

	if (isset($_POST['add-question']))
	{
		/*
		 * echo '<pre>', print_r($_POST), '</pre>';
		 * Print_R Dump:
		    [question_category] => 1
			[question_text] => Test
			[question_type] => 1
			[question_source] => Test
			[multiple_choice_a] => Test
			[multiple_choice_b] => Test
			[multiple_choice_c] => Test
			[multiple_choice_answer] => 4
			[multiple_choice_d] => Testtt
			[add-question] => 1
		 */
		$context['form_errors'] = array();

		if (!empty($_POST['question_category']))
			$_POST['question_category'] = (int) $_POST['question_category'];
		if (!empty($_POST['question_text']))
			$_POST['question_text'] = $smcFunc['htmlspecialchars']($_POST['question_text'], ENT_QUOTES);
		if (!empty($_POST['question_prompt']))
			$_POST['question_prompt'] = $smcFunc['htmlspecialchars']($_POST['question_prompt'], ENT_QUOTES);
		if (!empty($_POST['question_type']))
			$_POST['question_type'] = (int) $_POST['question_type'];
		if (!empty($_POST['question_source']))
			$_POST['question_source'] = $smcFunc['htmlspecialchars']($_POST['question_source'], ENT_QUOTES);

		if (empty($_POST['question_category']) || !array_key_exists($_POST['question_category'], $this_quiz['categories']))
			$context['form_errors']['invalid_category'] = 'Select a valid destination category for the question.';
		if (empty($_POST['question_text']))
			$context['form_errors']['empty_text'] = 'Enter text for the question.';
		if (empty($_POST['question_type']))
			$context['form_errors']['empty_question_type'] = 'Select a type of question.';
		if (!empty($_POST['question_type']) && !in_array($_POST['question_type'], array(1,2)))
			$context['form_errors']['invalid_question_type'] = 'Select a valid question type.';
		if (empty($_POST['question_source']))
			$context['form_errors']['question_source'] = 'Provide a source for the question and answer content.';

		//-- Multiple Choice
		if (!empty($_POST['question_type']) && $_POST['question_type'] == 1)
		{
			$context['mc_translation'] = array(1 => 'a', 'b', 'c', 'd');

			foreach ($context['mc_translation'] as $key => $value)
			{
				if (!empty($_POST['multiple_choice_' . $value]))
					$_POST['multiple_choice_' . $value] = $smcFunc['htmlspecialchars']($_POST['multiple_choice_' . $value], ENT_QUOTES);
			}

			if (!empty($_POST['multiple_choice_answer']))
				$_POST['multiple_choice_answer'] = (int) $_POST['multiple_choice_answer'];

			if (empty($_POST['multiple_choice_answer']))
				$context['form_errors']['multiple_choice_answer'] = 'You must select one multiple choice response as the question answer.';
			if (!empty($_POST['multiple_choice_answer']) && empty($_POST['multiple_choice_' . $context['mc_translation'][$_POST['multiple_choice_answer']]]))
				$context['form_errors']['empty_multiple_choice_correct'] = 'You must have text for the correct answer that was picked.';

			$empty_mc = 0;
			if (empty($_POST['multiple_choice_a']))
				$empty_mc += 1;
			if (empty($_POST['multiple_choice_b']))
				$empty_mc += 1;
			if (empty($_POST['multiple_choice_c']))
				$empty_mc += 1;
			if (empty($_POST['multiple_choice_d']))
				$empty_mc += 1;

			if ($empty_mc > 2)
				$context['form_errors']['too_few_mc'] = 'You must have at least two multiple choice answers entered.';

		}

		//-- True / False
		if (!empty($_POST['question_type']) && $_POST['question_type'] == 2)
		{
			if (!empty($_POST['true_false_answer']))
				$_POST['true_false_answer'] = (int) $_POST['true_false_answer'];

			if (empty($_POST['true_false_answer']))
				$context['form_errors']['no_true_false_answer'] = 'Select true or false for the question answer.';
		}

		if (empty($context['form_errors']))
			quizzes_addQuizQuestion($quiz_id, $this_quiz);
	}
}

function quizzes_addCategoryHandler()
{
	global $context, $scripturl, $txt;
	global $quiz_id, $this_quiz, $smcFunc;

	isAllowedTo('ebola_add_modify_categories');

	$quiz_id = $_REQUEST['quiz'];
	$this_quiz = $context['ebola']['quizzes'][$quiz_id];

	$context['page_title'] .= 'Add Category | ' . $this_quiz['quiz_name'];

	$context['linktree'][] = array(
		'url' => $scripturl . '?action=ebola-tools;area=quizzes;sa=view;quiz=' . $quiz_id,
		'name' => $this_quiz['quiz_name'],
	);

	$context['linktree'][] = array(
		'url' => $scripturl . '?action=ebola-tools;area=quizzes;sa=add-category;quiz=' . $quiz_id,
		'name' => 'Add Category',
	);

	$context['sub_template'] = 'add_quiz_category';

	if (isset($_POST['add-category']))
	{
		$context['form_errors'] = array();

		/*
		 *
		 * Dump (strips all non-alphanumeric characters and forces lowercase)
		 * Array ( [1] => symptoms [2] => prevention [3] => containment ) 1
		 */
		$category_names = quizzes_getCategoriesByNames($quiz_id);
		if (!empty($_POST['category_name']))
			$_POST['category_name'] = $smcFunc['htmlspecialchars']($_POST['category_name'], ENT_QUOTES);

		if (!empty($_POST['category_name']))
		{
			$stripped_category_name = $smcFunc['strtolower'](preg_replace('/[^a-zA-Z0-9]/', '', $_POST['category_name']));
			if (in_array($stripped_category_name, $category_names))
				$context['form_errors']['duplicate_category'] = 'A category already exists with the name: ' . $_POST['category_name'] . ' (' . $stripped_category_name . ').';
		}

		if (empty($_POST['category_name']))
			$context['form_errors']['empty_category_name'] = 'Enter a name for the category.';

		if (empty($context['form_errors']))
			quizzes_addQuizCategory($quiz_id, $this_quiz);
	}
}

function quizzes_exportQuizHandler()
{
	global $context, $scripturl, $txt;
	global $quiz_id, $this_quiz, $smcFunc;

	isAllowedTo('ebola_export_quizzes');

	$quiz_id = $_REQUEST['quiz'];
	$this_quiz = $context['ebola']['quizzes'][$quiz_id];

	if (empty($this_quiz['categories']))
		redirectexit('action=ebola-tools;area=quizzes;sa=add-category;quiz=' . $quiz_id . ';error=no_categories');

	$context['page_title'] .= 'Export Quiz | ' . $this_quiz['quiz_name'];

	$context['linktree'][] = array(
		'url' => $scripturl . '?action=ebola-tools;area=quizzes;sa=view;quiz=' . $quiz_id,
		'name' => $this_quiz['quiz_name'],
	);

	$context['linktree'][] = array(
		'url' => $scripturl . '?action=ebola-tools;area=quizzes;sa=export;quiz=' . $quiz_id,
		'name' => 'Export Quiz',
	);


	$context['sub_template'] = 'export_quiz';

	if (isset($_REQUEST['exporting']))
	{
		$context['form_errors'] = array();

		if (!empty($_POST['total_questions']))
			$_POST['total_questions'] = (int) $_POST['total_questions'];

		if (empty($_POST['total_questions']))
			$context['form_errors']['empty_total_questions'] = 'Enter a number of total questions to be exported.';
		else if (!empty($_POST['total_questions']) && $_POST['total_questions'] > $this_quiz['total_questions'])
			$_POST['total_questions'] = $this_quiz['total_questions'];

		if (empty($_POST['source_type']) || !in_array($_POST['source_type'], array('random', 'manual')))
			$context['form_errors']['empty_source_type'] = 'Select a source type for the questions to be exported from.';

		if (!empty($_POST['source_type']) && $_POST['source_type'] == 'manual')
		{
			$total_allocated = 0;
			foreach ($this_quiz['categories'] as $key => $value)
			{
				if (!empty($_POST['category_' . $key . '_questions']))
					$_POST['category_'  . $key . '_questions'] = (int) $_POST['category_' . $key . '_questions'];

				if (!empty($_POST['category_' . $key . '_questions']) && $_POST['category_' . $key . '_questions'] > $value['total_questions'])
					$_POST['category_' . $key . '_questions'] = $value['total_questions'];

				if (!empty($_POST['category_' . $key . '_questions']))
					$total_allocated += $_POST['category_' . $key . '_questions'];
			}

			if (empty($total_allocated))
				$context['form_errors']['allocate_questions'] = 'You must allocate some questions to export.';

			if ($total_allocated > $this_quiz['total_questions'])
				$context['form_errors']['more_than_max'] = 'Review your category allocations. It appears you allocated more than the total allocatable.';
		}

		if (empty($context['form_errors']))
		{
			$context['sub_template'] = 'export_quiz_output';

			$questions = quizzes_getAllQuestionIds();
			$context['export_questions']['ids'] = array();
			if ($_POST['source_type'] == 'random')
			{
				for ($i = 0; $i < $_POST['total_questions']; ++$i)
				{
					$random_key = array_rand($questions['all']);
					$context['export_questions']['ids'][] = $questions['all'][$random_key];
					unset($questions['all'][$random_key]);
					unset($random_key);
				}
			}
			else
			{
				foreach ($this_quiz['categories'] as $key => $value)
				{
					if (!empty($_POST['category_' . $key . '_questions']))
					{
						for ($i = 0; $i < $_POST['category_' . $key . '_questions']; ++$i)
						{
							$random_key = array_rand($questions['separated'][$key]);
							$context['export_questions']['ids'][] = $questions['separated'][$key][$random_key];
							unset($questions['separated'][$key][$random_key]);
							unset($random_key);
						}
					}
				}
			}

			$context['export_questions']['raw_boeing_format'] = '';
			$context['export_questions']['detailed'] = quizzes_retrieveRowsFromIds($context['export_questions']['ids']);
			$question_count = 1;
			$mc_translation = array(1 => 'A', 'B', 'C', 'D');
			foreach ($context['export_questions']['detailed']['questions'] as $key => $value)
			{
				if ($question_count > 1)
					$context['export_questions']['raw_boeing_format'] .= "\r\n\n\n";

				$number_length = strlen($question_count);

				if ($number_length == 1)
					$question_number = '00' . $question_count;
				else if ($number_length == 2)
					$question_number = '0' . $question_count;
				else if ($number_length == 3)
					$question_number = $question_count;

				$question_number = 'q' . $question_number . '.';

				$context['export_questions']['raw_boeing_format'] .= $question_number . 'Type=Split';
				$context['export_questions']['raw_boeing_format'] .= "\n\n" . $question_number . 'QType=Text';
				$context['export_questions']['raw_boeing_format'] .= "\n\n" . $question_number . 'AType=Multi';
				$context['export_questions']['raw_boeing_format'] .= "\n\n" . $question_number . 'Text=' . $value['question']['text'];
				$context['export_questions']['raw_boeing_format'] .= "\n\n" . $question_number . 'Prompt=' . (!empty($value['question']['prompt']) ? $value['question']['prompt'] : 'Select the best question for the answer above:');

				if ($value['question']['type'] == 1)
				{
					$context['export_questions']['raw_boeing_format'] .= "\n\n" . $question_number . 'A=' . (!empty($value['question_answer']['multiple_choice_a']) ? $value['question_answer']['multiple_choice_a'] : 'NULL');
					$context['export_questions']['raw_boeing_format'] .= "\n\n" . $question_number . 'B=' . (!empty($value['question_answer']['multiple_choice_a']) ? $value['question_answer']['multiple_choice_a'] : 'NULL');
					$context['export_questions']['raw_boeing_format'] .= "\n\n" . $question_number . 'C=' . (!empty($value['question_answer']['multiple_choice_a']) ? $value['question_answer']['multiple_choice_a'] : 'NULL');
					$context['export_questions']['raw_boeing_format'] .= "\n\n" . $question_number . 'D=' . (!empty($value['question_answer']['multiple_choice_a']) ? $value['question_answer']['multiple_choice_a'] : 'NULL');
					$context['export_questions']['raw_boeing_format'] .= "\n\n" . $question_number . 'Correct=' . $mc_translation[$value['question_answer']['multiple_choice_correct']];
				}
				else if ($value['question']['type'] == 2)
				{
					$context['export_questions']['raw_boeing_format'] .= "\n\n" . $question_number . 'A=True';
					$context['export_questions']['raw_boeing_format'] .= "\n\n" . $question_number . 'B=False';
					$context['export_questions']['raw_boeing_format'] .= "\n\n" . $question_number . 'C=NULL';
					$context['export_questions']['raw_boeing_format'] .= "\n\n" . $question_number . 'D=NULL';
					$context['export_questions']['raw_boeing_format'] .= "\n\n" . $question_number . 'Correct=' . ($value['question_answer']['true_false_answer'] == 1 ? 'A' : 'B');
				}

				/*
				 * q002.Type=Split
				 *
				 *	q002.QType=Text
				 *
				 *	q002.AType=Multi
				 *
				 *	q002.Text=The first contractor for the International Space Station, beginning in August 1993.
				 *
				 *	q002.Prompt=Select the best question for the answer above:
				 *
				 *	q002.A=What is The Boeing Company?
				 *
				 *	q002.B=What is McDonnell Douglas Corporation?
				 *
				 *	q002.C=What is European Aeronautic Defence and Space Company?
				 *
				 *	q002.D=What is Lockheed Martin?
				 *
				 *	q002.Correct=A
				 */

				++$question_count;
			}
		}
	}
}