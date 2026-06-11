<head>
    <meta charset="UTF-8">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href={{ asset('assets/img/aiina.png') }}>
    <title>
        PT Aplha Inovasi Indonesia
    </title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            overflow: hidden;
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .slide-up {
            animation: slideUp 1s ease forwards;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glow {
            box-shadow: 0 0 50px rgba(249, 115, 22, .35);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-orange-50 via-white to-orange-100">

    <div class="min-h-screen flex">

        <!-- Left Side -->
        <div class="hidden lg:flex lg:w-1/2 items-center justify-center relative">

            <!-- Background Circle -->
            <div class="absolute w-96 h-96 bg-orange-400 rounded-full blur-3xl opacity-20"></div>
            <div class="absolute bottom-20 right-20 w-72 h-72 bg-amber-300 rounded-full blur-3xl opacity-20"></div>

            <div class="relative z-10 text-center px-12">

                <div class="floating">
                    <img src="https://cdn-icons-png.flaticon.com/512/3500/3500833.png" class="w-72 mx-auto"
                        alt="">
                </div>

                <h1 class="text-5xl font-bold text-gray-800 mt-8">
                    Purchase Order
                </h1>

                <p class="mt-5 text-lg text-gray-600">
                    Supplier Management, Inventory Monitoring,
                    Purchase Request & Purchase Order System
                    in one platform.
                </p>

            </div>
        </div>

        <!-- Right Side -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6">

            <div class="slide-up w-full max-w-md">

                <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl glow p-10">

                    <div class="text-center">

                        <div
                            class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white text-white text-3xl mb-4">
                            <img src="{{ asset('assets/img/aiina.png') }}" alt="AIINA Logo">
                        </div>

                        <h2 class="text-3xl font-bold text-gray-800">
                            Welcome Back
                        </h2>

                        <p class="text-gray-500 mt-2">
                            Login to Purchase Order System
                        </p>

                    </div>

                    <form method="POST" action="/session" class="mt-8">
                        @csrf

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Username
                            </label>

                            <input type="text" name="name"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-100 outline-none transition"
                                placeholder="Enter username">
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Password
                            </label>

                            <input type="password" name="password"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-100 outline-none transition"
                                placeholder="Enter password">
                        </div>

                        <div class="flex justify-between items-center mb-6">

                            <label class="form-check-label" for="rememberMe">Remember me
                                <input class="form-check-input" type="checkbox" id="rememberMe" checked="">

                            </label>

                        </div>

                        <button type="submit"
                            class="w-full bg-gradient-to-r from-orange-500 to-amber-500 text-white py-3 rounded-xl font-semibold shadow-lg hover:scale-105 hover:shadow-orange-300 transition duration-300">

                            Sign In
                        </button>

                    </form>

                    <div class="mt-8 text-center text-sm text-gray-500">
                        © 2026 Purchase Order Management System
                    </div>

                </div>

            </div>

        </div>

    </div>

    <script>
        document.addEventListener('mousemove', (e) => {

            const card = document.querySelector('.glow');

            let x = (window.innerWidth / 2 - e.clientX) / 50;
            let y = (window.innerHeight / 2 - e.clientY) / 50;

            card.style.transform =
                `rotateY(${x}deg) rotateX(${-y}deg)`;
        });
    </script>
