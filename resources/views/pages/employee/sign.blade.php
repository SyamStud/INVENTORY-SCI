{{-- resources/views/documents/sign.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document Signing</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        #signatureCanvas {
            border: 2px dashed #ccc;
            border-radius: 4px;
            touch-action: none;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <div class="flex gap-4">
            <!-- PDF Preview Section -->
            <div class="w-1/2 bg-white rounded-lg shadow-md p-4">
                <h2 class="text-lg font-semibold mb-4">Preview Dokumen</h2>
                <embed src="{{ route('document.preview', $document->id) }}" type="application/pdf" width="100%"
                    height="600px" />
            </div>

            <!-- Signature Pad Section -->
            <div class="w-1/2 bg-white rounded-lg shadow-md p-4">
                <h2 class="text-lg font-semibold mb-4">Tanda Tangan</h2>
                <canvas id="signatureCanvas" width="500" height="300" class="w-full bg-white mb-4"></canvas>
                <div class="flex gap-2">
                    <button id="clearButton" class="w-1/2 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Clear
                    </button>
                    <button id="saveButton" class="w-1/2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const canvas = document.getElementById('signatureCanvas');
            const ctx = canvas.getContext('2d');
            let isDrawing = false;
            let lastX = 0;
            let lastY = 0;

            // Set up canvas context
            ctx.strokeStyle = '#000000';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';

            // Drawing functions
            function startDrawing(e) {
                isDrawing = true;
                [lastX, lastY] = [
                    e.offsetX || e.touches[0].clientX - canvas.offsetLeft,
                    e.offsetY || e.touches[0].clientY - canvas.offsetTop
                ];
            }

            function draw(e) {
                if (!isDrawing) return;

                e.preventDefault();

                const currentX = e.offsetX || e.touches[0].clientX - canvas.offsetLeft;
                const currentY = e.offsetY || e.touches[0].clientY - canvas.offsetTop;

                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(currentX, currentY);
                ctx.stroke();

                [lastX, lastY] = [currentX, currentY];
            }

            function stopDrawing() {
                isDrawing = false;
            }

            // Event listeners for mouse
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);

            // Event listeners for touch devices
            canvas.addEventListener('touchstart', startDrawing);
            canvas.addEventListener('touchmove', draw);
            canvas.addEventListener('touchend', stopDrawing);

            // Clear signature
            $('#clearButton').click(function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            });

            // Save signature
            $('#saveButton').click(function() {
                const signatureData = canvas.toDataURL('image/png');

                $.ajax({
                    url: '{{ route('signature.store') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        document_id: '{{ $document->id }}',
                        signature: signatureData
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Tanda tangan berhasil disimpan!');
                            window.location.href = response.redirect_url;
                        } else {
                            alert('Gagal menyimpan tanda tangan.');
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                });
            });
        });
    </script>
</body>

</html>
