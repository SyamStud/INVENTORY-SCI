<div class="{{ $position }}">
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "{{ $position }}",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
    </script>

    @if (session('incomplete'))
        <script>
            Toast.fire({
                icon: "warning",
                title: "{{ session('incomplete') }}"
            });
        </script>
    @elseif(session('error'))
        <script>
            Toast.fire({
                icon: "error",
                title: "{{ session('error') }}"
            });
        </script>
    @elseif (session('incomplete'))
        <script>
            Toast.fire({
                icon: "warning",
                title: "{{ session('incomplete') }}"
            });
        </script>
    @elseif (session('success'))
        <script>
            Toast.fire({
                icon: "success",
                title: "{{ session('success') }}"
            });
        </script>
    @endif
</div>
