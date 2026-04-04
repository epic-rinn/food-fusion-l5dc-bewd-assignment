<?php
$page_title = 'Educational Resources – Food Fusion';
$meta_description = 'Downloadable resources, infographics, and videos on renewable energy topics including solar, wind, and sustainable living.';
require_once dirname(__DIR__) . '/includes/header.php';

$pdf_url = 'https://www.edf-re.com/wp-content/uploads/How-Solar-Works-2024.pdf';

$downloads = [
    ['title' => 'Understanding Solar Photovoltaics', 'desc' => 'Covers how sunlight is converted into electricity through photovoltaic cells, panel types, and efficiency ratings explained in plain language.', 'emoji' => '☀️', 'type' => 'PDF Guide', 'pages' => '20 pages'],
    ['title' => 'Wind Energy Fundamentals', 'desc' => 'Explains onshore and offshore wind turbine mechanics, energy output calculations, and environmental considerations for wind farm development.', 'emoji' => '💨', 'type' => 'PDF Guide', 'pages' => '16 pages'],
    ['title' => 'Household Energy Savings Checklist', 'desc' => 'A room-by-room printable checklist to identify energy waste in your home — covering insulation, appliances, lighting, and heating systems.', 'emoji' => '📋', 'type' => 'Checklist', 'pages' => '4 pages'],
    ['title' => 'Electric vs. Petrol Vehicles: True Cost', 'desc' => 'A data-backed worksheet comparing total ownership costs of EVs and petrol cars including fuel, maintenance, insurance, and depreciation.', 'emoji' => '🚗', 'type' => 'Worksheet', 'pages' => '10 pages'],
    ['title' => 'Introduction to Hydropower', 'desc' => 'How dams and run-of-river systems generate clean electricity, their role in global energy supply, and ecological trade-offs.', 'emoji' => '🌊', 'type' => 'PDF Guide', 'pages' => '14 pages'],
    ['title' => 'Carbon Offset Strategies for Students', 'desc' => 'Practical steps students can take to reduce their personal carbon footprint — from transport and diet choices to digital habits.', 'emoji' => '🌱', 'type' => 'Action Guide', 'pages' => '8 pages'],
];

$infographics = [
    ['title' => 'Anatomy of a Solar Panel', 'desc' => 'A layer-by-layer visual showing how silicon wafers, anti-reflective coatings, and wiring come together to capture sunlight.', 'emoji' => '🔆', 'color' => 'from-yellow-100 to-amber-100'],
    ['title' => 'World Energy Sources Breakdown', 'desc' => 'Pie chart and bar graph showing the percentage share of fossil fuels vs. renewables in global electricity generation.', 'emoji' => '🌍', 'color' => 'from-green-100 to-emerald-100'],
    ['title' => 'Your Daily Carbon Footprint', 'desc' => 'Visual comparison of CO₂ emissions from driving, flying, eating meat, streaming, and using household appliances.', 'emoji' => '👣', 'color' => 'from-blue-100 to-cyan-100'],
    ['title' => 'Water Footprint of Energy Sources', 'desc' => 'Litres of water consumed per megawatt-hour for coal, natural gas, nuclear, solar, and wind power generation.', 'emoji' => '💧', 'color' => 'from-sky-100 to-blue-100'],
    ['title' => 'From Blade to Grid: Wind Turbine Lifecycle', 'desc' => 'Timeline infographic tracing a turbine from raw materials and factory assembly through 25 years of service to decommissioning.', 'emoji' => '🌬️', 'color' => 'from-teal-100 to-green-100'],
    ['title' => 'How Smart Grids Balance Supply & Demand', 'desc' => 'Diagram showing how sensors, battery banks, and software coordinate distributed energy sources across a modern power network.', 'emoji' => '⚡', 'color' => 'from-purple-100 to-indigo-100'],
];

$videos = [
    ['title' => 'Renewable Energy Sources Explained', 'desc' => 'National Geographic breaks down how solar, wind, hydro, and geothermal energy are harnessed and why transitioning to renewables is critical.', 'youtube_id' => '1kUE0BZtTRc'],
    ['title' => 'How Wind Turbines Generate Power', 'desc' => 'An engineering walkthrough of a modern wind turbine — from blade aerodynamics and the nacelle gearbox to grid-connected generators.', 'youtube_id' => '4do1-H-XKVM'],
    ['title' => 'The Future of Battery Storage', 'desc' => 'Examines how lithium-ion, solid-state, and grid-scale battery systems are solving the intermittency challenge of renewable energy.', 'youtube_id' => 'EoTVtB-cSps'],
];

$facts = [
    ['icon' => '☀️', 'stat' => '173,000 TW', 'label' => 'Solar energy striking Earth every second — over 10,000 times total global energy consumption.'],
    ['icon' => '💨', 'stat' => '10%', 'label' => 'Of worldwide electricity generation now comes from wind and solar power combined.'],
    ['icon' => '🔋', 'stat' => '90%', 'label' => 'Fall in lithium-ion battery prices since 2010, enabling large-scale energy storage.'],
    ['icon' => '🌱', 'stat' => '80%', 'label' => 'Of new power generation capacity added around the world in 2023 was from renewable sources.'],
];
?>

<!-- Hero Section -->
<div class="bg-gradient-to-br from-green-50 via-emerald-50 to-white py-16 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-72 h-72 bg-green-100 rounded-full -translate-y-1/2 translate-x-1/3 opacity-50"></div>
    <div class="absolute bottom-0 left-0 w-56 h-56 bg-emerald-100 rounded-full translate-y-1/3 -translate-x-1/4 opacity-50"></div>
    <div class="max-w-5xl mx-auto px-4 text-center relative z-10">
        <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full mb-4">EDUCATIONAL RESOURCES</span>
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">Learn About Renewable Energy</h1>
        <p class="text-gray-500 max-w-2xl mx-auto text-lg">
            Explore free PDF guides, data-rich infographics, and expert video explanations covering solar, wind, hydro, and battery technologies — everything you need to understand the clean energy transition.
        </p>
    </div>
</div>

<!-- Key Facts -->
<div class="max-w-6xl mx-auto px-4 -mt-6 relative z-20 mb-10">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php foreach ($facts as $fact): ?>
        <div class="bg-white border border-gray-100 rounded-2xl p-5 text-center shadow-sm">
            <div class="text-2xl mb-2"><?= $fact['icon'] ?></div>
            <div class="text-xl font-extrabold text-gray-900"><?= $fact['stat'] ?></div>
            <p class="text-gray-500 text-xs mt-1"><?= $fact['label'] ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Downloadable Resources Section -->
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-3">Downloadable Resources</h2>
        <p class="text-gray-500 max-w-xl mx-auto">Grab these free PDF guides, checklists, and worksheets to build a solid foundation in renewable energy and sustainable living.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($downloads as $dl): ?>
        <div class="bg-white border border-gray-100 rounded-2xl p-6 hover:shadow-lg transition group">
            <div class="w-14 h-14 bg-green-50 rounded-xl flex items-center justify-center text-3xl mb-4 group-hover:scale-110 transition"><?= $dl['emoji'] ?></div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-medium bg-green-100 text-green-700 px-2 py-0.5 rounded-full"><?= $dl['type'] ?></span>
                <span class="text-xs text-gray-400"><?= $dl['pages'] ?></span>
            </div>
            <h3 class="font-bold text-gray-900 text-lg mb-1"><?= $dl['title'] ?></h3>
            <p class="text-gray-500 text-sm mb-4"><?= $dl['desc'] ?></p>
            <a href="<?= $pdf_url ?>" target="_blank"
               class="w-full bg-green-50 text-green-700 font-medium text-sm py-2.5 rounded-xl hover:bg-green-100 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V3"/></svg>
                Download PDF
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Infographics Section -->
<div class="bg-gray-50 py-16">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Infographics</h2>
            <p class="text-gray-500 max-w-xl mx-auto">Bite-sized visual explainers that turn complex energy data into clear, shareable graphics — great for research projects and presentations.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($infographics as $info): ?>
            <div class="bg-white rounded-2xl overflow-hidden hover:shadow-lg transition group">
                <div class="bg-gradient-to-br <?= $info['color'] ?> h-40 flex items-center justify-center">
                    <span class="text-5xl group-hover:scale-110 transition"><?= $info['emoji'] ?></span>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-gray-900 mb-1"><?= $info['title'] ?></h3>
                    <p class="text-gray-500 text-sm mb-3"><?= $info['desc'] ?></p>
                    <a href="<?= $pdf_url ?>" target="_blank"
                       class="text-green-600 text-sm font-medium hover:text-green-700 transition inline-flex items-center gap-1">
                        View Infographic
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Videos Section -->
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-3">Educational Videos</h2>
        <p class="text-gray-500 max-w-xl mx-auto">Hand-picked videos that explain the science and engineering behind renewable energy in an accessible, engaging way.</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <?php foreach ($videos as $vid): ?>
        <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-lg transition">
            <div class="aspect-video">
                <iframe
                    src="https://www.youtube.com/embed/<?= $vid['youtube_id'] ?>"
                    title="<?= htmlspecialchars($vid['title']) ?>"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    class="w-full h-full">
                </iframe>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-gray-900 mb-1"><?= $vid['title'] ?></h3>
                <p class="text-gray-500 text-sm"><?= $vid['desc'] ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Newsletter CTA -->
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="bg-green-600 rounded-3xl p-10 md:p-14 text-center text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-green-500 rounded-full -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-green-700 rounded-full translate-y-1/2 -translate-x-1/4"></div>
        <div class="relative z-10">
            <!-- Subscribe Form -->
            <div id="edu-form">
                <h2 class="text-2xl md:text-3xl font-bold mb-3">Stay Updated on Clean Energy</h2>
                <p class="text-green-100 mb-6 max-w-md mx-auto">Subscribe to receive monthly digests on renewable energy breakthroughs, new learning resources, and sustainability tips.</p>
                <div class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input type="email" id="edu-email" placeholder="Enter your email" required
                        class="flex-1 px-5 py-3 rounded-xl text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                    <button onclick="subscribeEdu()" class="bg-gray-900 text-white font-semibold px-6 py-3 rounded-xl hover:bg-gray-800 transition text-sm whitespace-nowrap">
                        Subscribe
                    </button>
                </div>
                <p id="edu-error" class="text-green-200 text-xs mt-3 hidden">Please enter a valid email address.</p>
            </div>
            <!-- Success Message -->
            <div id="edu-success" class="hidden">
                <div class="text-5xl mb-4">🎉</div>
                <h2 class="text-2xl md:text-3xl font-bold mb-3">You're Subscribed!</h2>
                <p class="text-green-100 max-w-md mx-auto">Thanks for joining! You'll receive your first clean energy digest soon.</p>
            </div>
        </div>
    </div>
</div>

<script>
function subscribeEdu() {
    const email = document.getElementById('edu-email').value.trim();
    const error = document.getElementById('edu-error');
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        error.classList.remove('hidden');
        return;
    }
    error.classList.add('hidden');
    document.getElementById('edu-form').classList.add('hidden');
    document.getElementById('edu-success').classList.remove('hidden');
}
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
