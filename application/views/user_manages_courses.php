<?php

require_once BASEPATH . 'autoload.php';

echo userHTML( );
$sem = getCurrentSemester( );
$year = getCurrentYear( );

$runningCourses = array( );
$semCourses = getSemesterCourses( $year, $sem );
foreach( $semCourses as $rc )
{
    $cid = $rc[ 'course_id' ];
    $rc[ 'name' ] = getCourseName( $cid );
    $rc[ 'slot_tiles' ] = getCourseSlotTiles( $rc );
    $runningCourses[ $cid ] = $rc;
}

// User courses and slots.
$myCourses = getMyCourses( $sem, $year, $user = whoAmI() );

$mySlots = array( );
foreach( $myCourses as $c )
{
    // Get the running courses.  In rare case, use may have enrolled in course
    // which is not running anymore.
    $course = __get__( $runningCourses, $c['course_id'], null );
    if( $course )
        $mySlots[ ] = $runningCourses[ $c[ 'course_id' ] ]['slot'];
    else
    {
        // This course is no longer running. Drop it.
        updateTable(
            'course_registration'
            , 'student_id,year,semester,course_id'
            , 'status'
            , array( 'student_id' => $_SESSION[ 'user' ], 'year' => $year
                , 'semester' => $sem, 'course_id' => $c[ 'course_id' ]
                , 'status' => 'INVALID'
            )
        );
    }
}

$mySlots = array_unique( $mySlots );


/* --------------------------------------------------------------------------*/
/**
    * @Synopsis  Course enrollment.
    * Check each course date. If course is starting at date x, then start the
    * coruse x-7 days and let people register till x+7 days.
 */
/* ----------------------------------------------------------------------------*/
$today = strtotime( 'today' );

// Running course this semester.
$courseMap = array( );
$options = array( );

$blockedCourses = array( );
foreach( $runningCourses as $c )
{
    $cstart = strtotime( $c[ 'start_date' ] );

    // Registration is allowed within 4 weeks.
    if( $today > strtotime( '-14 day', $cstart) && $today <= strtotime( '+14 day', $cstart ) )
    {
        // Ignore any course which is colliding with any registered course.
        $cid = $c[ 'course_id' ];
        $cname = getCourseName( $cid );
        $slot = $c[ 'slot' ];
        if( in_array( $slot, $mySlots ) )
        {
            $blockedCourses[ $cid ] = $cname;
            continue;
        }

        if( $cid )
        {
            $options[] = $cid ;
            $courseMap[ $cid ] = getCourseName( $cid ) .
                " (slot " . getCourseSlotTiles( $c ) . ")";
        }
    }
}


echo "<h2>Registration form</h2>";
$courseSelect = arrayToSelectList( 'course_id', $options, $courseMap );
$default = array( 'student_id' => $_SESSION[ 'user' ]
                , 'semester' => $sem
                , 'year' => $year
                , 'course_id' => $courseSelect
                );

echo alertUser( "Any course running on already registered slot will not appear in your
    registration form."
    );
echo alertUser(
    "Courses will be visible in registration form from -2 weeks to +2 weeks from the
    <tt>start date</tt>."
    );
echo '<form method="post" action="manage_course/register">';
echo dbTableToHTMLTable( 'course_registration'
    , $default
    , 'course_id:required,type'
    , 'Submit'
    , 'status,registered_on,last_modified_on,grade,grade_is_given_on'
    );
echo '</form>';

// Show user which slots have been blocked.


/**
    * @name Show the registered courses.
    * @{ */
/**  @} */

$tofilter = 'student_id';
echo '<div style="font-size:small">';
echo '<table class="1">';
echo '<tr>';
$action = 'drop';

if( count( $myCourses ) > 0 )
{
    echo "<h1>You are registered for following courses for $sem $year</h1>";

}

if( count( $myCourses ) > 0 )
{
    echo ' <br />';
    // Dropping policy
    echo noteWithFAIcon( " <strong>Policy for dropping courses </strong> <br />
        Upto 30 days from starting of course, you are free to drop a course.
        After that, you need to write to your course instructor and academic office.
        ", "fa-bell-o" );
}

$count = 0;
foreach( $myCourses as $c )
{
    $action = 'drop';
    // Break at 3 courses.
    if( $count % 3 == 0 )
        echo '</tr><tr>';

    echo '<td>';
    echo '<form method="post" action="'.site_url("user/courses/update").'">';
    $cid = $c[ 'course_id' ];
    $course = getTableEntry( 'courses_metadata', 'id', array( 'id' => $cid ) );
    if( ! $course )
        continue;

    // If more than 30 days have passed, do not allow dropping courses.
    if( strtotime( 'today' ) >
        strtotime( '+30 day',strtotime($runningCourses[ $cid][ 'start_date' ])))
        $action = '';

    // TODO: Don't show grades unless student has given feedback.
    $tofilter = 'student_id,registered_on,last_modified_on';
    echo dbTableToHTMLTable( 'course_registration', $c, '', $action, $tofilter );
    echo '</form>';

    // Feeback form
    $form =  '<form action="'.site_url("user/manage_course/feedback").'" method="post">';
    $form .= ' <input type="hidden" name="course_id" value="'.$cid.'" />';
    $form .= ' <input type="hidden" name="semester" value="'.$c['semester'].'" />';
    $form .= ' <input type="hidden" name="year" value="'.$c['year'].'" />';
    $form .= ' <button style="float:right" name="response" value="submit">Feeback</button>';
    $form .= '</form>';

    echo $form;
    echo '</td>';

    $count += 1;
}

echo '</tr></table>';
echo '</div>';

// if( $runningCourses )
// {
//     echo '<h1> Running courses </h1>';
//     echo '<div style="font-size:small">';
//     echo '<table class="info">';
//     $ignore = 'id,semester,year,comment,ignore_tiles,slot';
//     $cs = array_values( $runningCourses );
//     echo arrayHeaderRow( $cs[0], 'info', $ignore );
//     foreach( $cs as $rc )
//         echo arrayToRowHTML( $rc, 'info', $ignore );
//     echo '</table>';
//     echo '</div>';
// }
//
//
// echo '<h1>Slots </h1>';
// echo slotTable( );

echo ' <br /> ';
echo goBackToPageLink( "user/home", "Go back" );

echo '<h1> My courses </h1>';

$user = whoAmI( );
$myAllCourses = getTableEntries( 'course_registration'
    , 'year, semester'
    , "student_id='$user' AND status='VALID'"
    );

$hide = 'student_id,status,last_modified_on';

if( count( $myAllCourses ) > 0 )
{
    echo '<table class="info sorttable">';
    echo arrayToTHRow( $myAllCourses[0], 'info', $hide );
    foreach( $myAllCourses as $course )
    {
        $cid = $course[ 'course_id' ];
        $cname = getCourseName( $cid );
        $course[ 'course_id' ] .= " <br /> $cname";
        echo arrayToRowHTML( $course, 'info', $hide );
    }
    echo "</table>";
}
else
{
    echo printInfo( "I could not find any course belonging to '$user' in my database." );
}

echo goBackToPageLink( "user/home", "Go back" );

?>
