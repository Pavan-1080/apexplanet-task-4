<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "db.php";

/* Total products */
$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM products"
);

$totalProducts = mysqli_fetch_assoc($result)["total"];


/* Total quantity */
$result = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(quantity), 0) AS total FROM products"
);

$totalQuantity = mysqli_fetch_assoc($result)["total"];


/* Total inventory value */
$result = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(price * quantity), 0) AS total FROM products"
);

$totalValue = mysqli_fetch_assoc($result)["total"];


/* Total categories */
$result = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT category) AS total FROM products"
);

$totalCategories = mysqli_fetch_assoc($result)["total"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard | Inventory Manager</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="style.css">

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

        <div>

            <span class="text-white me-3">
                <?= htmlspecialchars($_SESSION["name"]) ?>
            </span>

            <a
                href="logout.php"
                class="btn btn-outline-light btn-sm"
            >
                Logout
            </a>

        </div>

    </div>

</nav>


<div class="container py-5">

    <div class="mb-4">

        <h2>
            Welcome, <?= htmlspecialchars($_SESSION["name"]) ?> 👋
        </h2>

        <p class="text-muted">
            Inventory overview and management dashboard.
        </p>

    </div>


    <!-- STATISTICS -->

    <div class="row g-4 mb-4">


        <div class="col-md-3">

            <div class="dashboard-card text-center">

                <h6 class="text-muted">
                    Total Products
                </h6>

                <h2>
                    <?= $totalProducts ?>
                </h2>

            </div>

        </div>


        <div class="col-md-3">

            <div class="dashboard-card text-center">

                <h6 class="text-muted">
                    Total Quantity
                </h6>

                <h2>
                    <?= $totalQuantity ?>
                </h2>

            </div>

        </div>


        <div class="col-md-3">

            <div class="dashboard-card text-center">

                <h6 class="text-muted">
                    Categories
                </h6>

                <h2>
                    <?= $totalCategories ?>
                </h2>

            </div>

        </div>


        <div class="col-md-3">

            <div class="dashboard-card text-center">

                <h6 class="text-muted">
                    Inventory Value
                </h6>

                <h2>
                    ₹<?= number_format($totalValue, 2) ?>
                </h2>

            </div>

        </div>

    </div>


    <!-- MANAGEMENT -->

    <div class="dashboard-card">

        <h4>
            Inventory Management
        </h4>

        <p class="text-muted">
            Manage your products and inventory.
        </p>

        <a
            href="products.php"
            class="btn btn-primary"
        >
            View Products
        </a>


        <?php if ($_SESSION["role"] === "admin"): ?>

            <a
                href="add_product.php"
                class="btn btn-success"
            >
                Add Product
            </a>

        <?php endif; ?>

    </div>


</div>

</body>

</html>