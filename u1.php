<?php
// Database configuration (modify as needed)
$host = "localhost:3306";
$username = "root";
$password = "";
$database = "placement";

// Create a database connection
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $branch = $_POST["branch"]; // Assuming this field is added in the form
    $offerType = $_POST["offer_type"];
    $package = $_POST["package"];

    // Insert student data into the database
    $sql = "INSERT INTO placement (name, branch, offer_type, package) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $branch, $offerType, $package);

    if ($stmt->execute()) {
        // Data inserted successfully
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Calculate statistics for each branch and total
$sql = "SELECT branch, 
               COUNT(*) AS total_students, 
               AVG(package) AS avg_package, 
               SUM(CASE WHEN offer_type = 'Internship' THEN 1 ELSE 0 END) AS total_internship_offers, 
               SUM(CASE WHEN offer_type = 'Full Time' THEN 1 ELSE 0 END) AS total_full_time_offers 
        FROM placement 
        GROUP BY branch";

$result = $conn->query($sql);
$row = $result->fetch_assoc();

$avgPackage = $row["avg_package"];
// Calculate overall statistics
$overallStats = [
    'total_full_time_offers' => 0,
    'total_students' => 0
];
$studentsPerBranch = 150;
$branchStatistics = [];
while ($row = $result->fetch_assoc()) {
    $branch = $row['branch'];
    $branchStatistics[$branch] = $row;

    // Calculate percentage of full-time offers for each branch based on 150 students per branch
    $branchStatistics[$branch]['full_time_percentage'] = ($row['total_full_time_offers'] / $studentsPerBranch) * 100;

    // Sum up full-time offers and total students for overall statistics
    $overallStats['total_full_time_offers'] += $row['total_full_time_offers'];
    $overallStats['total_students'] += $studentsPerBranch; // Increment by 150 for each branch
}

// Calculate overall full-time offer percentage based on the total students from all branches
$overallStats['full_time_percentage'] = ($overallStats['total_full_time_offers'] / $overallStats['total_students']) * 100;
// Close the database connection
$conn->close();
?>
<?php
if (!empty($branchStatistics)) {
    echo "<h3>Statistics by Branch</h3>";
    echo "<table style='margin: 0 auto; text-align: center;'>";
    echo "<tr><th>Branch</th><th>Total Students</th><th>Average Package    </th><th>Total Internship Offers    </th><th>Total Full-Time Offers  </th><th>Full-Time Offer %  </th></tr>";

    foreach ($branchStatistics as $branch => $stats) {
        echo "<tr>";
        echo "<td>$branch</td>";
        echo "<td>{$stats['total_students']}</td>";
        echo "<td>{$stats['avg_package']}</td>";
        echo "<td>{$stats['total_internship_offers']}</td>";
        echo "<td>{$stats['total_full_time_offers']}</td>";
        echo "<td>{$stats['full_time_percentage']}%</td>";
        echo "</tr>";

    }

    echo "</table>";
    
}

if (!empty($overallStats)) {
    echo "<h3><br></br>Overall Statistics</h3>";
    echo "<p>Total Students: {$overallStats['total_students']}</p>";
    echo "<p>Full-Time Offers: {$overallStats['total_full_time_offers']}</p>";
    echo "<p>Full-Time Offer Percentage: {$overallStats['full_time_percentage']}%</p>";

}

?>
<tr>
    <td>Average Package</td>
    <td><?php echo $avgPackage; ?></td>
</tr>



<!DOCTYPE html>
<html>
<head>
    <title>Student Placement Form</title>
    <script>
function validateForm() {
    var name = document.getElementById('name').value;
    var branch = document.querySelector('input[name="branch"]:checked');
    var offerType = document.getElementById('offer_type').value;
    var package = document.getElementById('package').value;
    var nameRegex = /^[A-Za-z\s]+$/; // Regular expression for alphabetic characters and spaces
    var packageRegex = /^\d+$/; // Regular expression for numeric characters

    if (!name.match(nameRegex)) {
        alert("Please enter only text in the Name field");
        return false;
    }

    if (!package.match(packageRegex)) {
        alert("Please enter only numbers in the Package field");
        return false;
    }

    if (name === "" || branch === null || offerType === "" || package === "") {
        alert("Please fill in all fields");
        return false;
    }

    return true;
}
</script>

</head>
<body style="font-family: Arial, sans-serif; margin: 20px; text-align: center;">


<form method="POST"  onsubmit="return validateForm();">
<br></br>
<br></br>
<strong><label for="name" style="display: block; margin-top: 10px;">Name:</label></strong>
    <input type="text" id="name" name="name" required><br><br>
    <div style="display: flex; flex-direction: row; justify-content: center;">
    <strong><label for="name" style="display: block; margin-top: 10px;">Branch:</label></strong>
</div>

<div style="display: flex; flex-direction: row; justify-content: center; margin-top: 10px;">
    <div style="display: flex; flex-direction: row;">
        <input type="radio" id="branchCSE" name="branch" value="CSE" required>
        <label for="branchCSE" style="margin-top: 10px; margin-right: 10px;">CSE</label>
    </div>

    <div style="display: flex; flex-direction: row;">
        <input type="radio" id="branchMech" name="branch" value="Mech" required>
        <label for="branchMech" style="margin-top: 10px; margin-right: 10px;">Mechanical</label>
    </div>

    <div style="display: flex; flex-direction: row;">
        <input type="radio" id="branchISE" name="branch" value="ISE" required>
        <label for="branchISE" style="margin-top: 10px; margin-right: 10px;">ISE</label>
    </div>

    <div style="display: flex; flex-direction: row;">
        <input type="radio" id="branchEEE" name="branch" value="EEE" required>
        <label for="branchEEE" style="margin-top: 10px; margin-right: 10px;">EEE</label>
    </div>

    <div style="display: flex; flex-direction: row;">
        <input type="radio" id="branchChem" name="branch" value="Chem" required>
        <label for="branchChem" style="margin-top: 10px; margin-right: 10px;">Chemical</label>
    </div>

    <div style="display: flex; flex-direction: row;">
        <input type="radio" id="branchECE" name="branch" value="ECE" required>
        <label for="branchECE" style="margin-top: 10px;">ECE</label>
    </div>
</div>




    <!-- Other branch radio inputs... -->
<br></br>
    <strong><label for="offer_type">Offer Type:</label></strong>
    <select id="offer_type" name="offer_type" required>
        <option value="">Select offer type</option>
        <option value="Internship">Internship</option>
        <option value="Full Time">Full Time</option>
    </select><br><br>
    <br></br>
    <strong><label for="package">Package:</label></strong>
    <input type="text" id="package" name="package" required><br><br>

    <input type="submit" value="Submit" style="width: 100%; padding: 10px; background-color: #007bff; color: #fff; border: none; cursor: pointer; margin-top: 20px;">
</form>
<button onclick="window.location.href = 'toggle.html';">Go to Home</button>
</body>
</html>
