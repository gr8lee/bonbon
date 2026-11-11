<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Great Shopping' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    <!-- Reusable Navbar -->
    <nav 
        x-data="{ mobileOpen: false, scrolled: false }"
        x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
        :class="scrolled ? 'bg-white shadow-md py-3' : 'bg-white/90 backdrop-blur-md py-4 border-b border-gray-200'"
        class="fixed top-0 left-0 w-full z-50 transition-all duration-300 px-6 md:px-12 flex items-center justify-between"
    >
        <!-- Brand Logo -->
        <a href="/" class="text-xl  font-family: 'Inter', sans-serif; font-black text-gray-900 uppercase tracking-wider">
            <span class="text-cyan-600">Bon</span>Bon
        </a>

        <!-- Desktop Menu Links -->
        <ul class="hidden md:flex items-center space-x-8 font-bold text-sm text-gray-700">
            <li><a href="/" class="hover:text-cyan-600 transition">Home</a></li>
            <li><a href="/store" class="hover:text-cyan-600 transition">Store</a></li>
            <li><a href="/about" class="hover:text-cyan-600 transition">About</a></li>
            <li><a href="/contact" class="hover:text-cyan-600 transition">Contact</a></li>
        </ul>

        <!-- Mobile Hamburger Button -->
        <button @click="mobileOpen = !mobileOpen" class="md:hidden text-gray-700 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display: none;"/>
            </svg>
        </button>

        <!-- Mobile Navigation Drawer -->
        <div 
            x-show="mobileOpen" 
            x-transition 
            class="fixed inset-0 top-[60px] bg-white z-40 p-6 md:hidden flex flex-col space-y-4 font-bold text-lg"
            style="display: none;"
        >
            <a href="/" @click="mobileOpen = false">Home</a>
            <a href="/store" @click="mobileOpen = false">Store</a>
            <a href="/about" @click="mobileOpen = false">About</a>
            <a href="/contact" @click="mobileOpen = false">Contact</a>
        </div>
    </nav>

    <!-- Page Content Slot -->
    <main class="pt-16">
        @yield('content')
    </main>

</body>
</html>