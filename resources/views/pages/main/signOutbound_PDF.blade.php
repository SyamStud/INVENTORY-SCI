{{-- resources/views/documents/sign.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<x-header></x-header>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <div class="flex gap-4">
            <!-- PDF Preview Section -->
            <div class="w-1/2 bg-white rounded-lg shadow-md p-4">
                <h2 class="text-lg font-semibold mb-4">Preview Dokumen</h2>
                <embed src="{{ route('documents.outbounds.preview', $outbound->id) }}" type="application/pdf"
                    width="100%" height="600px" />
            </div>

            <!-- Signature Pad Section -->
            <div class="w-1/2 bg-white rounded-lg shadow-md p-4">
                <h2 class="text-lg font-semibold mb-4">TANDA TANGAN A.N {{ strtoupper($outbound->received_by) }}</h2>
                <canvas id="signatureCanvas" width="500" height="300" class="w-full bg-white mb-4"></canvas>
                <div class="flex gap-2">
                    <button id="clearButton" class="w-1/2 px-4 py-2 bg-gray-700 text-white rounded-md font-medium">
                        Hapus
                    </button>
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'save-signature')"
                        class="w-1/2 px-4 py-2 bg-green-700 text-white rounded-md  font-medium">
                        Simpan
                    </button>
                </div>
                <!-- Loading indicator -->
                <div id="loadingIndicator" class="hidden mt-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                </div>
                <!-- Success/Error messages -->
                <div id="messageArea" class="mt-4 text-center hidden">
                    <p id="messageText" class="px-4 py-2 rounded"></p>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="save-signature" :show="false">
        <div class="p-5">
            <h5 class="font-semibold text-md">Simpan Tanda Tangan</h5>

            <p class="mt-5">Apakah Anda yakin ingin menyimpan tanda tangan? Tanda tangan yang sudah disimpan <span
                    class="font-bold">tidak dapat diubah</span></p>

            <div class="w-full flex justify-end">
                <button id="save-button" type="submit" onclick="saveSignature()"
                    class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                    Ya, Simpan
                </button>
            </div>
        </div>
    </x-modal>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const canvas = document.getElementById('signatureCanvas');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });

        $(document).ready(function() {

            // Resize canvas to fit container
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear();
            }

            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();

            // Clear button handler
            document.getElementById('clearButton').addEventListener('click', function() {
                signaturePad.clear();
            });
        });

        // Save button handler
        function saveSignature() {
            if (signaturePad.isEmpty()) {
                alert('Please provide a signature first.');
                return;
            }

            const loadingIndicator = document.getElementById('loadingIndicator');
            const messageArea = document.getElementById('messageArea');
            const messageText = document.getElementById('messageText');

            // Show loading indicator
            loadingIndicator.classList.remove('hidden');
            messageArea.classList.add('hidden');

            // Get the signature as base64 image
            const signatureData = signaturePad.toDataURL('image/png');

            $('#save-button').prop('disabled', true);
            $('#save-button').css('cursor', 'not-allowed');
            $('#save-button').css('background-color', '#ccc');
            $('#save-button').html('Menyimpan...');

            // Send to server
            $.ajax({
                url: '{{ route('documents.sign.store', $outbound->id) }}',
                method: 'POST',
                data: {
                    signature: signatureData,
                    type: 'outbound-client',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    window.location.href = '{{ route('documents.outbounds.index') }}';
                },
                error: function(xhr) {
                    console.error(xhr);
                    loadingIndicator.classList.add('hidden');
                    messageArea.classList.remove('hidden');
                    messageText.textContent = 'Error saving signature. Please try again.';
                    messageText.classList.add('bg-red-100', 'text-red-700');
                    messageText.classList.remove('bg-green-100', 'text-green-700');
                }
            });
        }
    </script>
</body>

</html>
