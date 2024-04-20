<?php
// Database configuration (modify as needed)
$host = "localhost:3306";
$username = "root";
$password = "";
$database = "student";

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
    $company = $_POST["company"];

    // Insert student data into the database
    $sql = "INSERT INTO student (name, branch, offer_type, package, company) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $branch, $offerType, $package, $company);

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
               company as Cname,
               SUM(CASE WHEN offer_type = 'Internship' THEN 1 ELSE 0 END) AS total_internship_offers, 
               SUM(CASE WHEN offer_type = 'Full Time' THEN 1 ELSE 0 END) AS total_full_time_offers 
        FROM placement 
        GROUP BY branch";

$result = $conn->query($sql);

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
    echo "<table>";
    echo "<tr><th>Branch</th><th>Total Students</th><th>Average Package</th><th>Total Internship Offers</th><th>Total Full-Time Offers</th><th>Full-Time Offer Percentage</th><th>Company Name</th></tr>";
// Calculate statistics (simplified example)
foreach ($branchStatistics as $branch => $stats) {
    echo "<tr>";
    echo "<td>$branch</td>";
    echo "<td>{$stats['total_students']}</td>";
    echo "<td>{$stats['avg_package']}</td>";
    echo "<td>{$stats['total_internship_offers']}</td>";
    echo "<td>{$stats['total_full_time_offers']}</td>";
    echo "<td>{$stats['full_time_percentage']}%</td>";
    echo "<td>{$stats['Cname']}</td>";
    echo "</tr>";
}

echo "</table>";
}

if (!empty($overallStats)) {
echo "<h3>Overall Statistics</h3>";
echo "<p>Total Students: {$overallStats['total_students']}</p>";
echo "<p>Total Full-Time Offers: {$overallStats['total_full_time_offers']}</p>";
echo "<p>Full-Time Offer Percentage: {$overallStats['full_time_percentage']}%</p>";
}

?>

<!-- HTML to display the form and statistics -->

<!-- HTML to display the form and statistics in a table -->
<!DOCTYPE html>
<html>
<head>


    <title>Student Information</title>
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

<h1 style="color: #007bff; font-size: 24px; font-weight: bold; margin-top: 40px;">Enter Student Information</h1>

    <form method="POST" onsubmit="return validateForm() style="width: 300px; margin: 0 auto; text-align: left;">
        <label for="name" style="display: block; margin-top: 10px;">Name:</label>
        <input type="text" name="name" required style="width: 100%; padding: 5px;"><br>

        <label>Branch:</label><br>
 <input type="radio" id="branchCSE" name="branch" value="CSE" required>
 <label for="branchCSE">CSE</label><br>
 <input type="radio" id="branchECE" name="branch" value="ECE" required>
 <label for="branchECE">ECE</label><br>
 <input type="radio" id="branchCSE" name="branch" value="MECH" required>
 <label for="branchMech">Mech</label><br>
 <input type="radio" id="branchECE" name="branch" value="CH" required>
 <label for="branchCH">Chemical</label><br>
      

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



























