<!-- Footer -->
<footer class="bg-gradient-to-r from-[#7B0015] to-[#AF0020] text-white">
    <!-- Contact Info Section -->
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Company Info -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Event4U</h3>
                    <p class="text-white/80 mb-4">Discover your next favorite event. <br>Grab your seat in just a few clicks.</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-twitter text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-youtube text-white"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('welcome') }}" class="text-white/80 hover:text-white transition-colors">Home</a></li>
                        <li><a href="{{ route('events.index') }}" class="text-white/80 hover:text-white transition-colors">Events</a></li>
                        <li><a href="#" class="text-white/80 hover:text-white transition-colors">Terms and Conditions</a></li>
                        <li><a href="#" class="text-white/80 hover:text-white transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Contact Us</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1.5 mr-3 text-white/80"></i>
                            <span>57125 Surakarta<br>PT Tiket.in Indonesia<br>Gedung Selatan lantai 8</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-white/80"></i>
                            <span>pnonton@tiket.in</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3 text-white/80"></i>
                            <span>+62 123456789</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright Section -->
    <div class="border-t border-white/20 py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-white/80 text-center md:text-left">© 2025 PT Tiket.in Indonesia. Created by Kelompok 4.</p>
                <div class="mt-4 md:mt-0">
                    <p class="text-white/60 text-sm">Monday - Friday: 9AM - 5PM | Saturday: 10AM - 2PM</p>
                </div>
            </div>
        </div>
    </div>
</footer>
