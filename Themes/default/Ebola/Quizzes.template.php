<?php

/*
 * Ebola Effort
 *
 * Author: Matthew P. Kerle
 * Date Created: October 9th, 2014
 * From: Greenville College
 */

function template_view_quizzes()
{
	global $context;
	echo '
		<div id="quizzes" style="margin-top: 8px;">
			<div class="cat_bar"', !empty($context['add_quiz_permission']) ? ' style="float: left; width: 90%;"' : '', '>
				<h3 class="catbg">
					Quizzes
				</h3>
			</div>
			', template_button_strip($context['add_quiz_buttons'], 'right'), '
			', !empty($context['add_quiz_permissions']) ? '<br class="clear" />' : '', '
			', template_show_list('quizzes'), '
		</div>
	';
}

function template_view_specific_quiz()
{
	global $scripturl, $context;
	$quiz_id = $_REQUEST['quiz'];
	$this_quiz = $context['ebola']['quizzes'][$_REQUEST['quiz']];

	echo '
		<style type="text/css">
			.table_icon {
				margin-top: 5px;
			}
			.buttonlist {
				margin-top: 1px !important;
			}
		</style>
        <script type="text/javascript">
            $(document).ready(function() {
                $("#filter_questions_toggle").on("click", function() {
                    if ($(this).hasClass("toggle_up")) {
                        $("#filter_questions").slideUp("slow");
                        $(this).removeClass("toggle_up").addClass("toggle_down");
                    } else {
                        $("#filter_questions").slideDown("slow");
                        $(this).removeClass("toggle_down").addClass("toggle_up");
                    }
                });
               // $("#button_strip_add-question, #button_strip_add-category").removeClass("active");
            });
        </script>
		<div id="quiz_categories" style="margin-top: 8px;">
			<div class="cat_bar" style="float: left; width: 80%;">
				<h3 class="catbg">
					', $context['ebola']['quizzes'][$_REQUEST['quiz']]['quiz_name'], ' Quiz
				</h3>
			</div>
			', template_button_strip($context['add_question_buttons'], 'right'), '
			<br class="clear" />
			<div class="title_bar">
			    <h3 class="titlebg">
			        Filter Questions
			        <span id="filter_questions_toggle" class="floatright toggle_up" title="Hide Filters" style="cursor: pointer; margin-top: 3px;"></span>
                </h3>
			</div>
			<div id="filter_questions" class="information">
				<form action="', $scripturl, '?action=ebola-tools;area=quizzes;sa=view;quiz=', $quiz_id, !empty($_REQUEST['sort']) ? ';sort=' . $_REQUEST['sort'] : '', isset($_REQUEST['desc']) ? ';desc' : '', '" method="post">
					<em>If no filters are applied, all categories will be shown with all question types.</em>
					<br /><hr />
					<strong>Categories:</strong>';
					if (!empty($this_quiz['categories']))
					{
						foreach ($this_quiz['categories'] as $key => $value)
						{
							echo '
					<input type="checkbox" id="filter_category_', $key, '" name="filter_categories[]" value="', $key, '"', !empty($_REQUEST['filter_categories']) && in_array($key, $_REQUEST['filter_categories']) ? ' checked="checked"' : '', ' style="margin-top: 1px;" /> <label for="filter_category_', $key, '">', $value['category_name'], ' (', $value['total_questions'], ')</label>';
						}
					}
					else
					{
						echo '
					<em>No Categories Available</em>';
					}
					echo '
					<br />
					<strong>Question Types:</strong>
					<input type="checkbox" id="filter_question_type_1" name="filter_question_type_1" value="1"', !empty($_REQUEST['filter_question_type_1']) ? ' checked="checked"' : '', ' style="margin-top: 1px;" /> <label for="filter_question_type_1">Multiple Choice</label>
					<input type="checkbox" id="filter_question_type_2" name="filter_question_type_2" value="1"', !empty($_REQUEST['filter_question_type_2']) ? ' checked="checked"' : '', ' style="margin-top: 1px;" /> <label for="filter_question_type_2">True / False</label>
					<br /><hr />
					<strong>Show Additional Fields:</strong>
					<input type="checkbox" id="filter_include_fields_date_added" name="filter_include_fields_date_added" value="1"', !empty($_REQUEST['filter_include_fields_date_added']) ? ' checked="checked"' : '', ' style="margin-top: 1px;" /> <label for="filter_include_fields_date_added">Date Added</label>
					<input type="checkbox" id="filter_include_fields_prompt" name="filter_include_fields_prompt" value="1"', !empty($_REQUEST['filter_include_fields_prompt']) ? ' checked="checked"' : '', ' style="margin-top: 1px;" /> <label for="filter_include_fields_prompt">Prompt</label>
					<input type="checkbox" id="filter_include_fields_revisions" name="filter_include_fields_revisions" value="1"', !empty($_REQUEST['filter_include_fields_revisions']) ? ' checked="checked"' : '', ' style="margin-top: 1px;" /> <label for="filter_include_fields_revisions">Revisions</label>
					<input type="checkbox" id="filter_include_fields_source" name="filter_include_fields_source" value="1"', !empty($_REQUEST['filter_include_fields_source']) ? ' checked="checked"' : '', ' style="margin-top: 1px;" /> <label for="filter_include_fields_source">Source</label>
					<input type="checkbox" id="filter_include_fields_contributor" name="filter_include_fields_contributor" value="1"', !empty($_REQUEST['filter_include_fields_contributor']) ? ' checked="checked"' : '', ' style="margin-top: 1px;" /> <label for="filter_include_fields_contributor">Contributor</label>
					 <br /><br />
					 <input type="hidden" id="filter_questions" name="filter_questions" value="1" />
					 <input type="submit" value="Apply Filters" class="button_submit" style="margin: 0; float: left; margin-right: 8px;" />
					 <input type="submit" value="Remove Filters" id="remove_filters" name="remove_filters" class="button_submit" style="margin: 0; float: left;" />
			    </form>
			</div>
			<br />
			', template_show_list('specific_quiz'), '
		</div>
	';
}

function template_add_quiz_question()
{
	global $scripturl, $txt, $settings, $context;
	global $quiz_id, $this_quiz;

	echo '
	<style type="text/css">
		#question_text {
			width: 60%;
			height: 100px;
		}
		#question_source, #question_prompt {
			width: 60%;
		}
		/* Multiple Choice CSS */
		#multiple_choice_options .multiple_choice_letter {
			width: 2%;
			padding-right: 4px;
			text-align: right;
			font-size: 16pt;
			color: rgb(102, 103, 107);
			margin-top: 14px;
			font-family: \'Times New Roman\'
		}
		#multiple_choice_options .multiple_choice_letter > label > span {
			text-transform: uppercase;
			font-weight: normal;
		}
		#multiple_choice_options .multiple_choice_answer {
			width: 96%;
			margin-top: 4px;
			margin-left: 12px;
			margin-bottom: 6px;
			padding-bottom: 6px;
			border-bottom: 1px dotted #a6a6a6;
		}
		#multiple_choice_options .multiple_choice_answer > input[type=text] {
			width: 45%;
		}
	</style>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#question_text").on("keyup", function() {
				var left = 255 - $(this).val().length;
				if (left < 0) {
					left = 0;
				}
				$("#text_chars_left").text(left);
			});
			$("#question_type").on("change", function() {
				if ($(this).val() == 1) {
					$("#true_false_answer").slideUp("slow", function() {
						$("#mc_answer").slideDown("slow");
					});
				} else if ($(this).val() == 2) {
					$("#mc_answer").slideUp("slow", function() {
						$("#true_false_answer").slideDown("slow");
					});
				} else {
					$("#mc_answer").slideUp("slow");
					$("#true_false_answer").slideUp("slow");
				}
			});
		});
	</script>
	<div class="cat_bar" style="margin-top: 8px;">
		<h3 class="catbg">Add Question</h3>
	</div>';
	if (!empty($context['form_errors']))
	{
		echo '
	<div class="error">
		<strong>The following error', count($context['form_errors']) > 1 ? 's were' : ' was', ' encountered:</strong>
 		<ul>';
		foreach ($context['form_errors'] as $key => $value)
		{
			echo '
			<li id="error_', $key, '">', $value, '</li>';
		}
		echo '
 		</ul>
	</div>';
	}
	echo '
	<form action="', $scripturl, '?action=ebola-tools;area=quizzes;sa=add-question;quiz=', $quiz_id, '" method="post">
		<div class="windowbg">
			<div class="form_field">
				<div class="floatleft">
					<label for="question_category">
						<strong>Category:</strong>
					</label>
					<div class="smalltext">Select which category the question will be in, within the quiz.</div>
				</div>
				<div class="floatright">
					<select id="question_category" name="question_category" required="required">
						<option value="">Select a Category...</option>
						<optgroup label="Categories:">';
						if (!empty($this_quiz['categories']))
						{
							foreach ($this_quiz['categories'] as $key => $value)
							{
								echo '
								<option value="', $key, '"', !empty($_POST['question_category']) && $_POST['question_category'] == $key ? ' selected="selected"' : '', '>', $value['category_name'], ' (', $value['total_questions'], ')</option>';
							}
						}
						echo '
						</optgroup>
					</select>
				</div>
				<br class="clear" />
			</div>
			<div class="form_field">
				<div class="floatleft">
					<label for="question_text">
						<strong>Question Text:</strong>
					</label>
					<div class="smalltext">The actual question that is presented to the user.</div>
				</div>
				<div class="floatright">
					<textarea id="question_text" name="question_text" required="required" maxlength="255">', !empty($_POST['question_text']) ? $_POST['question_text'] : '', '</textarea>
					<div class="smalltext">
						<em>Characters Left: <span id="text_chars_left">255</span></em>
					</div>
				</div>
				<br class="clear" />
			</div>
			<div class="form_field">
				<div class="floatleft">
					<label for="question_prompt">
						<strong>Question Prompt:</strong>
					</label>
					<div class="smalltext">The question prompt that is given to the user.</div>
				</div>
				<div class="floatright">
					<input type="text" id="question_prompt" name="question_prompt" required="required"', !empty($_POST['question_prompt']) ? ' value="' . $_POST['question_prompt'] . '"' : '', ' placeholder="Select the best question for the answer above:" />
				</div>
				<br class="clear" />
			</div>
			<div class="form_field">
				<div class="floatleft">
					<label for="question_type">
						<strong>Type:</strong>
					</label>
					<div class="smalltext">The type of answer you want to be provided.</div>
				</div>
				<div class="floatright">
					<select id="question_type" name="question_type" required="required">
						<option value="">Select a Type...</option>
						<optgroup label="Types:">
							<option value="1"', !empty($_POST['question_type']) && $_POST['question_type'] == 1 ? ' selected="selected"' : '', '>Multiple Choice</option>
							<option value="2"', !empty($_POST['question_type']) && $_POST['question_type'] == 2 ? ' selected="selected"' : '', '>True / False</option>
						</optgroup>
					</select>
				</div>
				<br class="clear" />
			</div>
			<div class="form_field">
				<div class="floatleft">
					<label for="question_source">
						<strong>Source:</strong>
					</label>
					<div class="smalltext">Where the question and answer came from. Use details.</div>
				</div>
				<div class="floatright">
					<input type="text" id="question_source" name="question_source" required="required"', !empty($_POST['question_source']) ? ' value="' . $_POST['question_source'] . '"' : '', ' />
				</div>
				<br class="clear" />
			</div>
		</div>
		<div class="windowbg', empty($_POST['question_type']) || $_POST['question_type'] != 1 ? ' hidden' : '', '" id="mc_answer">
			<div id="multiple_choice_options">
				<div class="floatleft multiple_choice_letter">
					<label for="multiple_choice_a">
						<span>A</span>.
					</label>
				</div>
				<div class="floatleft multiple_choice_answer">
					<input type="radio" id="multiple_choice_a" name="multiple_choice_answer" value="1"', !empty($_POST['multiple_choice_answer']) && $_POST['multiple_choice_answer'] == 1 ? ' checked="checked"' : '', ' />
					<input type="text" id="multiple_choice_a" name="multiple_choice_a" class="input_text"', !empty($_POST['multiple_choice_a']) ? ' value="' . $_POST['multiple_choice_a'] . '"' : '', ' />
				</div>
				<br class="clear" />
				<div class="floatleft multiple_choice_letter">
					<label for="multiple_choice_b">
						<span>B</span>.
					</label>
				</div>
				<div class="floatleft multiple_choice_answer">
					<input type="radio" id="multiple_choice_b" name="multiple_choice_answer" value="2"', !empty($_POST['multiple_choice_answer']) && $_POST['multiple_choice_answer'] == 2 ? ' checked="checked"' : '', ' />
					<input type="text" id="multiple_choice_b" name="multiple_choice_b" class="input_text"', !empty($_POST['multiple_choice_b']) ? ' value="' . $_POST['multiple_choice_b'] . '"' : '', ' />
				</div>
				<br class="clear" />
				<div class="floatleft multiple_choice_letter">
					<label for="multiple_choice_c">
						<span>C</span>.
					</label>
				</div>
				<div class="floatleft multiple_choice_answer">
					<input type="radio" id="multiple_choice_c" name="multiple_choice_answer" value="3"', !empty($_POST['multiple_choice_answer']) && $_POST['multiple_choice_answer'] == 3 ? ' checked="checked"' : '', ' />
					<input type="text" id="multiple_choice_c" name="multiple_choice_c" class="input_text"', !empty($_POST['multiple_choice_c']) ? ' value="' . $_POST['multiple_choice_c'] . '"' : '', ' />
				</div>
				<br class="clear" />
				<div class="floatleft multiple_choice_letter">
					<label for="multiple_choice_d">
						<span>D</span>.
					</label>
				</div>
				<div class="floatleft multiple_choice_answer">
					<input type="radio" id="multiple_choice_d" name="multiple_choice_answer" value="4"', !empty($_POST['multiple_choice_answer']) && $_POST['multiple_choice_answer'] == 4 ? ' checked="checked"' : '', ' />
					<input type="text" id="multiple_choice_d" name="multiple_choice_d" class="input_text"', !empty($_POST['multiple_choice_d']) ? ' value="' . $_POST['multiple_choice_d'] . '"' : '', ' />
				</div>
				<br class="clear" />
			</div>
		</div>
		<div class="windowbg', empty($_POST['question_type']) || $_POST['question_type'] != 2 ? ' hidden' : '', '" id="true_false_answer">
			<div class="floatleft" style="width: 3%; padding-right: 4px; text-align: right; font-size: 16pt; color: rgb(102, 103, 107); margin-top: 7px; font-family: \'Times New Roman\'">
				<label for="true_false_answer_is_true">
					True
				</label>
			</div>
			<div class="floatleft" style="width: 96%; margin-top: 6px; margin-left: 2px; margin-bottom: 6px; padding-bottom: 6px; border-bottom: 1px dotted #a6a6a6;">
				 <input type="radio" id="true_false_answer_is_true" name="true_false_answer" value="1"', !empty($_POST['true_false_answer']) && $_POST['true_false_answer'] == 1 ? ' checked="checked"' : '', ' />
			</div>
			<br class="clear" />
			<div class="floatleft" style="width: 3%; padding-right: 4px; text-align: right; font-size: 16pt; color: rgb(102, 103, 107); margin-top: 7px; font-family: \'Times New Roman\'">
				<label for="true_false_answer_is_false">
					False
				</label>
			</div>
			<div class="floatleft" style="width: 96%; margin-top: 4px; margin-left: 2px; margin-bottom: 6px; padding-bottom: 6px; border-bottom: 1px dotted #a6a6a6;">
				 <input type="radio" id="true_false_answer_is_false" name="true_false_answer" value="2"', !empty($_POST['true_false_answer']) && $_POST['true_false_answer'] == 2 ? ' checked="checked"' : '', ' />
			</div>
			<br class="clear" />
		</div>
		<div class="windowbg form_field">
			<input type="hidden" id="add-question" name="add-question" value="1" />
			<input type="submit" value="Add Question" class="button_submit" />
		</div>
	</form>
	';
}

function template_add_quiz_category()
{
	global $scripturl, $txt, $settings, $context;
	global $quiz_id, $this_quiz;

	echo '
	<style type="text/css">
		#category_name {
			width: 60%;
		}
	</style>
	<div class="cat_bar" style="margin-top: 12px;">
		<h3 class="catbg">Add Category</h3>
	</div>';
	if (!empty($context['form_errors']))
	{
		echo '
	<div class="error">
		<strong>The following error', count($context['form_errors']) > 1 ? 's were' : ' was', ' encountered:</strong>
 		<ul>';
		foreach ($context['form_errors'] as $key => $value)
		{
			echo '
			<li id="error_', $key, '">', $value, '</li>';
		}
		echo '
 		</ul>
	</div>';
	}
	echo '
	<form action="', $scripturl, '?action=ebola-tools;area=quizzes;sa=add-category;quiz=', $quiz_id, '" method="post">
		<div class="windowbg">
			<div class="form_field">
				<div class="floatleft">
					<label for="category_name">
						<strong>Category Name:</strong>
					</label>
					<div class="smalltext">The name of the category.</div>
				</div>
				<div class="floatright">
					<input type="text" id="category_name" name="category_name" required="reqiured"', !empty($_POST['category_name']) ? ' value="' . $_POST['category_name'] . '"' : '', ' />
				</div>
				<br class="clear" />
			</div>
		</div>
		<div class="windowbg form_field">
			<input type="hidden" id="add-category" name="add-category" value="1" />
			<input type="submit" value="Add Category" class="button_submit" />
		</div>
	</form>
	';
}

function template_export_quiz()
{
	global $context, $scripturl, $settings;
	global $quiz_id, $this_quiz;

	$change_in_hex = 35;
	$colors = array(
		'498BE6', //-- Light Blue
		'8449E6', //-- Purple
		'55EA67', //-- Green
		'E5D94D', //-- Yellow
		'B33E38', //-- Red
		'59E6E3', //-- Teal
		'524BE6', //-- Dark Blue
		'EA7257', //-- Dark Orange
		'E5AE4F', //-- Light Orange
		'B33BA0', //-- Violet
	);

	foreach ($this_quiz['categories'] as $key => $value)
	{
		$color_key = array_rand($colors, 1);
		$this_quiz['categories'][$key]['color'] = $colors[$color_key];
		$split_hex_string = str_split($colors[$color_key], 2);
		foreach ($split_hex_string as $sub_key => $sub_value)
		{
			$split_hex_string[$sub_key] = base_convert($sub_value, 16, 10);
			$split_hex_string[$sub_key] -= $change_in_hex;
			$split_hex_string[$sub_key] = base_convert($split_hex_string[$sub_key], 10, 16);
		}
		$this_quiz['categories'][$key]['highlight'] = strtoupper(implode('', $split_hex_string));
		unset($colors[$color_key]);

		echo '
		<style type="text/css">
			#category_circle_' , $key, ' {
				background: #', $this_quiz['categories'][$key]['color'], ';
			}
			#category_circle_', $key, ':hover {
				background: #', $this_quiz['categories'][$key]['highlight'], ';
			}
		</style>';
	}

	//-- Isn't hacking a beautiful thing? ;)
	echo '<script type="text/javascript">
	var categories = [];
	// And for a doughnut chart
	var data = [
        {
            value: 100,
			color: "#555555",
			highlight: "#444444",
			label: "Unallocated"
    	}
    ];';
	foreach ($this_quiz['categories'] as $key => $value)
    {
		echo '
	categories.push(' . $key . ');
	data.push({
		value: 0,
		color: "' . $value['color'] . '",
		highlight: "' . $value['highlight'] . '",
		label: "' . $value['category_name'] . '"
	});';
	}
	echo '
	var categoriesObject = ' . json_encode($this_quiz['categories']) . ';
	</script>
	<script src="', $settings['default_theme_url'], '/scripts/Ebola/Chart.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {

			// Get context with jQuery - using jQuery\'s .get() method.
			var ctx = $("#category_percentages").get(0).getContext("2d");

			var categoriesDoughnutChart = new Chart(ctx).Doughnut(data, {
				segmentShowStroke: true, // The border around elements of the doughnut.
				percentageInnerCutout : 70, // The hole in the center of the doughnut.
				animateRotate : true, // Don\'t animate the doughnut into existence.
				animationSteps: 100,
				animateScale: true
			});

			//-- Are we wanting to select the weight of the categories or should it be COMPLETELY random?
			$("#source_type").on("change", function() {
				if ($(this).val() == "manual") {
					$("#source_type_manual").slideDown("slow");
				} else {
					$("#source_type_manual").slideUp("slow");
				}
			});

			//-- Allocation Availability
			$("#total_questions").on("change", function() {
				var total_questions = parseInt($(this).val());
				if (total_questions == 0) {
					$(".source_category").val("0").attr("disabled", "disabled");
					$("#allocate_questions").text("0");
				} else {
					$(".source_category").removeAttr("disabled");
					var properAllocatable = total_questions;
					for (i = 0; i < categories.length; i++) {
						properAllocatable -= parseInt($("#category_" + categories[i] + "_questions").val());
					}
					$("#allocate_questions").text(properAllocatable);
				}

				var questions_allocated = 0;
				for (i = 0; i < categories.length; i++) {
					questions_allocated += parseInt($("#category_" + categories[i] + "_questions").val());
				}
				if (total_questions < questions_allocated) {
					$(this).val(parseInt($(this).val()) + 1);
					$("#allocate_questions").text("0");
					$("#total_questions_info").fadeIn("slow");
				}
			});

			$(".source_category").on("change", { chart : categoriesDoughnutChart }, function(e) {

				var total_questions, questions_remaining;

				total_questions = parseInt($("#total_questions").val());
				questions_remaining = total_questions;

				var current_chart = e.data.chart;
				var categoryId = $(this).data("category-id");

				for (i = 0; i < categories.length; i++) {
					questions_remaining -= parseInt($("#category_" + categories[i] + "_questions").val());
				}
				if (questions_remaining < 0) {
					$(this).val($(this).val() - 1);
				}
				else
				{
					$("#allocate_questions").text(questions_remaining);
					var questions_used, this_percent;

					questions_used = total_questions - questions_remaining;
					this_percent = ((parseInt($(this).val()) / total_questions) * 100);
					$("#category_" + $(this).data("category-id") + "_percent").text(this_percent);

					for (i = 0; i < categories.length; i++) {
						var this_value, loop_percent;

						this_value = parseInt($("#category_" + categories[i] + "_questions").val());
						loop_percent = ((this_value / total_questions) * 100);

						$("#category_" + categories[i] + "_percent").text(loop_percent.toFixed(0));
					}

					var this_chart_percent = parseInt(this_percent.toFixed(0));

					var unallocated_percent = parseInt((((total_questions - questions_used) / total_questions) * 100).toFixed(0));

					current_chart.segments[0].value = unallocated_percent;

					current_chart.segments[parseInt($(this).data("numeric"))].value = parseInt(this_percent.toFixed(0));

					current_chart.update();
				}
			});
		});
	</script>
	<style type="text/css">
		#category_circle_0 {
			background: #555;
		}
		#category_circle_0:hover {
			background: #444;
		}
		.circle {
			border-radius: 50%;
			width: 30px;
			height: 30px;
			display: inline-block;
			vertical-align: middle;
		}
		#legend ul li {
			list-style: none;
			border-top: 1px solid #ccc;
			border-left: 1px solid #ccc;
			border-right: 1px solid #ccc;
			padding: 6px;
		}
		#legend ul li span.text {
			font-weight: bold;
			font-size: 12pt;
			color: #555;
		}
		#legend ul li:last-child {
			border-bottom: 1px solid #ccc;
		}
	</style>
	<div id="form_wrapper" style="margin-top: 12px;">
		<form action="', $scripturl, '?action=ebola-tools;area=quizzes;sa=export;quiz=', $quiz_id, '" method="post">
			<div class="cat_bar">
				<h3 class="catbg">Export Quiz</h3>
			</div>';
			if (!empty($context['form_errors']))
			{
				echo '
			<div class="error">
				<strong>The following error', count($context['form_errors']) > 1 ? 's were' : ' was', ' encountered:</strong>
				<ul>';
				foreach ($context['form_errors'] as $key => $value)
				{
					echo '
					<li id="error_', $key, '">', $value, '</li>';
				}
				echo '
				</ul>
			</div>';
			}
			echo '
			<div class="windowbg">
				<div class="form_field">
					<div class="floatleft">
						<label for="total_questions">
							<strong>Number of Questions:</strong>
						</label>
						<div class="smalltext">Enter the number of questions that you want to be exported.</div>
					</div>
					<div class="floatright">
						<input type="number" id="total_questions" name="total_questions" value="0" min="0" max="', $this_quiz['total_questions'], '" class="input_text" />
						&nbsp;<span id="total_questions_info" class="smalltext hidden" style="color: #555;">You must unallocate a question below to decrease the total questions.</span>
						<div class="smalltext element_text">Max Questions: ', $this_quiz['total_questions'], '</div>
					</div>
					<br class="clear" />
				</div>
				<div class="form_field">
					<div class="floatleft">
						<label for="source_type">
							<strong>Source Type:</strong>
						</label>
						<div class="smalltext">Select the source type for the questions that you want to be exported.</div>
					</div>
					<div class="floatright">
						<select id="source_type" name="source_type" required="required">
							<option value=""', empty($_POST['source_type']) ? ' selected="selected"' : '', '>Select a Source Type...</option>
							<optgroup label="Source Types:">
								<option value="random"', !empty($_POST['source_type']) && $_POST['source_type'] == 'random' ? ' selected="selected"' : '', '>Random</option>
								<option value="manual', !empty($_POST['source_type']) && $_POST['source_type'] == 'manual' ? ' selected="selected"' : '', '">Manual</option>
							</optgroup>
						</select>
					</div>
					<br class="clear" />
				</div>
				<div id="source_type_manual" class="form_field hidden">
					<div class="floatleft" style="width: 75%;">
						<div class="title_bar">
							<h3 class="titlebg">
								<span>Questions from Categories...</span>
								<div class="floatright" style="text-align: right;">
									Allocatable: <span id="allocate_questions">0</span>
								</div>
								<br class="clear" />
							</h3>
						</div>
						<div class="information">';
						$count = 1;
						foreach ($this_quiz['categories'] as $key => $value)
						{
							echo '
							<div class="form_field">
								<div class="floatleft">
									<label for="category_', $key, '_questions">
										<strong>', $value['category_name'], '</strong>&nbsp;(<span id="category_', $key, '_percent">0</span>%)
									</label>
									<div class="smalltext">Define the number of questions to be randomly selected from ', $value['category_name'], '.</div>
								</div>
								<div class="floatright" style="width: inherit;">
									<input type="number" id="category_', $key, '_questions" name="category_', $key, '_questions" data-category-id="', $key, '" data-numeric="', $count, '" value="', !empty($_POST['category_' . $key . '_questions']) ? $_POST['category_' . $key . '_questions'] : 0, '" min="0" max="', $value['total_questions'], '" class="input_text source_category" disabled="disabled" style="width: 100px; text-align: right;" />
									<div class="smalltext element_text">Max Questions: ', $value['total_questions'], '</div>
								</div>
								<br class="clear" />
							</div>';
							++$count;
						}
						echo '</div>
					</div>
					<div class="floatright" style="width: 24%; margin-top: 4px;">
						<div class="title_bar">
							<h3 class="titlebg centertext">Percentage Chart</h3>
						</div>
						<div class="information centertext">
							<canvas id="category_percentages" width="250" height="250"></canvas>
							<div id="legend">
								<ul>
									<li>
										<span id="category_circle_0" class="circle"></span>
										<span class="text">Unallocated</span>
										<br class="clear" />
									</li>';
									$even_counter = 0;
									foreach ($this_quiz['categories'] as $key => $value)
									{
										echo '
										<li>
											<span id="category_circle_', $key, '" class="circle"></span>
											<span class="text">', $value['category_name'], '</span>
											<br class="clear" />
										</li>';
										++$even_counter;
									}
									echo '
								</ul>
							</div>
						</div>
					</div>
					<br class="clear" />
				</div>
			</div>
			<div class="windowbg form_field">
				<input type="hidden" id="exporting" name="exporting" value="1" />
				<input type="submit" value="Export" class="button_submit" />
			</div>
		</form>
	</div>
	<br />';
}

function template_export_quiz_output()
{
	global $context;

	echo '
	<div class="cat_bar" style="margin-top: 12px;">
		<h3 class="catbg">Export Questions</h3>
	</div>
	<div class="floatleft" style="width: 30%;">
		<div class="windowbg smalltext">
			<strong>Exported Quiz Details:</strong>
			<ul style="list-style-type: circle; margin-left: 18px;">
				<li>
				Comprised of <strong>', $context['export_questions']['detailed']['total_questions'], ' questions</strong> from <strong>', count($context['export_questions']['detailed']['categories']), ' categories.</strong>
				</li>
			</ul>
		</div>
		<div class="windowbg smalltext">
			<strong>Contributors:</strong>
			<ul style="list-style-type: circle; margin-left: 18px;">
				<li>
				Comprised of <strong>', $context['export_questions']['detailed']['total_questions'], ' questions</strong> from <strong>', count($context['export_questions']['detailed']['categories']), ' categories.</strong>
				</li>
			</ul>
		</div>
	</div>
	<div class="floatright" style="width: 68%;">
		<div class="windowbg">
			<textarea style="width: 98%; height: 500px;">', $context['export_questions']['raw_boeing_format'], '</textarea>
		</div>
	</div>
	<br class="clear" />
	';
}