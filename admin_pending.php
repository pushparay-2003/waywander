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

function renderPending($conn, $table) {
    $result = $conn->query("SELECT * FROM $table WHERE status = 'pending'");

    echo "<h2 style='text-align:center; margin-top:40px;'>Pending " . ucfirst($table) . "</h2>";

    if ($result->num_rows > 0) {
        echo "<table style='width:95%; margin:20px auto; border-collapse:collapse; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.1);'>
                <tr style='background:#007bff; color:white;'>
                    <th style='padding:10px; border:1px solid #ccc;'>ID</th>
                    <th style='padding:10px; border:1px solid #ccc;'>Name</th>
                    <th style='padding:10px; border:1px solid #ccc;'>Location</th>
                    <th style='padding:10px; border:1px solid #ccc;'>Category</th>
                    <th style='padding:10px; border:1px solid #ccc;'>Image</th>
                    <th style='padding:10px; border:1px solid #ccc;'>Actions</th>
                </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td style='padding:10px; border:1px solid #ccc;'>{$row['id']}</td>
                    <td style='padding:10px; border:1px solid #ccc;'>" . htmlspecialchars($row['name']) . "</td>
                    <td style='padding:10px; border:1px solid #ccc;'>" . htmlspecialchars($row['location']) . "</td>
                    <td style='padding:10px; border:1px solid #ccc;'>" . htmlspecialchars($row['category']) . "</td>
                    <td style='padding:10px; border:1px solid #ccc; text-align:center;'>";
                        if (!empty($row['image_url'])) {
                            echo "<img src='" . htmlspecialchars($row['image_url']) . "' alt='Image' style='width:80px; height:60px; object-fit:cover; border-radius:4px;'>";
                        } else {
                            echo "<span style='color:#999;'>No image</span>";
                        }
            echo    "</td>
                    <td style='padding:10px; border:1px solid #ccc; text-align:center;'>
                        <form method='post' style='display:inline;'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button name='approve' style='background:#28a745; color:white; padding:6px 12px; border:none; border-radius:4px; cursor:pointer;'>Approve</button>
                        </form>
                        <form method='post' style='display:inline;'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button name='reject' style='background:#dc3545; color:white; padding:6px 12px; border:none; border-radius:4px; cursor:pointer;'>Reject</button>
                        </form>
                    </td>
                </tr>";
        }

        echo "</table>";
    } else {
        echo "<p style='text-align:center; color:#666;'>No pending $table found.</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pending Approvals</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align:center;
            color: #333;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<h1>Admin Panel - Pending Approvals</h1>

<?php
renderPending($conn, 'restaurants');
renderPending($conn, 'hotels');
renderPending($conn, 'attractions');
$conn->close();
?>

</body>
</html>
