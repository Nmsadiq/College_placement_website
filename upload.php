<?php
// Database configuration (modify as needed)
$host = "localhost:3306";
$username = "root";
$password = "";
$database = "students";

// Create a database connection
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $branch = $_POST["branch"];
    $offerType = $_POST["offer_type"];
    $package = $_POST["package"];

    // Insert student data into the database
    $sql = "INSERT INTO students (name, branch, offer_type, package) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $branch, $offerType, $package);

    if ($stmt->execute()) {
        // Data inserted successfully
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Calculate statistics (simplified example)
$sql = "SELECT COUNT(*) AS total_students, 
               AVG(package) AS avg_package, 
               SUM(CASE WHEN offer_type = 'Internship' THEN 1 ELSE 0 END) AS total_internship_offers, 
               SUM(CASE WHEN offer_type = 'Full Time' THEN 1 ELSE 0 END) AS total_full_time_offers, 
               COUNT(*) AS total_offers, 
               AVG(package) AS avg_ctc_or_package 
        FROM students";

$result = $conn->query($sql);
$row = $result->fetch_assoc();

$totalStudents = $row["total_students"];
$avgPackage = $row["avg_package"];
$totalInternshipOffers = $row["total_internship_offers"];
$totalFullTimeOffers = $row["total_full_time_offers"];
$totalOffers = $row["total_offers"];
$avgCTCOrPackage = $row["avg_ctc_or_package"];

// Close the database connection
$conn->close();
?>

<!-- HTML to display the form and statistics -->

<!-- HTML to display the form and statistics in a table -->
<!DOCTYPE html>
<html>
<head>


    <title>Student Information</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 20px; text-align: center;">

<h1 style="color: #007bff; font-size: 24px; font-weight: bold; margin-top: 40px;">Enter Student Information</h1>

    <form method="POST" style="width: 300px; margin: 0 auto; text-align: left;">
        <label for="name" style="display: block; margin-top: 10px;">Name:</label>
        <input type="text" name="name" required style="width: 100%; padding: 5px;"><br>

        <label for "branch" style="display: block; margin-top: 10px;">Branch:</label>
        <input type="text" name="branch" required style="width: 100%; padding: 5px;"><br>

        <label for="offer_type" style="display: block; margin-top: 10px;">Offer Type:</label>
        <select name="offer_type" style="width: 105%; padding: 5px;">
            <option value="Internship">Internship</option>
            <option value="Full Time">Full Time</option>
        </select><br>
        <label for="package" style="display: block; margin-top: 10px;">Package/CTC:</label>
        <input type="number" name="package" required style="width: 100%; padding: 5px;"><br>

        <input type="submit" value="Submit" style="width: 100%; padding: 10px; background-color: #007bff; color: #fff; border: none; cursor: pointer; margin-top: 20px;">
    </form>

    <h1 style="margin-top: 40px; background-color: #007bff; color: #fff; padding: 10px;">Statistics</h1>
 
    <table style="margin: 0 auto; width: 80%;">
        <tr>
            <th style="background-color: #333; color: white; padding: 8px;">Category</th>
            <th style="background-color: #333; color: white; padding: 8px;">Value</th>
        </tr>
        <tr>
            <td>Total Students Placed</td>
            <td><?php echo $totalStudents; ?></td>
        </tr>
        <tr>
            <td>Average Package</td>
            <td><?php echo $avgPackage; ?></td>
        </tr>
        <tr>
            <td>Total Internship Offers</td>
            <td><?php echo $totalInternshipOffers; ?></td>
        </tr>
        <tr>
            <td>Total Full Time Offers</td>
            <td><?php echo $totalFullTimeOffers; ?></td>
        </tr>
        <tr>
            <td>Total Offers</td>
            <td><?php echo $totalOffers; ?></td>
        </tr>
        <tr>
            <td>Average CTC/Package</td>
            <td><?php echo $avgCTCOrPackage; ?></td>
        </tr>
    </table>
    <button onclick="window.location.href = 'toggle.html';">Go to Home</button>
</body>
</html>



























