<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "db.php";

$isAdmin = ($_SESSION["role"] === "admin");

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$category = isset($_GET["category"]) ? trim($_GET["category"]) : "";

$limit = 5;
$page = isset($_GET["page"]) && is_numeric($_GET["page"])
    ? max(1, (int) $_GET["page"])
    : 1;

$offset = ($page - 1) * $limit;

/* Get categories */
$categories = [];

$categoryResult = mysqli_query(
    $conn,
    "SELECT DISTINCT category FROM products ORDER BY category ASC"
);

while ($row = mysqli_fetch_assoc($categoryResult)) {
    $categories[] = $row["category"];
}

/* Count matching products */
$countSql = "SELECT COUNT(*) AS total FROM products WHERE 1=1";
$countParams = [];
$countTypes = "";

if ($search !== "") {
    $countSql .= " AND (name LIKE ? OR description LIKE ?)";
    $searchValue = "%" . $search . "%";
    $countParams[] = $searchValue;
    $countParams[] = $searchValue;
    $countTypes .= "ss";
}

if ($category !== "") {
    $countSql .= " AND category = ?";
    $countParams[] = $category;
    $countTypes .= "s";
}

$countStmt = mysqli_prepare($conn, $countSql);

if (!empty($countParams)) {
    mysqli_stmt_bind_param($countStmt, $countTypes, ...$countParams);
}

mysqli_stmt_execute($countStmt);

$countResult = mysqli_stmt_get_result($countStmt);
$totalProducts = mysqli_fetch_assoc($countResult)["total"];

mysqli_stmt_close($countStmt);

$totalPages = max(1, ceil($totalProducts / $limit));

/* Get products */
$sql = "SELECT id, name, category, price, quantity, description
        FROM products
        WHERE 1=1";

$params = [];
$types = "";

if ($search !== "") {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $searchValue = "%" . $search . "%";
    $params[] = $searchValue;
    $params[] = $searchValue;
    $types .= "ss";
}

if ($category !== "") {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Products | Inventory Manager</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="style.css">
</head>

<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">

        <a class="navbar-brand" href="dashboard.php">
            Inventory Manager
        </a>

        <div class="d-flex align-items-center gap-2">

            <span class="text-white">
                <?= htmlspecialchars($_SESSION["name"]) ?>
            </span>

            <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                Dashboard
            </a>

            <a href="logout.php" class="btn btn-danger btn-sm">
                Logout
            </a>

        </div>

    </div>
</nav>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2>Products</h2>

            <p class="text-muted mb-0">
                Manage your inventory products.
            </p>
        </div>

        <?php if ($isAdmin): ?>

            <a href="add_product.php" class="btn btn-success">
                + Add Product
            </a>

        <?php endif; ?>

    </div>


    <!-- Search & Filter -->

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search product..."
                            value="<?= htmlspecialchars($search) ?>"
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Category
                        </label>

                        <select
                            name="category"
                            class="form-select"
                        >

                            <option value="">
                                All Categories
                            </option>

                            <?php foreach ($categories as $cat): ?>

                                <option
                                    value="<?= htmlspecialchars($cat) ?>"
                                    <?= $category === $cat ? "selected" : "" ?>
                                >
                                    <?= htmlspecialchars($cat) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Search
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- Products Table -->

    <div class="card shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th>ID</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Description</th>

                            <?php if ($isAdmin): ?>
                                <th>Actions</th>
                            <?php endif; ?>

                        </tr>

                    </thead>

                    <tbody>

                    <?php if (mysqli_num_rows($result) > 0): ?>

                        <?php while ($product = mysqli_fetch_assoc($result)): ?>

                            <tr>

                                <td>
                                    <?= $product["id"] ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($product["name"]) ?>
                                    </strong>
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars($product["category"]) ?>
                                    </span>
                                </td>

                                <td>
                                    ₹<?= number_format($product["price"], 2) ?>
                                </td>

                                <td>

                                    <?php if ($product["quantity"] <= 5): ?>

                                        <span class="badge bg-danger">
                                            <?= $product["quantity"] ?> Low Stock
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-success">
                                            <?= $product["quantity"] ?>
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>
                                    <?= htmlspecialchars($product["description"]) ?>
                                </td>


                                <?php if ($isAdmin): ?>

                                    <td>

                                        <a
                                            href="edit_product.php?id=<?= $product["id"] ?>"
                                            class="btn btn-sm btn-warning"
                                        >
                                            Edit
                                        </a>

                                        <a
                                            href="delete_product.php?id=<?= $product["id"] ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this product?');"
                                        >
                                            Delete
                                        </a>

                                    </td>

                                <?php endif; ?>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="<?= $isAdmin ? 7 : 6 ?>"
                                class="text-center py-4"
                            >
                                No products found.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>


            <!-- Pagination -->

            <?php if ($totalPages > 1): ?>

                <nav class="mt-4">

                    <ul class="pagination justify-content-center">

                        <?php if ($page > 1): ?>

                            <li class="page-item">

                                <a
                                    class="page-link"
                                    href="?search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&page=<?= $page - 1 ?>"
                                >
                                    Previous
                                </a>

                            </li>

                        <?php endif; ?>


                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                            <li
                                class="page-item <?= $i == $page ? 'active' : '' ?>"
                            >

                                <a
                                    class="page-link"
                                    href="?search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&page=<?= $i ?>"
                                >
                                    <?= $i ?>
                                </a>

                            </li>

                        <?php endfor; ?>


                        <?php if ($page < $totalPages): ?>

                            <li class="page-item">

                                <a
                                    class="page-link"
                                    href="?search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&page=<?= $page + 1 ?>"
                                >
                                    Next
                                </a>

                            </li>

                        <?php endif; ?>

                    </ul>

                </nav>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>

</html>

<?php
mysqli_stmt_close($stmt);
?>