<x-filament-panels::page>
    <div class="space-y-6" x-data="absensiApp()" x-init="init()">

        {{-- Info DUDI --}}
        @if($dudi)
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-800">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    <strong>Lokasi PKL:</strong> {{ $dudi->nama_perusahaan }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Radius maksimal: {{ $radius }} meter dari lokasi DUDI
                </p>
            </div>
        @else
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-700">Data penempatan PKL tidak ditemukan. Hubungi admin.</p>
            </div>
        @endif

        {{-- Status Hari Ini --}}
        @if($absensi_hari_ini)
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <p class="text-xs text-gray-500">Jam Masuk</p>
                    <p class="text-2xl font-bold">{{ $absensi_hari_ini->jam_masuk ?? '-' }}</p>
                    <p class="text-xs mt-1">
                        Status: <span class="px-2 py-0.5 rounded {{ $absensi_hari_ini->status_masuk === 'valid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $absensi_hari_ini->status_masuk }}
                        </span>
                    </p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <p class="text-xs text-gray-500">Jam Keluar</p>
                    <p class="text-2xl font-bold">{{ $absensi_hari_ini->jam_keluar ?? '-' }}</p>
                    <p class="text-xs mt-1">
                        Status: <span class="px-2 py-0.5 rounded {{ $absensi_hari_ini->status_keluar === 'valid' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $absensi_hari_ini->status_keluar ?? 'belum' }}
                        </span>
                    </p>
                </div>
            </div>
        @endif

        {{-- Kamera --}}
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-3">Kamera Selfie</h3>
            <div class="flex flex-col items-center gap-4">
                <video x-ref="video" autoplay playsinline class="w-full max-w-md rounded-lg border-2 border-gray-300" style="display: none;"></video>
                <canvas x-ref="canvas" style="display: none;"></canvas>
                <img x-ref="preview" class="w-full max-w-md rounded-lg border-2 border-gray-300" style="display: none;" />

                <div class="flex gap-2">
                    <button type="button" @click="startCamera()" x-show="!cameraActive"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Buka Kamera
                    </button>
                    <button type="button" @click="capture()" x-show="cameraActive && !photoTaken"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Ambil Foto
                    </button>
                    <button type="button" @click="retake()" x-show="photoTaken"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Ulangi
                    </button>
                </div>
            </div>
        </div>

        {{-- Lokasi GPS --}}
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-3">Lokasi GPS</h3>
            <div class="flex gap-2 items-center">
                <button type="button" @click="getLocation()"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Deteksi Lokasi
                </button>
                <span x-text="locationStatus" class="text-sm text-gray-600 dark:text-gray-400"></span>
            </div>
            <p class="text-xs mt-2 text-gray-500" x-show="lat">
                Lat: <span x-text="lat"></span>, Long: <span x-text="lng"></span>
            </p>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex gap-3">
            <button type="button" @click="submitAbsen('masuk')" :disabled="loading || !canSubmit('masuk')"
                    class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!loading">Absen Masuk</span>
                <span x-show="loading">Memproses...</span>
            </button>
            <button type="button" @click="submitAbsen('keluar')" :disabled="loading || !canSubmit('keluar')"
                    class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!loading">Absen Keluar</span>
                <span x-show="loading">Memproses...</span>
            </button>
        </div>

        {{-- Notifikasi --}}
        <div x-show="message" x-transition
             :class="success ? 'bg-green-100 text-green-800 border-green-300' : 'bg-red-100 text-red-800 border-red-300'"
             class="p-4 rounded-lg border" x-text="message"></div>

    </div>

    @push('scripts')
    <script>
        function absensiApp() {
            return {
                cameraActive: false,
                photoTaken: false,
                photoData: null,
                lat: null,
                lng: null,
                locationStatus: '',
                loading: false,
                message: '',
                success: false,
                hasMasuk: {{ $absensi_hari_ini && $absensi_hari_ini->jam_masuk ? 'true' : 'false' }},
                hasKeluar: {{ $absensi_hari_ini && $absensi_hari_ini->jam_keluar ? 'true' : 'false' }},

                init() {
                    this.$nextTick(() => {
                        // optional auto-start
                    });
                },

                async startCamera() {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: 'user', width: 640, height: 480 }
                        });
                        this.$refs.video.srcObject = stream;
                        this.$refs.video.style.display = 'block';
                        this.$refs.preview.style.display = 'none';
                        this.cameraActive = true;
                        this.photoTaken = false;
                    } catch (e) {
                        alert('Tidak bisa mengakses kamera: ' + e.message);
                    }
                },

                capture() {
                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    this.photoData = canvas.toDataURL('image/jpeg', 0.7);
                    this.$refs.preview.src = this.photoData;
                    this.$refs.preview.style.display = 'block';
                    this.$refs.video.style.display = 'none';

                    const stream = video.srcObject;
                    if (stream) stream.getTracks().forEach(t => t.stop());

                    this.cameraActive = false;
                    this.photoTaken = true;
                },

                retake() {
                    this.photoTaken = false;
                    this.photoData = null;
                    this.startCamera();
                },

                getLocation() {
                    this.locationStatus = 'Mendeteksi...';
                    if (!navigator.geolocation) {
                        this.locationStatus = 'Browser tidak mendukung GPS.';
                        return;
                    }
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            this.lat = pos.coords.latitude;
                            this.lng = pos.coords.longitude;
                            this.locationStatus = 'Lokasi terdeteksi.';
                        },
                        (err) => {
                            this.locationStatus = 'Gagal: ' + err.message;
                        },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                },

                canSubmit(tipe) {
                    if (!this.photoData || !this.lat || !this.lng) return false;
                    if (tipe === 'masuk' && this.hasMasuk) return false;
                    if (tipe === 'keluar' && (!this.hasMasuk || this.hasKeluar)) return false;
                    return true;
                },

                async submitAbsen(tipe) {
                    this.loading = true;
                    this.message = '';

                    const url = tipe === 'masuk' ? '/absensi/masuk' : '/absensi/keluar';

                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                latitude: this.lat,
                                longitude: this.lng,
                                foto: this.photoData,
                            }),
                        });

                        const data = await res.json();
                        this.success = data.success;
                        this.message = data.message;

                        if (data.success) {
                            if (tipe === 'masuk') this.hasMasuk = true;
                            if (tipe === 'keluar') this.hasKeluar = true;
                            setTimeout(() => window.location.reload(), 1500);
                        }
                    } catch (e) {
                        this.success = false;
                        this.message = 'Error: ' + e.message;
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
    @endpush
</x-filament-panels::page>