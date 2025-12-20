    </main>

    <!-- Footer -->
    <footer class="bg-dark-card border-t border-dark-border mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent mb-4">IE Coaching</h3>
                    <p class="text-gray-400">Your trusted partner in import/export business education and consulting.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="/index.php" class="text-gray-400 hover:text-white transition">Home</a></li>
                        <li><a href="/blog.php" class="text-gray-400 hover:text-white transition">Blog</a></li>
                        <li><a href="/events.php" class="text-gray-400 hover:text-white transition">Events</a></li>
                        <li><a href="/about.php" class="text-gray-400 hover:text-white transition">About</a></li>
                        <li><a href="/contact.php" class="text-gray-400 hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Info</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Email: info@iecoaching.com</li>
                        <li>Phone: +1 (555) 123-4567</li>
                        <li>Address: 123 Business St, City</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-dark-border mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> Import/Export Coaching. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    </script>
</body>
</html>
