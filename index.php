<?php
ini_set('display_errors', 1);
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobranie danych z formularza
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Połączenie z bazą danych sensordata
    $conn = new mysqli('localhost', 'root', 'raspberry', 'sensordata');
    if ($conn->connect_error) {
        die("Błąd połączenia: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($userId);
        $stmt->fetch();
        $_SESSION['loggedin'] = true;
        $_SESSION['userid'] = $userId;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Niepoprawna nazwa użytkownika lub hasło.";
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Logowanie</title>
  <style>
    * { box-sizing: border-box; font-family: Arial, sans-serif; }
    body {
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .login-container {
      background: #fff;
      padding: 40px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      width: 300px;
      text-align: center;
    }
    h2 { margin-bottom: 20px; color: #333; }
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px 20px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
      transition: border-color 0.3s;
    }
    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #007BFF;
      outline: none;
    }
    button {
      background: #007BFF;
      color: #fff;
      padding: 12px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 15px;
      width: 100%;
      font-size: 16px;
      transition: background 0.3s;
    }
    button:hover { background: #0056b3; }
    .error { color: red; margin-top: 10px; }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Logowanie</h2>
    <form method="post">
      <input type="text" name="username" placeholder="Nazwa użytkownika" required>
      <input type="password" name="password" placeholder="Hasło" required>
      <button type="submit">Zaloguj się</button>
    </form>
    <?php if (isset($error)): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
  </div>
</body>
</html>
