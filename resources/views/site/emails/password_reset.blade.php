<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lip Slay Home Salon</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #edf2f7;
            color: #718096;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            width: 100%;
            background-color: #edf2f7;
        }

        .header {
            padding: 25px 0;
            text-align: center;
        }

        .header a {
            color: #3d4852;
            font-size: 19px;
            font-weight: bold;
            text-decoration: none;
        }

        .content-cell {
            max-width: 100vw;
            padding: 32px;
        }
    </style>
</head>

<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center" class="header">
                <a href="http://127.0.0.1:8000">
                    <h1 style="color: #3d4852; font-size: 18px; font-weight: bold; margin-top: 0; text-align: left;">
                        Lip Slay Home Salon
                    </h1>
                </a>
            </td>
        </tr>

        <tr>
            <td class="content-cell">
                <h1 style="color: #3d4852; font-size: 18px; font-weight: bold; margin-top: 0; text-align: left;">
                    Hello!
                </h1>
                <p style="font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                    You are receiving this email because we received a password reset request for your account.
                </p>
                <p style="font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                    Your New Password is {{ $password }}.
                </p>
                <p style="font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                    Regards,<br />
                    Lip Slay Home Salon
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
