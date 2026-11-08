<div 
    x-data="{
        map: null,
        marker: null,
        lat: null,
        lng: null,
        initialized: false,

        init() {
            if (this.initialized) return;
            this.initialized = true;

            this.$nextTick(() => {
                setTimeout(() => {
                    this.initMap();
                }, 300);
            });
        },

        initMap() {
            if (typeof L === 'undefined') {
                console.warn('Leaflet belum ke-load, coba lagi...');
                setTimeout(() => this.initMap(), 500);
                return;
            }

            const mapEl = this.$refs.mapEl;
            if (!mapEl) return;

            const initialLat = parseFloat($wire.get('data.latitude')) || -6.0882;
            const initialLng = parseFloat($wire.get('data.longitude')) || 106.586;

            this.lat = initialLat;
            this.lng = initialLng;

            this.map = L.map(mapEl).setView([initialLat, initialLng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(this.map);

            this.marker = L.marker([initialLat, initialLng], {
                draggable: true,
            }).addTo(this.map);

            this.marker.on('dragend', (e) => {
                const pos = e.target.getLatLng();
                this.updateCoords(pos.lat, pos.lng);
            });

            this.map.on('click', (e) => {
                const { lat, lng } = e.latlng;
                this.marker.setLatLng([lat, lng]);
                this.updateCoords(lat, lng);
            });
        },

        updateCoords(lat, lng) {
            this.lat = parseFloat(lat).toFixed(7);
            this.lng = parseFloat(lng).toFixed(7);

            $wire.set('data.latitude', this.lat, false);
            $wire.set('data.longitude', this.lng, false);
        },

        detectLocation() {
            if (!navigator.geolocation) {
                alert('Browser tidak mendukung GPS.');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;

                    this.map.setView([lat, lng], 17);
                    this.marker.setLatLng([lat, lng]);
                    this.updateCoords(lat, lng);
                },
                (err) => {
                    alert('Gagal deteksi lokasi: ' + err.message);
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },
    }"
    x-init="init()"
    class="space-y-3"
>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div 
        x-ref="mapEl" 
        style="height: 400px; width: 100%; border-radius: 8px; z-index: 1;"
        class="border border-gray-300 dark:border-gray-700"
    ></div>

    <div class="flex flex-wrap gap-3 items-center">
        <button 
            type="button" 
            @click="detectLocation()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Deteksi Lokasi Saya
        </button>

        <span class="text-sm text-gray-500 dark:text-gray-400">
            Atau klik titik di peta untuk set lokasi manual
        </span>
    </div>

    <div class="grid grid-cols-2 gap-3 text-sm bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
        <div>
            <span class="text-gray-500 dark:text-gray-400">Latitude:</span>
            <span x-text="lat" class="font-mono font-semibold text-gray-900 dark:text-gray-100"></span>
        </div>
        <div>
            <span class="text-gray-500 dark:text-gray-400">Longitude:</span>
            <span x-text="lng" class="font-mono font-semibold text-gray-900 dark:text-gray-100"></span>
        </div>
    </div>
</div>