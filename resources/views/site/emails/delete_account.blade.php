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
            text-align: center;
        }

        .wrapper {
            width: 100%;
            background-color: #edf2f7;
            text-align: center;
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
            text-align: center;
        }

        .button {
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            position: relative;
            -webkit-text-size-adjust: none;
            border-radius: 4px;
            color: #fff;
            display: inline-block;
            overflow: hidden;
            text-decoration: none;
            background-color: #2d3748;
            border: 8px solid #2d3748;
            margin: 10px auto; /* Center the button horizontally */
            padding: 10px 20px;
            text-align: center;
        }

        .button:hover {
            background-color: #1a202c;
            border-color: #1a202c;
        }
    </style>
</head>

<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center" class="header">
                <a href="{{ route('storeHome') }}">
                    <h1 style="color: #3d4852; font-size: 18px; font-weight: bold; margin-top: 0; text-align: center;">
                        Lip Slay Home Salon
                    </h1>
                </a>
            </td>
        </tr>

        <tr>
            <td class="content-cell">
                <h1 style="color: #3d4852; font-size: 18px; font-weight: bold; margin-top: 0; text-align: center;">
                    Hello!
                </h1>
                <p style="font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: center;">
                    You are receiving this email because we received a delete Account request for your account.
                </p>
                <a href="{{ route('deleteAccountPage', ['id' => $id]) }}" class="button" target="_blank" rel="noopener">Delete Account</a>
                <p style="font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: center;">
                    If you did not request a password reset, no further action is required
                </p>
                <p style="font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: center;">
                    Regards,<br />
                    Lip Slay Home Salon
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
