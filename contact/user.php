<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Records</title>
    <link rel="stylesheet" href="try.css">
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="try.html">Home</a></li>
            <li><a href="user.php">User</a></li>
        </ul>
    </nav>
    <div class="container">
        <h1>User Records</h1>

        <!-- Search Form -->
        <div class="form-group">
            <form method="POST" action="user.php">
                <input type="text" name="search" placeholder="Search by name or mobile">
                <input type="submit" name="submit_search" value="Search">
            </form>
        </div>

        <!-- Search Results -->
        <div class="form-group">
            <?php
            // Database configuration
            $servername = "localhost";
            $username = "root"; // replace with your MySQL username
            $password = ""; // replace with your MySQL password
            $dbname = "contacts";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Delete record and reassign IDs
            if (isset($_GET['delete_id'])) {
                $delete_id = $_GET['delete_id'];

                // Begin transaction
                $conn->begin_transaction();

                try {
                    // Delete the record
                    $sql = "DELETE FROM dbinfo WHERE id=$delete_id";
                    if ($conn->query($sql) !== TRUE) {
                        throw new Exception("Error deleting record: " . $conn->error);
                    }

                    // Reassign IDs
                    $sql = "SET @count = 0";
                    $conn->query($sql);

                    $sql = "UPDATE dbinfo SET id = @count:= @count + 1";
                    if ($conn->query($sql) !== TRUE) {
                        throw new Exception("Error updating IDs: " . $conn->error);
                    }

                    // Commit transaction
                    $conn->commit();
                    echo "Record deleted and IDs reassigned successfully";
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $conn->rollback();
                    echo $e->getMessage();
                }
            }

            // Fetch records based on search
            if (isset($_POST['submit_search'])) {
                $search = $_POST['search'];
                $sql = "SELECT id, first_name, last_name, mobile_no, message FROM dbinfo WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR mobile_no LIKE '%$search%'";
            } else {
                // Default fetch all records
                $sql = "SELECT id, first_name, last_name, mobile_no, message FROM dbinfo";
            }
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table class='user-table'><tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Mobile no</th><th>Message</th><th>Action</th></tr>";
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["id"] . "</td>
                            <td>" . $row["first_name"] . "</td>
                            <td>" . $row["last_name"] . "</td>
                            <td>" . $row["mobile_no"] . "</td>
                            <td>" . $row["message"] . "</td>
                            <td>
                                <a href='user.php?edit_id=" . $row["id"] . "'>Edit</a> |
                                <a href='user.php?delete_id=" . $row["id"] . "' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>
                            </td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "0 results";
            }

            if (isset($_GET['edit_id'])) {
                $edit_id = $_GET['edit_id'];
                $sql = "SELECT id, first_name, last_name, mobile_no, message FROM dbinfo WHERE id = $edit_id";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                ?>
                <!-- Update Form -->
                <div class="form-group">
                    <h2>Update Record</h2>
                    <form method="POST" action="user.php">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="text" name="first_name" value="<?php echo $row['first_name']; ?>" placeholder="First Name">
                        <input type="text" name="last_name" value="<?php echo $row['last_name']; ?>" placeholder="Last Name">
                        <input type="text" name="mobile_no" value="<?php echo $row['mobile_no']; ?>" placeholder="Mobile Number">
                        <input type="text" name="message" value="<?php echo $row['message']; ?>" placeholder="Message">
                        <input type="submit" name="submit_update" value="Update">
                    </form>
                </div>
                <?php
            }

            // Update record
            if (isset($_POST['submit_update'])) {
                $id = $_POST['id'];
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $mobile_no = $_POST['mobile_no'];
                $message = $_POST['message'];

                $sql = "UPDATE dbinfo SET first_name='$first_name', last_name='$last_name', mobile_no='$mobile_no', message='$message' WHERE id=$id";

                if ($conn->query($sql) === TRUE) {
                    echo "Record updated successfully";
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            }

            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
