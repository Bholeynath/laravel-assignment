
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Invitation</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .box {
            background: white;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            border-radius: 8px;
            box-shadow: 0 3px 15px rgba(0,0,0,.08);
        }

        h2, p { text-align: center; }

        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #message { margin-top: 15px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>

<div class="box">
    <h2>Accept Invitation</h2>
    <p>Create your account password</p>

    <form id="acceptForm">
        <label>Password</label>
        <input type="password" id="password" required minlength="8">

        <label>Confirm Password</label>
        <input type="password" id="confirmPassword" required minlength="8">

        <button type="submit" id="submitBtn">Accept Invitation</button>
    </form>

    <div id="message"></div>
</div>

<script>
    const token = new URLSearchParams(window.location.search).get('token');

    const form = document.getElementById('acceptForm');
    const message = document.getElementById('message');

    if (!token) {
        form.style.display = 'none';
        message.className = 'error';
        message.innerText = 'Invalid invitation link. Token not found.';
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const password = document.getElementById('password').value;
        const confirmPassword =
            document.getElementById('confirmPassword').value;

        if (password !== confirmPassword) {
            message.className = 'error';
            message.innerText = 'Passwords do not match.';
            return;
        }

        const button = document.getElementById('submitBtn');
        button.disabled = true;
        button.innerText = 'Please wait...';

        try {
            const response = await fetch('/api/v1/invitations/accept', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    token: token,
                    password: password,
                    password_confirmation: confirmPassword
                })
            });

            const result = await response.json();

            if (!response.ok || !result.status) {
                throw new Error(result.message || 'Invitation acceptance failed.');
            }

            message.className = 'success';
            message.innerText = result.message;

            setTimeout(() => {
                window.location.href = '/';
            }, 1500);

        } catch (error) {
            message.className = 'error';
            message.innerText = error.message;
        } finally {
            button.disabled = false;
            button.innerText = 'Accept Invitation';
        }
    });
</script>

</body>
</html>
