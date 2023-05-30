<?php  
/*
Make date stamps human readable
*/
function nice_date( $timestamp ){
	$date = new DateTime( $timestamp );
	return $date->format( 'F jS' );
}

/*
convert timstamp to amout of time ago (1 day ago)
https://stackoverflow.com/questions/1416697/converting-timestamp-to-time-ago-in-php-e-g-1-day-ago-2-days-ago
 */
function time_ago($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

/*
Count the approved comments on any post
 */
function count_comments( $post_id ){
	//use the database connection from the global scope
	global $DB;
	$result = $DB->prepare('SELECT COUNT(*) AS total
							FROM comments
							WHERE post_id = ?
							AND is_approved = 1');
	//pass a variable to the placeholder in the query (?)
	$result->execute( array( $post_id ) );
	if( $result->rowCount() ){
		while( $row = $result->fetch() ){
			return $row['total'];
		} //end while
	} //end if
}  //end function count_comments


/*
Show user info (profile pic and username combined)
*/
function user_info( $id, $username, $size, $profile_pic = ''){  
    ?>
    <div class="user">
        <a href="profile.php?user_id=<?php echo $id; ?>">
           <?php show_profile_pic($profile_pic); ?>
            <span><?php echo $username; ?></span>
        </a>
    </div>
    <?php   
}
/*
Show a profile pic at any size
 */
function show_profile_pic($src, $size = 50, $alt = 'Profile Picture' ){
    //check if src is blank
    if( '' ==  $src ){
        $src =  'avatars/default.png';
    }
    ?>
    <img class="profile-pic" src="<?php echo $src ?>" alt="<?php echo $alt ?>" width="<?php echo $size ?>" height="<?php echo $size ?>">
    <?php 
}

/**
 * Display the HTML feedback element for basic forms
 * @param  string $heading the H2 content
 * @param  array  $list    the list of issues to fix
 * @param  string $class   either "success" or "error"
 * @return mixed          HTML element
 */
function show_feedback( $heading, $class = 'error', $list = array()  ){
    if( isset( $heading ) AND $heading != '' ){
        echo "<div class='feedback $class'>";
        echo "<h2>$heading</h2>";
        //if the list is not empty, show it is a <ul>
        if( ! empty( $list ) ){
            echo '<ul>';
            foreach( $list as $item ){
                echo "<li>$item</li>";
            }
            echo '</ul>';
        }
        echo '</div>';
    }
}
/**
* displays sql query information including the computed parameters.
* Silent unless DEBUG MODE is set to 1 in CONFIG.php
* @param [statement handler] $sth -  any PDO statement handler that needs troubleshooting
*/
function debug_statement($sth){
    if( DEBUG_MODE ){
        echo '<pre class="full">';
        $info = debug_backtrace();
        echo '<b>Debugger ran from ' . $info[0]['file'] . ' on line ' . $info[0]['line'] . '</b><br><br>';
        $sth->debugDumpParams();
        echo '</pre>';
    }
}
/**
 * check to see if the viewer is logged in
 * @return array|bool false if not logged in, array of all user data if they are logged in
 */

function check_login(){
    global $DB;
    //if the cookie is valid, turn it into session data
    if(isset($_COOKIE['access_token']) AND isset($_COOKIE['user_id'])){
        $_SESSION['access_token'] = $_COOKIE['access_token'];
        $_SESSION['user_id'] = $_COOKIE['user_id'];
    }

   //if the session is valid, check their credentials
    if( isset($_SESSION['access_token']) AND isset($_SESSION['user_id']) ){
        //check to see if these keys match the DB     

        $data = array( 'access_token' =>$_SESSION['access_token']  );

        $result = $DB->prepare(
            "SELECT * FROM users
            WHERE  access_token = :access_token
            LIMIT 1");
        $result->execute( $data );

        if($result->rowCount() > 0){
            //token found. confirm the user_id
            $row = $result->fetch();
            if( password_verify( $row['user_id'], $_SESSION['user_id'] ) ){
                //success! return all the info about the logged in user
                return $row;
            }else{
                return false;
            }

        }else{
            return false;
        }
    }else{
        //not logged in
        return false;
    }
}

/*
Display all categories as a form dropdown
*/
function category_dropdown(){
    global $DB;
    $result = $DB->prepare('SELECT * FROM categories ORDER BY name ASC');
    $result->execute();
    if($result->rowCount()){
        echo '<select name="category_id">';
        while( $row = $result->fetch() ){
            extract($row);
            echo "<option value='$category_id'>$name</option>";
        }
        echo '</select>';
    }
}

/*
Checkbox Helper
*/
function checked( $thing1, $thing2 ){
    if( $thing1 == $thing2 ){
        echo 'checked';
    }
}

/*
Select helper
*/
function selected( $thing1, $thing2 ){
    if( $thing1 == $thing2 ){
        echo 'selected';
    }
}

/**
 * Display the image of any post
 * @param  string $unique The unique string identifier of the image
 * @param  string $size   'small' 'medium' or 'large'
 * @param  string $alt    img alt text
 * @return string         HTML img tag
 */
function show_post_image( $unique, $size = 'medium', $alt = 'post image'  ){
    $url = "uploads/$unique" . '_' . "$size.jpg";
    //if the "unique" is not an absolute path, format it. 
    //this makes our old placeholder images still work. Not really necessary but nice for this class. 
    if( strpos( $unique, 'http' ) === 0 ){
        $url = $unique;
    }
echo "<img src='$url' alt='$alt' class='post-image is-$size'>";
}


/*
Edit Post Button
 */
function edit_post_button( $post_id = 0, $post_author = 0 ){
    global $logged_in_user;
    //if the user is logged in and this is their post, show the button
    if( $logged_in_user AND $logged_in_user['user_id'] == $post_author ){
        echo "<a href='edit-post.php?post_id=$post_id' class='button edit-post-button' >Edit</a>";
    }
}

/**
 * LIKE BUTTON ADDITIONS
 * Count the likes on any post
 */

function count_likes( $post_id ){
  global $DB;
  $result = $DB->prepare( "SELECT COUNT(*) AS total_likes
            FROM likes
            WHERE post_id = ?" );
  $result->execute( array($post_id) );
  if( $result->rowCount() >= 1 ){
    $row = $result->fetch();
    $total = $row['total_likes'];

    //return the count (ternary operator example)
    //return $total == 1 ? '1 Like' : "$total Likes" ;
    return $total;

  }
}


function like_interface( $post_id ){
    global $DB;
    global $logged_in_user;
    //if the user is logged in, figure out if they like this post
    if( $logged_in_user ){
        //do they like it?
        $result = $DB->prepare('SELECT * FROM likes
                                WHERE user_id = ?
                                AND post_id = ?
                                LIMIT 1');
        $result->execute( array( $logged_in_user['user_id'], $post_id ) );
        $class = '';
        if( $result->rowCount() ){
            $class = 'you-like';
        }else{
            $class = 'not-liked';
        }

    }//end if logged in
    ?>
    <span class="like-interface">
        <span class="<?php echo $class; ?>">      
          <span class="heart-button" data-postid="<?php echo $post_id; ?>">❤</span>
          <?php echo count_likes( $post_id ); ?>
        </span>
    </span>
    <?php
}
//no close php