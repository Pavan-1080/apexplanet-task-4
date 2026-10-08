<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
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

    <title>Inventory Manager</title>

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
            class="navbar-brand fw-bold"
            href="index.php"
        >
            Inventory Manager
        </a>

        <div>

            <a
                href="login.php"
                class="btn btn-outline-light btn-sm me-2"
            >
                Login
            </a>

            <a
                href="register.php"
                class="btn btn-success btn-sm"
            >
                Register
            </a>

        </div>

    </div>

</nav>


<section class="py-5">

    <div class="container">

        <div class="row align-items-center min-vh-75">

            <div class="col-lg-7">

                <span class="badge bg-success mb-3">
                    Full Stack Inventory System
                </span>

                <h1 class="display-4 fw-bold mb-3">
                    Manage Your Inventory
                    <br>
                    With Confidence.
                </h1>

                <p class="lead text-muted mb-4">
                    A simple and secure inventory management system
                    for tracking products, stock levels, categories,
                    and inventory value.
                </p>

                <a
                    href="login.php"
                    class="btn btn-dark btn-lg me-2"
                >
                    Login
                </a>

                <a
                    href="register.php"
                    class="btn btn-outline-dark btn-lg"
                >
                    Create Account
                </a>

            </div>


            <div class="col-lg-5 mt-5 mt-lg-0">

                <div class="card shadow-lg border-0">

                    <div class="card-body p-4">

                        <h4 class="fw-bold mb-4">
                            System Features
                        </h4>

                        <div class="mb-3">
                            <strong>✓ Product Management</strong>
                            <p class="text-muted mb-0">
                                Add, edit, delete and manage products.
                            </p>
                        </div>

                        <div class="mb-3">
                            <strong>✓ Search & Filtering</strong>
                            <p class="text-muted mb-0">
                                Quickly find products by name or category.
                            </p>
                        </div>

                        <div class="mb-3">
                            <strong>✓ Admin Dashboard</strong>
                            <p class="text-muted mb-0">
                                Monitor inventory statistics and stock levels.
                            </p>
                        </div>

                        <div>
                            <strong>✓ Secure Authentication</strong>
                            <p class="text-muted mb-0">
                                Role-based access with secure passwords.
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<footer class="bg-dark text-white text-center py-3">

    <p class="mb-0">
        Inventory Manager | ApexPlanet Internship Task 4
    </p>

</footer>


</body>

</html>