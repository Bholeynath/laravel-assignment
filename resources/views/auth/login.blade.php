
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - URL Shortener</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        #message {
            margin-top: 15px;
            text-align: center;
            color: #dc2626;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>URL Shortener</h2>
    <p class="subtitle">Login to your account</p>

    <form id="loginForm">
        <label>Email Address</label>
        <input type="email" id="email" required
               placeholder="Enter your email">

        <label>Password</label>
        <input type="password" id="password" required
               placeholder="Enter your password">

        <button type="submit" id="loginButton">Login</button>

        <div id="message"></div>
    </form>
</div>

<script>
    const loginForm = document.getElementById('loginForm');
    const message = document.getElementById('message');
    const loginButton = document.getElementById('loginButton');

    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        message.innerText = '';
        loginButton.disabled = true;
        loginButton.innerText = 'Logging in...';

        try {
            const response = await fetch('/api/v1/login', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value
                })
            });

            const result = await response.json();

            if (!response.ok || !result.status) {
                throw new Error(result.message || 'Login failed.');
            }

            localStorage.setItem('auth_token', result.data.token);
            localStorage.setItem(
                'user',
                JSON.stringify(result.data.user)
            );

            window.location.href = '/dashboard';

        } catch (error) {
            message.innerText = error.message;
        } finally {
            loginButton.disabled = false;
            loginButton.innerText = 'Login';
        }
    });
</script>

</body>
</html>
