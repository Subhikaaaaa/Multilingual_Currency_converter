<?php
session_start(); 
$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "login_db";

    $conn = new mysqli($servername, $db_username, $db_password, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password);

            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                header("Location: login.php"); 
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="s_register.css">
    <style>
        #google_translate_element {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000; 
        }
    </style>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
        }

        function storeLanguage() {
            var langSelect = document.querySelector('.goog-te-combo');
            if (langSelect) {
                langSelect.addEventListener('change', function() {
                    localStorage.setItem('selectedLanguage', langSelect.value);
                });
            }
        }

        function applyStoredLanguage() {
            var lang = localStorage.getItem('selectedLanguage');
            if (lang) {
                var select = document.querySelector('.goog-te-combo');
                if (select) {
                    select.value = lang;
                    select.dispatchEvent(new Event('change'));
                }
            }
        }

        window.onload = function() {
            applyStoredLanguage();
            storeLanguage();
        };
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</head>
<body>
    <div id="google_translate_element"></div> <!-- Google Translate widget -->

    <div class="register-container">
        <?php if ($error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php endif; ?>
        <h2>Register</h2>
        <form action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>
            <input type="submit" value="Register">
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
