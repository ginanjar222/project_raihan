@extends('layouts.scan-layout')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="scan-wrapper">
    <div class="scan-container">
        <div class="scan-header">
            <h2 class="scan-title">
                <i class="fas fa-qrcode me-2"></i>
                Sistem Presensi RFID
            </h2>
            <p class="scan-subtitle">Tempelkan kartu RFID Anda pada reader</p>
        </div>

        <!-- Menambahkan area kamera untuk auto capture -->
        <div class="camera-section">
            <div class="camera-container">
                <video id="cameraVideo" autoplay muted playsinline></video>
                <canvas id="captureCanvas" style="display: none;"></canvas>
                <div class="camera-overlay">
                    <div class="camera-frame">
                        <div class="camera-corners">
                            <div class="corner top-left"></div>
                            <div class="corner top-right"></div>
                            <div class="corner bottom-left"></div>
                            <div class="corner bottom-right"></div>
                        </div>
                    </div>
                </div>
                <div class="camera-status" id="camera-status">
                    <i class="fas fa-camera"></i>
                    <span>Kamera Standby</span>
                </div>
            </div>
        </div>

        <div class="status-section">
            <!-- Status display -->
            <div class="status-container" id="scan-status">
                <div class="status-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h4 class="status-text">Menunggu Scan Kartu...</h4>
                <div class="status-details" id="status-details" style="display: none;">
                    <div class="guru-info">
                        <div class="guru-avatar" id="guru-avatar"></div>
                        <div class="guru-data">
                            <div class="guru-name" id="guru-name"></div>
                            <div class="guru-position" id="guru-position"></div>
                            <div class="guru-time" id="guru-time"></div>
                            <div class="guru-verification mt-2" id="guru-verification"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions panel -->
            <div class="instructions-panel">
                <h6><i class="fas fa-info-circle me-2"></i>Petunjuk Penggunaan</h6>
                <ul>
                    <li><i class="fas fa-check me-2"></i>Pastikan kartu RFID dalam kondisi baik</li>
                    <li><i class="fas fa-check me-2"></i>Tempelkan kartu pada area scanner</li>
                    <li><i class="fas fa-check me-2"></i>Tunggu konfirmasi keberhasilan scan</li>
                    <li><i class="fas fa-camera me-2"></i>Kamera otomatis mengambil foto saat scan</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.scan-wrapper {
    height: calc(100vh - 140px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    margin: 0;
    overflow: hidden;
}

.scan-container {
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    animation: slideInUp 0.8s ease-out;
    width: 100%;
    max-width: 550px;
    box-sizing: border-box;
    position: relative;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.scan-header {
    margin-bottom: 2rem;
}

.scan-title {
    color: #1f2937;
    font-weight: 700;
    font-size: 2.2rem;
    margin-bottom: 0.8rem;
}

.scan-subtitle {
    color: #6b7280;
    font-size: 1.1rem;
    font-weight: 400;
}

.status-section {
    margin: 2rem 0;
    position: relative;
}

.camera-container {
    width: 400px;
    height: 280px;
    margin: 0 auto;
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    border: 3px solid #64b6f7;
    background: #000;
}

.camera-status {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

.camera-status.capturing {
    background: rgba(34, 197, 94, 0.8);
    animation: pulse 0.5s ease-in-out;
}

.camera-status.error {
    background: rgba(239, 68, 68, 0.8);
}

@keyframes pulse {
    0%, 100% { transform: translateX(-50%) scale(1); }
    50% { transform: translateX(-50%) scale(1.1); }
}

.status-container {
    background: #f9fafb;
    border-radius: 18px;
    padding: 1.5rem;
    margin: 1.5rem 0;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.status-icon {
    font-size: 2.5rem;
    color: #64b6f7;
    margin-bottom: 0.8rem;
}

.status-text {
    color: #1f2937;
    font-weight: 600;
    margin-bottom: 0.8rem;
    font-size: 1.2rem;
}

.status-details {
    margin-top: 1rem;
}

.guru-info {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    background: #ffffff;
    padding: 1rem;
    border-radius: 15px;
    border: 1px solid #e5e7eb;
}

.guru-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #64b6f7, #007acc);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.5rem;
}

.guru-data {
    text-align: left;
    color: #1f2937;
}

.guru-name {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 0.2rem;
}

.guru-position {
    font-size: 0.9rem;
    color: #6b7280;
    margin-bottom: 0.2rem;
}

.guru-time {
    font-size: 0.8rem;
    color: #9ca3af;
}

.instructions-panel {
    background: #f9fafb;
    border-radius: 15px;
    padding: 1rem;
    margin-top: 1rem;
    text-align: left;
    border: 1px solid #e5e7eb;
}

.instructions-panel h6 {
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.instructions-panel ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.instructions-panel li {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 0.3rem;
}

/* Status variations */
.status-success {
    background: #f0fdf4 !important;
    border-color: #22c55e !important;
}

.status-success .status-icon i {
    color: #22c55e !important;
}

.status-error {
    background: #fef2f2 !important;
    border-color: #ef4444 !important;
}

.status-error .status-icon i {
    color: #ef4444 !important;
}

.status-processing {
    background: #fffbeb !important;
    border-color: #f59e0b !important;
}

.status-processing .status-icon i {
    color: #f59e0b !important;
}

/* Animations */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .scan-container {
        padding: 2rem;
        margin: 0 1rem;
    }
    
    .scan-title {
        font-size: 1.8rem;
    }
    
    /* Responsive styling untuk kamera */
    .camera-container {
        width: 250px;
        height: 170px;
    }
    
    .guru-info {
        flex-direction: column;
        text-align: center;
    }
    
    .guru-data {
        text-align: center;
    }
}
</style>

<script>
class KeyboardShortcutHandler {
    constructor() {
        this.keySequence = []
        this.requiredSequence = ['Control', 'c', 'b']
        this.timeout = null
        this.isOnScanPage = true
        this.isNavigating = false
        this.lastKeyTime = 0
        this.keyPressCount = 0
        
        document.addEventListener('keydown', (e) => this.handleKeyDown(e), true)
        document.addEventListener('keyup', (e) => this.handleKeyUp(e), true)
        
        // Deteksi navigasi dengan mendengarkan click event
        document.addEventListener('click', () => {
            this.isNavigating = true
            setTimeout(() => {
                this.isNavigating = false
            }, 500)
        })
    }
    
    handleKeyDown(e) {
        if (this.isNavigating || this.isInputFocused()) {
            this.keySequence = []
            return
        }
        
        const currentTime = Date.now()
        
        // Reset jika lebih dari 3 detik sejak key terakhir
        if (currentTime - this.lastKeyTime > 3000) {
            this.keySequence = []
        }
        
        this.lastKeyTime = currentTime
        
        if (e.key === 'Control') {
            this.keySequence = ['Control']
            clearTimeout(this.timeout)
            this.timeout = setTimeout(() => {
                this.keySequence = []
            }, 3000)
            return
        }
        
        if (this.keySequence.length > 0 && this.keySequence[0] === 'Control') {
            const key = e.key.toLowerCase()
            
            // Jangan tambahkan jika key yang sama diulang
            if (this.keySequence.length > 1 && this.keySequence[this.keySequence.length - 1] === key) {
                return
            }
            
            this.keySequence.push(key)
            
            if (this.keySequence.length === this.requiredSequence.length) {
                if (this.keySequence[0] === 'Control' && 
                    this.keySequence[1] === 'c' && 
                    this.keySequence[2] === 'b') {
                    console.log('[v0] Kombinasi Ctrl+C+B terdeteksi di halaman scan, membuka halaman login...')
                    this.openLoginPage()
                }
                this.keySequence = []
            }
        }
        
        if (e.key.toLowerCase() === 'p' && !this.isInputFocused() && !e.ctrlKey && !e.altKey && !e.metaKey) {
            e.preventDefault()
            this.toggleFullscreen()
        }
    }
    
    handleKeyUp(e) {
        // Reset jika Ctrl dilepas
        if (e.key === 'Control') {
            clearTimeout(this.timeout)
            this.timeout = setTimeout(() => {
                this.keySequence = []
            }, 1000)
        }
    }
    
    isInputFocused() {
        const activeElement = document.activeElement
        return activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA' || activeElement.tagName === 'SELECT'
    }
    
    toggleFullscreen() {
        const elem = document.documentElement
        
        if (!document.fullscreenElement) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen()
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen()
            } else if (elem.mozRequestFullScreen) {
                elem.mozRequestFullScreen()
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen()
            }
            console.log('[v0] Fullscreen mode activated')
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen()
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen()
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen()
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen()
            }
            console.log('[v0] Fullscreen mode deactivated')
        }
    }
    
    openLoginPage() {
        this.isNavigating = true
        window.location.href = '{{ route("login") }}'
    }
}

class CameraHandler {
    constructor() {
        this.video = null
        this.canvas = null
        this.context = null
        this.stream = null
        this.isInitialized = false
        
        console.log("Camera Handler initialized")
        this.init()
    }
    
    async init() {
        try {
            this.video = document.getElementById('cameraVideo')
            this.canvas = document.getElementById('captureCanvas')
            this.context = this.canvas.getContext('2d')
            
            await this.startCamera()
        } catch (error) {
            console.error('Error initializing camera:', error)
            this.updateCameraStatus('error', 'Kamera tidak tersedia')
        }
    }
    
    async startCamera() {
        try {
            this.updateCameraStatus('loading', 'Mengaktifkan kamera...')
            
            const constraints = {
                video: {
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: 'user'
                },
                audio: false
            }
            
            this.stream = await navigator.mediaDevices.getUserMedia(constraints)
            this.video.srcObject = this.stream
            
            this.video.onloadedmetadata = () => {
                this.canvas.width = this.video.videoWidth
                this.canvas.height = this.video.videoHeight
                this.isInitialized = true
                this.updateCameraStatus('ready', 'Kamera Standby')
                console.log('Camera initialized successfully')
            }
            
        } catch (error) {
            console.error('Error starting camera:', error)
            this.updateCameraStatus('error', 'Gagal mengaktifkan kamera')
            
            // Show permission dialog info
            if (error.name === 'NotAllowedError') {
                this.updateCameraStatus('error', 'Izin kamera ditolak')
            } else if (error.name === 'NotFoundError') {
                this.updateCameraStatus('error', 'Kamera tidak ditemukan')
            }
        }
    }
    
    capturePhoto() {
        return new Promise((resolve, reject) => {
            try {
                if (!this.isInitialized || !this.video || !this.canvas) {
                    reject(new Error('Camera not initialized'))
                    return
                }
                
                this.updateCameraStatus('capturing', 'Mengambil foto...')
                
                // Draw video frame to canvas
                this.context.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height)
                
                // Convert to blob
                this.canvas.toBlob((blob) => {
                    if (blob) {
                        console.log('Photo captured successfully, size:', blob.size)
                        this.updateCameraStatus('ready', 'Foto berhasil diambil')
                        
                        // Reset status after 2 seconds
                        setTimeout(() => {
                            this.updateCameraStatus('ready', 'Kamera Standby')
                        }, 2000)
                        
                        resolve(blob)
                    } else {
                        reject(new Error('Failed to capture photo'))
                    }
                }, 'image/jpeg', 0.8)
                
            } catch (error) {
                console.error('Error capturing photo:', error)
                this.updateCameraStatus('error', 'Gagal mengambil foto')
                reject(error)
            }
        })
    }
    
    updateCameraStatus(type, message) {
        const statusElement = document.getElementById('camera-status')
        if (!statusElement) return
        
        statusElement.className = 'camera-status'
        statusElement.classList.add(type)
        
        const icon = statusElement.querySelector('i')
        const text = statusElement.querySelector('span')
        
        switch (type) {
            case 'ready':
                icon.className = 'fas fa-camera'
                break
            case 'capturing':
                icon.className = 'fas fa-camera-retro'
                break
            case 'loading':
                icon.className = 'fas fa-spinner fa-spin'
                break
            case 'error':
                icon.className = 'fas fa-exclamation-triangle'
                break
        }
        
        text.textContent = message
    }
    
    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop())
            this.stream = null
        }
        this.isInitialized = false
    }
}

class RFIDScanHandler {
    constructor() {
        this.buffer = ""
        this.timeout = null
        this.isScanning = false
        this.scanDelay = 2000 // 2 detik delay antar scan
        this.lastScanTime = 0
        this.cameraHandler = null

        console.log("RFID Scan Handler initialized")
        this.init()
    }

    init() {
        // Listen untuk keypress di seluruh document
        document.addEventListener("keypress", (e) => {
            // Abaikan jika sedang focus di input field
            if (document.activeElement.tagName === "INPUT" || document.activeElement.tagName === "TEXTAREA") {
                return
            }

            this.handleKeyPress(e)
        })

        // Listen untuk Enter key
        document.addEventListener("keydown", (e) => {
            if (e.keyCode === 13 || e.key === "Enter") {
                if (this.isScanning) {
                    e.preventDefault()
                    this.processUID()
                }
            }
        })

        console.log("Event listeners attached for RFID scanning")
    }

    setCameraHandler(cameraHandler) {
        this.cameraHandler = cameraHandler
    }

    handleKeyPress(e) {
        // Prevent default
        e.preventDefault()

        const char = e.key

        // Mulai scanning
        if (!this.isScanning) {
            this.isScanning = true
            this.buffer = ""
            this.updateStatus("processing", "Membaca kartu RFID...")
            console.log("Started RFID scanning...")
        }

        // Tambahkan karakter ke buffer
        this.buffer += char
        console.log("Buffer: " + this.buffer)

        // Reset timeout
        clearTimeout(this.timeout)
        this.timeout = setTimeout(() => {
            this.processUID()
        }, 100) // 100ms timeout
    }

    processUID() {
        if (this.buffer.length > 0) {
            const uid = this.buffer.trim()
            console.log("Processing UID: " + uid)

            // Cek delay antar scan
            const now = Date.now()
            if (now - this.lastScanTime < this.scanDelay) {
                console.log("Scan too fast, ignoring...")
                this.resetBuffer()
                return
            }

            this.sendUID(uid)
            this.lastScanTime = now
        }

        this.resetBuffer()
    }

    async sendUID(uid) {
        console.log("Sending UID to server: " + uid)

        // Update UI
        this.updateStatus("processing", "Memproses absensi...")

        let photoBlob = null
        let capturedFaceDescriptor = null
        if (this.cameraHandler && this.cameraHandler.isInitialized) {
            try {
                console.log("Capturing photo automatically...")
                photoBlob = await this.cameraHandler.capturePhoto()

                // Ekstrak fitur wajah menggunakan face-api jika model sudah siap
                if (window.scanFaceModelsReady && this.cameraHandler.canvas) {
                    try {
                        const detection = await faceapi.detectSingleFace(this.cameraHandler.canvas)
                            .withFaceLandmarks()
                            .withFaceDescriptor()
                        if (detection) {
                            capturedFaceDescriptor = Array.from(detection.descriptor)
                            console.log("Wajah terdeteksi dan fitur berhasil diekstrak saat scan!")
                        }
                    } catch (faceErr) {
                        console.warn("Face detection error during scan:", faceErr)
                    }
                }
            } catch (error) {
                console.error("Failed to capture photo:", error)
                // Continue without photo
            }
        }

        const currentTime = new Date()
        
        // Pastikan menggunakan timezone Asia/Jakarta untuk semua operasi waktu
        const wibTime = new Date(currentTime.toLocaleString("en-US", {timeZone: "Asia/Jakarta"}))
        
        const localTimeString = wibTime.toLocaleTimeString('id-ID', { 
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        })
        
        const localDateString = wibTime.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: '2-digit', 
            day: '2-digit'
        })

        console.log("Sending WIB time to server:", localTimeString, localDateString)

        const formData = new FormData()
        formData.append('uid', uid)
        formData.append('client_time', localTimeString)
        formData.append('client_date', localDateString)
        formData.append('timezone', 'Asia/Jakarta')
        formData.append('wib_timestamp', wibTime.getTime())
        
        if (photoBlob) {
            const fileName = `absensi_${uid}_${Date.now()}.jpg`
            formData.append('photo', photoBlob, fileName)
            console.log("Photo attached to request:", fileName)
        }

        if (capturedFaceDescriptor) {
            formData.append('face_descriptor', JSON.stringify(capturedFaceDescriptor))
            console.log("Face descriptor attached to request")
        }

        // Gunakan simulasi jika AbsensiManager tersedia
        if (window.absensiManager) {
            window.absensiManager.processUID(uid)
                .then ? window.absensiManager.processUID(uid).then(data => this.handleResponse(data))
                : Promise.resolve(window.absensiManager.processUID(uid)).then(data => this.handleResponse(data))
        } else if (window.simulateScanProcess) {
            window.simulateScanProcess(Object.fromEntries(formData))
                .then(data => this.handleResponse(data))
                .catch(error => {
                    console.error("Error:", error)
                    this.updateStatus("error", "Terjadi kesalahan sistem")
                })
        } else {
            fetch("{{ url('scan/process') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    Accept: "application/json",
                },
                body: formData,
            })
                .then((response) => {
                    console.log("Response status:", response.status)
                    return response.json()
                })
                .then((data) => {
                    console.log("Server response:", data)
                    this.handleResponse(data)
                })
                .catch((error) => {
                    console.error("Error:", error)
                    this.updateStatus("error", "Terjadi kesalahan koneksi")
                })
        }
    }

    handleResponse(data) {
        if (data.success) {
            this.updateStatus("success", data.message, data.data)

            // Auto reset setelah 5 detik
            setTimeout(() => {
                this.updateStatus("waiting", "Menunggu Scan Kartu...")
                this.hideGuruDetails()
            }, 5000)
        } else {
            this.updateStatus("error", data.message)

            // Auto reset setelah 3 detik
            setTimeout(() => {
                this.updateStatus("waiting", "Menunggu Scan Kartu...")
            }, 3000)
        }
    }

    updateStatus(type, message, guruData = null) {
        const statusContainer = document.getElementById("scan-status")
        if (!statusContainer) return

        const statusIcon = statusContainer.querySelector(".status-icon i")
        const statusText = statusContainer.querySelector(".status-text")

        // Remove all status classes
        statusContainer.classList.remove("status-success", "status-error", "status-processing")

        switch (type) {
            case "success":
                statusContainer.classList.add("status-success")
                statusIcon.className = "fas fa-check-circle"
                if (guruData) {
                    this.showGuruDetails(guruData)
                }
                break
            case "error":
                statusContainer.classList.add("status-error")
                statusIcon.className = "fas fa-times-circle"
                this.hideGuruDetails()
                break
            case "processing":
                statusContainer.classList.add("status-processing")
                statusIcon.className = "fas fa-spinner fa-spin"
                this.hideGuruDetails()
                break
            default:
                statusIcon.className = "fas fa-clock"
                this.hideGuruDetails()
        }

        statusText.textContent = message

        // Add animation
        statusContainer.style.transform = "scale(1.05)"
        setTimeout(() => {
            statusContainer.style.transform = "scale(1)"
        }, 200)
    }

    convertToLocalTime(timeString, dateString) {
        try {
            console.log("Converting server time to WIB:", timeString, dateString)
            
            if (!timeString || !dateString) {
                console.log("Missing time or date, using current WIB time")
                return this.getCurrentWIBTime()
            }

            // Parse waktu dari server
            let serverDateTime
            
            // Coba berbagai format parsing
            if (dateString.includes('-')) {
                // Format YYYY-MM-DD atau DD-MM-YYYY
                if (dateString.split('-')[0].length === 4) {
                    // YYYY-MM-DD format
                    serverDateTime = new Date(`${dateString}T${timeString}`)
                } else {
                    // DD-MM-YYYY format
                    const parts = dateString.split('-')
                    serverDateTime = new Date(`${parts[2]}-${parts[1]}-${parts[0]}T${timeString}`)
                }
            } else if (dateString.includes('/')) {
                // Format DD/MM/YYYY
                const parts = dateString.split('/')
                const day = parts[0].padStart(2, '0')
                const month = parts[1].padStart(2, '0')
                const year = parts[2]
                serverDateTime = new Date(`${year}-${month}-${day}T${timeString}`)
            } else {
                console.log("Unknown date format, using current time")
                return this.getCurrentWIBTime()
            }

            console.log("Parsed server datetime:", serverDateTime)

            // Jika server mengirim waktu dalam UTC, konversi ke WIB
            // Jika server sudah dalam WIB, gunakan langsung
            let wibDateTime
            
            // Cek apakah waktu server perlu dikonversi dari UTC ke WIB
            const currentWIB = new Date(new Date().toLocaleString("en-US", {timeZone: "Asia/Jakarta"}))
            const serverHour = serverDateTime.getHours()
            const currentHour = currentWIB.getHours()
            
            // Jika selisih jam terlalu besar (>7 jam), kemungkinan server dalam UTC
            if (Math.abs(serverHour - currentHour) > 7) {
                console.log("Server time appears to be in UTC, converting to WIB")
                wibDateTime = new Date(serverDateTime.getTime() + (7 * 60 * 60 * 1000))
            } else {
                console.log("Server time appears to be in WIB already")
                wibDateTime = serverDateTime
            }

            const wibTime = wibDateTime.toLocaleTimeString('id-ID', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit'
            })
            
            const wibDate = wibDateTime.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            })

            console.log("Final WIB time:", wibTime, wibDate)
            return { time: wibTime, date: wibDate }
            
        } catch (error) {
            console.error('Error converting time:', error)
            return this.getCurrentWIBTime()
        }
    }

    getCurrentWIBTime() {
        const now = new Date()
        const wibTime = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Jakarta"}))
        
        const time = wibTime.toLocaleTimeString('id-ID', {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit'
        })
        
        const date = wibTime.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        })
        
        return { time, date }
    }

    validateScheduleAndDetermineStatus(guruJamMasuk, currentTime) {
        try {
            if (!guruJamMasuk) return { isValid: true, status: 'HADIR', isLate: false }
            
            // Parse jam masuk guru (format HH:MM)
            const [jamMasukHour, jamMasukMinute] = guruJamMasuk.split(':').map(Number)
            
            // Parse waktu saat ini
            const [currentHour, currentMinute] = currentTime.split(':').map(Number)
            
            if (isNaN(jamMasukHour) || isNaN(jamMasukMinute) || isNaN(currentHour) || isNaN(currentMinute)) {
                console.error('Invalid time format:', guruJamMasuk, currentTime)
                return { isValid: true, status: 'HADIR', isLate: false, message: 'Absensi masuk berhasil!' }
            }
            
            // Konversi ke menit untuk perbandingan
            const jamMasukInMinutes = (jamMasukHour * 60) + jamMasukMinute
            const currentInMinutes = (currentHour * 60) + currentMinute
            
            // Toleransi 30 menit sebelum jam masuk (untuk absen lebih awal)
            const toleransiSebelum = 30
            const earliestAllowedTime = jamMasukInMinutes - toleransiSebelum
            
            // Toleransi 30 menit setelah jam masuk (untuk keterlambatan)
            const toleransiTerlambat = 30
            const latestOnTimeAllowed = jamMasukInMinutes + toleransiTerlambat
            
            console.log(`Validating schedule: Jam masuk ${guruJamMasuk}, Current ${currentTime}`)
            console.log(`Earliest allowed: ${Math.floor(earliestAllowedTime/60)}:${(earliestAllowedTime%60).toString().padStart(2,'0')}`)
            console.log(`Latest on-time: ${Math.floor(latestOnTimeAllowed/60)}:${(latestOnTimeAllowed%60).toString().padStart(2,'0')}`)
            
            // Cek apakah terlalu awal (sebelum toleransi)
            if (currentInMinutes < earliestAllowedTime) {
                return { 
                    isValid: false, 
                    status: 'TERLALU_AWAL', 
                    isLate: false,
                    message: 'Belum waktunya absen. Silakan coba lagi nanti.'
                }
            }
            
            // Cek apakah tepat waktu (dalam rentang jam masuk + toleransi 30 menit)
            if (currentInMinutes <= latestOnTimeAllowed) {
                const isExactlyOnTime = currentInMinutes <= jamMasukInMinutes
                return { 
                    isValid: true, 
                    status: isExactlyOnTime ? 'HADIR' : 'HADIR', 
                    isLate: false,
                    message: isExactlyOnTime ? 'Absensi masuk berhasil!' : 'Absensi masuk berhasil! (Dalam toleransi waktu)'
                }
            }
            
            // Jika lebih dari toleransi 30 menit, dianggap terlambat
            const keterlambatanMenit = currentInMinutes - jamMasukInMinutes
            
            if (keterlambatanMenit <= 0 || isNaN(keterlambatanMenit)) {
                return { 
                    isValid: true, 
                    status: 'HADIR', 
                    isLate: false,
                    message: 'Absensi masuk berhasil!'
                }
            }
            
            const jam = Math.floor(keterlambatanMenit / 60)
            const menit = keterlambatanMenit % 60
            
            let keterlambatanText = ""
            if (jam > 0) {
                keterlambatanText = `${jam.toString().padStart(2, '0')}:${menit.toString().padStart(2, '0')}`
            } else {
                keterlambatanText = `00:${menit.toString().padStart(2, '0')}`
            }
            
            return { 
                isValid: true, 
                status: 'TERLAMBAT', 
                isLate: true,
                keterlambatan: keterlambatanText,
                message: `Absensi masuk berhasil! Anda terlambat. Terlambat ${keterlambatanText}`
            }
            
        } catch (error) {
            console.error('Error validating schedule:', error)
            return { isValid: true, status: 'HADIR', isLate: false, message: 'Absensi masuk berhasil!' }
        }
    }

    showGuruDetails(guruData) {
        const statusDetails = document.getElementById("status-details")
        const guruAvatar = document.getElementById("guru-avatar")
        const guruName = document.getElementById("guru-name")
        const guruPosition = document.getElementById("guru-position")
        const guruTime = document.getElementById("guru-time")

        if (statusDetails && guruData) {
            // Set avatar dengan inisial nama
            const initials = guruData.nama.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
            guruAvatar.textContent = initials

            // Set data guru
            guruName.textContent = guruData.nama
            guruPosition.textContent = guruData.jabatan
            
            let statusText = guruData.status || 'HADIR'
            
            if (guruData.keterlambatan && guruData.keterlambatan !== 'NaN' && guruData.keterlambatan !== 'undefined') {
                // Validasi format keterlambatan
                if (typeof guruData.keterlambatan === 'string' && guruData.keterlambatan.includes(':')) {
                    statusText += ` (${guruData.keterlambatan})`
                } else if (typeof guruData.keterlambatan === 'number' && !isNaN(guruData.keterlambatan)) {
                    // Konversi menit ke format HH:MM
                    const jam = Math.floor(guruData.keterlambatan / 60)
                    const menit = guruData.keterlambatan % 60
                    const formatKeterlambatan = `${jam.toString().padStart(2, '0')}:${menit.toString().padStart(2, '0')}`
                    statusText += ` (${formatKeterlambatan})`
                }
            }
            
            guruTime.textContent = `${guruData.status === 'pulang' ? 'Jam Pulang' : 'Jam Masuk'}: ${guruData.status === 'pulang' ? guruData.jam_pulang : guruData.jam_masuk} | ${guruData.tanggal} | Status: ${statusText}`;

            // Tampilkan badge status verifikasi wajah
            const guruVerification = document.getElementById("guru-verification")
            if (guruVerification) {
                if (guruData.verification) {
                    const v = guruData.verification
                    if (v.status === 'terverifikasi') {
                        guruVerification.innerHTML = `<span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3); padding: 6px 14px; border-radius: 20px; font-size: 0.82rem; font-weight: 600;"><i class="fas fa-check-circle me-1"></i> Wajah Terverifikasi (${v.similarity || 100}%)</span>`
                    } else if (v.status === 'gagal') {
                        guruVerification.innerHTML = `<span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3); padding: 6px 14px; border-radius: 20px; font-size: 0.82rem; font-weight: 600;"><i class="fas fa-exclamation-triangle me-1"></i> Wajah Tidak Cocok!</span>`
                    } else {
                        guruVerification.innerHTML = `<span class="badge" style="background: rgba(107, 114, 128, 0.15); color: #6b7280; border: 1px solid rgba(107, 114, 128, 0.3); padding: 6px 14px; border-radius: 20px; font-size: 0.82rem; font-weight: 500;"><i class="fas fa-info-circle me-1"></i> Wajah Belum Didaftarkan</span>`
                    }
                } else {
                    guruVerification.innerHTML = ''
                }
            }

            // Show details
            statusDetails.style.display = "block"
            
            // Add animation
            statusDetails.style.opacity = "0"
            statusDetails.style.transform = "translateY(20px)"
            setTimeout(() => {
                statusDetails.style.opacity = "1"
                statusDetails.style.transform = "translateY(0)"
                statusDetails.style.transition = "all 0.3s ease"
            }, 100)
        }
    }

    hideGuruDetails() {
        const statusDetails = document.getElementById("status-details")
        const guruVerification = document.getElementById("guru-verification")
        if (guruVerification) guruVerification.innerHTML = ''
        if (statusDetails) {
            statusDetails.style.display = "none"
        }
    }

    resetBuffer() {
        this.buffer = ""
        this.isScanning = false
        clearTimeout(this.timeout)
    }
}

document.addEventListener("DOMContentLoaded", () => {
    console.log("DOM loaded, initializing handlers...")
    
    window.keyboardShortcutHandler = new KeyboardShortcutHandler()
    
    // Initialize camera handler
    window.cameraHandler = new CameraHandler()
    
    // Initialize RFID handler
    window.rfidScanHandler = new RFIDScanHandler()
    
    // Connect camera handler to RFID handler
    window.rfidScanHandler.setCameraHandler(window.cameraHandler)
})

window.addEventListener('beforeunload', () => {
    if (window.cameraHandler) {
        window.cameraHandler.stopCamera()
    }
})
</script>

<!-- Load Face-API.js untuk verifikasi wajah presensi -->
<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
window.scanFaceModelsReady = false;
async function initScanFaceModels() {
    try {
        const MODEL_URL = "{{ asset('models') }}";
        console.log("Loading face-api models for scan verification...");
        await Promise.all([
            faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
        ]);
        window.scanFaceModelsReady = true;
        console.log("Face-api models ready on scan page!");
    } catch (err) {
        console.warn("Face-api models could not be loaded on scan page:", err);
    }
}
document.addEventListener("DOMContentLoaded", () => {
    initScanFaceModels();
});
</script>
@endsection
