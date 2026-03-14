<?php if (isset($program)): ?>
<section class="relative min-h-[70vh] flex items-center justify-center overflow-hidden bg-black pt-16">
    <!-- Background Image with Blur -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-cover bg-center scale-105 transition-transform duration-1000 ease-out" 
             style="background-image: url('<?= htmlspecialchars($program['hero_image']) ?>'); opacity: 0.6;"></div>
    </div>

    <!-- Overlay Gradient -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/60 to-black z-0"></div>

    <!-- Decorative Elements -->
    <div class="absolute inset-0 z-10 overflow-hidden">
        <div class="absolute w-[400px] h-[400px] rounded-full bg-[#FFBE49]/5 blur-[100px] top-[-10%] right-[-5%]"></div>
        <div class="absolute w-[300px] h-[300px] rounded-full bg-[#FFBE49]/5 blur-[80px] bottom-[10%] left-[-10%]"></div>
    </div>

    <!-- Content -->
    <div class="relative z-20 max-w-5xl mx-auto px-6 w-full text-center">
        <div class="fade-in">
            <span class="inline-block px-4 py-1.5 rounded-full border border-[#FFBE49]/30 bg-[#FFBE49]/10 text-[#FFBE49] text-sm font-medium tracking-wider uppercase mb-6 shadow-[0_0_15px_rgba(255,190,73,0.15)]">
                Signature Program
            </span>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-serif font-bold leading-tight mb-6 tracking-tight text-white drop-shadow-2xl">
                <?= htmlspecialchars($program['title']) ?>
            </h1>
            <p class="text-xl md:text-2xl leading-relaxed text-gray-300 max-w-3xl mx-auto mb-10">
                <?= htmlspecialchars($program['subtitle']) ?>
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-5 mt-4">
                <a href="/contact" class="px-8 py-4 rounded-full bg-[#FFBE49] text-black font-semibold text-lg hover:bg-[#ffcc66] hover:scale-105 hover:shadow-[0_0_20px_rgba(255,190,73,0.4)] transition-all duration-300 flex items-center justify-center group">
                    <span>Apply to Know Pricing</span>
                    <svg class="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="https://wa.me/+917096412268" target="_blank" class="px-8 py-4 rounded-full border border-white/30 text-white font-semibold text-lg hover:bg-white/10 hover:border-white/50 transition-all duration-300 flex items-center justify-center group backdrop-blur-sm">
                    <svg class="w-6 h-6 mr-2 group-hover:scale-110 transition-transform text-[#25D366]" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                    <span>WhatsApp Me</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Smooth Transition to next section -->
    <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-black to-transparent z-10"></div>
</section>
<?php endif; ?>
