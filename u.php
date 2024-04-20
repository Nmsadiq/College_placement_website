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

// Build the URL with data as parameters
$redirectURL = "display.html?totalStudents=$totalStudents&avgPackage=$avgPackage&totalInternshipOffers=$totalInternshipOffers&totalFullTimeOffers=$totalFullTimeOffers&totalOffers=$totalOffers&avgCTCOrPackage=$avgCTCOrPackage";

// Redirect to the display.html page with the data
header("Location: $redirectURL");
exit;
?>
