<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3 d-flex justify-content-between align-items-center">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
                    {{ str_replace('-', ' ', Request::path()) }}
                </li>
            </ol>
            <h6 class="font-weight-bolder mb-0 text-capitalize">{{ str_replace('-', ' ', Request::path()) }}</h6>
        </nav>

        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex justify-content-end align-items-center" id="navbar">

            <div class="me-4 d-none d-md-flex align-items-center bg-white px-3 py-2 rounded-3 shadow-sm border border-light-gray">
                <i class="fa fa-clock me-2" style="background-image: linear-gradient(310deg, #fb6340, #fbb140); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 0.95rem;"></i>

                <div class="d-flex flex-column align-items-start">
                    <span id="live-clock" class="font-weight-bolder text-dark" style="font-family: 'Open Sans', sans-serif; font-size: 1rem; letter-spacing: -0.5px; line-height: 1.2;">
                        00:00
                    </span>
                    <span id="live-date" class="text-secondary font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px; text-transform: uppercase; margin-top: 1px;">
                        Kamis, 11 Jun 2026
                    </span>
                </div>
            </div>

            <ul class="navbar-nav justify-content-end">
                <li class="nav-item dropdown d-flex align-items-center">

                    <a href="javascript:;" class="nav-link text-body font-weight-bold px-0 d-flex align-items-center" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar avatar-sm text-white rounded-circle me-sm-2 d-flex align-items-center justify-content-center font-weight-bold text-xs" style="width: 32px; height: 32px; background: #fb6340;">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="d-sm-inline d-none me-1 text-sm text-dark font-weight-bold">{{ Auth::user()->name ?? 'User' }}</span>
                        <i class="fa fa-caret-down text-xs opacity-6 text-dark"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4 shadow-lg border-0 border-radius-md" aria-labelledby="dropdownMenuButton">
                        <li class="mb-2">
                            <a class="dropdown-item border-radius-md d-flex align-items-center py-2" href="javascript:;">
                                <i class="fa fa-user-circle text-secondary me-2"></i>
                                <span class="text-sm">My Profile</span>
                            </a>
                        </li>
                        <hr class="horizontal dark mt-2 mb-2">
                        <li>
                            <a class="dropdown-item border-radius-md d-flex align-items-center py-2 text-danger font-weight-bold"
                               href="javascript:;"
                               onclick="confirmLogout();">
                                <i class="fa fa-sign-out-alt me-2"></i>
                                <span class="text-sm">Sign Out</span>
                            </a>
                            <form id="logout-form" action="{{ url('/logout') }}" method="get" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>

                </li>
            </ul>
        </div>

    </div>
</nav>

<script>
    // ==========================================
    // LOGIC JAM & TANGGAL REAL-TIME MINIMALIS
    // ==========================================
    function updateClock() {
        const now = new Date();

        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        // Efek titik dua (:) berkedip lembut tiap detik
        const blinkColon = now.getSeconds() % 2 === 0 ? ':' : '<span style="opacity: 0.25">:</span>';
        document.getElementById('live-clock').innerHTML = `${hours}${blinkColon}${minutes}`;

        // Format Tanggal Indonesia
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        const dayName = days[now.getDay()];
        const dayNum = now.getDate();
        const monthName = months[now.getMonth()];
        const year = now.getFullYear();

        document.getElementById('live-date').textContent = `${dayName}, ${dayNum} ${monthName} ${year}`;
    }
    setInterval(updateClock, 1000);
    updateClock(); // Jalankan instan tanpa nunggu 1 detik pertama

    // ==========================================
    // LOGIC MODAL ALERT MODERN (SweetAlert2)
    // ==========================================
    function confirmLogout() {
        Swal.fire({
            title: 'Yakin Ingin Keluar?',
            text: "Sesi Anda akan diakhiri dan Anda harus login kembali.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#fb6340', // Warna orange murni dashboard
            cancelButtonColor: '#6c757d', // Abu-abu sekunder
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal',
            reverseButtons: true, // Menaruh tombol 'Batal' di sebelah kiri
            customClass: {
                popup: 'border-radius-md shadow-lg',
                confirmButton: 'btn bg-gradient-danger text-white px-4',
                cancelButton: 'btn bg-light text-dark px-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form POST logout tersembunyi
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>
