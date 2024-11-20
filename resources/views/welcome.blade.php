<!DOCTYPE html>
<html>

<head>
    <title>DOCX Viewer</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/docx-preview/0.1.20/docx-preview.min.js"></script>
    <style>
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        #viewer {
            border: 1px solid #ddd;
            padding: 20px;
            margin-top: 20px;
            min-height: 200px;
        }

        .upload-area {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>DOCX Viewer</h1>
        <div class="upload-area">
            <input type="file" id="fileInput" accept=".docx">
            <p>Pilih file DOCX atau drag & drop di sini</p>
        </div>
        <div id="viewer"></div>
    </div>

    <script>
        const fileInput = document.getElementById('fileInput');
        const viewer = document.getElementById('viewer');

        async function renderDocx(file) {
            try {
                // Pastikan tipe file yang diterima adalah DOCX
                if (file.type !== 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                    viewer.innerHTML = 'Mohon pilih file DOCX';
                    return;
                }

                const arrayBuffer = await file.arrayBuffer();

                // Render dokumen menggunakan docx-preview
                await docx.renderAsync(arrayBuffer, viewer, viewer, {
                    className: 'docx',
                    inWrapper: true,
                    ignoreWidth: false,
                    ignoreHeight: false,
                    ignoreFonts: false,
                    breakPages: true,
                    experimental: false,
                    trimXmlDeclaration: true,
                    debug: true // Aktifkan mode debug untuk debugging
                });
            } catch (error) {
                console.error('Error:', error);
                viewer.innerHTML = 'Error: Gagal memproses file DOCX';
            }
        }

        // Handle file selection
        fileInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                renderDocx(file);
            }
        });

        // Handle drag & drop
        const uploadArea = document.querySelector('.upload-area');

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#000';
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '#ccc';
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#ccc';

            const file = e.dataTransfer.files[0];
            if (file && file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                fileInput.files = e.dataTransfer.files;
                renderDocx(file);
            } else {
                alert('Mohon upload file DOCX');
            }
        });
    </script>
</body>

</html>
