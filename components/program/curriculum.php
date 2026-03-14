<?php if (isset($program) && !empty($program['curriculum'])): ?>
<section class="max-w-7xl mx-auto px-6 py-24 bg-black relative">
    <div class="text-center mb-16 fade-in">
        <h2 class="text-sm font-bold tracking-widest text-[#FFBE49] uppercase mb-4">Inside The Program</h2>
        <h3 class="text-4xl md:text-5xl font-serif text-white mb-6">The Curriculum Structure</h3>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto">A step-by-step roadmap to scale. No fluff, just practical execution frameworks.</p>
    </div>

    <!-- Timeline/Steps Layout -->
    <div class="relative max-w-4xl mx-auto">
        <!-- Connecting Line -->
        <div class="absolute left-[27px] md:left-1/2 top-10 bottom-10 w-[2px] bg-gradient-to-b from-[#FFBE49]/50 via-[#FFBE49]/20 to-transparent transform md:-translate-x-1/2 hidden sm:block"></div>

        <div class="space-y-12">
            <?php foreach ($program['curriculum'] as $index => $module): ?>
            <div class="relative flex flex-col md:flex-row items-start <?= $index % 2 == 0 ? 'md:flex-row-reverse' : '' ?> group fade-in">
                
                <!-- Center Node -->
                <div class="absolute left-0 md:left-1/2 w-14 h-14 rounded-full bg-black border-4 border-[#FFBE49] flex items-center justify-center transform -translate-y-1 md:-translate-x-1/2 z-10 shadow-[0_0_15px_rgba(255,190,73,0.3)] group-hover:scale-110 group-hover:bg-[#FFBE49] transition-all duration-300 hidden sm:flex">
                    <span class="text-[#FFBE49] font-serif font-bold group-hover:text-black transition-colors"><?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?></span>
                </div>

                <!-- Spacer for alternate layout -->
                <div class="hidden md:block md:w-1/2"></div>

                <!-- Content Card -->
                <div class="w-full md:w-1/2 pl-16 sm:pl-24 md:pl-0 <?= $index % 2 == 0 ? 'md:pr-16 text-left md:text-right' : 'md:pl-16 text-left' ?>">
                    <div class="bg-[#111] border border-white/10 p-6 md:p-8 rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.5)] group-hover:border-[#FFBE49]/40 group-hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                        
                        <!-- Mobile Number Badge -->
                        <div class="absolute top-0 right-0 w-12 h-12 bg-white/5 flex items-center justify-center rounded-bl-2xl sm:hidden">
                            <span class="text-[#FFBE49]/50 font-serif font-bold"><?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?></span>
                        </div>

                        <h4 class="text-xl md:text-2xl font-serif text-white mb-4 pr-10 sm:pr-0">
                            <?= is_array($module) ? htmlspecialchars($module['title']) : htmlspecialchars($module['title'] ?? $module) ?>
                        </h4>
                        
                        <?php if (is_array($module) && isset($module['items']) && !empty($module['items'])): ?>
                        <ul class="space-y-2 mt-4 inline-block <?= $index % 2 == 0 ? 'md:text-right md:mr-0 md:ml-auto' : 'text-left' ?>">
                            <?php foreach ($module['items'] as $item): ?>
                            <li class="flex items-start text-sm text-gray-400 group-hover:text-gray-300 transition-colors">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#FFBE49]/50 mt-1.5 mr-2 <?= $index % 2 == 0 ? 'md:ml-2 md:mr-0 md:order-2' : '' ?>"></span>
                                <span class="<?= $index % 2 == 0 ? 'md:order-1' : '' ?>"><?= htmlspecialchars($item) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        
                        <!-- Decorative glow on hover -->
                        <div class="absolute -bottom-2 -<?= $index % 2 == 0 ? 'right' : 'left' ?>-2 w-24 h-24 bg-[#FFBE49] opacity-0 group-hover:opacity-5 blur-2xl transition-opacity duration-500 rounded-full"></div>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
