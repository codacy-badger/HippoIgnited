<?php

require_once BASEPATH. 'autoload.php';
echo userHTML( );

$thisSem = getCurrentSemester( ) . ' ' . getCurrentYear( );
// Only show this section if user is eligible for AWS.
$userInfo = getLoginInfo( $_SESSION[ 'user' ] );

$html = '<table class="admin">';
$html .= '<tr><td>
            <i class="fa fa-book fa-3x"></i>
            <a class="clickable" href="'. site_url('/user/courses' ) . '">My Courses</a>
            <br /> Manage courses for semester  (' . $thisSem . ' ) 
                <small>Register/deregister courses for this semster. </small>
        </td>';

if( __get__($userInfo, 'eligible_for_aws', 'NO' ) == 'YES' )
{
    $html .=  '<td> <i class="fa fa-graduation-cap fa-3x"></i>
        <a class="clickable" href="'. site_url("/user/aws"). '">My AWS</a> <br />
        List of your Annual Work Seminar <br />
        <small> See your previous AWSs and update them. Check
        the details about upcoming AWS and provide preferred dates.
        </small> <br />
        <a href="'. site_url("/user/update/supervisors"). '">Update TCM Members/Supervisors</a>
        </td>';
}
else
    $html .= ' <td></td> ';
$html .= '</tr></table>';

echo $html;

// Journal club entry.
echo ' <h1>Journal clubs</h1> ';
$table = '<table class="admin">
    <tr>
        <td>
            <a class="clickable" href="'. site_url("/user/jc"). '">My Journal Clubs</a> <br />
            Subscribe/Unsubscribe from journal club. See upcoming presentation.
            Vote on presentation requests.
         </td>
        <td>
            <a class="clickable" href="'. site_url("/user/jc/presentation_requests"). '">
                My JC Presentation Requests</a>
            <br />
            Submit a journal paper and a preferred date to present it. You can submit
            one presentations requests for nay date. The community can vote on the
            presentation requests.
         </td>
    </tr>';

if( isJCAdmin( $_SESSION[ 'user' ] ) )
{
    $table .= '<tr>
        <td>
        <i class="fa fa-cogs fa-3x"></i>
        <a class="clickable" href="'. site_url("/user/jc/admin"). '">JC Admin</a> <br />
        Journal club admin</td>
        <td></td>
    </tr>';
}


$table .= '</table>';
echo $table;

echo '<h1>Booking</h1>';

$html = '<table class="admin">';
$html .= '
    <tr>
    <td>
        <i class="fa fa-hand-pointer-o fa-3x"></i>
         <a class="clickable" href="'. site_url("/user/book/venue"). '">Book Private Event</a>
         <br />
         No email needs to be sent to Academic community e.g. Labmeets,meeting,interview etc.
         Otherwise use <tt>Book Public Event</tt> link below.
    </td>
    <td>
        <a href="'. site_url("/user/show_private") . '" class="clickable">
            Manage My Private Events</a> <br />
        You can see your private bookings. You can modify their description, and
        cancel them if neccessary.
    </td>
    </tr>
   <tr>
    <td>
        <i class="fa fa-comments fa-3x"></i>
        <a class="clickable" href="'. site_url("/user/register_talk"). '">Book Public Event</a>
        <br />
        Register a new talk, seminar, or a thesis seminar.
        <small>Keep the email and photograph of speaker handy, not neccessary but highly recommended.</small>
    </td>
    <td>
        <a class="clickable" href="'. site_url("/user/show_public"). '">Manage My Public Events</a> <br />
        Edit/update a previously registered public event and book a venue for it.
    </td>
   </tr>
   </table>';
echo $html;

// Community services.
echo "<h1>Community services</h1>";
echo '<table class="admin">
    <tr>
        <td> <i class="fa fa-archive fa-3x"></i>
            <a class="clickable" href="'. site_url("/user/inventory/browse"). '">Browse inventory</a> <br />
            You can browse inventory. Items listed here can be borrowed.
        </td>
        <td>
            <a class="clickable" href="'. site_url("/user/inventory/add"). '">My Inventry Items</a>
            <br /> <br />
            Add items to inventory.
            By adding item here, you are letting others know that
            they can borrow this item from you.
        </td>
   </tr>
    <tr>
       <td>
            <i class="fa fa-building fa-3x"></i>
            <a class="clickable" href="'. site_url("/user/tolet/browse"). '"> Browse TO-LET list</a>
        </td>
       <td>
             <a class="clickable" href="'. site_url("/user/tolet/create"). '">My TO-LET and Alerts</a> <br />
            Create email-alerts and create a TO-LET entry for community.
            Email is sent to registered user.
        </td>
   </tr>
   </table>';


if( anyOfTheseRoles( 'ADMIN,BOOKMYVENUE_ADMIN,JOURNALCLUB_ADMIN,AWS_ADMIN' ) )
{
   echo "<h1> <i class=\"fa fa-cogs\"></i>   Admin</h1>";
   $roles =  getRoles( $_SESSION['user'] );

   $html = "<table class=\"admin\">";
   if( in_array( "ADMIN", $roles ) )
       $html .= '<tr><td><a class="clickable" href="'. site_url("/admin"). '">
                <i class="fa fa-lock fa-2x"></i><br />Admin</a></td>';

   if( in_array( "BOOKMYVENUE_ADMIN", $roles ) )
       $html .= '<td><a class="clickable" href="'. site_url("adminbmv"). '"> 
           <i class="fa fa-calendar-plus-o fa-2x"></i><br />BookMyVenue Admin</a></td>';
   else
       $html .= ' <td></td>';

   if( in_array( "AWS_ADMIN", $roles ) )
       $html .= '<td> <a class="clickable" href="'. site_url("/adminacad"). '">
           <i class="fa fa-graduation-cap fa-2x"></i><br />Academic Admin</a></td>';
   else
       $html .= ' <td></td>';

   $html .= "</tr></table>";
   echo $html;
}

?>
