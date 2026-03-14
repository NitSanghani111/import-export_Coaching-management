<?php if (isset($program) && !empty($program['faq'])): ?>
<section class="max-w-4xl mx-auto px-6 py-20 bg-black relative">
    <div class="text-center mb-14 fade-in">
        <h2 class="text-3xl md:text-5xl font-serif text-white mb-4">Frequently Asked Questions</h2>
        <p class="text-gray-400 text-lg">Everything you need to know about the program before applying.</p>
    </div>

    <div class="space-y-4 fade-in">
        <?php foreach ($program['faq'] as $index => $faq): ?>
        <div class="border border-white/10 rounded-2xl bg-[#0c0c0c] overflow-hidden hover:border-white/20 transition-colors">
            <button class="w-full px-6 py-5 text-left flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-[#FFBE49]/50 transition-colors" onclick="toggleFAQ(this)">
                <span class="text-lg font-medium text-white"><?= htmlspecialchars($faq['question']) ?></span>
                <span class="icon text-gray-500 text-2xl font-light w-6 text-center transition-transform hover:text-[#FFBE49]">+</span>
            </button>
            <div class="max-height-0 overflow-hidden transition-all duration-300" style="max-height: 0;">
                <div class="px-6 pb-6 pt-2 text-gray-400 leading-relaxed border-t border-white/5 mt-2">
                    <?= htmlspecialchars($faq['answer']) ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
