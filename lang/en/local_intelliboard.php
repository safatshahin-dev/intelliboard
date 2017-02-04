<?php
// IntelliBoard.net
//
// IntelliBoard.net is built to work with any LMS designed in Moodle
// with the goal to deliver educational data analytics to single dashboard instantly.
// With power to turn this analytical data into simple and easy to read reports,
// IntelliBoard.net will become your primary reporting tool.
//
// Moodle
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// IntelliBoard.net is built as a local plugin for Moodle.

/**
 * IntelliBoard.net
 *
 *
 * @package    	intelliboard
 * @copyright  	2015 IntelliBoard, Inc
 * @license    	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @created by	IntelliBoard, Inc
 * @website		www.intelliboard.net
 */

$string['pluginname'] = 'IntelliBoard.net Plugin';
$string['tracking_title'] = 'Time Tracking';
$string['tracking'] = 'Session Tracking';
$string['dashboard'] = 'Dashboard';
$string['settings'] = 'Settings';
$string['adv_settings'] = 'Advanced Setting';
$string['intelliboardroot'] = 'IntelliBoard';
$string['report'] = 'Report';
$string['reports'] = 'Reports';
$string['learners'] = 'Learners';
$string['courses'] = 'Courses';
$string['load'] = 'Load';
$string['inactivity'] = 'Inactivity';
$string['inactivity_desc'] = 'User inactivity time (in seconds)';
$string['ajax'] = 'Frequency';
$string['ajax_desc'] = 'Session storing frequency via AJAX. 0 - AJAX disabled (in seconds)';
$string['enabled'] = 'Enabled Tracking';
$string['enabled_desc'] = 'Enable Tracking';
$string['trackadmin'] = 'Tracking Admins';
$string['logs'] = 'Migration Tool';
$string['trackadmin_desc'] = 'Enable tracking for admin users (not recommended)';
$string['intelliboard:students'] = 'IntelliBoard [student]';
$string['intelliboard:view'] = 'IntelliBoard [view]';
$string['intelliboard:manage'] = 'IntelliBoard [manage]';
$string['tls12'] = 'TLS v1.2';
$string['tls12_desc'] = 'Advanced settings: TLS v1.2';
$string['sizemode'] = 'Large size mode';
$string['sizemode_desc'] = 'Large ( > 5.000 users)';


$string['filters'] = 'Dashboard filters';
$string['filter1'] = 'Deleted users';
$string['filter2'] = 'Suspended users';
$string['filter3'] = 'Guest user';
$string['filter4'] = 'Course filter';
$string['filter5'] = 'Enrolment method filter';
$string['filter6'] = 'User enrolment filter';
$string['filter7'] = 'Activities/Resourses filter';
$string['filter8'] = 'Enrolled users filter';
$string['filter1_desc'] = 'Show deleted users';
$string['filter2_desc'] = 'Show suspended users';
$string['filter3_desc'] = 'Show [guest] user in reports';
$string['filter4_desc'] = 'Show not visible courses';
$string['filter5_desc'] = 'Show not active enrollment methods';
$string['filter6_desc'] = 'Show users with not active enrollment status';
$string['filter7_desc'] = 'Show not visible Activities/Resourses';
$string['filter8_desc'] = 'Show only enrolled users (not recommended)';


$string['intelliboardstudent'] = 'IntelliBoard';
$string['intelliboardaccess'] = 'You do not have permission to view Learner Dashboard. Please contact your Administrator for assistance.';
$string['tex1'] = 'IntelliBoard Learner Dashboard is not enabled.';
$string['te1'] = 'Subscription email';
$string['te1_desc'] = 'Enter email used with IntelliBoard.net subscription. If you do not have an active subscription, please register at <a target="_blank" href="https://intelliboard.net">IntelliBoard.net</a>. The Learner Dashboard is available with a subscription of Level 3 and higher.';

$string['ts1'] = 'Learner Dashboard';
$string['ts2'] = 'Learner Dashboard Page';
$string['ts3'] = 'Learner Dashboard Courses';
$string['ts4'] = 'Learner Dashboard Grades';
$string['ts5'] = 'Learner Dashboard Reports';

$string['t01'] = 'Time filter: Last Month';
$string['t02'] = 'Time filter: Last Quarter';
$string['t03'] = 'Time filter: Last Semester';
$string['t04'] = 'Enable [header] Completed courses';
$string['t05'] = 'Enable [header] Courses in progress';
$string['t06'] = 'Enable [header] Courses avg. grade';
$string['t07'] = 'Enable [header] Messages';

$string['t1'] = 'Enable Learner Dashboard';
$string['t2'] = 'Enable Dashboard';
$string['t3'] = 'Enable Courses';
$string['t4'] = 'Enable Grades';
$string['t48'] = 'Enable Reports';
$string['t5'] = 'Enable [Activity progress] chart';
$string['t6'] = 'Enable [Course progress] chart';
$string['t7'] = 'Enable [My course average]';
$string['t8'] = 'Enable [Overall course average]';
$string['t9'] = 'Enable [Assignments] widget';
$string['t10'] = 'Enable [Quizzes] widget';
$string['t11'] = 'Enable [Course Progress] widget';
$string['t12'] = 'Enable [Activity participation] widget';
$string['t13'] = 'Enable [Learning] widget';
$string['t14'] = 'Enable [Course success] widget';
$string['t15'] = 'Enable [Correlations] widget';
$string['t16'] = 'Enable course teacher';
$string['t17'] = 'Enable course category';
$string['t18'] = 'Enable course completion';
$string['t19'] = 'Enable course grade';
$string['t20'] = 'Enable course class average';
$string['t21'] = 'Enable course time spent';
$string['t22'] = 'Enable course start date';
$string['t23'] = 'Enable [Course start date] column';
$string['t24'] = 'Enable [Enrolled date] column';
$string['t25'] = 'Enable [Progress] column';
$string['t26'] = 'Enable [Letter] column';
$string['t27'] = 'Enable [Completed Activities] column';
$string['t28'] = 'Enable [Score] column';
$string['t29'] = 'Enable [Course Completion Status] column';
$string['t30'] = 'Enable [Activity Grades] column';

$string['t31'] = 'Enable [Assignments][Grade] widget column';
$string['t32'] = 'Enable [Assignments][Due Date] widget column';
$string['t33'] = 'Enable [Quizzes][Grade] widget column';
$string['t34'] = 'Enable [Quizzes][Due Date] widget column';
$string['t35'] = 'Enable [Course Progress][Progress] widget column';
$string['t36'] = 'Enable [Course Progress][Grade] widget column';
$string['t37'] = 'Enable [Course Progress][Enrolled] widget column';
$string['t38'] = 'Enable [Course Progress][Completed] widget column';
$string['t39'] = 'Enable [Progress][Goal Grade] option';
$string['t40'] = 'Enable [Progress][Class Avg.] option';
$string['t41'] = 'Enable [Activity Grades header] Completed on';
$string['t42'] = 'Enable [Activity Grades header] Last access on course';
$string['t43'] = 'Enable [Activity Grades] Type column';
$string['t44'] = 'Enable [Activity Grades] Score column';
$string['t45'] = 'Enable [Activity Grades] Graded column';
$string['t46'] = 'Enable [Activity Grades] Completed column';
$string['t47'] = 'Courses [grid] background color';
$string['t49'] = 'Report filter columns';
$string['t50'] = 'Teacher roles';
$string['t51'] = 'Student roles';

$string['current_grade'] = 'current grade';
$string['average_grade'] = 'average grade';
$string['type_here'] = 'Type here...';
$string['enrolled_date'] = 'Enrolled date';
$string['teacher'] = 'Teacher';
$string['category'] = 'Category';
$string['current_grade'] = 'Current grade';
$string['completion'] = 'Completion';
$string['class_average'] = 'Class average';
$string['time_spent'] = 'Time Spent';
$string['completed_on'] = 'Completed on {$a}';
$string['last_access_on_course'] = 'Last access on course: {$a}';
$string['you_have_certificates'] = 'You have {$a} certificates';
$string['close'] = 'Close';
$string['view_course_details'] = 'View course details';
$string['incomplete'] = 'Incomplete';
$string['return_to_grades'] = 'Return to Grades';
$string['grade'] = 'Grade';
$string['last_week'] = 'Last Week';
$string['last_month'] = 'Last Month';
$string['last_quarter'] = 'Last Quarter';
$string['last_semester'] = 'Last Semester';
$string['activity_progress'] = 'Activity progress';
$string['course_progress'] = 'Course progress';
$string['my_course_average_all'] = 'my course average (all courses)';
$string['overall_course_average'] = 'overall course average (all learners; all courses)';
$string['assignments'] = 'Assignments';
$string['quizzes'] = 'Quizzes';
$string['assignment_name'] = 'Assignment name';
$string['due_date'] = 'Due Date';
$string['no_data'] = 'No data';
$string['quiz_name'] = 'Quiz name';
$string['all_courses'] = 'All courses';
$string['time_period_due'] = 'Time period (Due Date)';
$string['all_data'] = 'All data';
$string['progress'] = 'Progress';
$string['enrolled'] = 'Enrolled';
$string['completed'] = 'Completed';
$string['activity_participation'] = 'Activity participation';
$string['learning'] = 'Learning';
$string['course_success'] = 'Course success';
$string['correlations'] = 'Correlations';
$string['course_start_date'] = 'Course start date';
$string['letter'] = 'Letter';
$string['completed_activities'] = 'Completed Activities';
$string['score'] = 'Score';
$string['course_completion_status'] = 'Course Completion Status';
$string['activity_grades'] = 'Activity Grades';
$string['completion_is_not_enabled'] = 'Completion is not enabled for this course';
$string['activities'] = 'Activities';
$string['activity_name'] = 'Activity name';
$string['type'] = 'Type';
$string['graded'] = 'Graded';
$string['completed_courses'] = 'Completed courses';
$string['courses_in_progress'] = 'Courses in progress';
$string['courses_avg_grade'] = 'Courses avg. grade';
$string['grades'] = 'Grades';
$string['messages'] = 'Messages';
$string['x_completions'] = '{$a} Completions';
$string['completion_status'] = 'Completion status';
$string['users_activity'] = 'Users activity';
$string['daily'] = 'Daily';
$string['weekly'] = 'Weekly';
$string['monthly'] = 'Monthly';
$string['number_of_sessions'] = 'Number of sessions';
$string['number_today'] = '{$a} today';
$string['number_this_week'] = '{$a} this week';
$string['course_completions'] = 'Course completions';
$string['user_enrolments'] = 'User Enrolments';
$string['users'] = 'Users';
$string['modules'] = 'Modules';
$string['categories'] = 'Categories';
$string['total'] = 'Total';
$string['users_overview'] = 'Users Overview';
$string['enable_time_and_visits_users_overview'] = 'Enable time spent and visits in Users Overview';
$string['disable_time_and_visits_users_overview'] = 'Disable time spent and visits in Users Overview';
$string['loading'] = 'Loading...';
$string['enrollments'] = 'Enrollments';
$string['registrations'] = 'Registrations';
$string['participation'] = 'Participation';
$string['time'] = 'Time';
$string['enrolment_method'] = 'Enrolment Method';
$string['intelliBoard_migration_tool'] = 'IntelliBoard Migration Tool';
$string['importing_totals'] = 'Importing totals';
$string['total_numbers'] = 'Date: {$a->timepoint}, Sessions: {$a->sessions}, Visits: {$a->visits},  Time Spent: {$a->timespend}';
$string['total_numbers2'] = 'USER: {$a->userid}, Page: {$a->page}, Param:{$a->param}, Visits: {$a->visits},  Time Spent: {$a->timespend}';
$string['total_numbers3'] = '----Date: {$a->timecreated}, Track ID: {$a->trackid}, Visits: {$a->visits},  Time Spent: {$a->timespend}';
$string['logs_to_process'] = 'Logs to process {$a}';
$string['please_wait_or_cancel'] = 'Please wait to continue or <a href="{$a}">Cancel</a>';
$string['done'] = 'Done!';
$string['return_to_home'] = 'Return to home';
$string['importing_logs'] = 'Importing logs';
$string['intelliBoard_migration_tool_info'] = 'IntelliBoard migration tool is used to migrate historical data from Moodle logs table into new format. Please note, Moodle logs storing procedure will not change. Once historical data migrated to new format, historical values like \'Time Spent\' and \'Visits\' will be available for preview at IntelliBoard.net.';
$string['moodle_logs'] = 'Moodle logs';
$string['intelliboard_tracking'] = 'IntelliBoard tracking';
$string['intelliboard_logs'] = 'IntelliBoard logs';
$string['intelliboard_totals'] = 'IntelliBoard totals';
$string['intelliboard_start_tracking'] = 'IntelliBoard start tracking';
$string['total_values_include'] = 'Total Values include unique sessions, courses, visits, time spent.';
$string['items_per_query'] = 'Items per-query';
$string['import'] = 'Import';
$string['log_values_include'] = 'Log values include logs for each user per day.';
$string['powered_by'] = 'Powered by <a href="https://intelliboard.net/">IntelliBoard.net</a>';
$string['intelliboardnet'] = 'IntelliBoard.net';
$string['visits'] = 'Visits';
$string['registered'] = 'Registered';
$string['disabled'] = 'Disabled';
$string['enrolled_completed'] = 'Enrolled: {$a->courses}, Completed: {$a->completed_courses}';
$string['enrolled_users_completed'] = 'Enrolled users: {$a->users}, Completed: {$a->completed}';
$string['user_grade_avg'] = '{$a->user} grade: {$a->grade}, Average grade: {$a->avg_grade_site})';
$string['user_visit_avg'] = '{$a->user} visits: {$a->visits}, Average visits: {$a->avg_visits_site}';
$string['user_time_avg'] = '{$a->user} time: {$a->timespend}, Average time: {$a->avg_timespend_site}';
$string['more_users'] = 'More users';
$string['more_courses'] = 'More courses';
$string['showing_1_to_10'] = 'Showing 1 to 10';
$string['course_grade'] = 'Course Grade';
$string['completed_activities_resourses'] = 'Completed Activities/Resourses';
$string['save'] = 'Save';


