<?php
session_start();
include 'db_config.php';

$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$country_filter = isset($_GET['country']) ? $_GET['country'] : '';
$location_filter = isset($_GET['location']) ? $_GET['location'] : '';
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : 'asc';

$sql_countries = "SELECT DISTINCT country FROM offers ORDER BY country";
$result_countries = $conn->query($sql_countries);

$sql_locations = "SELECT DISTINCT location FROM offers ORDER BY location";
$result_locations = $conn->query($sql_locations);


$sql = "SELECT * FROM offers WHERE 1=1";

$types = ''; 
$params = []; 

if (!empty($country_filter)) {
    $sql .= " AND country = ?";
    $types .= 's';
    $params[] = $country_filter;
}

if (!empty($location_filter)) {
    $sql .= " AND location = ?";
    $types .= 's';
    $params[] = $location_filter;
}

$sql .= " ORDER BY price " . ($sort_order === 'desc' ? 'DESC' : 'ASC');

$stmt = $conn->prepare($sql);

if ($types) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Offers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <nav>
            <ul>
                <li><a href="index.php" class="button">Home</a></li>
                <?php if (isset($_SESSION['role'])) { ?>
                    <li><a href="logout.php" class="button">Logout</a></li>
                <?php } else { ?>
                    <li><a href="login.php" class="button">Login</a></li>
                    <li><a href="register.php" class="button">Register</a></li>
                <?php } ?>
            </ul>
        </nav>
    </header>

    <main>
        <section id="filter" class="main-content">
            <form method="get" action="view_offers.php">
                <label for="country">Country:</label>
                <select name="country" id="country">
                    <option value="">Select Country</option>
                    <?php while ($row = $result_countries->fetch_assoc()) { ?>
                        <option value="<?php echo htmlspecialchars($row['country']); ?>" <?php echo ($country_filter === $row['country']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['country']); ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="location">Location:</label>
                <select name="location" id="location">
                    <option value="">Select Location</option>
                    <?php while ($row = $result_locations->fetch_assoc()) { ?>
                        <option value="<?php echo htmlspecialchars($row['location']); ?>" <?php echo ($location_filter === $row['location']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['location']); ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="sort">Sort By Price:</label>
                <select name="sort" id="sort">
                    <option value="asc" <?php echo ($sort_order === 'asc') ? 'selected' : ''; ?>>Ascending</option>
                    <option value="desc" <?php echo ($sort_order === 'desc') ? 'selected' : ''; ?>>Descending</option>
                </select>

                <input type="submit" value="Filter & Sort">
            </form>
        </section>

        <section id="offers" class="main-content">
                <?php if ($is_admin) { ?>
                    <a href="add_offer.php" class="button">Add Offer</a>
                <?php } ?>
            <h2><?php echo $is_admin ? 'Manage Offers' : 'Available Offers'; ?></h2>
            <?php if ($result->num_rows > 0) { ?>
                <table>
                    <tr>
                        <th>Location &nbsp;</th>
                        <th>Description  &nbsp;</th>
                        <th>Price  &nbsp;</th>
                        <th>Trip Duration From  &nbsp;</th>
                        <th>Trip Duration To &nbsp;</th>
                        <th>Country &nbsp;</th>
                        <?php if (!$is_admin) { ?>
                            <th>Action</th>
                        <?php } ?>
                    </tr>
                    <?php while ($offer = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($offer['location']); ?></td>
                        <td><?php echo htmlspecialchars($offer['description']); ?></td>
                        <td><?php echo htmlspecialchars($offer['price']); ?></td>
                        <td><?php echo htmlspecialchars($offer['available_from']); ?></td>
                        <td><?php echo htmlspecialchars($offer['available_to']); ?></td>
                        <td><?php echo htmlspecialchars($offer['country']); ?></td>
                        <?php if ($is_admin) { ?>
                            <td>
                                <a href="edit_offer.php?id=<?php echo htmlspecialchars($offer['id']); ?>" class="button">Edit</a>
                                <a>&nbsp;&nbsp;</a>
                                <a href="delete_offer.php?id=<?php echo htmlspecialchars($offer['id']); ?>" class="button">Delete</a>
                            </td>
                        <?php } else { ?>
                            <td>
                                <form action="buy_offer.php" method="post" style="display:inline;">
                                    <input type="hidden" name="offer_id" value="<?php echo htmlspecialchars($offer['id']); ?>">
                                    <input type="submit" value="Buy">
                                </form>
                            </td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p>No offers available at the moment.</p>
            <?php } ?>
       


    <footer>
        <br><br>
        <p>&copy; <?php echo date('Y'); ?> Costea Tour. All rights reserved.</p>
    </footer>
    </section>
    </main>
</body>
</html>
