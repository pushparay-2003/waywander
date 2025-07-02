<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

include 'db.php';

function handleAction($conn, $table) {
    if (isset($_POST['approve'])) {
        $id = $_POST['id'];
        $conn->query("UPDATE $table SET status = 'approved' WHERE id = $id");
    } elseif (isset($_POST['reject'])) {
        $id = $_POST['id'];
        $conn->query("UPDATE $table SET status = 'rejected' WHERE id = $id");
    }
}

handleAction($conn, 'restaurants');
handleAction($conn, 'hotels');
handleAction($conn, 'attractions');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pending Approvals</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        h2 { margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; border: 1px solid #ccc; }
        form { display: inline; }
        button { padding: 4px 8px; }
    </style>
</head>
<body>

<h1>Admin Panel - Pending Items</h1>

<?php
function renderPending($conn, $table) {
    $result = $conn->query("SELECT * FROM $table WHERE status = 'pending'");
    echo "<h2>Pending " . ucfirst($table) . "</h2>";
    if ($result->num_rows > 0) {
        echo "<table><tr><th>ID</th><th>Name</th><th>Actions</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>
                    <form method='post'><input type='hidden' name='id' value='{$row['id']}'>
                        <button name='approve'>Approve</button>
                        <button name='reject'>Reject</button>
                    </form>
                </td>
            </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No pending $table found.</p>";
    }
}

renderPending($conn, 'restaurants');
renderPending($conn, 'hotels');
renderPending($conn, 'attractions');

$conn->close();
?>

</body>
</html>
