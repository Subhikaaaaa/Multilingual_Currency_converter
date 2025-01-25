<?php
session_start();

$error = "";

$servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "login_db";

$conn = new mysqli($servername, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_username, $stored_password);
        $stmt->fetch();

        if ($password === $stored_password) {
            $_SESSION['username'] = $username;
            header("Location: welcome.html");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="s_login.css">
    <style>
        #google_translate_element {
            position: absolute;
            top: 10px;
            left: 10px; /* Move to the top left corner */
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

    <div class="container">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title text-center">Login</h3> <!-- Title for the form -->
                <?php if ($error): ?>
                    <p class="text-danger"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <form action="login.php" method="POST">
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="username" name="username" placeholder="username" required>
                    </div>
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                        </div>
                        <input type="password" class="form-control" id="password" name="password" placeholder="password" required>
                    </div>
                    <div class="row align-items-center remember">
                        <input type="checkbox">Remember Me
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Login" class="btn float-right login_btn">
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-center links">
                    Don't have an account?<a href="register.php">Sign Up</a>
                </div>
                <div class="d-flex justify-content-center">
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>
