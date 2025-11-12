<?php
// Replace with your own password
$password = "hustle@10";

// Hash the password using PHP's secure bcrypt algorithm
$hashed = password_hash($password, PASSWORD_DEFAULT);

echo "Your password: " . $password . "<br>";
echo "Hashed version:<br><strong>" . $hashed . "</strong>";
?>
