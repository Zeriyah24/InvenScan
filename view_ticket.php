<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_base_school";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize editing flag
$isEditing = false;

if (isset($_POST['edit'])) {
    $isEditing = true;
}

// Fetch the ticket details based on the ticket ID
if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];

    // Fetch ticket details
    $sql = "SELECT *, last_updated FROM tickets WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $ticket = $result->fetch_assoc();
    } else {
        $message = "Ticket not found.";
    }

    // Fetch comments associated with the ticket
    $comment_sql = "SELECT id, commenter_name, comment, created_at FROM comments WHERE ticket_id = ?";
    $comment_stmt = $conn->prepare($comment_sql);
    $comment_stmt->bind_param("i", $ticket_id);
    $comment_stmt->execute();
    $comments_result = $comment_stmt->get_result();
    $comments = $comments_result->fetch_all(MYSQLI_ASSOC);
} else {
    $message = "No ticket ID provided.";
}

// Update the ticket if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        // Update ticket logic
        $school_id = $_POST['school_id'];
        $subject = $_POST['subject'];
        $description = $_POST['description'];
        $request_email = $_POST['request_email'];
        $status = $_POST['status'];

        // SQL to update the ticket in the database
        $update_sql = "UPDATE tickets SET school_id = ?, subject = ?, description = ?, request_email = ?, status = ?, last_updated = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("issssi", $school_id, $subject, $description, $request_email, $status, $ticket_id);

        if ($update_stmt->execute()) {
            $message = "Ticket updated successfully!";
            header("Location: ticketing_system.php");
            exit();
        } else {
            $message = "Error updating ticket: " . $update_stmt->error;
        }
    } elseif (isset($_POST['delete'])) {
        // Delete ticket logic
        $delete_sql = "DELETE FROM tickets WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $ticket_id);

        if ($delete_stmt->execute()) {
            header("Location: ticketing_system.php"); // Redirect to previous page
            exit();
        } else {
            $message = "Error deleting ticket: " . $delete_stmt->error;
        }
    } elseif (isset($_POST['comment'])) {
        // Add comment logic
        $commenter_name = $_SESSION['name']; // Assuming the user's name is stored in the session
        $comment = $_POST['comment'];

        // SQL to insert comment in the database
        $insert_comment_sql = "INSERT INTO comments (ticket_id, commenter_name, comment) VALUES (?, ?, ?)";
        $insert_comment_stmt = $conn->prepare($insert_comment_sql);
        $insert_comment_stmt->bind_param("iss", $ticket_id, $commenter_name, $comment);

        if ($insert_comment_stmt->execute()) {
            $message = "Comment added successfully!";
            header("Location: view_ticket.php?id=" . $ticket_id);
            exit();
        } else {
            $message = "Error adding comment: " . $insert_comment_stmt->error;
        }
    } elseif (isset($_POST['delete_comment'])) {
        // Delete comment logic
        $comment_id = $_POST['comment_id'];
        $delete_comment_sql = "DELETE FROM comments WHERE id = ?";
        $delete_comment_stmt = $conn->prepare($delete_comment_sql);
        $delete_comment_stmt->bind_param("i", $comment_id);

        if ($delete_comment_stmt->execute()) {
            $message = "Comment deleted successfully!";
            header("Location: view_ticket.php?id=" . $ticket_id); // Refresh to see changes
            exit();
        } else {
            $message = "Error deleting comment: " . $delete_comment_stmt->error;
        }
    }
}

// Fetch available schools for dropdown
$school_sql = "SELECT school_id, school_name FROM schools";
$school_result = $conn->query($school_sql);
$schools = [];
if ($school_result->num_rows > 0) {
    while ($row = $school_result->fetch_assoc()) {
        $schools[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Additional styling for the header */
        .school-division h2 {
            margin: 0;
            font-size: 36px;
            color: green;
            text-align: center;
        }
        #pst-container {
            text-align: right;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="logo6.png" alt="School Logo" style="height: 75px;">
        </a>
        <div class="school-division mx-auto">
            <h2>
                SCHOOL DIVISION
                <br>
                OFFICE OF CALOOCAN
            </h2>
        </div>
        <div id="pst-container" class="ml-auto text-right">
            <div>Philippine Standard Time:</div>
            <div id="pst-time"></div>
            <div id="pst-date"></div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>View/Edit Ticket</h1>

        <?php if (isset($message)) : ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="school_id">School</label>
                <select class="form-control" name="school_id" required <?php echo !$isEditing ? 'disabled' : ''; ?>>
                    <?php foreach ($schools as $school): ?>
                        <option value="<?php echo $school['school_id']; ?>" <?php echo ($school['school_id'] == $ticket['school_id']) ? 'selected' : ''; ?>>
                            <?php echo $school['school_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" class="form-control" name="subject" value="<?php echo $ticket['subject']; ?>" required <?php echo !$isEditing ? 'readonly' : ''; ?>>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" name="description" required <?php echo !$isEditing ? 'readonly' : ''; ?>><?php echo $ticket['description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="request_email">Request to Email</label>
                <input type="email" class="form-control" name="request_email" value="<?php echo $ticket['request_email']; ?>" required <?php echo !$isEditing ? 'readonly' : ''; ?>>
            </div>
            
            <div class="form-group">
                <label for="creator_name">Creator Name</label>
                <input type="text" class="form-control" name="creator_name" value="<?php echo htmlspecialchars($ticket['creator_name']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="creator_email">Creator Email</label>
                <input type="email" class="form-control" name="creator_email" value="<?php echo htmlspecialchars($ticket['creator_email']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" name="status" required <?php echo !$isEditing ? 'disabled' : ''; ?>>
                    <option value="NEW" <?php echo ($ticket['status'] == 'NEW') ? 'selected' : ''; ?>>NEW</option>
                    <option value="WORK IN PROGRESS" <?php echo ($ticket['status'] == 'WORK IN PROGRESS') ? 'selected' : ''; ?>>WORK IN PROGRESS</option>
                    <option value="RESOLVED" <?php echo ($ticket['status'] == 'RESOLVED') ? 'selected' : ''; ?>>RESOLVED</option>
                    <option value="RE-OPENED" <?php echo ($ticket['status'] == 'RE-OPENED') ? 'selected' : ''; ?>>RE-OPENED</option>
                    <option value="CLOSED" <?php echo ($ticket['status'] == 'CLOSED') ? 'selected' : ''; ?>>CLOSED</option>
                </select>
            </div>
            <div class="form-group">
                <label for="last_updated">Last Updated</label>
                <input type="text" class="form-control" name="last_updated" value="<?php echo $ticket['last_updated']; ?>" readonly>
            </div>
            <!-- Edit button -->
            <?php if (!$isEditing): ?>
                <button type="submit" name="edit" class="btn btn-primary">Edit</button>
                <a href="ticketing_system.php" class="btn btn-secondary">Back</a>
            <?php else: ?>
                <button type="submit" name="update" class="btn btn-success">Update</button>
                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
            <?php endif; ?>
        </form>

        <h2>Comments</h2>
        <form method="POST" action="" class="mt-3">
            <div class="form-group">
                <label for="comment">Comment</label>
                <textarea class="form-control" name="comment" id="comment" required placeholder="Type your comment here..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>


        <?php if (!empty($comments)): ?>
            <ul class="list-group mt-3">
                <?php foreach ($comments as $comment): ?>
                    <li class="list-group-item">
                        <strong><?php echo htmlspecialchars($comment['commenter_name']); ?></strong>
                        <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                        <small><?php echo $comment['created_at']; ?></small>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                            <button type="submit" name="delete_comment" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No comments yet.</p>
        <?php endif; ?>
    </div>

    <script>
        // Update time and date
        setInterval(() => {
            const now = new Date();
            document.getElementById("pst-time").innerText = now.toLocaleTimeString();
            document.getElementById("pst-date").innerText = now.toLocaleDateString();
        }, 1000);
    </script>
</body>
</html>
