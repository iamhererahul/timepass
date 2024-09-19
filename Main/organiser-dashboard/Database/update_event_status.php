<?php
session_start();
include('../Database/config.php'); // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../seeker/seekerlogin.html');
    exit();
}

$user_id = $_SESSION['user_id']; // Use 'id' instead of 'user_id'

// Function to handle errors
function handle_error($message) {
    // Log the error for debugging
    error_log($message, 3, "/var/log/myapp/errors.log");

    // Redirect to a page with an error message
    header('Location: ../pages/eventstatus.php?error=' . urlencode($message));
    exit();
}

// Handle status update if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id']) && isset($_POST['status'])) {
    $event_id = filter_input(INPUT_POST, 'event_id', FILTER_SANITIZE_NUMBER_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

    // Validate status value
    if ($status !== 'Pending' && $status !== 'Confirmed') {
        handle_error('Invalid status value');
    }

    // Update the status of the event using event_id
    $sql = "UPDATE event_registration 
            SET status = ? 
            WHERE event_id = ?"; // Adjust column names based on your table

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $status, $event_id); // Assuming 'id' is the primary key and 'event_id' is used for filtering
        if (!$stmt->execute()) {
            handle_error('Error updating event status: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        handle_error('Error preparing status update query: ' . $conn->error);
    }

    // Redirect to the same page to reflect changes
    header('Location: ../pages/eventstatus.php');
    exit();
}

$conn->close();
?>
