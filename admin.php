<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: dashboard.php");
    exit;
}

require_once "db.php";

/* Inventory statistics */

$totalProducts = 0;
$totalQuantity = 0;
$totalValue = 0;
$totalCategories = 0;

$result = mysqli_query(
    $conn,
    "SELECT
        COUNT(*) AS total_products,
        COALESCE(SUM(quantity), 0) AS total_quantity,
        COALESCE(SUM(price * quantity), 0) AS total_value,
        COUNT(DISTINCT category) AS total_categories
     FROM products"
);

if ($result) {
    $stats = mysqli_fetch_assoc($result);

    $totalProducts = $stats["total_products"];
    $totalQuantity = $stats["total_quantity"];
    $totalValue = $stats["total_value"];
    $totalCategories = $stats["total_categories"];
}

/* Low stock products */

$lowStockResult = mysqli_query(
    $conn,
    "SELECT id, name, category, quantity
     FROM products
     WHERE quantity <= 5
     ORDER BY quantity ASC
     LIMIT 5"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Panel | Inventory Manager</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>

<body>

<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a
            class="navbar-brand"
            href="dashboard.php"
        >
            Inventory Manager
        </a>

        <div class="d-flex align-items-center gap-2">

            <span class="text-white">
                Admin: <?= htmlspecialchars($_SESSION["name"]) ?>
            </span>

            <a
                href="dashboard.php"
                class="btn btn-outline-light btn-sm"
            >
                Dashboard
            </a>

            <a
                href="logout.php"
                class="btn btn-danger btn-sm"
            >
                Logout
            </a>

        </div>

    </div>

</nav>


<div class="container py-5">

    <div class="mb-4">

        <h2>
            Admin Panel
        </h2>

        <p class="text-muted">
            Monitor inventory performance and manage products.
        </p>

    </div>


    <!-- Statistics -->

    <div class="row g-4 mb-5">

        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <h6 class="text-muted">
                        Total Products
                    </h6>

                    <h2>
                        <?= $totalProducts ?>
                    </h2>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <h6 class="text-muted">
                        Total Quantity
                    </h6>

                    <h2>
                        <?= $totalQuantity ?>
                    </h2>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <h6 class="text-muted">
                        Inventory Value
                    </h6>

                    <h2>
                        ₹<?= number_format($totalValue, 2) ?>
                    </h2>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <h6 class="text-muted">
                        Categories
                    </h6>

                    <h2>
                        <?= $totalCategories ?>
                    </h2>

                </div>

            </div>

        </div>

    </div>


    <!-- Quick Actions -->

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <h4 class="mb-3">
                Quick Actions
            </h4>

            <a
                href="products.php"
                class="btn btn-primary me-2"
            >
                Manage Products
            </a>

            <a
                href="add_product.php"
                class="btn btn-success"
            >
                Add Product
            </a>

        </div>

    </div>


    <!-- Low Stock -->

    <div class="card shadow-sm">

        <div class="card-body">

            <h4 class="mb-3">
                Low Stock Products
            </h4>

            <?php if (mysqli_num_rows($lowStockResult) > 0): ?>

                <div class="table-responsive">

                    <table class="table table-hover">

                        <thead class="table-dark">

                            <tr>

                                <th>Product</th>
                                <th>Category</th>
                                <th>Quantity</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php while ($product = mysqli_fetch_assoc($lowStockResult)): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($product["name"]) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($product["category"]) ?>
                                    </td>

                                    <td>

                                        <span class="badge bg-danger">

                                            <?= $product["quantity"] ?>

                                        </span>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="alert alert-success mb-0">

                    All products have sufficient stock.

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>

</html>