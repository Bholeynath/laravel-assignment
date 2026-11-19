
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - URL Shortener</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
        }

        header {
            background: #1e293b;
            color: white;
            padding: 18px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header button {
            background: #dc2626;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 5px;
            cursor: pointer;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .card {
            background: white;
            padding: 22px;
            border-radius: 8px;
            margin-bottom: 22px;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
        }

        h2 { margin-top: 0; }

        input, select {
            width: 100%;
            padding: 11px;
            margin: 8px 0 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .primary {
            background: #2563eb;
            color: white;
            padding: 11px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            overflow-wrap: anywhere;
        }

        th { background: #f8fafc; }

        .message { margin-top: 12px; }
        .success { color: green; }
        .error { color: red; }

        .user-info { color: #cbd5e1; }
    </style>
</head>
<body>

<header>
    <div>
        <h2>URL Shortener</h2>
        <div class="user-info" id="userInfo"></div>
    </div>

    <button onclick="logout()">Logout</button>
</header>

<div class="container">

    <div class="card">
        <h2>Dashboard</h2>
        <p>Welcome, <strong id="userName"></strong></p>
        <p>Role: <strong id="userRole"></strong></p>
    </div>

    <div class="card" id="createUrlSection">
        <h2>Create Short URL</h2>

        <form id="urlForm">
            <label>Original URL</label>
            <input type="url" id="originalUrl"
                   placeholder="https://example.com" required>

            <button class="primary" type="submit">
                Create Short URL
            </button>
        </form>

        <div id="urlMessage" class="message"></div>
    </div>

    <div class="card">
        <h2>Short URLs</h2>

        <table>
            <thead>
                <tr>
                    <th>Short URL</th>
                    <th>Original URL</th>
                    <th>Short Code</th>
                    <th>Created At</th>
                </tr>
            </thead>

            <tbody id="urlTable">
                <tr>
                    <td colspan="4">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card" id="invitationSection" style="display:none;">
        <h2>Send Invitation</h2>

        <form id="invitationForm">

            <label>Name</label>
            <input type="text" id="inviteName" required>

            <label>Email</label>
            <input type="email" id="inviteEmail" required>

            <label>Role</label>
            <select id="inviteRole" required></select>

            <div id="companyField">
                <label>Company Name</label>
                <input type="text" id="companyName">
            </div>

            <button class="primary" type="submit">
                Send Invitation
            </button>
        </form>

        <div id="inviteMessage" class="message"></div>
    </div>

</div>

<script>
    const token = localStorage.getItem('auth_token');
    const user = JSON.parse(localStorage.getItem('user') || 'null');

    if (!token || !user) {
        window.location.href = '/';
    }

    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + token
    };

    document.getElementById('userName').innerText = user.name;
    document.getElementById('userRole').innerText = user.role;
    document.getElementById('userInfo').innerText =
        user.name + ' (' + user.role + ')';

    // Role-based sections
    if (user.role === 'SuperAdmin') {
        document.getElementById('createUrlSection').style.display = 'none';
        document.getElementById('invitationSection').style.display = 'block';

        document.getElementById('inviteRole').innerHTML =
            '<option value="Admin">Admin</option>';

        document.getElementById('companyField').style.display = 'block';
        document.getElementById('companyName').required = true;

    } else if (user.role === 'Admin') {
        document.getElementById('invitationSection').style.display = 'block';

        document.getElementById('inviteRole').innerHTML =
            '<option value="Admin">Admin</option>' +
            '<option value="Member">Member</option>';

        document.getElementById('companyField').style.display = 'none';
        document.getElementById('companyName').required = false;
    }

    // Fetch Short URLs
    async function loadUrls() {
        const table = document.getElementById('urlTable');

        try {
            const response = await fetch('/api/v1/short-urls', {
                headers: headers
            });

            const result = await response.json();

            if (!response.ok || !result.status) {
                throw new Error(result.message || 'Unable to fetch URLs.');
            }

            table.innerHTML = '';

            if (!result.data.length) {
                table.innerHTML =
                    '<tr><td colspan="4">No Short URLs found.</td></tr>';
                return;
            }

            result.data.forEach(url => {
                const row = document.createElement('tr');

                const shortUrl = document.createElement('a');
                shortUrl.href = url.short_url;
                shortUrl.target = '_blank';
                shortUrl.rel = 'noopener noreferrer';
                shortUrl.innerText = url.short_url;

                const values = [
                    shortUrl,
                    url.original_url,
                    url.short_code,
                    url.created_at
                ];

                values.forEach(value => {
                    const cell = document.createElement('td');

                    if (value instanceof HTMLElement) {
                        cell.appendChild(value);
                    } else {
                        cell.innerText = value ?? '';
                    }

                    row.appendChild(cell);
                });

                table.appendChild(row);
            });

        } catch (error) {
            table.innerHTML =
                '<tr><td colspan="4">' + error.message + '</td></tr>';
        }
    }

    // Create Short URL
    document.getElementById('urlForm').addEventListener('submit',
        async function(e) {
            e.preventDefault();

            const message = document.getElementById('urlMessage');

            try {
                const response = await fetch('/api/v1/short-urls', {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({
                        original_url:
                            document.getElementById('originalUrl').value
                    })
                });

                const result = await response.json();

                if (!response.ok || !result.status) {
                    throw new Error(result.message || 'URL creation failed.');
                }

                message.className = 'message success';
                message.innerText = result.message;

                this.reset();
                loadUrls();

            } catch (error) {
                message.className = 'message error';
                message.innerText = error.message;
            }
        }
    );

    // Send Invitation
    document.getElementById('invitationForm').addEventListener('submit',
        async function(e) {
            e.preventDefault();

            const message = document.getElementById('inviteMessage');

            const data = {
                name: document.getElementById('inviteName').value,
                email: document.getElementById('inviteEmail').value,
                role: document.getElementById('inviteRole').value
            };

            if (user.role === 'SuperAdmin') {
                data.company_name =
                    document.getElementById('companyName').value;
            }

            try {
                const response = await fetch('/api/v1/invitations', {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (!response.ok || !result.status) {
                    throw new Error(result.message || 'Invitation failed.');
                }

                message.className = 'message success';
                message.innerText = result.message +
                    ' Invitation URL: ' + result.data.invitation_url;

                this.reset();

            } catch (error) {
                message.className = 'message error';
                message.innerText = error.message;
            }
        }
    );

    // Logout
    async function logout() {
        try {
            await fetch('/api/v1/logout', {
                method: 'POST',
                headers: headers
            });
        } finally {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            window.location.href = '/';
        }
    }

    loadUrls();
</script>

</body>
</html>
