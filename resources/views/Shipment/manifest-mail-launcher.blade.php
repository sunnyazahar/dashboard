<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manifest email draft</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 24px;
            background: #f8fafc;
            color: #1f2937;
        }
        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            max-width: 480px;
            margin: 0 auto;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }
        h1 {
            font-size: 18px;
            margin: 0 0 8px;
        }
        p, li {
            font-size: 14px;
            line-height: 1.5;
            color: #4b5563;
        }
        p { margin: 0 0 12px; }
        .status {
            font-size: 13px;
            padding: 10px 12px;
            border-radius: 8px;
            background: #ecfeff;
            color: #0f766e;
            margin-bottom: 16px;
        }
        .status.error {
            background: #fef2f2;
            color: #b91c1c;
        }
        button {
            display: block;
            width: 100%;
            border: none;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 10px;
        }
        .btn-primary {
            background: #0f766e;
            color: #fff;
        }
        .btn-primary:disabled {
            opacity: 0.7;
            cursor: wait;
        }
        .btn-secondary {
            background: #eef2f7;
            color: #1f2937;
        }
        ul {
            margin: 12px 0 0;
            padding-left: 18px;
        }
        code {
            font-size: 12px;
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Manifest email draft</h1>
        <p>Shipment <strong>{{ $shipment->shipment_number }}</strong></p>
        <p>{{ count($attachmentSources) }} PDF attachment(s) ready: manifest and combined PO documents.</p>

        <div id="status" class="status">Click the button below to open your mail app with all attachments.</div>

        <button type="button" id="open-mail-btn" class="btn-primary">Open in mail app with attachments</button>
        <button type="button" id="download-eml-btn" class="btn-secondary">Download .eml draft instead</button>

        <ul id="fallback-help" style="display:none;">
            <li>If sharing is not supported, your mail app opens with the message text.</li>
            <li>Open the downloaded <code>{{ $emlFilename }}</code> file for a draft that includes all PDFs.</li>
        </ul>
    </div>

    <script>
        (function() {
            var preview = @json($manifestMailPreview);
            var attachmentSources = @json($attachmentSources);
            var emlUrl = @json($emlUrl);
            var emlFilename = @json($emlFilename);
            var statusEl = document.getElementById('status');
            var openBtn = document.getElementById('open-mail-btn');
            var downloadEmlBtn = document.getElementById('download-eml-btn');
            var fallbackHelp = document.getElementById('fallback-help');

            function setStatus(message, isError) {
                statusEl.textContent = message;
                statusEl.className = 'status' + (isError ? ' error' : '');
            }

            function openEmlDraft(emlFilename) {
                emlFilename = emlFilename || 'draft.eml';

                return fetch(emlUrl, { credentials: 'same-origin' })
                    .then(function(response) {
                        if (!response.ok) {
                            return response.text().then(function(text) {
                                throw new Error(text || 'Could not prepare .eml draft.');
                            });
                        }
                        return response.blob();
                    })
                    .then(function(blob) {
                        var fileBlob = new Blob([blob], { type: 'message/rfc822' });
                        var blobUrl = URL.createObjectURL(fileBlob);

                        var frame = document.getElementById('manifest-eml-opener-frame');
                        if (!frame) {
                            frame = document.createElement('iframe');
                            frame.id = 'manifest-eml-opener-frame';
                            frame.style.cssText = 'display:none;width:0;height:0;border:0';
                            document.body.appendChild(frame);
                        }
                        frame.src = blobUrl;

                        var link = document.createElement('a');
                        link.href = blobUrl;
                        link.download = emlFilename;
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        setTimeout(function() { URL.revokeObjectURL(blobUrl); }, 120000);
                    });
            }

            openBtn.addEventListener('click', function() {
                openBtn.disabled = true;
                setStatus('Opening mail app with attachments...');

                openEmlDraft(emlFilename)
                    .then(function() {
                        setStatus('Mail app opened. Review the draft and send when ready.');
                    })
                    .catch(function(error) {
                        setStatus(error.message || 'Could not open mail draft.', true);
                    })
                    .finally(function() {
                        openBtn.disabled = false;
                    });
            });

            downloadEmlBtn.addEventListener('click', function() {
                downloadEmlBtn.disabled = true;
                setStatus('Downloading .eml draft...');

                fetch(emlUrl, { credentials: 'same-origin' })
                    .then(function(response) {
                        if (!response.ok) {
                            return response.text().then(function(text) {
                                throw new Error(text || 'Could not prepare .eml draft.');
                            });
                        }
                        return response.blob();
                    })
                    .then(function(blob) {
                        var blobUrl = URL.createObjectURL(new Blob([blob], { type: 'message/rfc822' }));
                        var link = document.createElement('a');
                        link.href = blobUrl;
                        link.download = emlFilename;
                        link.click();
                        setTimeout(function() { URL.revokeObjectURL(blobUrl); }, 120000);
                        setStatus('Draft downloaded. Double-click ' + emlFilename + ' in Downloads to open in your mail app with all attachments.');
                    })
                    .catch(function(error) {
                        setStatus(error.message || 'Could not download .eml draft.', true);
                    })
                    .finally(function() {
                        downloadEmlBtn.disabled = false;
                    });
            });
        })();
    </script>
</body>
</html>
