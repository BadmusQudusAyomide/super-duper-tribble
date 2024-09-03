<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_attendance_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for adding a venue
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_venue'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $location = $_POST['location'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $sql = "INSERT INTO venues (name, address, location, latitude, longitude) VALUES ('$name', '$address', '$location', '$latitude', '$longitude')";
    $conn->query($sql);
}

// Handle form submission for editing a venue
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_venue'])) {
    $venue_id = $_POST['venue_id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $location = $_POST['location'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $sql = "UPDATE venues SET name='$name', address='$address', location='$location', latitude='$latitude', longitude='$longitude' WHERE id=$venue_id";
    $conn->query($sql);
}

// Handle venue deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM venues WHERE id=$delete_id";
    $conn->query($sql);
}

// Retrieve all venues
$sql = "SELECT * FROM venues";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Venues</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .container {
            margin-top: 50px;
        }
        .table {
            margin-top: 20px;
        }
        .modal-header {
            background-color: #007bff;
            color: white;
        }
        .edit-icon, .delete-icon {
            cursor: pointer;
        }
        .edit-icon:hover, .delete-icon:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Venues</h2>
        <button class="btn btn-primary" data-toggle="modal" data-target="#addVenueModal">Add Venue</button>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Location</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['location']; ?></td>
                    <td><?php echo $row['latitude']; ?></td>
                    <td><?php echo $row['longitude']; ?></td>
                    <td>
                        <i class="fas fa-edit edit-icon" data-toggle="modal" data-target="#editVenueModal"
                            data-id="<?php echo $row['id']; ?>"
                            data-name="<?php echo $row['name']; ?>"
                            data-address="<?php echo $row['address']; ?>"
                            data-location="<?php echo $row['location']; ?>"
                            data-latitude="<?php echo $row['latitude']; ?>"
                            data-longitude="<?php echo $row['longitude']; ?>"></i>
                        <a href="manage_venues.php?delete_id=<?php echo $row['id']; ?>"><i class="fas fa-trash delete-icon"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Venue Modal -->
    <div class="modal fade" id="addVenueModal" tabindex="-1" role="dialog" aria-labelledby="addVenueModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addVenueModalLabel">Add Venue</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="manage_venues.php">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="form-group">
                            <label for="latitude">Latitude</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" required>
                        </div>
                        <div class="form-group">
                            <label for="longitude">Longitude</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="add_venue" class="btn btn-primary">Add Venue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Venue Modal -->
    <div class="modal fade" id="editVenueModal" tabindex="-1" role="dialog" aria-labelledby="editVenueModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVenueModalLabel">Edit Venue</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="manage_venues.php">
                    <div class="modal-body">
                        <input type="hidden" id="edit_venue_id" name="venue_id">
                        <div class="form-group">
                            <label for="edit_name">Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_address">Address</label>
                            <input type="text" class="form-control" id="edit_address" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_location">Location</label>
                            <input type="text" class="form-control" id="edit_location" name="location" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_latitude">Latitude</label>
                            <input type="text" class="form-control" id="edit_latitude" name="latitude" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_longitude">Longitude</label>
                            <input type="text" class="form-control" id="edit_longitude" name="longitude" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="edit_venue" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Pass data to the edit modal
        $('#editVenueModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var name = button.data('name');
            var address = button.data('address');
            var location = button.data('location');
            var latitude = button.data('latitude');
            var longitude = button.data('longitude');

            var modal = $(this);
            modal.find('#edit_venue_id').val(id);
            modal.find('#edit_name').val(name);
            modal.find('#edit_address').val(address);
            modal.find('#edit_location').val(location);
            modal.find('#edit_latitude').val(latitude);
            modal.find('#edit_longitude').val(longitude);
        });
    </script>
</body>
</html>
