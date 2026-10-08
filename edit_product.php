<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: dashboard.php");
    exit;
}

require_once "db.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: products.php");
    exit;
}

$id = (int) $_GET["id"];
$message = "";


/* =========================
   FETCH PRODUCT
========================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, name, category, price, quantity, description
     FROM products
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$product) {
    header("Location: products.php");
    exit;
}


/* =========================
   UPDATE PRODUCT
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $category = trim($_POST["category"]);
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];
    $description = trim($_POST["description"]);

    if (
        empty($name) ||
        empty($category) ||
        $price === "" ||
        $quantity === ""
    ) {

        $message = "Please fill in all required fields.";

    } elseif (!is_numeric($price) || $price < 0) {

        $message = "Please enter a valid price.";

    } elseif (!is_numeric($quantity) || $quantity < 0) {

        $message = "Please enter a valid quantity.";

    } else {

        $price = (float) $price;
        $quantity = (int) $quantity;

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE products
             SET name = ?,
                 category = ?,
                 price = ?,
                 quantity = ?,
                 description = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssdisi",
            $name,
            $category,
            $price,
            $quantity,
            $description,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {

            header("Location: products.php");
            exit;

        } else {

            $message = "Failed to update product.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Product | Inventory Manager</title>

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

        <a
            href="products.php"
            class="btn btn-outline-light btn-sm"
        >
            Products
        </a>

    </div>

</nav>


<div class="container py-5">

    <div class="profile-card">

        <h2>
            Edit Product
        </h2>

        <p class="text-muted">
            Update product information.
        </p>


        <?php if ($message): ?>

            <div class="alert alert-danger">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="mb-3">

                <label class="form-label">
                    Product Name
                </label>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="<?= htmlspecialchars($product["name"]) ?>"
                    required
                >

            </div>


            <div class="mb-3">

                <label class="form-label">
                    Category
                </label>

                <input
                    type="text"
                    name="category"
                    class="form-control"
                    value="<?= htmlspecialchars($product["category"]) ?>"
                    required
                >

            </div>


            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Price
                    </label>

                    <input
                        type="number"
                        name="price"
                        class="form-control"
                        value="<?= htmlspecialchars($product["price"]) ?>"
                        min="0"
                        step="0.01"
                        required
                    >

                </div>


                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Quantity
                    </label>

                    <input
                        type="number"
                        name="quantity"
                        class="form-control"
                        value="<?= htmlspecialchars($product["quantity"]) ?>"
                        min="0"
                        required
                    >

                </div>

            </div>


            <div class="mb-3">

                <label class="form-label">
                    Description
                </label>

                <textarea
                    name="description"
                    class="form-control"
                    rows="4"
                ><?= htmlspecialchars($product["description"] ?? "") ?></textarea>

            </div>


            <button
                type="submit"
                class="btn btn-success"
            >
                Update Product
            </button>


            <a
                href="products.php"
                class="btn btn-secondary"
            >
                Cancel
            </a>

        </form>

    </div>

</div>

</body>

</html>