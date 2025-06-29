<?php
global $conn;
?>

<section class="py-10 bg-green-50" style="background-image: url('uploads/baground.png'); background-size: cover; background-position: center;">
    <div class="text-center text-white bg-green-800 bg-opacity-75 p-10">
        <h1 class="text-4xl font-bold mb-4">Welcome to PLANTOPIA</h1>
        <p class="text-lg mb-6">Join the green revolution! Whether you're a beginner or a seasoned plant lover, Plantopia helps you take care of your plants with ease. Connect with a thriving community of fellow gardeners.</p>
        <a href="?page=kontak" class="bg-white text-green-800 px-6 py-3 rounded hover:bg-gray-200">Contact Us</a>
    </div>
</section>

<section class="py-10">
    <h1 class="text-3xl font-bold text-center mb-8">MARKETPLACE</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto px-4">
        <div class="border p-4 rounded shadow text-center">
            <img src="uploads/daunpandan.png" alt="Herbs Plant" class="w-full h-48 object-cover mb-2">
            <h2 class="text-xl font-semibold">Daun Pandan</h2>
            <a href="?page=Marketplace&keyword=Daun Pandan" class="bg-green-500 text-white px-4 py-2 rounded mt-2 inline-block">View</a>
        </div>
        <div class="border p-4 rounded shadow text-center">
            <img src="uploads/bungadaisy.png" alt="Orchids" class="w-full h-48 object-cover mb-2">
            <h2 class="text-xl font-semibold">Bunga Daisy</h2>
            <a href="?page=Marketplace&keyword=Bunga Daisy" class="bg-green-500 text-white px-4 py-2 rounded mt-2 inline-block">View</a>
        </div>
        <div class="border p-4 rounded shadow text-center">
            <img src="uploads/mint.png" alt="Succulents" class="w-full h-48 object-cover mb-2">
            <h2 class="text-xl font-semibold">Mint</h2>
            <a href="?page=Marketplace&keyword=Mint" class="bg-green-500 text-white px-4 py-2 rounded mt-2 inline-block">View</a>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="faq-section py-10">
    <div class="container max-w-6xl mx-auto px-4">
        <div class="faq-container">
            <div class="faq-item" onclick="toggleContent('faq1')">
                <div class="faq-header">
                    <h3 class="faq-question">Ordering for Delivery?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <p id="faq1" class="faq-answer hidden">We offer fast and reliable delivery services to your doorstep.</p>
            </div>
            <div class="faq-item" onclick="toggleContent('faq2')">
                <div class="faq-header">
                    <h3 class="faq-question">Potting Services</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <p id="faq2" class="faq-answer hidden">We provide professional potting services for your plants.</p>
            </div>
            <div class="faq-item" onclick="toggleContent('faq3')">
                <div class="faq-header">
                    <h3 class="faq-question">Do we Ship Plants?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <p id="faq3" class="faq-answer hidden">Yes, we ship plants nationwide with special packaging.</p>
            </div>
            <div class="faq-item" onclick="toggleContent('faq4')">
                <div class="faq-header">
                    <h3 class="faq-question">Ordering for Pick up?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <p id="faq4" class="faq-answer hidden">You can order online and pick up your plants at our store.</p>
            </div>
        </div>
    </div>
</section>

<!-- Join the Community -->
<section class="community-section py-10">
    <div class="container max-w-6xl mx-auto px-4">
        <h2 class="community-title">Join the Community!</h2>
        <p class="community-text">Stay updated with the latest news and special offers.</p>
        <div class="community-buttons">
            <button class="community-button">Instagram</button>
            <button class="community-button">X</button>
            <button class="community-button">WhatsApp</button>
        </div>
    </div>
</section>

<style>
    /* FAQ Section Styles */
    .faq-section {
        padding: 2rem 0;
    }

    .faq-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .faq-item {
        border: 1px solid #e5e7eb;
        border-radius: 0.25rem;
        padding: 1rem;
        cursor: pointer;
    }

    .faq-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .faq-question {
        font-size: 1.25rem;
        font-weight: bold;
    }

    .faq-answer {
        margin-top: 0.5rem;
    }

    .hidden {
        display: none;
    }

    /* Community Section Styles */
    .community-section {
        background-color: #1a3c27;
        color: white;
        text-align: center;
    }

    .community-title {
        font-size: 1.875rem;
        font-weight: bold;
    }

    .community-text {
        margin-top: 0.5rem;
    }

    .community-buttons {
        margin-top: 1rem;
        display: flex;
        justify-content: center;
        gap: 0.5rem;
    }

    .community-button {
        background-color: white;
        color: #1a3c27;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        border: none;
        cursor: pointer;
    }
</style>

<script>
    function toggleContent(id) {
        const content = document.getElementById(id);
        content.classList.toggle('hidden');
    }
</script>