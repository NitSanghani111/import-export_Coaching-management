<?php if (isset($program) && (!empty($program['tools_resources']) || !empty($program['deliverables']))): ?>
<section class="max-w-7xl mx-auto px-6 py-20 bg-black relative">
    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-[#FFBE49]/20 to-transparent"></div>
    
    <div class="text-center mb-16 fade-in">
        <h2 class="text-4xl md:text-5xl font-serif text-white mb-6">What's Included</h2>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto">Everything you need to execute, tracking templates, and resources to build your systems.</p>
    </div>

    <div class="grid grid-cols-1 <?= !empty($program['tools_resources']) && !empty($program['deliverables']) ? 'lg:grid-cols-2' : '' ?> gap-8">
        
        <?php if (!empty($program['tools_resources'])): ?>
        <!-- Tools & Resources -->
        <div class="slide-in-left bg-[#0A0A0A] border border-white/5 rounded-[32px] p-8 md:p-10 shadow-[0_15px_40px_rgba(0,0,0,0.6)] hover:border-white/10 transition-colors">
            <div class="flex items-center mb-8">
                <div class="w-12 h-12 rounded-full bg-[#FFBE49]/10 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-[#FFBE49]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-serif text-white">Tools & Resources</h3>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php foreach ($program['tools_resources'] as $tool): ?>
                <div class="bg-[#111] p-4 rounded-xl border border-white/5 hover:border-[#FFBE49]/30 hover:bg-[#151515] transition-all duration-300 group hover-target">
                    <div class="flex items-start">
                        <div class="w-1.5 h-1.5 rounded-full bg-gray-600 mt-2 mr-3 flex-shrink-0 hover-bullet transition-all duration-300"></div>
                        <p class="text-sm text-gray-400 group-hover:text-gray-200 transition-colors leading-relaxed"><?= htmlspecialchars($tool) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($program['deliverables'])): ?>
        <!-- Deliverables -->
        <div class="slide-in-right bg-[#0A0A0A] border border-white/5 rounded-[32px] p-8 md:p-10 shadow-[0_15px_40px_rgba(0,0,0,0.6)] hover:border-white/10 transition-colors">
            <div class="flex items-center mb-8">
                <div class="w-12 h-12 rounded-full bg-[#FFBE49]/10 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-[#FFBE49]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="text-2xl font-serif text-white">Program Deliverables</h3>
            </div>
            
            <ul class="space-y-4">
                <?php foreach ($program['deliverables'] as $deliverable): ?>
                <li class="flex items-center p-4 bg-[#111] rounded-xl border border-white/5 hover:border-[#FFBE49]/30 hover:-translate-y-1 transition-all duration-300 group">
                    <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center mr-4 group-hover:bg-[#FFBE49]/20 transition-colors">
                        <span class="text-[#FFBE49] text-xs">✔</span>
                    </div>
                    <span class="text-gray-300 font-medium text-sm group-hover:text-white transition-colors"><?= htmlspecialchars($deliverable) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
    </div>
</section>
<?php endif; ?>
