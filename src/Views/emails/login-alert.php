<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Security Alert</title>
</head>
<body>
    <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.5; color: #222; background-color: #fff5f5; padding: 20px;">
        <h2 style="margin:0 0 15px 0; font-size: 20px; font-weight: bold; color: darkred;">
            Security Alert
        </h2>
        <p style="margin:0 0 15px 0; font-size: 14px; line-height: 1.5;">
            Hello,<br><br>
            We detected a new login to your account:
        </p>
        <table role="presentation" border="1" cellpadding="8" cellspacing="0" style="font-size:14px; color:#333; margin-bottom:15px; border-collapse: collapse; border-color: darkred; width:100%;">
            <tr>
                <td><strong>IP Address:</strong></td>
                <td><?= $ipAddress ?></td>
            </tr>
            <tr>
                <td><strong>Location:</strong></td>
                <td><?= $location ?></td>
            </tr>
            <tr>
                <td><strong>Device:</strong></td>
                <td><?= $device ?></td>
            </tr>
            <tr>
                <td><strong>Time:</strong></td>
                <td><?= $loginTime ?></td>
            </tr>
        </table>
        <p style="margin:0 0 15px 0; font-size: 14px; line-height: 1.5;">
            If this was you, no further action is needed.<br>
            If this wasn’t you, please secure your account immediately:
        </p>
        <p style="margin:0;">
            <a href="<?= $secureAccountUrl ?>" 
               style="background-color:darkred; color:#ffffff; padding:10px 20px; text-decoration:none; border-radius:4px; display:inline-block; font-size:14px;">
               Secure My Account
            </a>
        </p>
        <p style="margin: 20px 0 0 0; font-size: 12px; color:#777;">
            &copy; <?= date('Y') ?> Your Company. All rights reserved.
        </p>
    </div>
</body>
</html>
