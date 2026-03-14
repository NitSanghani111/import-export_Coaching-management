<?php if (isset($program)): ?>
<section class="max-w-7xl mx-auto px-6 py-24 relative bg-black">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <!-- Text Content -->
        <div class="slide-in-left order-2 lg:order-1">
            <h2 class="text-[#FFBE49] tracking-widest text-sm font-bold uppercase mb-4 pl-1 border-l-2 border-[#FFBE49]">Program Overview</h2>
            <h3 class="text-3xl md:text-5xl font-serif text-white mb-8 leading-tight">
                Designed for ambitious founders ready to scale.
            </h3>
            <p class="text-gray-400 text-lg leading-relaxed mb-10">
                <?= htmlspecialchars($program['description']) ?>
            </p>
            
            <h4 class="text-xl font-medium text-white mb-6">What You Will Achieve:</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                <?php foreach ($program['achievements'] as $achievement): ?>
                <div class="flex items-start group">
                    <div class="mt-1 min-w-[20px] mr-3">
                        <div class="w-5 h-5 rounded-full bg-[#FFBE49]/10 flex items-center justify-center border border-[#FFBE49]/30 group-hover:bg-[#FFBE49] group-hover:scale-110 transition-all duration-300">
                            <svg class="w-3 h-3 text-[#FFBE49] group-hover:text-black transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <span class="text-gray-300 group-hover:text-white transition-colors duration-300"><?= htmlspecialchars($achievement) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Image/Visual Content -->
        <div class="slide-in-right order-1 lg:order-2 w-full max-w-lg mx-auto lg:max-w-none">
            <div class="relative rounded-2xl overflow-hidden aspect-[4/5] shadow-[0_0_40px_rgba(255,190,73,0.15)] border border-white/5 group">
                <!-- Inner glow -->
                <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent z-10"></div>
                <!-- Frame overlay -->
                <div class="absolute inset-4 border border-[#FFBE49]/20 rounded-xl z-20 pointer-events-none group-hover:scale-105 group-hover:border-[#FFBE49]/50 transition-all duration-700"></div>
                
                <img src="<?= htmlspecialchars($program['hero_image']) ?>" alt="Program Overview" class="w-full h-full object-cover filter grayscale-[30%] group-hover:grayscale-0 group-hover:scale-105 transition-all duration-700 ease-[cubic-bezier(0.25,1,0.5,1)]" />
                
                <div class="absolute bottom-8 left-8 right-8 z-30 transform translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500 delay-100">
                    <div class="bg-black/80 backdrop-blur-md border border-white/10 p-5 rounded-xl">
                        <p class="text-[#FFBE49] font-serif italic text-lg text-center">"Results-driven execution over theory."</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
