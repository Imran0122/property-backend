<!-- Hero Section -->
<section class="relative bg-gray-100">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="https://www.zameen.com/assets/homepage-banner.jpg" 
             alt="Hero Banner" 
             class="w-full h-[500px] object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-6 py-16 text-center text-white">
        <h1 class="text-3xl md:text-5xl font-bold mb-6">
            Find Your Dream Home
        </h1>
        <p class="mb-8 text-lg">Buy, Rent, or Invest in properties across Pakistan</p>

        <!-- Search Box -->
        <div class="bg-white rounded-lg shadow-lg p-6 text-black">
            <!-- Tabs -->
            <div class="flex space-x-4 mb-6 border-b">
                <button class="py-2 px-4 font-semibold border-b-2 border-green-600 text-green-600">
                    Buy
                </button>
                <button class="py-2 px-4 font-semibold text-gray-600 hover:text-green-600">
                    Rent
                </button>
            </div>

            <!-- Filters Grid -->
            <form action="#" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <!-- City -->
                <select name="city" class="border rounded-lg p-2">
                    <option value="">Select City</option>
                    <option>Karachi</option>
                    <option>Lahore</option>
                    <option>Islamabad</option>
                </select>

                <!-- Location -->
                <input type="text" name="location" placeholder="Location" 
                       class="border rounded-lg p-2">

                <!-- Property Type -->
                <select name="type" class="border rounded-lg p-2">
                    <option value="">Property Type</option>
                    <option>House</option>
                    <option>Flat</option>
                    <option>Plot</option>
                    <option>Commercial</option>
                </select>

                <!-- Price -->
                <select name="price" class="border rounded-lg p-2">
                    <option value="">Price Range</option>
                    <option>Up to 50 Lac</option>
                    <option>50 Lac - 1 Crore</option>
                    <option>1 Crore - 5 Crore</option>
                </select>

                <!-- Beds -->
                <select name="beds" class="border rounded-lg p-2">
                    <option value="">Beds</option>
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4+</option>
                </select>

                <!-- Area -->
                <input type="text" name="area" placeholder="Area (Marla/Kanal)" 
                       class="border rounded-lg p-2">

                <!-- Keyword -->
                <input type="text" name="keyword" placeholder="Keyword" 
                       class="border rounded-lg p-2">

                <!-- More Filters (Expandable) -->
                <div x-data="{ open: false }" class="col-span-full">
                    <button type="button" @click="open = !open" 
                            class="text-green-600 font-semibold">+ More Filters</button>

                    <div x-show="open" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <select class="border rounded-lg p-2">
                            <option value="">Furnishing</option>
                            <option>Furnished</option>
                            <option>Unfurnished</option>
                        </select>

                        <select class="border rounded-lg p-2">
                            <option value="">Construction Status</option>
                            <option>New</option>
                            <option>Used</option>
                        </select>

                        <select class="border rounded-lg p-2">
                            <option value="">Purpose</option>
                            <option>Residential</option>
                            <option>Commercial</option>
                        </select>
                    </div>
                </div>

                <!-- Search Button -->
                <div class="col-span-full flex justify-center mt-4">
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg shadow">
                        Search Properties
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
