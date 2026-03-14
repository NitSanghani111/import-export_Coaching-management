<?php if (isset($program)): ?>
<section class="max-w-7xl mx-auto px-6 py-20 bg-black relative">
    <div class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-[#FFBE49]/30 to-transparent"></div>
    
    <div class="text-center mb-16 fade-in">
        <h2 class="text-3xl md:text-5xl font-serif text-white mb-4">Is This Program Right For You?</h2>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto">We value your time. This program is highly targeted for specific founders to ensure maximum results.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 relative z-10">
        <!-- Yes Box -->
        <div class="slide-in-left rounded-[32px] bg-[radial-gradient(ellipse_200%_100%_at_50%_0%,rgba(30,40,30,0.4)_0%,rgba(10,10,10,0.95)_100%)] p-8 md:p-12 border border-[#4ADE80]/20 shadow-[0_20px_40px_rgba(0,0,0,0.5)] transform hover:-translate-y-2 transition-transform duration-500">
            <div class="w-16 h-16 rounded-full bg-[#4ADE80]/10 flex items-center justify-center border border-[#4ADE80]/30 mb-8 mx-auto md:mx-0">
                <svg class="w-8 h-8 text-[#4ADE80]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <h3 class="text-3xl font-serif text-white mb-8 text-center md:text-left">Who This Is For</h3>
            
            <ul class="space-y-6">
                <?php foreach ($program['who_its_for'] as $item): ?>
                <li class="flex items-start group">
                    <div class="mr-4 mt-1 shrink-0 w-5 h-5 rounded-full bg-[#4ADE80]/15 border border-[#4ADE80]/40 flex items-center justify-center group-hover:bg-[#4ADE80]/30 transition-colors">
                        <svg class="w-3 h-3 text-[#4ADE80]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-gray-300 text-base group-hover:text-white transition-colors"><?= htmlspecialchars($item) ?></span>
                </li>
                <?php endforeach; ?>
                
                <?php if (isset($program['how_it_works'])): ?>
                    <li class="pt-6 mt-6 border-t border-white/10">
                        <strong class="block text-white mb-3 tracking-wide text-sm font-semibold uppercase text-[#FFBE49]">How It Works:</strong>
                        <ul class="space-y-3">
                            <?php foreach ($program['how_it_works'] as $howItem): ?>
                                <li class="flex items-center text-sm text-gray-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#FFBE49] mr-3"></span>
                                    <?= htmlspecialchars($howItem) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- No Box -->
        <div class="slide-in-right rounded-[32px] bg-[radial-gradient(ellipse_200%_100%_at_50%_0%,rgba(40,20,20,0.4)_0%,rgba(10,10,10,0.95)_100%)] p-8 md:p-12 border border-[#F87171]/20 shadow-[0_20px_40px_rgba(0,0,0,0.5)] transform hover:-translate-y-2 transition-transform duration-500">
            <div class="w-16 h-16 rounded-full bg-[#F87171]/10 flex items-center justify-center border border-[#F87171]/30 mb-8 mx-auto md:mx-0">
                <svg class="w-8 h-8 text-[#F87171]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            
            <h3 class="text-3xl font-serif text-white mb-8 text-center md:text-left">Not For You If</h3>
            
            <ul class="space-y-6">
                <?php foreach ($program['not_for_you'] as $item): ?>
                <li class="flex items-start group">
                    <div class="mr-4 mt-1 shrink-0 w-5 h-5 rounded-full bg-[#F87171]/15 border border-[#F87171]/40 flex items-center justify-center group-hover:bg-[#F87171]/30 transition-colors">
                        <svg class="w-3 h-3 text-[#F87171]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <span class="text-gray-300 text-base group-hover:text-white transition-colors"><?= htmlspecialchars($item) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            
            <div class="mt-12 p-6 rounded-2xl bg-black/50 border border-white/5 text-center">
                <p class="text-sm text-gray-400 italic">"The wrong program for the wrong person wastes everyone's time. We want you to win."</p>
            </div>
        </div>
    </div>
    
    <div class="absolute bottom-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
</section>
<?php endif; ?>
