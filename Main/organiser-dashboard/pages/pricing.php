<?php
session_start();
include('../Database/config.php'); // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../seeker/seekerlogin.html');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch basic user details from the 'wurkify_user' table
$sql_user = "SELECT username, email FROM `wurkify_user` WHERE user_id = ? AND role = 'organizer'";
if ($stmt_user = $conn->prepare($sql_user)) {
    $stmt_user->bind_param("i", $user_id);
    if ($stmt_user->execute()) {
        $result_user = $stmt_user->get_result();

        if ($result_user->num_rows === 1) {
            $user = $result_user->fetch_assoc();

            // Fetch profile picture from the 'organiser_profile_pictures' table
            $sql_picture = "SELECT file_name FROM organiser_profile_pictures WHERE user_id = ?";
            if ($stmt_picture = $conn->prepare($sql_picture)) {
                $stmt_picture->bind_param("i", $user_id);
                if ($stmt_picture->execute()) {
                    $result_picture = $stmt_picture->get_result();

                    // Check if a profile picture is set
                    if ($result_picture->num_rows === 1) {
                        $picture = $result_picture->fetch_assoc();
                        $profile_picture = $picture['file_name'] 
                            ? '../organiser_photos/' . $picture['file_name'] 
                            : '../default.jpeg';
                    } else {
                        // Use default profile picture if none is found
                        $profile_picture = '../default.jpeg';
                    }
                } else {
                    echo 'Error executing profile picture query: ' . $stmt_picture->error;
                    exit();
                }
                $stmt_picture->close();
            } else {
                echo 'Error preparing profile picture query: ' . $conn->error;
                exit();
            }

            // Set user details
            $username = $user['username'];
            $email = $user['email'];
        } else {
            echo 'User not found';
            exit();
        }

        $stmt_user->close();
    } else {
        echo 'Error executing user query: ' . $stmt_user->error;
        exit();
    }
} else {
    echo 'Error preparing user details query: ' . $conn->error;
    exit();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/all.min.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
    <link
      href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;500&amp;display=swap"
      rel="stylesheet"
    />
    <title>Plans</title>
  </head>
  <body>
    <div class="page-content">
      <div class="sidebar">
        <div class="brand">
          <i class="fa-solid fa-xmark xmark"></i>
          <h3><?php echo htmlspecialchars($user['username']); ?></h3>
        </div>
        <ul>
                <li><a href="../index.php" class="sidebar-link"><i class="fa-solid fa-tachometer-alt fa-fw"></i><span>Dashboard</span></a></li>
                <li><a href="./Profile.php" class="sidebar-link"><i class="fa-solid fa-user fa-fw"></i><span>Profile</span></a></li>
                <li><a href="./events.php" class="sidebar-link"><i class="fa-solid fa-calendar-day fa-fw"></i><span>Events</span></a></li>
                <li><a href="./eventstatus.php" class="sidebar-link"><i class="fa-solid fa-calendar-check fa-fw"></i><span>Event Status</span></a></li>
                <li><a href="../Applicants.php" class="sidebar-link"><i class="fa-solid fa-credit-card fa-fw"></i><span>Applicants</span></a></li>
                <li><a href="./pricing.php" class="sidebar-link"><i class="fa-solid fa-tags fa-fw"></i><span>Pricing</span></a></li>
                <li><a href="./feedback.php" class="sidebar-link"><i class="fa-solid fa-comment-dots fa-fw"></i><span>Feedback</span></a></li>
                <li><a href="./settings.php" class="sidebar-link"><i class="fa-solid fa-cog fa-fw"></i><span>Settings</span></a></li>
            </ul>
      </div>
      <main>
    <div class="header">
        <i class="fa-solid fa-bars bar-item"></i>
        <div class="search">
            <input type="search" placeholder="Type A Keyword" />
        </div>

        <div class="profile">
            <span class="bell"><i class="fa-regular fa-bell fa-lg"></i></span>
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="No Image" style="border-radius: 50%;" />
        </div>
    </div>

    <div class="main-content">
        <div class="title">
            <h1>Plans</h1>
        </div>

        <div class="plans-boxes">
            <!-- Free Plan -->
            <div class="plan-box">
                <div class="plan-title-container">
                    <div class="plan-title">
                        <h2>Free</h2>
                        <p><span>$</span> 0.00</p>
                    </div>
                </div>
                <ul>
                    <li>
                        <div><i class="fa-solid fa-check"></i><span>Post 3 Jobs</span></div>
                        <i class="fa-solid fa-circle-info help"></i>
                    </li>
                    <li>
                        <div><i class="fa-solid fa-check"></i><span>Access to Seeker Profiles</span></div>
                        <i class="fa-solid fa-circle-info help"></i>
                    </li>
                    <li>
                        <div><i class="fa-solid fa-xmark red"></i><span>No Featured Job Listings</span></div>
                        <i class="fa-solid fa-circle-info help"></i>
                    </li>
                    <li>
                        <div><i class="fa-solid fa-xmark red"></i><span>No Priority Support</span></div>
                        <i class="fa-solid fa-circle-info help"></i>
                    </li>
                    <!-- <li>
                        <a href="/#">Join</a>
                    </li> -->
                </ul>
            </div>

            <!-- Basic Plan -->
            <div class="plan-box">
                <div class="plan-title-container">
                    <div class="plan-title">
                        <h2>Basic</h2>
                        <p><span>$</span> 9.99 / month</p>
                    </div>
                </div>
                <ul>
                    <li>
                        <div><i class="fa-solid fa-check"></i><span>Post 10 Jobs</span></div>
                        <i class="fa-solid fa-circle-info help"></i>
                    </li>
                    <li>
                        <div><i class="fa-solid fa-check"></i><span>Access to Seeker Profiles</span></div>
                        <i class="fa-solid fa-circle-info help"></i>
                    </li>
                    <li>
                        <div><i class="fa-solid fa-check"></i><span>Featured Job Listings</span></div>
                        <i class="fa-solid fa-circle-info help"></i>
                    </li>
                    <li>
                        <div><i class="fa-solid fa-xmark red"></i><span>No Premium Support</span></div>
                        <i class="fa-solid fa-circle-info help"></i>
                    </li>
                    <!-- <li>
                        <a href="/#">Choose Basic</a>
                    </li> -->
                </ul>
            </div>

            <!-- Premium Plan -->
            <div class="plan-box">
                <div class="plan-title-container">
                    <div class="plan-title">
                        <h2>Premium</h2>
                        <p><span>$</span> 19.99 / month</p>
                    </div>
                </div>
                <ul>
                    <li>
                        <div><i class="fa-solid fa-check"></i><span>Unlimited Job Posts</span></div>
                        <i class="fa-solid fa-circle-info help"></i>
                    </li>
                    <li>
                        <div><i class="fa-solid fa-check"></i><span>Access to All Seeker Profiles</span></div>
                        <i class="fa-solid fa-circle-info help"></i>
                    </li>
                    <li>
                        <div><i class="fa-solid fa-check"></i><span>Featured Job Listings</span></div>
                        <i class="fa-solid fa-circle-info help"></i>
                    </li>
                    <li>
                        <div><i class="fa-solid fa-check"></i><span>Priority Support</span></div>
                        <i class="fa-solid fa-circle-info help"></i>
                    </li>
                    <li>
                        <p>This Is Your Current Plan</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</main>

    </div>
    <script src="../js/main.js"></script>
  </body>
</html>
