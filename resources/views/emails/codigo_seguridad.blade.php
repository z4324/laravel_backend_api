<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Código de Seguridad</title>
    <style>
        body {
            background: #f0e8d9;
            margin: 0;
            padding: 0;
            font-family: 'Georgia', serif;
        }
        .container {
            max-width: 500px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #d4a017, #f1c40f);
            padding: 30px 0;
            text-align: center;
            border-bottom: 2px solid #e0b800;
        }
        .header h1 {
            color: #fff;
            font-size: 2em;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .content {
            padding: 40px;
            text-align: center;
        }
        .content h2 {
            color: #d4a017;
            font-size: 1.5em;
            margin-bottom: 20px;
            font-weight: normal;
        }
        .content p {
            color: #4a4a4a;
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .code-box {
            background: #fff3e0;
            border: 2px solid #d4a017;
            border-radius: 10px;
            padding: 20px 30px;
            display: inline-block;
            margin-bottom: 25px;
        }
        .code-box span {
            color: #d4a017;
            font-size: 2.5em;
            letter-spacing: 12px;
            font-weight: bold;
        }
        .content a {
            color: #d4a017;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        .content a:hover {
            color: #f1c40f;
        }
        .footer {
            color: #8a6d3b;
            font-size: 0.9em;
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #e0b800;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Luxury Space</h1>
        </div>
        <div class="content">
            <h2>¡Hola querido huésped!</h2>
            <p>Has solicitado un código para cambiar tu contraseña.<br>
                <b>No compartas este código con nadie.</b></p>
            <div class="code-box">
                <span>{{ $codigo }}</span>
            </div>
            <p>Si <b>no fuiste tú</b> quien solicitó este código, por favor <a href="https://luxuryspace.com/soporte">haz clic aquí</a> para reportarlo.</p>
        </div>
        <div class="footer">
            Luxury Space © 2025
        </div>
    </div>
</body>
</html>