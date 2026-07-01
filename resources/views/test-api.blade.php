<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #1a1a2e; color: #eee; font-family: monospace; padding: 30px; }
        .card { background: #16213e; border: 1px solid #0f3460; }
        .form-control { background: #0f3460; color: #eee; border: 1px solid #533483; }
        .form-control:focus { background: #0f3460; color: #eee; border-color: #e94560; box-shadow: none; }
        pre { background: #0f3460; padding: 15px; border-radius: 8px; white-space: pre-wrap; word-break: break-all; max-height: 500px; overflow-y: auto; }
        .badge-http { font-size: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">API Tester</h2>

        <div class="row">
            <div class="col-md-6">
                <div class="card p-4 mb-4">
                    <h5>Request</h5>
                    <form id="apiForm">
                        <div class="form-group">
                            <label>URL</label>
                            <input type="text" class="form-control" id="url" value="http://160.19.103.122:40120/YusorOnline/api/OnlinePaymentServices/Signin">
                        </div>
                        <div class="form-group">
                            <label>Body (JSON)</label>
                            <textarea class="form-control" id="body" rows="8">{
    "userId": 100589,
    "pin": "U3f@Zh",
    "providerId": 7070,
    "authUserType": 0
}</textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block" id="sendBtn">
                            Send POST Request
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-4 mb-4">
                    <h5>Response <span id="httpBadge" class="badge badge-secondary badge-http d-none"></span></h5>
                    <div id="loading" class="text-center d-none">
                        <div class="spinner-border text-danger" role="status"></div>
                        <p>Sending...</p>
                    </div>
                    <pre id="response">Waiting for request...</pre>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('apiForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const btn = document.getElementById('sendBtn');
        const loading = document.getElementById('loading');
        const responseEl = document.getElementById('response');
        const badge = document.getElementById('httpBadge');

        btn.disabled = true;
        loading.classList.remove('d-none');
        responseEl.textContent = 'Sending...';
        badge.classList.add('d-none');

        try {
            const res = await fetch('/test-api', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    url: document.getElementById('url').value,
                    body: document.getElementById('body').value
                })
            });

            const data = await res.json();

            badge.classList.remove('d-none');
            badge.textContent = 'HTTP ' + data.http_code;
            badge.className = 'badge badge-' + (data.http_code >= 200 && data.http_code < 300 ? 'success' : data.http_code >= 400 ? 'danger' : 'warning') + ' badge-http';

            if (data.error) {
                responseEl.textContent = 'Error: ' + data.error;
            } else {
                responseEl.textContent = JSON.stringify(data.response, null, 2);
            }
        } catch (err) {
            responseEl.textContent = 'Fetch Error: ' + err.message;
        } finally {
            btn.disabled = false;
            loading.classList.add('d-none');
        }
    });
    </script>
</body>
</html>
