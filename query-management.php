<?php
/*
Plugin Name: Query Management System
Description: This plugin will help to create the essential tables in database and add functionality to register and login users and showing them the data.
Version: 1.0
Author:  (Abdullah Sarfraz, Ruqyya, Ahmad)
Author URI: http://querymanagement.local/
*/

// Define a function to enqueue your CSS file
function qms_dev_team_my_plugin_enqueue_styles() {
    // Enqueue your CSS file
    wp_enqueue_style('my-plugin-css', plugins_url('/style.css', __FILE__));
}

// Hook the function to the appropriate action
add_action('wp_enqueue_scripts', 'qms_dev_team_my_plugin_enqueue_styles');

function qms_dev_team_create_plugin_pages() {
    
    $pages = array(
        'HR DASHBOARD' => '[hrdashboard_shortcode]',
        'EMPLOYEE' => '[employee_shortcode]',
        'QUERY-FORM' => '[my_queryform_shortcode]',
        'REPLY FORM' => '[replyform_shortcode]',
        'GENERATE REPORTS' => '[reportsystem_shortcode]'
        
    );

    foreach ($pages as $title => $content) {
        // Check if the page doesn't exist already
        if (!get_page_by_title($title)) {
            $new_page = array(
                'post_title'    => $title,
                'post_content'  => $content,
                'post_status'   => 'publish',
                'post_type'     => 'page',
            );

            // Insert the page into the database
            wp_insert_post($new_page);
        }
    }
}


function qms_dev_team_landing_shortcode() {

    if (isset($_POST['loginlandingpage']) ) {
        wp_redirect(home_url('/wp-login.php'));
        exit();
    }

    get_header();

    ?>

    
<div class="full-page-background"></div>
    <div class="login-container">
        <h1>Welcome to Employee Query Management System</h1>
        <p  class= "discription">The Employee Query Management System plugin is a robust solution designed to efficiently handle employee inquiries within your organization. This plugin provides an intuitive interface for employees to submit queries and view their status, while administrators can easily prioritize, resolve, and ensure timely feedback on queries. A centralized dashboard provides an overview of all queries, their statuses, comprehensive analytics and reporting features. Also it allows HR to generate and view reports based on parameters such as query category, date range, or employee name.
        Employee Query Management System plugin simplifies the handling of employee inquiries, leading to improved efficiency and employee satisfaction.
        </p>
        <h3>Click below to Login</h3>


        <form method="post">
            <button type="submit" name="loginlandingpage">Login</button>
        </form>
    </div>


<?php




}

add_shortcode('landing_shortcode', 'qms_dev_team_landing_shortcode');


// Wordpress login page redirection

function qms_dev_team_custom_login_redirect( $redirect_to, $request, $user ) {
    // Check if the user has roles and if the roles array is not empty
    if ( isset( $user->roles ) && is_array( $user->roles ) && ! empty( $user->roles ) ) {
    // Get the current user's role
    $user_role = $user->roles[0];
 
    // Set the URL to redirect users to based on their role
    if ( $user_role == 'subscriber' ) {
        $redirect_to = '/employeedashboard/';
    }elseif ( $user_role == 'editor' ) {
        $redirect_to = '/hrdashboard/';

    }
}
    return $redirect_to;
}
add_filter( 'login_redirect', 'qms_dev_team_custom_login_redirect', 10, 3 );

// DATABASE for Queries
function qms_dev_team_create_table_for_queries_on_activation() {
    global $wpdb;

    // Define the table name with the WordPress prefix
    $table_name = $wpdb->prefix . 'queryform';

    $sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(50) NOT NULL DEFAULT 'Anonymous',
        email varchar(100) NOT NULL DEFAULT 'Anonymous',
		category VARCHAR(100) NOT NULL DEFAULT 'Anonymous',
        priorty VARCHAR(100) NOT NULL DEFAULT 'not defined',
        status VARCHAR(100) NOT NULL DEFAULT 'not defined',
        description TEXT NOT NULL,
        answers TEXT NOT NULL,
        file_path VARCHAR(255) NULL  DEFAULT 'none.jpg',
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (id)
	  );";

  

    // Execute the SQL query to create the voter table
    $wpdb->query($sql);
}

register_activation_hook(__FILE__, 'qms_dev_team_create_table_for_queries_on_activation');

function qms_dev_team_disabled_chatboxes() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'disabledchatboxes';

    $sql = "CREATE TABLE $table_name(
        id int(11) NOT NULL AUTO_INCREMENT,
        email_id varchar(50) NOT NULL,
        queryno varchar(50) NOT NULL,
        identity varchar (100) NOT NULL,
        PRIMARY KEY (id)
    );";

    $wpdb->query($sql);
    
}

register_activation_hook(__FILE__, 'qms_dev_team_disabled_chatboxes');

// DATABASE 
function qms_dev_team_create_table_for_messages_on_activation() {
    global $wpdb;

    
    $table_name = $wpdb->prefix . 'messagingchat';

    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
		email_id varchar(50) NOT NULL ,
		queryno varchar(50) NOT NULL,
        messages TEXT NOT NULL,
		identity varchar(100) NOT NULL, 
		timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
	  );";

    
    $wpdb->query($sql);
}
register_activation_hook( __FILE__, 'qms_dev_team_create_table_for_messages_on_activation' );

//creating shortcode of the Query Form page
function qms_dev_team_querform_shortcode() {

    if(is_user_logged_in() && current_user_can('subscriber')) {
        ob_start();

        $current_user = wp_get_current_user();
    
        // Access user data
        $user_name = $current_user->display_name;
        $user_email = $current_user->user_email;
        
        ?>
    
            <div class="main-form" >
                <form id="myQueryForm" action="<?php echo esc_attr( admin_url('admin-post.php') ); ?>" method="POST">
                    <input type="hidden" name="action" value="<?php echo esc_attr( 'qms_dev_team_save_my_custom_form4' ); ?>" />
                            <br>
                        
                            <label for="name" style="color: #000; font-weight: 600; margin-right: 20px;">Your Name:</label>
                            <label for="nameoutput" style="color: #000; font-weight: 600; margin-right: 20px;"><?php echo $user_name; ?></label>
                                
                            <input type="hidden" id="name" name="name" value="<?php echo $user_name; ?>" />  
                            <br>    
                            <br>   
                            <label for="e1" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 20px;">Email:</label>
                            <label for="e_output" ><?php echo $user_email; ?></label>
                            
                            <input type="hidden" name="email" value="<?php echo $user_email; ?>" />
                            <br>
                            <br> 
                            <label for="category" style="color: #000; font-weight: 600; margin-top: 5px;">Query Category:</label>
                            <select name="category" id="category" style="border: none; outline: none;">
                                <option value="General">General</option>
                                <option value="Technical">Technical</option>
                                <option value="Hardware">Hardware</option>
                                <option value="Software">Software</option>
                            </select>
                            <label for="priority" style="color: #000; font-weight: 600; margin-top: 5px;">Priority:</label>
                            <select name="priority" id="priority" style="border: none; outline: none;">
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                            <br>
                            <br> 
                            <label for="desc" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 285px;">Description:</label>
                            <br>
                            <br> 
                            <textarea id="desc" name="desc" rows="4" cols="50" style=" outline: none;">   
                            </textarea>
                            
                            <br>
                            <br> 
                            <input type="submit" name="querybtn_one" value="Submit Query" style="padding: 8px 25px; border-radius: 14px; color: #fff; background-color: green;">
                            <br>
                        
                </form>

                
                <button class="anonymous">Send a Anonymous Query</button>
            </div>   

        <!-- Second Anonymous Form -->
            <div class="second-form" style=" display: none;">
                <form id="myqueryForm1" action="<?php echo esc_attr( admin_url('admin-post.php') ); ?>" method="POST">
                    <input type="hidden" name="action" value="<?php echo esc_attr( 'qms_dev_team_save_my_custom_form5' ); ?>" />
                    <h2 style="color: blue;">Fill the Anonymous Form</h2>
                    <br>
                        
                        <label for="name" style="color: #000; font-weight: 600; margin-right: 250px;">Employee Name: Anonymous</label>
                        <br>    
                        <input type="hidden" placeholder="Enter your Name" style="width: 26%; margin-top: 5px; border-radius: 8px; padding: 5px 10px;" id="name" name="name" value="anonymous"/>  
                        <br>    
                        <br>   
                        <label for="e1" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 20px;">Email:</label>
                            <label for="e_output" ><?php echo $user_email; ?></label>
                            
                            <input type="hidden" name="email" value="<?php echo $user_email; ?>" />
                        <br>
                        <br>
                
                    <br> 
                    
                    
                    <br>
                    <br> 
                    <label for="category" style="color: #000; font-weight: 600; margin-top: 5px;">Query Category:</label>
                    <select name="category" id="category" style="border: none; outline: none;">
                        <option value="General">General</option>
                        <option value="Technical">Technical</option>
                        <option value="Hardware">Hardware</option>
                        <option value="Software">Software</option>
                    </select>
                    <label for="priority" style="color: #000; font-weight: 600; margin-top: 5px;">Priority:</label>
                    <select name="priority" id="priority" style="border: none; outline: none;">
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                    <br>
                    <br>
                    
                    <label for="desc" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 285px;">Description:</label>
                    <br>
                    <br> 
                    <textarea id="desc" name="desc" rows="4" cols="50" style=" outline: none;">   
                    </textarea>
                    <br>
                    <br> 
                    <input type="submit" name="querybtn_second" value="Submit Query" style="padding: 8px 25px; border-radius: 14px; color: #fff; background-color: green;">
                    <br>

                
                </form>

                <button class="public_btn">Send a public Query</button>
            </div>

            <script>

            const secondDiv = document.getElementsByClassName ('second-form')[0];
            const leftClick = document.getElementsByClassName ('anonymous')[0];
            const firstDiv = document.getElementsByClassName ('main-form')[0];
            const rightClick = document.getElementsByClassName ('public_btn')[0];

            leftClick.addEventListener('click', ()=> {
                
                secondDiv.style.display = 'block';
                firstDiv.style.display = 'none';
            });

            rightClick.addEventListener('click', ()=> {
                secondDiv.style.display = 'none';
                firstDiv.style.display = 'block';

            });

            </script>
        <?php   
        return ob_get_clean();
    }else{
        wp_redirect(home_url('/wp-login.php'));
    }
    
}

add_shortcode('my_queryform_shortcode', 'qms_dev_team_querform_shortcode');

//Query Form Submittion with name
function qms_dev_team_save_my_custom_form4() {
	global $wpdb;
    $table_name = $wpdb->prefix . 'queryform';

	$name = $_POST['name'];
    $email = $_POST['email'];
    $category = $_POST['category'];
    $priority = ($_POST['priority']);
    $desc = $_POST['desc'];
    $myfile = $_POST['myfile'];
    

            $check = $wpdb->insert(
                $table_name,
                $data = array(
                    'name' => $name,
                    'email' => $email,
                    'category' => $category,
                    'priorty' => $priority,
                    'description' => $desc,
                    'file_path' => $myfile,

                ),
                array( '%s', '%s', '%s', '%s','%s', '%s', '%s', )
            );

            if ($check) {

                echo "<script>alert('Your Query Submitted !'); window.location.href = '" . site_url('/employeedashboard') . "';</script>";
                exit;

            } else {

                echo "<script>alert('Data not inserted: " . $wpdb->last_error . "')</script>";
                $wpdb->print_error();
            }        
            
        
                }
add_action( 'admin_post_nopriv_qms_dev_team_save_my_custom_form4', 'qms_dev_team_save_my_custom_form4' );
add_action( 'admin_post_qms_dev_team_save_my_custom_form4', 'qms_dev_team_save_my_custom_form4' );


//Query Form Submittion without name
function qms_dev_team_save_my_custom_form5() {
	global $wpdb;
    $table_name = $wpdb->prefix . 'queryform';

	$name = $_POST['name'];
    $email = $_POST['email'];
    $category = $_POST['category'];
    $priority = ($_POST['priority']);
    $desc = $_POST['desc'];
    $myfile = $_POST['myfile'];
    

            $check = $wpdb->insert(
                        $table_name,
                        $data = array(
                           
                            'name' => $name,
                            'email' => $email,
                            'category' => $category,
                            'priorty' => $priority,
                            'description' => $desc,
                            'file_path' => $myfile,
    
                        ),
                        array( '%s', '%s', '%s', '%s', '%s', '%s' )
                        );

                    if ($check) {

                        echo "<script>alert('Your Query Submitted !'); window.location.href = '" . site_url('/query-form') . "';</script>";
                        exit;

                    } else {

                        echo "<script>alert('Data not inserted: " . $wpdb->last_error . "')</script>";
                        $wpdb->print_error();
                    }        
            
        
                }
add_action( 'admin_post_nopriv_qms_dev_team_save_my_custom_form5', 'qms_dev_team_save_my_custom_form5' );
add_action( 'admin_post_qms_dev_team_save_my_custom_form5', 'qms_dev_team_save_my_custom_form5' );


//creating shortcode for the Employee Dashboard
function qms_dev_team_employee_shortcode() {

    if(is_user_logged_in() && current_user_can('subscriber')) {

        ob_start();


        $current_user = wp_get_current_user();
    
        // Access user data
        $user_name = $current_user->display_name;
        $user_email = $current_user->user_email;
        $user_id = get_current_user_id();
    
        // Display user data
        $live_user = $user_email;
    
                                                       
    ?>
        <div class="top-header" style=" background-color: green; padding-top: 10px; padding-bottom: 10px;">
        
            <?php echo '<h3 style="font: bold; color: white; ">  > '. $user_name . '</h3>';
                echo '<h3 style="font: bold; color: white; ">  > '. $user_email . '</h3>';
            // Check if the user is logged in
        
            // Check if the user meta data exists
            if ($user_name && $user_email) {
                // Display user data
                $live_user = $user_email;
            }
                            ?>
                <h3 style=" color: #fff; font-size: 16px; text-align: center; font-family: 'oswald', sans-serif;">Employee Queries Portal</h3>
        </div>
            
        <div class="emp-queries">
            <div style=" margin-top: 30px; text-align: center; margin-left: 1000px;">
                <form method="post" >
                    <button type="submit" name="logout" style="color: #fff; text-decoration: none; padding: 5px 10px; background-color: purple; border-radius: 14px; border: none; outline: none;">Logout</button>
                </form>    
            </div>
          
            <?php
                if (isset($_POST['logout']) ) {
                    wp_redirect(home_url('/wp-login.php'));
                    exit();
                }
            ?>
            <div class="query-btn" style=" margin-top: 30px; text-align: center; margin-right: 1000px;">
                <a href="<?php echo esc_url( site_url( '/query-form' ) ); ?>" style="color: #fff; text-decoration: none; padding: 5px 10px; background-color: purple; border-radius: 14px; border: none; outline: none;">Create a Query</a>
            </div>
            <br>
    
            <form id="date-filter-form" method="get" action="<?php echo esc_url( site_url( '/query-page' ) ); ?>">
                <label for="date" style="color: #000; font-weight: 600; margin-right: 20px;">Starting Date:</label>
                <input type="date" name="" id="" placholder="Starting Date">
                <label for="date" style="color: #000; font-weight: 600; margin-right: 20px; margin-left: 50px;">Ending Date:</label>
                <input type="date" name="" id="" placholder="Ending Date">                           
        
                <button type="submit" name="filter_queries" style="color: #fff; text-decoration: none; padding: 5px 10px; background-color: purple; border-radius: 14px; border: none; outline: none; margin-left: 50px;">Filter</button>
            </form>
    
    
    
            <div class="queries-table">
                <h4 style="font-size: 32px; text-align: center; font-family: 'oswald', sans-serif;">Your Queries</h4>
                <br>
                <br>
                <table style=" font-size: 18px; font-family: 'oswald', sans-serif; border-collapse: collapse; width: 83%; margin-left: 110px;">
                    <tr>
                      <th name="id" style="border: 1px solid skyblue; background-color: skyblue;  color: #fff; padding-top: 5px; padding-right: 2px;">ID</th>         
                      <th name="category" style="border: 1px solid skyblue; background-color: skyblue;  color: #fff; padding-top: 5px; padding-right: 2px;">category</th>
                      <th name="prty" style="border: 1px solid skyblue; background-color: skyblue;  color: #fff; padding-top: 5px; padding-right: 2px;">Priority</th>
                      <th name="des" style="border: 1px solid skyblue; background-color: skyblue;  color: #fff; padding-top: 5px; padding-right: 2px;">Description</th>
                      <th name="status" style="border: 1px solid skyblue; background-color: skyblue;  color: #fff; padding-top: 5px; padding-right: 2px;">Status</th>        
                      <th name="chat" style="border: 1px solid skyblue; background-color: skyblue;  color: #fff; padding-top: 5px; padding-right: 2px;">Chat</th>    
                    </tr>
                        <?php
    
                            global $wpdb;
                            $table_name = $wpdb->prefix . 'queryform';
    
                            
    
                            // Using $wpdb->prepare to safely insert the variable into the query
                            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE email = %s", $live_user);
                            // Fetch data using $wpdb with Array accociate pattern means
                            // that the result will be an associative array where the column names are used as keys.
                            $rows = $wpdb->get_results($query, ARRAY_A);
    
                            foreach ($rows as $row) {
                                
                                $id = $row["id"];
                                $category = $row["category"];
                                $description =  wp_trim_words( $row['description'], 8 ) ;
                                $answers = $row["answers"];
                                $status = $row["status"];
                                $priorty = $row["priorty"];
                                $user_type = 'employee';
                            
                                echo "
                                    <tr>
                                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$id</th>
                                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$category</th>
                                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$priorty</th>
                                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$description</th>
                                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$status</th>
                                    
                                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>
                                        <a href='/replyform?id=$id&type=$user_type' >followUp</a>
                                    </th>
                                    
                                    </tr>
                                ";       
                                    
                            }
    
    
    
    
            ?>           
                </table>
            </div>
    
            
        </div>
    
        <?php
        return ob_get_clean();
    }else{
        wp_redirect(home_url('/wp-login.php'));
    }

    
}

add_shortcode('employee_shortcode', 'qms_dev_team_employee_shortcode');

// shortcode for the HR Dashboard page
function qms_dev_team_hrdashboard_shortcode() {

    if(is_user_logged_in() && current_user_can('editor')) {

        get_header();

        ob_start();

        global $wpdb;
        $table_name = $wpdb->prefix . 'queryform';
    
        $total = "SELECT COUNT(*) FROM $table_name";
        $total_tickets = $wpdb->get_var($total);
    
        $status = "not defined";
        $opened = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE status= %s", $status);
        $opened_tickets = $wpdb->get_var($opened);
    
        $answer = "";
        $answered = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE answers = %s", $answer);
        $not_answered = $wpdb->get_var($answered);
        $answered_tickets = $total_tickets - $not_answered;
    
        $pend = "Pending";
        $pending = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE status= %s", $pend);
        $pending_tickets = $wpdb->get_var($pending);
    
        $decl = "Declined";
        $decline = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE status= %s", $decl);
        $decline_tickets = $wpdb->get_var($decline);
    
        $proc = "In Process";
        $process = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE status= %s", $proc);
        $process_tickets = $wpdb->get_var($process);
    
        ?>
        <div class="head-section">
            <div class="hr-main">
                <div class="total-tickets" style=" padding: 10px 20px; margin-left: 100px;">
                  <label style="color:black ;">Total Tickets</label>
                    <br>
                    <label style="padding: 15px 30px; color:blue;" for="" value="35" name="35"><?php echo $total_tickets; ?></label>  
                </div>

                <div class="open-tickets" style="margin-left: 10px; padding: 10px 10px;">   
                    <label style="color:black;">Open/New Tickets</label>
                    <br>
                    <label style="padding: 25px 50px; color:brown;" for="" value="35" name="35"><?php echo $opened_tickets; ?></label>
                </div>

                <div class="answered" style="margin-left: 10px; padding: 10px 10px;"> 
                    <label style="color:black;">Answered </label>
                    <br>
                    <label style="padding: 5px 20px; color:green; " for="" value="35" name="35"><?php echo $answered_tickets; ?></label>
                </div>
                
                <div class="pending" style="margin-left: 10px; padding: 10px 10px;">  
                    <label style="color:black;">Pending</label>
                    <br>
                    <label style="padding: 5px 20px; color:2a0d0d;" for="" value="35" name="35"><?php echo $pending_tickets; ?></label>
                </div>

                <div class="declined" style="margin-left: 10px; padding: 10px 20px;">  
                    <label style="color:black;">Declined</label>
                    <br>
                    <label style="padding: 5px 20px; color:red;" for="" value="35" name="35"><?php echo $decline_tickets; ?></label>
                </div>

                <div class="process" style="margin-left: 10px; padding: 10px 20px;">                   
                    <label style="color:black;">In Process</label>
                    <br>
                    <label style="padding: 5px 20px; color:black;" for="" value="35" name="35"><?php echo $process_tickets; ?></label>
                </div>
            </div>
    
            <div style=" margin-top: 45px; text-align: center; margin-left: 400px;">
                <form method="post" >
                   <div class="hr-logout" >
                        <button type="submit" name="hr-logout" class="hr-logout" style="padding: 10px 10px;  border-radius: 18px; border: none; outline: none;" >Logout</button>
                    </div>
                    <br>
                </form>  

                <?php
                    // Check if the logout parameter is present in the URL
                    if (isset($_POST['hr-logout']) && $_POST['hr-logout'] == 1) {
                    // Call the WordPress logout function
                    wp_logout();
                
                    // Redirect to the login page
                    wp_redirect(home_url('/wp-login.php'));
                
                    // Exit to stop further execution
                    exit();
                }
    
    
    
                if (isset($_POST['logout']) ) {
                    wp_redirect(home_url('/wp-login.php'));
                    exit();
                }
                ?>   

                <a href="/reportingsystem" style="color: black; text-decoration: none; padding: 5px 10px; background-color: rgb(194, 207, 231); border-radius: 18px; border: none; outline: none;">View and print Reports</a>  
                <br>        
            </div>
            
        </div>
    
        <br><br><br><br>
        
        <table style="font-size: 18px; font-family: 'oswald', sans-serif; border-collapse: collapse; width: 88%; margin-left: 70px; ">
            <tr>
              <th name="name" style=" border: 1px solid rgb(7, 7, 61); background-color: rgb(7, 7, 61); color: #fff; padding-top: 5px; padding-right: 2px;">Name</th>
              <th name="email" style=" border: 1px solid rgb(7, 7, 61); background-color: rgb(7, 7, 61); color: #fff; padding-top: 5px; padding-right: 2px;">Email</th>
              <th name="details" style=" border: 1px solid rgb(7, 7, 61); background-color: rgb(7, 7, 61); color: #fff; padding-top: 5px; padding-right: 2px;">Details</th>
              <th name="category" style=" border: 1px solid rgb(7, 7, 61); background-color: rgb(7, 7, 61); color: #fff; padding-top: 5px; padding-right: 2px;">Category</th>
              <th name="status" style=" border: 1px solid rgb(7, 7, 61); background-color: rgb(7, 7, 61); color: #fff; padding-top: 5px; padding-right: 2px;">Status</th>
              <th name="priority" style=" border: 1px solid rgb(7, 7, 61); background-color: rgb(7, 7, 61); color: #fff; padding-top: 5px; padding-right: 2px;">Priority</th>
              <th name="update" style=" border: 1px solid rgb(7, 7, 61); background-color: rgb(7, 7, 61); color: #fff; padding-top: 5px; padding-right: 2px;">Update</th>
            </tr>
    
            <?php
    
                global $wpdb;
                $table_name = $wpdb->prefix . 'queryform';

                // Your SQL query here
                $query = "SELECT * FROM $table_name";

                // Use WP_Query to get the total number of rows
                $total_rows = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

                $rows_per_page = 10; // You can change this to the desired number
                $total_pages = ceil($total_rows / $rows_per_page);
                $current_page = get_query_var('paged') ? get_query_var('paged') : 1;
                $offset = ($current_page - 1) * $rows_per_page;

                // Add LIMIT and OFFSET to your SQL query
                $query .= " LIMIT $rows_per_page OFFSET $offset";

                // Fetch data using WP_Query
                $rows = $wpdb->get_results($query, ARRAY_A);

                

                foreach ($rows as $row) {

                    $queryId = $row['id'];
                    $name = $row["name"];
                    $email = $row["email"];
                    $category = $row["category"];
                    $description =  wp_trim_words( $row['description'], 5 ) ;
                    $answers =  wp_trim_words( $row['answers'], 5 ) ;
                    $status = $row["status"];
                    $priorty = $row["priorty"];
                    $user_type = 'hr';

                    if($name == "anonymous") {
                            echo "
                            <tr>
                            <th color: #000; padding-top: 5px; padding-right: 2px;'>Anonymous</th>
                            <th color: #000; padding-top: 5px; padding-right: 2px;'>Anonymous</th>
                            <th color: #000; padding-top: 5px; padding-right: 2px;'>$description</th>
                            <th color: #000; padding-top: 5px; padding-right: 2px;'>$category</th>
                            <th color: #000; padding-top: 5px; padding-right: 2px;'>$status</th>
                            <th color: #000; padding-top: 5px; padding-right: 2px;'>$priorty</th>
                            <th color: #000; padding-top: 5px; padding-right: 2px;'>
                                <a href='/replyform?id=$queryId&type=$user_type'>Update</a>
                            </tr>
                        "; 
                    }else{
                        echo "
                        <tr>
                        <th  color: #000; padding-top: 5px; padding-right: 2px;'>$name</th>
                        <th  color: #000; padding-top: 5px; padding-right: 2px;'>$email</th>
                        <th  color: #000; padding-top: 5px; padding-right: 2px;'>$description</th>
                        <th  color: #000; padding-top: 5px; padding-right: 2px;'>$category</th>
                        <th  color: #000; padding-top: 5px; padding-right: 2px;'>$status</th>
                        <th  color: #000; padding-top: 5px; padding-right: 2px;'>$priorty</th>
                        <th  color: #000; padding-top: 5px; padding-right: 2px;'>
                            <a href='/replyform?id=$queryId&type=$user_type'>Update</a>
                        </tr>
                        "; 
                    }
                
                    

                        
                        
                } ?>
        </table>
    
          <?php     
    
                echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '?paged=%#%',
                    'prev_text' => __('&laquo; Previous'),
                    'next_text' => __('Next &raquo;'),
                    'total' => $total_pages,
                    'current' => $current_page,
                ));
    
                return ob_get_clean();
    }else{
        wp_redirect(home_url('/wp-login.php'));
    }

    
                        
}

add_shortcode('hrdashboard_shortcode', 'qms_dev_team_hrdashboard_shortcode');


// shortcode for the HR Update form Page
function qms_dev_team_replyform_shortcode() {

    if(is_user_logged_in() && current_user_can('subscriber') || current_user_can('editor')) {

        ob_start();

        $test_id = isset($_GET['id']) ? $_GET['id'] : '';
        $user_type = isset($_GET['type']) ? $_GET['type'] : '';
        
        
        
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'queryform';
    
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $test_id); //prepare
        $rows = $wpdb->get_results($query);
    
        foreach ($rows as $row) {
            $name = $row->name;
            $email = $row->email;
            $category = $row->category;
            $priorty = $row->priorty;
            $status = $row->status;
            $description = $row->description;
            
        }
        ?>
        <div class="main_form">
            <form id="updateform" action="<?php echo esc_attr(admin_url('admin-post.php')); ?>" method="POST">
                <input type="hidden" name="action" value="<?php echo esc_attr('qms_dev_team_save_my_custom_form9'); ?>" />
                <br>
                <label for="name" style="color: #000; font-weight: 600; margin-right: 20px;">Employee Name:</label>
                <label for="name" style="color: #000; font-weight: 600; margin-right: 250px;"><?php echo $name; ?></label>
                <input type="hidden" name="name" value="<?php echo $name; ?>">
                <br>
                <br>
                <label for="e1" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 20px;">Email:</label>
                <label for="e1" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 330px;"><?php echo $email; ?></label>
                <input type="hidden" name="email" value="<?php echo $email; ?>">
    
                
                <br>
                <br>
                <label for="category" style="color: #000; font-weight: 600; margin-top: 5px;">Query Category:</label>
                <select name="category" id="category" style="border: none; outline: none;">
                    <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                    <option value="General">General</option>
                    <option value="Technical">Technical</option>
                    <option value="Hardware">Hardware</option>
                    <option value="Software">Software</option>
                </select>
                <label for="priority" style="color: #000; font-weight: 600; margin-top: 5px;">Priority:</label>
                <select name="priority" id="priority" style="border: none; outline: none;">
                    <option value="<?php echo $priorty; ?>"><?php echo $priorty; ?></option>
                    <option value="High">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low">Low</option>
                </select>
                <br>
                <br>
                <label for="status" style="color: #000; font-weight: 600; margin-top: 5px;">Status:</label>
                <select name="status" id="status" style="border: none; outline: none;">
                    <option value="<?php echo $status; ?>"><?php echo $status; ?></option>
                    <option value="Pending">Pending</option>
                    <option value="Declined">Declined</option>
                    <option value="In Process">In Process</option>
                    
                </select>
                <br>
                <br>
                <label for="desc" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 285px;">Description:</label>
                <br>
                <br>
                
                <label for="desc" style="color: #000; border: 1px solid #000;  padding :10px 10px;"><?php echo $description; ?></label>
                <input type="hidden" name="desc" value="<?php echo $description; ?>">
                <br>
               
                
                <br>
                <input type="hidden" name="id" value="<?php echo $test_id; ?>">
                <input type="submit" name="reply_querybtn_one" value="Update Status"
                style="padding: 8px 25px; border-radius: 14px; color: #fff; background-color: green;">
                <br>
            </form>
        </div>
    
        <?php
    
            global $wpdb;
            $table_name = $wpdb->prefix . 'messagingchat';
    
            
            
    
            $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE email_id = %s AND queryno = %s ORDER BY timestamp ASC", $email, $test_id );
            
            // Execute the SQL query
            $results = $wpdb->get_results($sql, ARRAY_A);
            
            // Check if there are any results
            if ($results) {
                // Loop through the results to process each message
                foreach ($results as $row) {
                    
                    $message_content = $row['messages'];
                    $identity = $row['identity'];
                    
            
                    if($identity == 'hr') {
                        echo '<h5 style=" font: bold; ">HR : </h5>';
                        echo $message_content;
                    }elseif($identity == 'employee') {
                        echo '<h5 style=" font: bold; ">Employee : </h5>';
                        echo $message_content;
                    }else{
                        echo 'User type is not defined!';
                    }
                }
                
            } else {
                echo '<br>Start Conversation.'; // Display a message if no matching messages are found
            }
    
        ?>
    
    
        <?php
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'disabledchatboxes';
    
        
        $data_closed = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE email_id = %s AND queryno = %s AND identity = %s", $email, $test_id, $user_type);
        $closed_query = $wpdb->get_var($data_closed);
    
        if($closed_query>0){
            echo '<br><br><br><h3>Query Closed from your Side</h3>';
        }else{
            ?>
    
            <!-- message form -->
                <form id="messageform" action="<?php echo esc_attr( admin_url('admin-post.php') ); ?>" method="POST">
                    <input type="hidden" name="action" value="<?php echo esc_attr( 'qms_dev_team_messageformfu' ); ?>" />
                    
                    <br>
                            <br> 
                            <label for="desc" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 285px;">Reply:</label>
                            <br>
                            <br> 
                            <textarea id="desc" name="messagetosend" rows="4" cols="50" style=" outline: none;">   
                            </textarea>
                    <br>
                            <input type="hidden" name="mail_id" value="<?php echo $email; ?>">
                            <input type="hidden" name="id" value="<?php echo $test_id; ?>">
                            <input type="hidden" name="type" value="<?php echo $user_type; ?>">
                    <br> 
                    <input type="submit" name="msgbtn_one" value="Send" style="padding: 8px 25px; border-radius: 14px; color: #fff; background-color: green;">
                    <br>
                </form>
    
                <form id="closingbtnform" action="<?php echo esc_attr( admin_url('admin-post.php') ); ?>" method="POST">
                        <input type="hidden" name="action" value="<?php echo esc_attr( 'qms_dev_team_closing_query' ); ?>" />
                        
                        
                                <input type="hidden" name="mail_id" value="<?php echo $email; ?>">
                                <input type="hidden" name="id" value="<?php echo $test_id; ?>">
                                <input type="hidden" name="type" value="<?php echo $user_type; ?>">
                        <br> 
                        <input type="submit" name="msgbtn_two" value="Close the Query" style="padding: 8px 25px; border-radius: 14px; color: #fff; background-color: green;">
                        <br>
                </form>
            <?php
        }
        
        return ob_get_clean();
    }else{
        wp_redirect(home_url('/wp-login.php'));
    }


}

add_shortcode('replyform_shortcode', 'qms_dev_team_replyform_shortcode');

function qms_dev_team_closing_query() {
    $test_id = isset($_POST['id']) ? intval($_POST['id']) : 0; // query number 


    global $wpdb;
    $table_name = $wpdb->prefix . 'disabledchatboxes';

    
    $email = sanitize_text_field($_POST['mail_id']);
    $user_type = sanitize_text_field($_POST['type']); // identity

    $check = $wpdb->insert(
        $table_name,
         array(
            'email_id' => $email,
            'queryno' => $test_id,
            'identity' => $user_type,
        
        ),
        
        array( '%s', '%d', '%s' )
    );

    if ($check) {

        if($check && $user_type == 'employee') {
            echo "<script>alert('Query Closed from your Side !'); window.location.href = '" . site_url("/replyform/?id=$test_id&type=employee") . "';</script>";
            exit;
        }elseif($check && $user_type == 'hr') {
            echo "<script>alert('Query Closed from your Side !'); window.location.href = '" . site_url("/replyform/?id=$test_id&type=hr") . "';</script>";

            exit;
        }else {
            echo "<script>alert('Query not closed, go to dashboard and comeback here again: " . $wpdb->last_error . "')</script>";
            $wpdb->print_error();
        }


    } else {

        echo "<script>alert('Query not closed, go to dashboard and comeback here again: " . $wpdb->last_error . "')</script>";
        $wpdb->print_error();
    }


}
add_action('admin_post_nopriv_qms_dev_team_closing_query', 'qms_dev_team_closing_query');
add_action('admin_post_qms_dev_team_closing_query', 'qms_dev_team_closing_query');

function qms_dev_team_messageformfu() {
    $test_id = isset($_POST['id']) ? intval($_POST['id']) : 0; // query number 


    global $wpdb;
    $table_name = $wpdb->prefix . 'messagingchat';

    
    $email = sanitize_text_field($_POST['mail_id']);
    $messagetosend = sanitize_text_field($_POST['messagetosend']);
    $user_type = sanitize_text_field($_POST['type']); // identity
    

            $check = $wpdb->insert(
                $table_name,
                 array(
                    'email_id' => $email,
                    'queryno' => $test_id,
                    'messages' => $messagetosend,
                    'identity' => $user_type,
                    

                ),
                
                array( '%s', '%d', '%s', '%s' ),
                
                
            );

            if ($check) {

                if($check && $user_type == 'employee') {
                    echo "<script>alert('Your reply sent !'); window.location.href = '" . site_url("/replyform/?id=$test_id&type=employee") . "';</script>";
                    exit;
                }elseif($check && $user_type == 'hr') {
                    echo "<script>alert('Your reply sent !'); window.location.href = '" . site_url("/replyform/?id=$test_id&type=hr") . "';</script>";

                    exit;
                }else {
                    echo "<script>alert('Data not inserted: " . $wpdb->last_error . "')</script>";
                    $wpdb->print_error();
                }


            } else {

                echo "<script>alert('Data not inserted: " . $wpdb->last_error . "')</script>";
                $wpdb->print_error();
            }

}
add_action('admin_post_nopriv_qms_dev_team_messageformfu', 'qms_dev_team_messageformfu');
add_action('admin_post_qms_dev_team_messageformfu', 'qms_dev_team_messageformfu');

// Query Form Updation
function qms_dev_team_save_my_custom_form9() {
    $test_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    global $wpdb;
    $table_name = $wpdb->prefix . 'queryform';

    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_text_field($_POST['email']);
    $category = sanitize_text_field($_POST['category']);
    $priority = sanitize_text_field($_POST['priority']);
    $desc = sanitize_textarea_field($_POST['desc']);
    $myfile = sanitize_text_field($_POST['myfile']);
    $status = sanitize_text_field($_POST['status']);
    $answers = sanitize_textarea_field($_POST['answers']);

    $check = $wpdb->update(
        $table_name,
        array(
            'name' => $name,
            'email' => $email,
            'category' => $category,
            'priorty' => $priority,
            'status' => $status,
            'description' => $desc,
            'answers' => $answers,
            'file_path' => $myfile,
        ),
        array('id' => $test_id),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),
        array('%d') // format for the WHERE condition (id is an integer, so use %d)
    );

    if ($check !== false) {
        
        echo "<script>alert('Your Query Updated!'); window.location.href = '" . site_url('/hrdashboard') . "';</script>";
        exit;
    } else {
        echo "<script>alert('Data not updated: " . $wpdb->last_error . "')</script>";
        $wpdb->print_error();
    }
}

add_action('admin_post_nopriv_qms_dev_team_save_my_custom_form9', 'qms_dev_team_save_my_custom_form9');
add_action('admin_post_qms_dev_team_save_my_custom_form9', 'qms_dev_team_save_my_custom_form9');


function qms_dev_team_reportsystem_shortcode() {

    if(is_user_logged_in() && current_user_can('editor')) {

        ob_start();
        ?>
    
        <h2>Select options to generate a report</h2><br><br>
        <form id="myRegisterationForm" action="<?php echo esc_attr( admin_url('admin-post.php') ); ?>" method="POST">
            <input type="hidden" name="action" value="<?php echo esc_attr( 'qms_dev_team_save_my_custom_form8' ); ?>" />
            <div class="test" style="display:flex; gap:10px;">
                <div>
                    <label for="name" style=" font-weight: bold;">Name:</label><br><br>
                    <input type="text" name="name" id="name" style=" outline: none;"></input><br><br>
                </div>
        
                <div>
                    <label for="email" style=" font-weight: bold;">Email:</label><br><br>
                    <input type="email" name="email" id="email" style=" outline: none;"><br><br>
                </div>
        
                <div>
                    <label for="category" style=" font-weight: bold;">Category:</label><br><br>
                    <select name="category" id="category" style=" outline: none;">
                        <option value=""></option>
                        <option value="General">General</option>
                        <option value="Technical">Technical</option>
                        <option value="Hardware">Hardware</option>
                        <option value="Software">Software</option>
                    </select><br><br>
                </div>
        
                <div>
                    <label for="priority" style=" font-weight: bold;">Priority:</label><br><br>
                    <select name="priority" id="priority" style=" outline: none;">
                        <option value=""></option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select><br><br>
                </div>
        
                <div>
                    <label for="status" style=" font-weight: bold;">Status:</label><br><br>
                    <select name="status" id="status" style=" outline: none;">
                        <option value=""></option>
                        <option value="Pending">Pending</option>
                        <option value="Declined">Declined</option>
                        <option value="In Process">In Process</option>
                    </select>
                </div>
        
                <div>
                    <button class="reportgenerating" type="submit" style="color: #fff; text-decoration: none; margin-left:150px; margin-top:15px; padding: 5px 10px; background-color: purple; border-radius: 14px; border: none; outline: none;">Generate Report</button>
                </div>
            </div>
            
        
        </form>
        
            <?php
        return ob_get_clean();
    }else{
        wp_redirect(home_url('/wp-login.php'));
    }

}
add_shortcode('reportsystem_shortcode', 'qms_dev_team_reportsystem_shortcode');

function qms_dev_team_save_my_custom_form8() {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $category = $_POST['category'];
    $priority = ($_POST['priority']);
    $status = $_POST['status'];
?>
    
    <div class="reportclass">
        <br>
        <br>
        <table style="font-size: 18px; font-family: 'oswald', sans-serif; border-collapse: collapse; width: 88%; margin-left: 70px;">
            <tr>
            <th name="name" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Name</th>
            <th name="email" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Email</th>
            <th name="category" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Category</th>
            <th name="status" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Status</th>
            <th name="priority" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Priority</th>
            <th name="update" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Update</th>
            </tr>

            <?php

            global $wpdb;
            $table_name = $wpdb->prefix . 'queryform';

            $conditions = array();

            if (!empty($name)) {
                $conditions[] = $wpdb->prepare("name = %s", $name);
            }
            if (!empty($email)) {
                $conditions[] = $wpdb->prepare("email = %s", $email);
            }
            if (!empty($category)) {
                $conditions[] = $wpdb->prepare("category = %s", $category);
            }
            if (!empty($priority)) {
                $conditions[] = $wpdb->prepare("priorty = %s", $priority);
            }
            if (!empty($status)) {
                $conditions[] = $wpdb->prepare("status = %s", $status);
            }

            $where_clause = implode(' AND ', $conditions);

            $query = "SELECT * FROM $table_name";
            if(!empty($where_clause)) {
                $query .= " WHERE $where_clause";
            }
            
            $rows = $wpdb->get_results($query, ARRAY_A);

            

            foreach ($rows as $row) {

                $queryId = $row['id'];
                $name = $row["name"];
                $email = $row["email"];
                $category = $row["category"];
                $status = $row["status"];
                $priorty = $row["priorty"];
                $user_type = 'hr';
            
                echo "
                    <tr>
                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$name</th>
                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$email</th>
                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$category</th>
                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$status</th>
                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$priorty</th>
                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>
                        <a href='/replyform?id=$queryId&type=$user_type'>Update</a>
                    </tr>
                ";                  
            } ?>
        </table>
    </div>


    <script>
    function printTable() {
        window.print();
    }
    </script>

    <!-- Button to trigger printing -->
    <button style="color: #fff; text-decoration: none; margin-left:150px; margin-top:15px; padding: 5px 10px; background-color: purple; border-radius: 14px; border: none; outline: none;" onclick="printTable()">Print/Download Table</button>
    <?php
}
add_action('admin_post_nopriv_qms_dev_team_save_my_custom_form8', 'qms_dev_team_save_my_custom_form8');
add_action('admin_post_qms_dev_team_save_my_custom_form8', 'qms_dev_team_save_my_custom_form8');
?>

















