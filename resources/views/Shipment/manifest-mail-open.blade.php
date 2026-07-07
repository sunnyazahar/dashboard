<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opening email draft</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 32px;
            background: #f8fafc;
            color: #334155;
            text-align: center;
        }
        p { margin: 0 0 12px; line-height: 1.5; }
        a {
            color: #0f766e;
            font-weight: 600;
        }
        .error { color: #b91c1c; }
    </style>
</head>
<body>
    <p><strong>Opening email draft in your mail app...</strong></p>
    <p id="status" style="font-size:14px;color:#64748b">Downloading draft with attachments.</p>
    <p id="fallback" style="display:none;font-size:14px">
        If Mail did not open, <a id="download-link" href="{{ $emlUrl }}" download="{{ $filename }}">click here to download the .eml draft</a>
        and double-click it to open in your mail app.
    </p>
    <p id="error" class="error" style="display:none"></p>

    <script>
        (function() {
            var emlUrl = @json($emlUrl);
            var filename = @json($filename);
            var statusEl = document.getElementById('status');
            var fallbackEl = document.getElementById('fallback');
            var errorEl = document.getElementById('error');

            fetch(emlUrl, { credentials: 'same-origin' })
                .then(function(response) {
                    if (!response.ok) {
                        return response.text().then(function(text) {
                            throw new Error(text || 'Could not prepare email draft.');
                        });
                    }
                    return response.blob();
                })
                .then(function(blob) {
                    var fileBlob = new Blob([blob], { type: 'message/rfc822' });
                    var blobUrl = URL.createObjectURL(fileBlob);
                    var link = document.createElement('a');
                    link.href = blobUrl;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    statusEl.textContent = 'Draft downloaded. Your mail app should open automatically.';
                    fallbackEl.style.display = 'block';
                    document.getElementById('download-link').href = blobUrl;

                    setTimeout(function() {
                        window.close();
                    }, 4000);

                    setTimeout(function() {
                        URL.revokeObjectURL(blobUrl);
                    }, 120000);
                })
                .catch(function(error) {
                    statusEl.style.display = 'none';
                    errorEl.style.display = 'block';
                    errorEl.textContent = error.message || 'Could not open email draft.';
                    fallbackEl.style.display = 'block';
                });
        })();
    </script>
</body>
</html>
