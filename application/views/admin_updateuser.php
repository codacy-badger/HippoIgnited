<?php 

require_once BASEPATH.'autoload.php';
require_once __DIR__.'/snippets/pi_specialization.php';

mustHaveAnyOfTheseRoles( Array( 'ADMIN', 'AWS_ADMIN' ) );

echo userHTML( );


if( ! array_key_exists( 'login', $_POST ) )
{
    echo printInfo( "You didn't select anyone. Go back and select someone." );
    goBackToPageLink( 'admin' );
    exit;
}

$default = getUserInfo( $_POST['login'] );
$buttonVal = 'update';
if( ! $default )
{
    $default = getUserInfoFromLdap( $_POST[ 'login' ] );
    if( ! $default )
    {
        echo printWarning( "Invalid username. I did not find anyone named " .
            $_POST[ 'login' ] . " on LDAP server" 
        );
        redirect( 'admin' );
    }

    $default[ 'login' ] = $_POST[ 'login' ];
    $buttonVal = 'addnew';
}

echo '<form method="post" action="' . site_url( "admin/updateuser/$user") . '">';
echo dbTableToHTMLTable(
    'logins', $default
    , Array( 'alternative_email', 'roles', 'status'
                , 'title', 'eligible_for_aws', 'joined_on'
                , 'valid_until' , 'laboffice', 'specialization', 'pi_or_host'
            ) 
    , $buttonVal
    );
echo '</form>';

// Button for deleting user.
echo '<br/><br/>';
echo '<form action="'. site_url( "admin/deleteuser/$user") . ' method="post"> ';
echo '<button type="submit" name="response" value="Delete">Delete User!</button>';
echo '</form>';

echo goBackToPageLink( 'admin', 'Go back' );

?>