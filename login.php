<?php
session_start();

require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {

        $message = "Please enter username and password.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, name, username, password, role
             FROM users
             WHERE username = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $username
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {

            if (password_verify($password, $user["password"])) {

                // Clear any old session data
                session_unset();

                // Create fresh session data
                $_SESSION["user_id"] = (int) $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"] = $user["role"];

                header("Location: dashboard.php");
                exit;

            } else {

                $message = "Invalid username or password.";
            }

        } else {

            $message = "Invalid username or password.";
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

    <title>Login | Inventory Manager</title>

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

<div class="auth-container">

    <div class="auth-card">

        <h1>
            Inventory Manager
        </h1>

        <p class="text-muted">
            Sign in to continue
        </p>


        <?php if ($message): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="mb-3">

                <label class="form-label">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    class="form-control"
                    required
                >

            </div>


            <div class="mb-3">

                <label class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn btn-primary w-100"
            >
                Login
            </button>

        </form>


        <p class="text-center mt-3">

            Don't have an account?

            <a href="register.php">
                Create Account
            </a>

        </p>

    </div>

</div>

</body>

</html>