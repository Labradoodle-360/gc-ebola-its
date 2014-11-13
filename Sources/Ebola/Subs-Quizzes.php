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

function quizzes_retrieveQuizzes()
{
	global $smcFunc;

	//-- Populate the actual quizzes.
	$quizzes = array();
	$request = $smcFunc['db_query']('', '
		SELECT itq.id, itq.quiz_name, itq.total_questions
		FROM {db_prefix}it_quizzes AS itq'
	);
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$quizzes[$row['id']] = array(
			'quiz_name' => $row['quiz_name'],
			'total_questions' => $row['total_questions'],
			'total_categories' => 0,
			'categories' => array(),
		);
	}
	$smcFunc['db_free_result']($request);

	//-- Now fetch the quiz categories.
	$request = $smcFunc['db_query']('', '
		SELECT itc.id, itc.quizzes_id, itc.category_name, itc.total_questions
		FROM {db_prefix}it_quizzes_categories AS itc'
	);
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$quizzes[$row['quizzes_id']]['total_categories'] += 1;

		$quizzes[$row['quizzes_id']]['categories'][$row['id']] = array(
			'category_name' => $row['category_name'],
			'total_questions' => !empty($row['total_questions']) ? $row['total_questions'] : 0,
		);
	}
	$smcFunc['db_free_result']($request);

	return $quizzes;
}

function quizzes_listQuizzes($start, $items_per_page, $sort)
{
	global $context, $settings, $scripturl, $smcFunc;
	$request = $smcFunc['db_query']('', '
		SELECT itq.id, itq.quiz_name, itq.total_questions
		FROM {db_prefix}it_quizzes AS itq
		ORDER BY ' . $sort . '
		LIMIT ' . $start . ', ' . $items_per_page
	);
	$response = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$actions = '';

		$categories = array();
		$sub_request = $smcFunc['db_query']('', '
			SELECT id, category_name, total_questions
			FROM {db_prefix}it_quizzes_categories
			WHERE quizzes_id = {int:this_id}
			ORDER BY category_name ASC',
			array(
				'this_id' => $row['id'],
			)
		);
		while ($sub_row = $smcFunc['db_fetch_assoc']($sub_request))
		{
			$categories[$sub_row['id']] = $sub_row['category_name'] . ' (' . $sub_row['total_questions'] . ')';
		}
		$smcFunc['db_free_result']($sub_request);

		$response[$row['id']] = array(
			'quiz_name' => '<a href="' . $scripturl . '?action=ebola-tools;area=quizzes;sa=view;quiz=' . $row['id'] . '">' . $row['quiz_name'] . '</a>',
			'categories' => !empty($categories) ? implode(', ', $categories) : '<em>None</em>',
			'total_questions' => !empty($row['total_questions']) ? $row['total_questions'] : 0,
			'actions' => $actions . '
				<a href="' . $scripturl . '?action=ebola-tools;area=quizzes;sa=modify;quiz=' . $row['id'] . '" class="action_icon modify_quiz">
					<img src="' . $settings['default_theme_url'] . '/images/Ebola/pencil.png" alt="Modify" title="Modify" />
				</a>
			',
		);
	}
	$smcFunc['db_free_result']($request);
	return $response;
}

function quizzes_countQuizzes()
{
	global $smcFunc;
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(id) AS total_quizzes
		FROM {db_prefix}it_quizzes'
	);
	list($count) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);
	return $count;
}

function quizzes_listSpecificQuiz($start, $items_per_page, $sort, $params)
{
	global $context, $settings, $scripturl, $smcFunc;

	$where_clause = '';
	if (!empty($params['filter_question_type']))
		$where_clause .= ' AND qq.question_type = {int:question_type}';
	if (!empty($params['filter_categories']))
		$where_clause .= ' AND qq.quizzes_categories_id IN({array_int:filter_categories})';

	$request = $smcFunc['db_query']('', '
		SELECT
			qq.id, qq.quizzes_id, qq.quizzes_categories_id, qq.question_type, qq.question_text, qq.question_prompt, qq.source, qq.revisions, qq.date_added, qq.id_member,
			qc.category_name,
			qa.multiple_choice_a, qa.multiple_choice_b, qa.multiple_choice_c, qa.multiple_choice_d, qa.multiple_choice_correct, qa.true_false_answer,
			cf1.value AS student_id,
			cf2.value AS full_name
		FROM {db_prefix}it_questions AS qq
			LEFT JOIN {db_prefix}it_quizzes_categories AS qc ON (qq.quizzes_categories_id = qc.id AND qq.quizzes_id = qc.quizzes_id)
			LEFT JOIN {db_prefix}it_questions_answers AS qa ON (qq.id = qa.questions_id)
			LEFT JOIN {db_prefix}themes AS cf1 ON (qq.id_member = cf1.id_member AND cf1.variable = {string:gc_student_id})
			LEFT JOIN {db_prefix}themes AS cf2 ON (qq.id_member = cf2.id_member AND cf2.variable = {string:full_name})
		WHERE qq.quizzes_id = {int:quiz_id}' . $where_clause . '
		ORDER BY ' . $sort . '
		LIMIT ' . $start . ', ' . $items_per_page,
		array(
			'quiz_id' => $params['quiz_id'],
			'question_type' => !empty($params['filter_question_type']) ? $params['filter_question_type'] : 0,
			'filter_categories' => !empty($params['filter_categories']) ? explode(',', $params['filter_categories']) : 0,
			'gc_student_id' => 'cust_greenv',
			'full_name' => 'cust_firstn'
		)
	);
	$response = array();
	$question_types = array(1 => 'Multiple Choice', 'True / False');
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$actions = '';

		$multiple_choice[1] = '<img src="' . $settings['default_images_url'] . '/Ebola/cross.png" class="table_icon" />';
		$multiple_choice[2] = '<img src="' . $settings['default_images_url'] . '/Ebola/cross.png" class="table_icon" />';
		$multiple_choice[3] = '<img src="' . $settings['default_images_url'] . '/Ebola/cross.png" class="table_icon" />';
		$multiple_choice[4] = '<img src="' . $settings['default_images_url'] . '/Ebola/cross.png" class="table_icon" />';

		$true_false_answer = 'cross';

		$mc_translation_table = array(1 => 'a', 'b', 'c', 'd');
		if ($row['question_type'] == 1 && !empty($row['multiple_choice_correct']))
		{
			foreach ($multiple_choice as $key => $value)
			{
				$mc_text = $row['multiple_choice_' . $mc_translation_table[$key]];
				$multiple_choice[$key] = $row['multiple_choice_correct'] == $key ? '<strong>' . $mc_text . '</strong>' : $mc_text;
			}
		}
		else if ($row['question_type'] == 2 && !empty($row['true_false_answer']))
			$true_false_answer = $row['true_false_answer'] == 1 ? 'thumb-up' : 'thumb';

		$response[$row['id']] = array(
			'quiz_id' => $row['quizzes_id'],
			'quizzes_categories_id' => $row['quizzes_categories_id'],
			'question_type' => $question_types[$row['question_type']],
			'question_text' => $row['question_text'],
			'question_prompt' => !empty($row['question_prompt']) ? $row['question_prompt'] : '<em>Select the best question for the answer above:</em>',
			'source' => $row['source'],
			'revisions' => comma_format($row['revisions']),
			'date_added' => timeformat($row['date_added'], true),
			'id_member' => $row['id_member'],
			'category_name' => $row['category_name'],
			'multiple_choice_a' => $multiple_choice[1],
			'multiple_choice_b' => $multiple_choice[2],
			'multiple_choice_c' => $multiple_choice[3],
			'multiple_choice_d' => $multiple_choice[4],
			'true_false_answer' => '<img src="' . $settings['default_images_url'] . '/Ebola/' . $true_false_answer . '.png" class="table_icon" />',
			'student_id' => $row['student_id'],
			'full_name' => $row['full_name'],
			'contributor' => $row['student_id'] . '<br />' . $row['full_name'],
			'actions' => $actions . '
				<a href="' . $scripturl . '?action=ebola-tools;area=quizzes;sa=modify;quiz=' . $row['id'] . '" class="table_icon action_icon modify_quiz">
					<img src="' . $settings['default_theme_url'] . '/images/Ebola/pencil.png" alt="Modify" title="Modify" />
				</a>
			',
		);
	}
	$smcFunc['db_free_result']($request);
	return $response;
}

function quizzes_countSpecificQuiz($params)
{
	global $smcFunc;

	$where_clause = '';
	if (!empty($params['filter_question_type']))
		$where_clause .= ' AND question_type = {int:question_type}';
	if (!empty($params['filter_categories']))
		$where_clause .= ' AND quizzes_categories_id IN({array_int:filter_categories})';

	$request = $smcFunc['db_query']('', '
		SELECT COUNT(id) AS total_questions
		FROM {db_prefix}it_questions
		WHERE quizzes_id = {int:quiz_id}' . $where_clause,
		array(
			'quiz_id' => $params['quiz_id'],
			'question_type' => !empty($params['filter_question_type']) ? $params['filter_question_type'] : 0,
			'filter_categories' => !empty($params['filter_categories']) ? explode(',', $params['filter_categories']) : 0,
		)
	);
	list($count) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);
	return $count;
}

function quizzes_getAllQuestionIds()
{
	global $smcFunc;
	$request = $smcFunc['db_query']('', '
		SELECT id, quizzes_categories_id
		FROM {db_prefix}it_questions'
	);
	$response = array(
		'all' => array(),
		'separated' => array()
	);
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$response['all'][] = $row['id'];
		$response['separated'][$row['quizzes_categories_id']][] = $row['id'];
	}
	return $response;
}

function quizzes_addQuizQuestion($quiz_id, $this_quiz)
{
	global $smcFunc, $user_info, $context;

	$context['mc_translation'] = array(1 => 'a', 'b', 'c', 'd');
	foreach ($context['mc_translation'] as $key => $value)
	{
		if (empty($_POST['multiple_choice_' . $value]))
			$_POST['multiple_choice_' . $value] = '';
	}

	if (empty($_POST['multiple_choice_answer']))
		$_POST['multiple_choice_answer'] = 0;

	if (empty($_POST['true_false_answer']))
		$_POST['true_false_answer'] = 0;

	$smcFunc['db_insert']('insert',
		'{db_prefix}it_questions',
		array(
			'quizzes_id' => 'int',
			'quizzes_categories_id' => 'int',
			'question_type' => 'int',
			'question_text' => 'string',
			'question_prompt' => 'string',
			'source' => 'string',
			'revisions' => 'int',
			'date_added' => 'int',
			'id_member' => 'int',
		),
		array(
			$quiz_id,
			$_POST['question_category'],
			$_POST['question_type'],
			$_POST['question_text'],
			$_POST['question_prompt'],
			$_POST['question_source'],
			0,
			time(),
			$user_info['id'],
		),
		array('id')
	);

	$questions_id = $smcFunc['db_insert_id']('{db_prefix}it_questions', 'questions_id');

	$smcFunc['db_insert']('insert',
		'{db_prefix}it_questions_answers',
		array(
			'questions_id' => 'int',
			'multiple_choice_a' => 'string',
			'multiple_choice_b' => 'string',
			'multiple_choice_c' => 'string',
			'multiple_choice_d' => 'string',
			'multiple_choice_correct' => 'int',
			'true_false_answer' => 'int',
		),
		array(
			$questions_id,
			$_POST['multiple_choice_a'],
			$_POST['multiple_choice_b'],
			$_POST['multiple_choice_c'],
			$_POST['multiple_choice_d'],
			$_POST['multiple_choice_answer'],
			$_POST['true_false_answer'],
		),
		array('questions_id')
	);

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}it_quizzes_categories
		SET total_questions = total_questions + 1
		WHERE id = {int:category_id} AND quizzes_id = {int:quiz_id}',
		array(
			'category_id' => $_POST['question_category'],
			'quiz_id' => $quiz_id,
		)
	);

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}it_quizzes
		SET total_questions = total_questions + 1
		WHERE id = {int:quizzes_id}',
		array(
			'quizzes_id' => $quiz_id,
		)
	);

	redirectexit('action=ebola-tools;area=quizzes;sa=view;quiz=' . $quiz_id . ';question_added=' . $questions_id);

}

function quizzes_addQuizCategory($quiz_id, $this_quiz)
{
	global $smcFunc, $user_info, $context;

	$smcFunc['db_insert']('insert',
		'{db_prefix}it_quizzes_categories',
		array(
			'quizzes_id' => 'int',
			'category_name' => 'string',
			'total_questions' => 'int',
			'date_added' => 'int',
			'id_member' => 'int',
		),
		array(
			$quiz_id,
			$_POST['category_name'],
			0,
			time(),
			$user_info['id'],
		),
		array('id')
	);

	$category_id = $smcFunc['db_insert_id']('{db_prefix}it_quizzes_categories', 'id');

	redirectexit('action=ebola-tools;area=quizzes;sa=view;quiz=' . $quiz_id . ';category_added=' . $category_id);

}

function quizzes_retrieveRowsFromIds($question_ids)
{
	global $smcFunc;
	$request = $smcFunc['db_query']('', '
		SELECT
			qq.id, qq.quizzes_id, qq.quizzes_categories_id,
			qq.question_type, qq.question_text, qq.question_prompt, qq.revisions, qq.date_added, qq.id_member,
			itq.quiz_name, itq.total_questions AS quiz_total_questions,
			qc.category_name, qc.total_questions AS category_total_questions,
			qa.multiple_choice_a, qa.multiple_choice_b, qa.multiple_choice_c, qa.multiple_choice_d,
			qa.multiple_choice_correct, qa.true_false_answer,
			cf1.value AS student_id,
			cf2.value AS full_name
		FROM {db_prefix}it_questions AS qq
			LEFT JOIN {db_prefix}it_quizzes AS itq ON (qq.quizzes_id = itq.id)
			LEFT JOIN {db_prefix}it_quizzes_categories AS qc ON (qq.quizzes_categories_id = qc.id AND qq.quizzes_id = qc.quizzes_id)
			LEFT JOIN {db_prefix}it_questions_answers AS qa ON (qq.id = qa.questions_id)
			LEFT JOIN {db_prefix}themes AS cf1 ON (qq.id_member = cf1.id_member AND cf1.variable = {string:gc_student_id})
			LEFT JOIN {db_prefix}themes AS cf2 ON (qq.id_member = cf2.id_member AND cf2.variable = {string:full_name})
		WHERE qq.id IN({array_int:question_ids})',
		array(
			'question_ids' => $question_ids,
			'gc_student_id' => 'cust_greenv',
			'full_name' => 'cust_firstn'
		)
	);
	$response = array(
		'questions' => array(),
		'categories' => array(),
		'total_questions' => 0,
		'contributors' => array(),
	);
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$response['total_questions'] += 1;

		if (!empty($response['categories'][$row['quizzes_categories_id']]))
			$response['categories'][$row['quizzes_categories_id']] += 1;
		else
			$response['categories'][$row['quizzes_categories_id']] = 1;

		$response['questions'][$row['id']] = array(
			'quiz' => array(
				'id' => $row['quizzes_id'],
				'total_questions' => $row['quiz_total_questions'],
			),
			'category' => array(
				'id' => $row['quizzes_categories_id'],
				'name' => $row['category_name'],
				'total_questions' => $row['category_total_questions']
			),
			'question' => array(
				'id' => $row['id'],
				'type' => $row['question_type'],
				'text' => $row['question_text'],
				'prompt' => $row['question_prompt'],
				'revisions' => comma_format($row['revisions']),
				'date_added' => timeformat($row['date_added'], true),
				'contributor' => array(
					'id_member' => $row['id_member'],
					'student_id' => $row['student_id'],
					'full_name' => $row['full_name'],
				),
			),
			'question_answer' => array(
				'multiple_choice_a' => $row['multiple_choice_a'],
				'multiple_choice_b' => $row['multiple_choice_b'],
				'multiple_choice_c' => $row['multiple_choice_c'],
				'multiple_choice_d' => $row['multiple_choice_d'],
				'multiple_choice_correct' => $row['multiple_choice_correct'],
				'true_false_answer' => $row['true_false_answer'],
			),
		);
	}
	return $response;
}

function quizzes_getCategoriesByNames($quiz_id)
{
	global $smcFunc;
	$request = $smcFunc['db_query']('', '
		SELECT id, quizzes_id, category_name
		FROM {db_prefix}it_quizzes_categories
		WHERE quizzes_id = {int:this_quiz}',
		array(
			'this_quiz' => $quiz_id
		)
	);
	$response = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$response[$row['id']] = $smcFunc['strtolower'](preg_replace('/[^a-zA-Z0-9]/', '', $row['category_name']));
	}
	return $response;
}