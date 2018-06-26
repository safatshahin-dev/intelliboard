<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();

function intelliboard_data($type, $userid, $showing_user) {
    global $DB;

    $data = array();
    $page = optional_param($type.'_page', 0, PARAM_INT);
    $search = clean_raw(optional_param('search', '', PARAM_RAW));
    $t = optional_param('type', '', PARAM_ALPHANUMEXT);
    $perpage = 10;
    $start = $page * $perpage;
    $params = array();
    $query = "";

    if($type == 'assignment'){
        $sql = "";
        if($search and $t == 'assignment'){
            $sql .= " AND (" . $DB->sql_like('c.fullname', ":fullname", false, false);
            $sql .= " OR " . $DB->sql_like('a.name', ":name", false, false);
            $sql .= ")";
            $params['fullname'] = "%$search%";
            $params['name'] = "%$search%";
        }
        if($showing_user->activity_courses){
            $sql .= " AND c.id = :activity_courses";
            $params['activity_courses'] = intval($showing_user->activity_courses);
        }else{
            $sql .= " AND c.id IN (SELECT e.courseid FROM {user_enrolments} ue, {enrol} e WHERE ue.userid = :userid1 AND e.id = ue.enrolid )";
            $params['userid1'] = $showing_user->id;
        }
        if($showing_user->activity_time !== -1){
            list($timestart, $timefinish) = get_timerange($showing_user->activity_time);
            $sql .= " AND a.duedate BETWEEN :timestart AND :timefinish";
            $params['timestart'] = $timestart;
            $params['timefinish'] = $timefinish;
        }
        $grade_single = intelliboard_grade_sql();
        $query = "SELECT a.id, a.name, a.duedate, c.fullname, $grade_single AS grade, cmc.completionstate, cm.id as cmid
                    FROM {course} c, {assign} a
                        LEFT JOIN {modules} m ON m.name = 'assign'
                        LEFT JOIN {course_modules} cm ON cm.module = m.id AND cm.instance = a.id
                        LEFT JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id AND cmc.userid = :userid2
                        LEFT JOIN {grade_items} gi ON gi.itemmodule = m.name AND gi.iteminstance = a.id
                        LEFT JOIN {grade_grades} g ON g.itemid = gi.id AND g.userid = :userid3
                    WHERE c.id = a.course AND cm.visible = 1 AND c.visible = 1 $sql ORDER BY cm.added ASC";
        $params['userid2'] = $userid;
        $params['userid3'] = $userid;

        $data = $DB->get_records_sql($query, $params, $start, $perpage);
    }elseif ($type == 'quiz') {
        $sql = "";
        if($search and $t == 'quiz'){
            $sql .= " AND (" . $DB->sql_like('c.fullname', ":fullname", false, false);
            $sql .= " OR " . $DB->sql_like('a.name', ":name", false, false);
            $sql .= ")";
            $params['fullname'] = "%$search%";
            $params['name'] = "%$search%";
        }

        if($showing_user->activity_courses){
            $sql .= " AND c.id = :activity_courses";
            $params['activity_courses'] = intval($showing_user->activity_courses);
        }else{
            $sql .= " AND c.id IN (SELECT e.courseid FROM {user_enrolments} ue, {enrol} e WHERE ue.userid = :userid AND e.id = ue.enrolid )";
            $params['userid'] = $showing_user->id;
        }
        if($showing_user->activity_time !== -1){
            list($timestart, $timefinish) = get_timerange($showing_user->activity_time);
            $sql .= " AND a.timeclose BETWEEN :timestart AND :timefinish";
            $params['timestart'] = $timestart;
            $params['timefinish'] = $timefinish;
        }
        $grade_single = intelliboard_grade_sql();

        $query = "SELECT gi.id, a.name, a.timeclose, c.fullname, $grade_single AS grade, cmc.completionstate, cm.id as cmid
                  FROM {course} c, {quiz} a
                    LEFT JOIN {modules} m ON m.name = 'quiz'
                    LEFT JOIN {course_modules} cm ON cm.module = m.id AND cm.instance = a.id
                    LEFT JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id AND cmc.userid = :userid2
                    LEFT JOIN {grade_items} gi ON gi.itemmodule = m.name AND gi.iteminstance = a.id
                    LEFT JOIN {grade_grades} g ON g.itemid = gi.id AND g.userid = :userid3
                  WHERE c.id = a.course AND cm.visible = 1 AND c.visible = 1 $sql ORDER BY cm.added ASC";
        $params['userid2'] = $userid;
        $params['userid3'] = $userid;

        $data = $DB->get_records_sql($query, $params, $start, $perpage);
    }elseif ($type == 'course') {
        $sql = "";
        if($search and $t == 'course'){
            $sql .= " AND " . $DB->sql_like('c.fullname', ":fullname", false, false);
            $params['fullname'] = "%$search%";
        }
        $params['userid1'] = $userid;
        $params['userid2'] = $userid;
        $params['userid3'] = $userid;
        $params['userid4'] = $userid;
        $params['userid5'] = $userid;

        $grade_single = intelliboard_grade_sql();

        $completion = intelliboard_compl_sql("cmc.");


        $query = "SELECT MAX(c.id) AS id, c.fullname, MIN(ue.timemodified) AS timemodified,
                    (SELECT $grade_single FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = c.id AND g.userid = :userid1) AS grade,
                    (SELECT COUNT(cmc.id) FROM {course_modules} cm, {course_modules_completion} cmc WHERE cm.id = cmc.coursemoduleid $completion AND cm.visible = 1 AND cm.course = c.id AND cmc.userid = :userid4) AS completedmodules,
                    (SELECT SUM(timespend) FROM {local_intelliboard_tracking} WHERE userid = :userid3 AND courseid = c.id) AS duration,
                    (SELECT COUNT(id) FROM {course_modules} WHERE visible = 1 AND completion > 0 AND course = c.id) AS modules,
                    (SELECT timecompleted FROM {course_completions} WHERE course = c.id AND userid = :userid5) AS timecompleted
                  FROM {user_enrolments} ue
                    LEFT JOIN {enrol} e ON e.id = ue.enrolid
                    LEFT JOIN {course} c ON c.id = e.courseid
                  WHERE ue.userid = :userid2 AND c.visible = 1 $sql GROUP BY c.id ORDER BY c.sortorder";

        $data = $DB->get_records_sql($query, $params, $start, $perpage);
    }elseif ($type == 'courses') {
        $sql = "";
        if($search){
            $sql .= " AND " . $DB->sql_like('c.fullname', ":fullname", false, false);
            $params['fullname'] = "%$search%";
        }

        $res = $DB->get_record_sql("SELECT COUNT(cm.id) as certificates FROM {course_modules} cm, {modules} m WHERE m.name = 'certificate' AND cm.module = m.id AND cm.visible = 1");
        $sql_select = "";
        $sql_join = "";
        if($res->certificates){
            $sql_select = ", (SELECT COUNT(ci.id) FROM {certificate} c, {certificate_issues} ci WHERE c.id = ci.certificateid AND ci.userid = :userid1 AND c.course = c.id) AS certificates";
        }else{
            $sql_select = ",'' as certificates";
        }
        $params['userid1'] = $userid;
        $params['userid2'] = $userid;
        $params['userid3'] = $userid;
        $params['userid4'] = $userid;
        $params['userid5'] = $userid;
        $params['userid6'] = $userid;

        $grade_single = intelliboard_grade_sql();
        $grade_avg = intelliboard_grade_sql(true);
        $completion = intelliboard_compl_sql("cmc.");

        $teacher_roles = get_config('local_intelliboard', 'filter10');
        list($sql_teacher_roles, $params) = intelliboard_filter_in_sql($teacher_roles, "ra.roleid", $params);

        $query = "SELECT c.id, c.fullname, MIN(ue.timemodified) AS timemodified,
                (SELECT $grade_single FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = c.id AND g.userid = :userid6) AS grade,
                (SELECT $grade_avg FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = c.id) AS average,
                (SELECT SUM(timespend) FROM {local_intelliboard_tracking} WHERE userid = :userid2 AND courseid = c.id) AS duration,
                (SELECT name FROM {course_categories} WHERE id = c.category) AS category,
                (SELECT COUNT(cmc.id) FROM {course_modules} cm, {course_modules_completion} cmc WHERE cm.id = cmc.coursemoduleid $completion AND cm.visible = 1 AND cm.course = c.id AND cmc.userid = :userid4) AS completedmodules,
                (SELECT COUNT(id) FROM {course_modules} WHERE visible = 1 AND completion > 0 AND course = c.id) AS modules,
                (SELECT timecompleted FROM {course_completions} WHERE course = c.id AND userid = :userid5) AS timecompleted,
                (SELECT DISTINCT u.id
                    FROM {role_assignments} AS ra
                        JOIN {user} u ON ra.userid = u.id
                        JOIN {context} AS ctx ON ctx.id = ra.contextid
                    WHERE ctx.instanceid = c.id AND ctx.contextlevel = 50 $sql_teacher_roles LIMIT 1
                ) AS teacher
                $sql_select
            FROM {user_enrolments} ue
                LEFT JOIN {enrol} e ON e.id = ue.enrolid
                LEFT JOIN {course} c ON c.id = e.courseid
                    $sql_join
                  WHERE ue.userid = :userid3 AND c.visible = 1 $sql GROUP BY c.id ORDER BY c.sortorder";


        $data = $DB->get_records_sql($query, $params, $start, $perpage);
    }

    $count = $DB->count_records_sql("SELECT COUNT(*) FROM ($query) AS x", $params);
    $pagination = get_pagination($count, $page, $perpage, $type);

    return array("pagination"=>$pagination, "data"=>$data);
}

function get_timerange($time){
    if($time == 0){
        $timestart = strtotime('-1 week');
        $timefinish = time();
    }elseif($time == 1){
        $timestart = strtotime('-1 month');
        $timefinish = time();
    }elseif($time == 2){
        $timestart = strtotime('-4 month');
        $timefinish = time();
    }elseif($time == 3){
        $timestart = strtotime('-6 month');
        $timefinish = time();
    }elseif($time == 4){
        $timestart = strtotime(date('01/01/Y'));
        $timefinish = time();
    }elseif($time == 5){
        $timestart = strtotime(date('01/01/Y', strtotime('-1 year')));
        $timefinish = strtotime(date('01/01/Y'));
    }else{
        $timestart = strtotime('-14 days');
        $timefinish = strtotime('+14 days');
    }
    return array($timestart,$timefinish);
}
function get_pagination($count = 0, $page = 0, $perpage = 15, $type = 'intelliboard') {
    global $OUTPUT, $PAGE;

    $pages = (int)ceil($count/$perpage);
    if ($pages == 1 || $pages == 0) {
        return '';
    }
    $link = new moodle_url($PAGE->url, array());
    return $OUTPUT->paging_bar($count, $page, $perpage, $link, $type.'_page');
}

function intelliboard_learner_course_progress($courseid, $userid){
    global $DB;

    $timestart = strtotime('-30 days');
    $timefinish = time();

    $data = array();
    $params = array();
    $params['userid'] = $userid;
    $params['timestart'] = $timestart;
    $params['timefinish'] = $timefinish;
    $params['courseid'] = $courseid;

    $grade_avg = intelliboard_grade_sql(true);
    $grade_avg_percent = intelliboard_grade_sql(true, null, 'g.', 0, 'gi.', true);

    $data[] = $DB->get_records_sql("SELECT floor(g.timemodified / 86400) * 86400 as timepoint, $grade_avg AS grade, $grade_avg_percent AS grade_percent
                                    FROM {grade_items} gi, {grade_grades} g
                                    WHERE gi.id = g.itemid AND g.userid = :userid AND gi.itemtype = 'mod' AND g.finalgrade IS NOT NULL AND g.timemodified BETWEEN :timestart AND :timefinish AND gi.courseid = :courseid
                                    GROUP BY timepoint ORDER BY timepoint", $params);

    $data[] = $DB->get_records_sql("SELECT floor(g.timemodified / 86400) * 86400 as timepoint, $grade_avg AS grade, $grade_avg_percent AS grade_percent
                                    FROM {grade_items} gi, {grade_grades} g
                                    WHERE gi.id = g.itemid AND g.userid != :userid AND gi.itemtype = 'mod' AND g.finalgrade IS NOT NULL AND g.timemodified BETWEEN :timestart AND :timefinish AND gi.courseid = :courseid
                                    GROUP BY timepoint ORDER BY timepoint", $params);
    return $data;
}
function intelliboard_learner_progress($time, $userid){
    global $DB;

    list($timestart, $timefinish) = get_timerange($time);

    $data = array();
    $params = array();
    $params['userid'] = $userid;
    $params['timestart'] = $timestart;
    $params['timefinish'] = $timefinish;

    $grade_avg = intelliboard_grade_sql(true);
    $grade_avg_percent = intelliboard_grade_sql(true, null, 'g.', 0, 'gi.', true);

    $data[] = $DB->get_records_sql("SELECT floor(g.timemodified / 86400) * 86400 AS timepoint, $grade_avg as grade, $grade_avg_percent AS grade_percent
                                    FROM {grade_items} gi, {grade_grades} g
                                    WHERE gi.id = g.itemid AND g.userid = :userid AND gi.itemtype = 'mod' AND g.finalgrade IS NOT NULL AND g.timemodified BETWEEN :timestart AND :timefinish
                                    GROUP BY timepoint ORDER BY timepoint", $params);

    $data[] = $DB->get_records_sql("SELECT floor(g.timemodified / 86400) * 86400 AS timepoint, $grade_avg as grade, $grade_avg_percent AS grade_percent
                                    FROM {grade_items} gi, {grade_grades} g
                                    WHERE gi.id = g.itemid AND g.userid != :userid AND gi.itemtype = 'mod' AND g.finalgrade IS NOT NULL AND g.timemodified BETWEEN :timestart AND :timefinish
                                    GROUP BY timepoint ORDER BY timepoint", $params);
    return $data;
}

function intelliboard_learner_courses($userid){
    global $DB;

    $params = array();
    $params['userid1'] = $userid;
    $params['userid2'] = $userid;
    $params['userid3'] = $userid;

    $grade_single = intelliboard_grade_sql(false);
    $grade_avg = intelliboard_grade_sql(true);

    $grade_single_percent = intelliboard_grade_sql(false,null, 'g.', 0, 'gi.',true);
    $grade_avg_percent = intelliboard_grade_sql(true,null, 'g.', 0, 'gi.',true);

    $scale_real = get_config('local_intelliboard', 'scale_real');
    if($scale_real){
        $params['userid4'] = $userid;
        $data = $DB->get_records_sql("
        SELECT c.id, c.fullname, '0' AS duration_calc,
            (SELECT $grade_single_percent FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = c.id AND g.userid = :userid3) AS grade,
            (SELECT $grade_avg_percent FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = c.id) AS average,
            (SELECT $grade_single FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = c.id AND g.userid = :userid4) AS grade_real,
            (SELECT $grade_avg FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = c.id) AS average_real,
            (SELECT SUM(timespend) FROM {local_intelliboard_tracking} WHERE userid = :userid1 AND courseid = c.id) AS duration
        FROM {user_enrolments} ue, {enrol} e, {course} c
        WHERE e.id = ue.enrolid AND c.id = e.courseid AND ue.userid = :userid2 AND c.visible = 1 GROUP BY c.id ORDER BY c.sortorder ASC", $params);
    }else{
        $data = $DB->get_records_sql("
        SELECT c.id, c.fullname, '0' AS duration_calc,
            (SELECT $grade_single FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = c.id AND g.userid = :userid3) AS grade,
            (SELECT $grade_avg FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = c.id) AS average,
            (SELECT SUM(timespend) FROM {local_intelliboard_tracking} WHERE userid = :userid1 AND courseid = c.id) AS duration
        FROM {user_enrolments} ue, {enrol} e, {course} c
        WHERE e.id = ue.enrolid AND c.id = e.courseid AND ue.userid = :userid2 AND c.visible = 1 GROUP BY c.id ORDER BY c.sortorder ASC", $params);
    }

    $d = 0;
    foreach($data as $c){
        $d = ($c->duration > $d)?$c->duration:$d;
    }
    if($d){
        foreach($data as $c){
            $c->duration_calc =  (intval($c->duration)/$d)*100;
        }
    }
    return $data;
}

function intelliboard_learner_totals($userid){
    global $DB;

    $params = array();
    $params['userid1'] = $userid;
    $params['userid2'] = $userid;
    $params['userid3'] = $userid;
    $params['userid4'] = $userid;
    $params['userid5'] = $userid;
    $params['userid6'] = $userid;
    $params['userid7'] = $userid;
    $params['userid8'] = $userid;
    $params['userid9'] = $userid;
    $params['userid10'] = $userid;

    $grade_avg = intelliboard_grade_sql(true);

    $data = $DB->get_record_sql("SELECT
                                    (SELECT count(distinct e.courseid) FROM {user_enrolments} ue, {enrol} e WHERE e.status = 0 AND ue.status = 0 AND ue.userid = :userid1 AND e.id = ue.enrolid) AS enrolled,
                                    (SELECT count(id) FROM {message} WHERE useridto = :userid2) AS messages,
                                    (SELECT count(distinct course) FROM {course_completions} WHERE userid = :userid3 AND timecompleted > 0) AS completed,
                                    (SELECT count(distinct e.courseid) FROM {user_enrolments} ue, {enrol} e WHERE e.status = 0 AND ue.status = 0 AND ue.userid = :userid4 AND e.id = ue.enrolid AND (e.courseid IN (
                                        SELECT distinct cm.course FROM {course_modules_completion} cmc, {course_modules} cm WHERE cmc.coursemoduleid = cm.id and cmc.userid = :userid5) OR e.courseid IN (
                                            SELECT distinct gi.courseid FROM {grade_items} gi, {grade_grades} g WHERE g.userid = :userid6 AND g.finalgrade IS NOT NULL AND gi.id = g.itemid)) AND e.courseid NOT IN (
                                                SELECT distinct course FROM {course_completions} WHERE userid = :userid7 AND timecompleted > 0)) as inprogress,
                                    (SELECT $grade_avg FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'course' AND g.userid != :userid8 AND g.finalgrade IS NOT NULL AND gi.id = g.itemid AND gi.courseid IN (
                                        SELECT e.courseid FROM {user_enrolments} ue, {enrol} e WHERE e.status = 0 AND ue.status = 0 AND ue.userid = :userid9 AND e.id = ue.enrolid)) as average,
                                    (SELECT $grade_avg FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'course' AND g.userid = :userid10 AND g.finalgrade IS NOT NULL AND gi.id = g.itemid) as grade", $params);

    if(get_config('local_intelliboard', 't08')){
        $sum_grade = $DB->get_record_sql("SELECT
                          (CASE WHEN (AVG((SELECT value FROM {grade_settings} WHERE courseid=gi.courseid AND name='displaytype'))=MIN((SELECT value FROM {grade_settings} WHERE courseid=gi.courseid AND name='displaytype')))
                                     OR (AVG((SELECT value FROM {grade_settings} WHERE courseid=gi.courseid AND name='displaytype')) IS NULL AND MIN((SELECT value FROM {grade_settings} WHERE courseid=gi.courseid AND name='displaytype')) IS NULL)
                            THEN (CASE MIN((SELECT value FROM {grade_settings} WHERE courseid=gi.courseid AND name='displaytype'))
                                  WHEN 1 THEN ROUND(SUM(g.finalgrade), 0)
                                  WHEN 12 THEN ROUND(SUM(g.finalgrade), 0)
                                  WHEN 13 THEN ROUND(SUM(g.finalgrade), 0)
                                  WHEN 2 THEN CONCAT(ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0),'%')
                                  WHEN 21 THEN CONCAT(ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0),
                                                      '% (',CASE MIN(gi.gradetype)
                                                            WHEN 1 THEN ROUND(AVG(g.finalgrade), 0)
                                                            WHEN 2 THEN (SELECT
                                                                           SUBSTRING_INDEX(SUBSTRING_INDEX(scale, ',', ROUND(AVG(g.finalgrade))), ',', -1)
                                                                         FROM {scale} s WHERE s.id=MIN(gi.scaleid))
                                                            WHEN 3 THEN GROUP_CONCAT(DISTINCT g.feedback ORDER BY g.feedback ASC SEPARATOR '; ')
                                                            END,')')
                                  WHEN 23 THEN CONCAT(ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0),
                                                      '% (',CASE WHEN (SELECT gl.letter
                                                                       FROM {grade_letters} gl, {context} ctx
                                                                                                WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                                       ORDER BY gl.lowerboundary
                                                                       LIMIT 1) IS NOT NULL
                                                            THEN (SELECT gl.letter
                                                                  FROM {grade_letters} gl, {context} ctx
                                                                                           WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                                  ORDER BY gl.lowerboundary
                                                                  LIMIT 1)
                                                            ELSE
                                                              CASE
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 93 THEN 'A'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 90 THEN 'A-'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 87 THEN 'B+'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 83 THEN 'B'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 80 THEN 'B-'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 77 THEN 'C+'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 73 THEN 'C'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 70 THEN 'C-'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 67 THEN 'D+'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 60 THEN 'D'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 0 THEN 'F'
                                                              ELSE ''
                                                              END
                                                            END,')')
                                  WHEN 3 THEN CASE WHEN (SELECT gl.letter
                                                         FROM {grade_letters} gl, {context} ctx
                                                                                  WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                         ORDER BY gl.lowerboundary
                                                         LIMIT 1) IS NOT NULL
                                              THEN (SELECT gl.letter
                                                    FROM {grade_letters} gl, {context} ctx
                                                                             WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                    ORDER BY gl.lowerboundary
                                                    LIMIT 1)
                                              ELSE
                                                CASE
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 93 THEN 'A'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 90 THEN 'A-'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 87 THEN 'B+'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 83 THEN 'B'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 80 THEN 'B-'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 77 THEN 'C+'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 73 THEN 'C'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 70 THEN 'C-'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 67 THEN 'D+'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 60 THEN 'D'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 0 THEN 'F'
                                                ELSE ''
                                                END
                                              END
                                  WHEN 31 THEN CONCAT(CASE WHEN (SELECT gl.letter
                                                                 FROM {grade_letters} gl, {context} ctx
                                                                                          WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                                 ORDER BY gl.lowerboundary
                                                                 LIMIT 1) IS NOT NULL
                                                      THEN (SELECT gl.letter
                                                            FROM {grade_letters} gl, {context} ctx
                                                                                     WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                            ORDER BY gl.lowerboundary
                                                            LIMIT 1)
                                                      ELSE
                                                        CASE
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 93 THEN 'A'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 90 THEN 'A-'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 87 THEN 'B+'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 83 THEN 'B'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 80 THEN 'B-'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 77 THEN 'C+'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 73 THEN 'C'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 70 THEN 'C-'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 67 THEN 'D+'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 60 THEN 'D'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 0 THEN 'F'
                                                        ELSE ''
                                                        END
                                                      END ,' (', CASE MIN(gi.gradetype)
                                                                 WHEN 1 THEN ROUND(MIN(g.finalgrade), 0)
                                                                 WHEN 2 THEN (SELECT
                                                                                SUBSTRING_INDEX(SUBSTRING_INDEX(scale, ',', ROUND(MIN(g.finalgrade))), ',', -1)
                                                                              FROM {scale} s WHERE s.id=MIN(gi.scaleid))
                                                                 WHEN 3 THEN GROUP_CONCAT(DISTINCT g.feedback ORDER BY g.feedback ASC SEPARATOR '; ')
                                                                 END, ')')
                                  WHEN 32 THEN CONCAT(CASE WHEN (SELECT gl.letter
                                                                 FROM {grade_letters} gl, {context} ctx
                                                                                          WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                                 ORDER BY gl.lowerboundary
                                                                 LIMIT 1) IS NOT NULL
                                                      THEN (SELECT gl.letter
                                                            FROM {grade_letters} gl, {context} ctx
                                                                                     WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                            ORDER BY gl.lowerboundary
                                                            LIMIT 1)
                                                      ELSE
                                                        CASE
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 93 THEN 'A'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 90 THEN 'A-'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 87 THEN 'B+'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 83 THEN 'B'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 80 THEN 'B-'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 77 THEN 'C+'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 73 THEN 'C'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 70 THEN 'C-'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 67 THEN 'D+'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 60 THEN 'D'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 0 THEN 'F'
                                                        ELSE ''
                                                        END
                                                      END ,' (', ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0), '%)')
                                  ELSE CONCAT(ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0),'%')
                                  END)
                           ELSE CONCAT(ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0),'%')
                           END ) AS grade

                        FROM {user_enrolments} ue
                          JOIN {enrol} e ON e.id = ue.enrolid
                          JOIN {course} c ON c.id = e.courseid
                          JOIN {grade_items} gi ON gi.courseid=c.id AND gi.itemtype='course'
                          JOIN {grade_grades} g ON g.itemid=gi.id AND g.userid=ue.userid
                        WHERE ue.userid = :userid1 AND c.visible = 1 AND c.id IN(2,21)", $params);

        $data->sum_grade = (!empty($sum_grade->grade))?$sum_grade->grade:'-';
    }

    return $data;
}
function intelliboard_learner_course($userid, $courseid){
    global $DB;

    $params = array();
    $params['userid1'] = $userid;
    $params['userid2'] = $userid;
    $params['userid3'] = $userid;
    $params['courseid'] = $courseid;

    $grade_single = intelliboard_grade_sql();

    return $DB->get_record_sql("SELECT c.id, c.fullname, ul.timeaccess, c.enablecompletion, cc.timecompleted, $grade_single AS grade
                                FROM {course} c
                                  LEFT JOIN {user_lastaccess} ul ON ul.courseid = c.id AND ul.userid = :userid1
                                  LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = :userid2
                                  LEFT JOIN {grade_items} gi ON gi.courseid = c.id AND gi.itemtype = 'course'
                                  LEFT JOIN {grade_grades} g ON g.itemid = gi.id AND g.userid = :userid3
                                WHERE c.id = :courseid ORDER BY c.sortorder ASC", $params);
}
function intelliboard_learner_modules($userid){
    global $DB;

    $params = array();
    $params['userid1'] = $userid;
    $params['userid2'] = $userid;
    $params['userid3'] = $userid;
    $completion = intelliboard_compl_sql("cmc.");

    return $DB->get_records_sql("SELECT m.id, m.name, count(distinct cm.id) as modules, count(distinct cmc.id) as completed_modules, count(distinct l.id) as start_modules, sum(l.timespend) as duration
                                  FROM {modules} m, {course_modules} cm
                                    LEFT JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id AND cmc.userid = :userid1 $completion
                                    LEFT JOIN {local_intelliboard_tracking} l ON l.page = 'module' AND l.userid = :userid2 AND l.param = cm.id
                                  WHERE cm.visible = 1 AND cm.module = m.id and cm.course IN (
                                    SELECT distinct e.courseid FROM {enrol} e, {user_enrolments} ue WHERE ue.userid = :userid3 AND e.id = ue.enrolid) GROUP BY m.id", $params);
}
