<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Your Password</title>
</head>
<body>  
  <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.5; color: #222; background-color: #fff5f5; padding: 20px;">
    <h2 style="color: darkred; font-size: 22px; margin-bottom: 8px; font-weight: 600;">Password Reset Request</h2>

    <p style="font-size: 15px;">Hello,</p>
    <p style="font-size: 15px;">We received a request to reset your account password.</p>
    <p style="font-size: 15px;">If this was you, click the link below to set a new password:</p>

    <p>
      <a href="<?= esc($reset_url) ?>" style="display: inline-block; padding: 10px 18px; background-color: darkred; color: white; font-size: 14px; font-weight: bold; text-decoration: none; border-radius: 4px;">Reset Password</a>
    </p>

    <p style="margin-top: 12px; word-break: break-all; color: #555; font-size: 13px;">Or copy and paste this link into your browser:<br><?= esc($reset_url) ?></p>

    <p style="font-size: 14px;">This link will expire in 5 minutes for your security.</p>
    <p style="font-size: 14px;">If you didn’t request a password reset, you can safely ignore this email.</p>

    <br>
    <p style="font-size: 12px; color: #888888;">
      &copy; <?= date('Y') ?> SrvKit Authentication
    </p>
  </div>
</body>
</html>

