<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch all offers for the dropdown menu
$offers = [];
$sql = "SELECT id, location, country FROM offers";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $offers[] = $row;
}

// If an offer is selected, fetch its details
$offer = null;
if (isset($_GET['id']) || isset($_POST['offer_id'])) {
    $id = isset($_GET['id']) ? $_GET['id'] : $_POST['offer_id'];
    $sql = "SELECT * FROM offers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $offer = $result->fetch_assoc();
    $stmt->close();
}

// Update offer if form is submitted
$update_success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_offer'])) {
    $id = $_POST['offer_id'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $available_from = $_POST['available_from'];
    $available_to = $_POST['available_to'];
    $country = $_POST['country'];

    $sql = "UPDATE offers SET location = ?, description = ?, price = ?, available_from = ?, available_to = ?, country = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsssi", $location, $description, $price, $available_from, $available_to, $country, $id);

    if ($stmt->execute()) {
        $update_success = true;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();

    if ($update_success) {
        echo "<script>alert('Offer updated successfully.'); window.location.href = 'view_offers.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Offer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="main-content">
    <h2>Edit Offer</h2>


    <form method="get" action="edit_offer.php">
        <label for="offer_id">Select Offer to Edit:</label>
        <select name="id" id="offer_id" onchange="this.form.submit()">
            <option value="">--Select an Offer--</option>
            <?php foreach ($offers as $offer_option): ?>
                <option value="<?php echo $offer_option['id']; ?>" <?php echo isset($offer['id']) && $offer['id'] == $offer_option['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($offer_option['location'] . " (" . $offer_option['country'] . ")"); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>


    <?php if ($offer): ?>
        <form method="post" action="edit_offer.php">
            <input type="hidden" name="offer_id" value="<?php echo $offer['id']; ?>">
            <label for="location">Location:</label>
            <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($offer['location']); ?>" required><br>
            <label for="description">Description:</label>
            <textarea name="description" id="description" required><?php echo htmlspecialchars($offer['description']); ?></textarea><br>
            <label for="price">Price:</label>
            <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($offer['price']); ?>" required><br>
            <label for="available_from">Available From:</label>
            <input type="date" name="available_from" id="available_from" value="<?php echo htmlspecialchars($offer['available_from']); ?>" required><br>
            <label for="available_to">Available To:</label>
            <input type="date" name="available_to" id="available_to" value="<?php echo htmlspecialchars($offer['available_to']); ?>" required><br>
            <label for="country">Country:</label>
            <input type="text" name="country" id="country" value="<?php echo htmlspecialchars($offer['country']); ?>" required><br>
            <input type="submit" name="update_offer" value="Update Offer">
        </form>
    <?php endif; ?>
    </main>
</body>
</html>
