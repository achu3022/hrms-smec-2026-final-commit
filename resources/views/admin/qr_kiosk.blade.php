<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMEC Front Office Attendance Kiosk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-mono-num {
            font-family: 'Space Grotesk', sans-serif;
        }
        @keyframes float-blob {
            0%, 100% { transform: translate(0px, 0px) scale(1); }
            50% { transform: translate(25px, -20px) scale(1.08); }
        }
        .animate-blob-1 { animation: float-blob 10s infinite ease-in-out; }
        .animate-blob-2 { animation: float-blob 14s infinite ease-in-out reverse; }
        
        @keyframes laser-beam {
            0% { top: 0%; opacity: 0.8; }
            50% { top: 95%; opacity: 1; }
            100% { top: 0%; opacity: 0.8; }
        }
        .laser-line {
            animation: laser-beam 3s infinite ease-in-out;
        }

        @keyframes card-glow {
            0%, 100% { box-shadow: 0 0 50px rgba(79, 70, 229, 0.25), 0 0 100px rgba(124, 58, 237, 0.15); }
            50% { box-shadow: 0 0 80px rgba(79, 70, 229, 0.45), 0 0 140px rgba(124, 58, 237, 0.3); }
        }
        .qr-card-glow {
            animation: card-glow 5s infinite ease-in-out;
        }

        .glass-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
    </style>
</head>
<body class="h-full text-slate-100 flex flex-col justify-between overflow-hidden bg-slate-950 relative select-none">

    <!-- Ambient Glowing Background Blobs -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
        <div class="animate-blob-1 absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/30 rounded-full blur-[120px]"></div>
        <div class="animate-blob-2 absolute top-1/2 -right-40 w-[30rem] h-[30rem] bg-purple-600/25 rounded-full blur-[140px]"></div>
        <div class="animate-blob-1 absolute -bottom-40 left-1/3 w-[26rem] h-[26rem] bg-blue-600/20 rounded-full blur-[130px]"></div>
    </div>

    <!-- Header Section -->
    <header class="relative z-10 px-8 py-5 flex justify-between items-center glass-card border-b border-white/10">
        <!-- SMEC White Logo & Title -->
        <div class="flex items-center gap-5">
            <div class="p-2 bg-white/5 rounded-2xl border border-white/10 shadow-lg flex items-center justify-center">
                <img src="{{ asset('smec-white.png') }}" class="h-10 md:h-12 w-auto object-contain drop-shadow-md" alt="SMEC Logo">
            </div>
            <div class="h-10 w-px bg-white/15 hidden sm:block"></div>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                    Front Office Attendance Kiosk
                    <span class="text-xs font-extrabold px-2.5 py-0.5 rounded-full bg-indigo-500/20 border border-indigo-400/40 text-indigo-300">LIVE</span>
                </h1>
                <p class="text-xs font-semibold text-slate-400">SMEC Attendance & Selfie Verification Station</p>
            </div>
        </div>

        <!-- Clock & Fullscreen Control -->
        <div class="flex items-center gap-6">
            <div class="text-right hidden sm:block">
                <div id="liveClock" class="text-2xl font-black tracking-wider text-white font-mono-num drop-shadow">--:--:-- --</div>
                <div id="liveDate" class="text-[11px] font-extrabold text-indigo-400 uppercase tracking-widest">-- --- ----</div>
            </div>
            <button onclick="toggleFullScreen()" class="w-11 h-11 rounded-2xl bg-white/10 hover:bg-white/20 text-slate-200 hover:text-white flex items-center justify-center transition border border-white/15 shadow-lg backdrop-blur-md" title="Toggle Full Screen">
                <i class="fa-solid fa-expand text-lg"></i>
            </button>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="relative z-10 flex-1 flex flex-col items-center justify-center px-4 py-8">
        
        <!-- Animated Toast Notification on Scan -->
        <div id="scanAlertToast" class="hidden absolute top-6 bg-emerald-500/95 text-white px-8 py-3.5 rounded-full font-bold text-base shadow-[0_10px_40px_rgba(16,185,129,0.5)] backdrop-blur-md border border-emerald-300/60 flex items-center gap-3 transition-all duration-300 transform -translate-y-2 z-50">
            <div class="w-8 h-8 rounded-full bg-white text-emerald-600 flex items-center justify-center text-lg shadow">
                <i class="fa-solid fa-check"></i>
            </div>
            <span id="scanToastMessage">Scan Detected! Generating next QR code...</span>
        </div>

        <!-- Center Card Container -->
        <div class="flex flex-col items-center max-w-lg w-full">
            
            <!-- QR Frame Card -->
            <div class="qr-card-glow glass-card p-7 rounded-[2.5rem] relative flex flex-col items-center justify-center w-full max-w-[360px] aspect-square transition-all duration-300 border border-white/20 shadow-2xl">
                
                <!-- Laser Scanning Animation Line -->
                <div class="absolute inset-[18px] pointer-events-none rounded-[1.8rem] overflow-hidden">
                    <div class="laser-line absolute left-0 right-0 h-1 bg-gradient-to-r from-transparent via-indigo-400 to-transparent shadow-[0_0_15px_#818cf8]"></div>
                </div>

                <!-- White Background Card containing QR Code SVG -->
                <div class="w-full h-full bg-white rounded-3xl p-4 shadow-inner flex items-center justify-center relative overflow-hidden">
                    
                    <div id="qrSvgContainer" class="w-full h-full flex items-center justify-center transform transition-all duration-300">
                        <div class="animate-pulse text-slate-400 flex flex-col items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin text-4xl text-indigo-600"></i>
                            <span class="text-xs font-bold text-slate-600">Generating Secure QR...</span>
                        </div>
                    </div>

                    <!-- Watermark SMEC Pill in center of QR -->
                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                        <div class="bg-slate-900/90 backdrop-blur-md border border-indigo-500/40 text-white px-3 py-1 rounded-full shadow-lg flex items-center gap-1.5 opacity-95">
                            <img src="{{ asset('smec-white.png') }}" class="h-3.5 w-auto object-contain" alt="SMEC Logo">
                            <span class="text-[10px] font-black tracking-wider uppercase">SMEC</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic One Scan Badge -->
            <div class="mt-8 flex items-center gap-2.5 px-5 py-2.5 rounded-full bg-indigo-500/15 border border-indigo-400/30 text-indigo-300 text-xs font-extrabold shadow-lg backdrop-blur-md">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                Dynamic QR Code &bull; 1 Scan Per Employee
            </div>

            <!-- Scanner Instructions -->
            <div class="text-center mt-5 space-y-1.5">
                <h2 class="text-xl md:text-2xl font-black text-white tracking-tight">Scan QR with Phone Camera</h2>
                <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">
                    QR code dynamically rotates immediately after each employee scan for attendance security.
                </p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 px-8 py-4 glass-card border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-slate-400">
        <div class="flex items-center gap-2 font-medium">
            <i class="fa-solid fa-shield-halved text-emerald-400 text-sm"></i>
            <span>SMEC Dynamic Attendance Verification System</span>
        </div>
        <div class="flex items-center gap-4">
            <button onclick="fetchActiveToken(true)" class="hover:text-white flex items-center gap-1.5 transition font-bold px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/15 border border-white/10">
                <i class="fa-solid fa-arrows-rotate"></i> Force Refresh QR
            </button>
        </div>
    </footer>

    <!-- Audio Beep Generator for Scan Confirmation -->
    <script>
        let currentToken = null;
        let isProcessingScan = false;

        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateStr = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: '2-digit', year: 'numeric' });
            
            document.getElementById('liveClock').innerText = timeStr;
            document.getElementById('liveDate').innerText = dateStr;
        }

        // Web Audio Synthesizer Beep
        function playScanBeep() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, audioCtx.currentTime); // A5 note
                gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.25);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.25);
            } catch (e) {
                // Audio Context fallback silent
            }
        }

        async function fetchActiveToken(forceRefresh = false) {
            try {
                const url = `{{ route('api.qr.kiosk-token') }}${forceRefresh ? '?force_refresh=1' : ''}`;
                const res = await fetch(url);
                const data = await res.json();

                if (data.success) {
                    if (currentToken !== null && currentToken !== data.token) {
                        // QR code rotated (Scan occurred or force refresh)
                        if (!forceRefresh) {
                            playScanBeep();
                            showScanToast(data.scanned_by ? `Scan Verified for ${data.scanned_by}!` : 'Scan Verified!');
                        }
                    }

                    currentToken = data.token;
                    document.getElementById('qrSvgContainer').innerHTML = data.qr_svg;
                    
                    // Scale SVG properly
                    const svg = document.getElementById('qrSvgContainer').querySelector('svg');
                    if (svg) {
                        svg.setAttribute('class', 'w-full h-full');
                    }
                }
            } catch (err) {
                console.error("Kiosk API error:", err);
            }
        }

        function showScanToast(msg) {
            const toast = document.getElementById('scanAlertToast');
            document.getElementById('scanToastMessage').innerText = msg;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => console.error(err));
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // Initialize Kiosk Polling (1 second interval for instant scan detection)
        document.addEventListener('DOMContentLoaded', () => {
            fetchActiveToken(true);
            
            updateClock();
            setInterval(updateClock, 1000);

            // Poll API every 1 second: if employee scans, kiosk rotates instantly!
            setInterval(() => {
                fetchActiveToken(false);
            }, 1000);
        });
    </script>
</body>
</html>
