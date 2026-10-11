@extends('employee.portal.layout')

@section('title', 'Front Office QR Attendance')

@section('content')
<div class="max-w-xl mx-auto px-4 py-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-qrcode text-indigo-600"></i> QR Attendance Punch
            </h1>
            <p class="text-xs text-slate-500 font-medium">Front Office Dynamic Verification</p>
        </div>
        <a href="{{ route('employee.dashboard') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-xl transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard
        </a>
    </div>

    <!-- Error State -->
    @if(isset($error))
        <div class="bg-rose-50 border border-rose-200 rounded-3xl p-6 text-center shadow-sm mb-6">
            <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <h3 class="text-base font-bold text-rose-900 mb-2">Scan Failed or Expired</h3>
            <p class="text-xs text-rose-700 leading-relaxed mb-6">{{ $error }}</p>
            <div class="flex flex-col sm:flex-row justify-center gap-3">
                <button onclick="startCameraScanner()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-bold transition shadow-lg shadow-indigo-600/20">
                    <i class="fa-solid fa-camera mr-1.5"></i> Scan Fresh QR Code
                </button>
                <a href="{{ route('employee.dashboard') }}" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-2xl text-xs font-bold transition">
                    Return to Dashboard
                </a>
            </div>
        </div>
    @endif

    <!-- Valid Token State: Camera & Liveness Selfie Punch UI -->
    @if($token && $session && !isset($error))
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-indigo-900 via-slate-950 to-purple-950 p-6 text-white text-center relative border-b border-indigo-500/20">
                <img src="{{ asset('smec-white.png') }}" class="h-8 mx-auto mb-3 object-contain drop-shadow" alt="SMEC Logo">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-[11px] font-bold tracking-wide uppercase mb-2 border border-white/15">
                    <i class="fa-solid fa-shield-check text-emerald-400"></i> Front Office QR Verified
                </div>
                <h2 class="text-xl font-black">Selfie Liveness Verification</h2>
                <p class="text-xs text-indigo-200 mt-1">Blink your eyes to auto-capture selfie</p>
            </div>

            <!-- Employee Info -->
            <div class="p-6">
                <div class="flex items-center justify-between bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                            {{ substr($employee->name ?? 'E', 0, 2) }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-800">{{ $employee->name }}</div>
                            <div class="text-xs text-slate-500 font-medium">ID: {{ $employee->employee_id }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-extrabold px-2.5 py-1 rounded-lg {{ $todayAttendance && $todayAttendance->check_in ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $todayAttendance && $todayAttendance->check_in ? ($todayAttendance->check_out ? 'Punched Out' : 'Ready to Punch Out') : 'Ready to Punch In' }}
                        </span>
                    </div>
                </div>

                <!-- Circular Selfie Webcam Frame with Liveness Detection -->
                <div id="videoContainer" class="relative mx-auto bg-slate-900 rounded-full overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.15)] mb-4 border-4 border-indigo-500/30 transition-all duration-300" style="width: 240px; height: 240px;">
                    <video id="webcamVideo" autoplay playsinline class="w-full h-full object-cover transform scale-x-[-1]"></video>
                    <canvas id="webcamCanvas" class="hidden w-full h-full object-cover transform scale-x-[-1]"></canvas>

                    <!-- Liveness Status Overlay -->
                    <div id="livenessStatusOverlay" class="absolute inset-0 flex flex-col justify-end items-center pb-3 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent z-10 pointer-events-none transition-all">
                        <div id="livenessStatusText" class="text-xs font-bold text-white text-center px-2 drop-shadow leading-tight">
                            <span class="text-amber-300">Scanning face...</span>
                        </div>
                    </div>

                    <!-- Camera Loading / Status -->
                    <div id="cameraLoadingOverlay" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900 text-white text-xs gap-2 z-20">
                        <i class="fa-solid fa-spinner fa-spin text-2xl text-indigo-400"></i>
                        <span>Starting Selfie Camera...</span>
                    </div>
                </div>

                <p id="livenessInstructionText" class="text-center text-xs font-semibold text-slate-500 mb-6">
                    <i class="fa-solid fa-eye text-indigo-500 mr-1"></i> Position face inside circle & <strong class="text-slate-800">BLINK EYES</strong> to capture
                </p>

                <!-- Controls & Action Buttons -->
                <div class="space-y-3">
                    <div class="flex justify-center gap-2 mb-3">
                        <button type="button" id="manualSnapBtn" onclick="executeSnapshot()" class="hidden px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full text-xs font-bold transition shadow">
                            <i class="fa-solid fa-camera mr-1"></i> Capture Snapshot
                        </button>
                        <button type="button" id="retakeBtn" onclick="retakePhoto()" class="hidden px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-full text-xs font-bold transition shadow">
                            <i class="fa-solid fa-rotate-left mr-1"></i> Retake Photo
                        </button>
                    </div>

                    @if(!$todayAttendance || !$todayAttendance->check_in)
                        <button type="button" onclick="submitQrPunch('check_in')" id="btnPunchIn" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-sm font-black transition shadow-lg shadow-emerald-600/25 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-right-to-bracket"></i> PUNCH IN NOW
                        </button>
                    @elseif($todayAttendance->check_in && !$todayAttendance->check_out)
                        <button type="button" onclick="submitQrPunch('check_out')" id="btnPunchOut" class="w-full py-3.5 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl text-sm font-black transition shadow-lg shadow-rose-600/25 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-right-from-bracket"></i> PUNCH OUT NOW
                        </button>
                    @else
                        <div class="bg-emerald-50 text-emerald-800 text-xs font-bold p-4 rounded-2xl text-center border border-emerald-200">
                            <i class="fa-solid fa-circle-check text-base mr-1"></i> You have already completed your attendance punch today!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- No Token State: In-Browser Camera Scanner -->
    @if(!$token && !isset($error))
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl p-6 text-center">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fa-solid fa-camera-retro"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-800 mb-2">Scan Front Office QR Screen</h2>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">Point your phone's camera at the QR code displayed on the front office TV/kiosk display screen.</p>

            <!-- Scanner Camera Box -->
            <div id="scannerBox" class="relative max-w-sm mx-auto bg-slate-900 rounded-2xl overflow-hidden aspect-square border-2 border-dashed border-indigo-500 flex items-center justify-center mb-6">
                <video id="qrScannerVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                <div class="absolute inset-0 border-2 border-indigo-400/60 rounded-2xl pointer-events-none m-8 flex items-center justify-center">
                    <div class="w-full h-0.5 bg-indigo-500 shadow-[0_0_15px_#6366f1] animate-bounce"></div>
                </div>
                <div id="scannerOverlay" class="absolute inset-0 bg-slate-900/80 flex flex-col items-center justify-center text-white text-xs gap-3">
                    <i class="fa-solid fa-camera text-3xl text-indigo-400 animate-pulse"></i>
                    <button type="button" onclick="initQrScanner()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold">
                        Start Camera Scanner
                    </button>
                </div>
            </div>

            <p class="text-xs text-slate-400">
                <i class="fa-solid fa-lightbulb text-amber-400 mr-1"></i> Tip: You can also open your native Mobile Phone Camera app and scan the screen!
            </p>
        </div>
    @endif
</div>

<!-- JS Camera, MediaPipe Eye Blink Liveness & Scan Handlers -->
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<script>
    let webcamStream = null;
    let faceMeshInstance = null;
    let cameraUtilsInstance = null;
    let isLivenessVerified = false;
    let eyeBlinkState = 'OPEN';
    let capturedPhotoBase64 = null;

    @if($token && $session && !isset($error))
    document.addEventListener('DOMContentLoaded', async () => {
        startSelfieCamera();
    });

    async function startSelfieCamera() {
        const videoContainer = document.getElementById('videoContainer');
        const video = document.getElementById('webcamVideo');

        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { width: { ideal: 640 }, height: { ideal: 640 }, facingMode: "user" },
                audio: false
            });
            webcamStream = stream;
            video.srcObject = stream;
            await video.play();
            document.getElementById('cameraLoadingOverlay').classList.add('hidden');

            if (typeof FaceMesh !== 'undefined') {
                startBlinkLivenessDetection(video);
            } else {
                document.getElementById('manualSnapBtn')?.classList.remove('hidden');
            }
        } catch (err) {
            console.error("Selfie camera error:", err);
            document.getElementById('cameraLoadingOverlay').innerHTML = `
                <div class="p-4 text-center text-rose-300">
                    <i class="fa-solid fa-triangle-exclamation text-2xl mb-1"></i>
                    <div class="font-bold">Camera Access Error</div>
                    <div class="text-[10px] opacity-80">Please grant camera permissions to take selfie.</div>
                </div>
            `;
        }
    }

    function calculateEAR(landmarks, indices) {
        const pUpper = landmarks[indices[0]];
        const pLower = landmarks[indices[1]];
        const pOuter = landmarks[indices[2]];
        const pInner = landmarks[indices[3]];

        const distV = Math.hypot(pUpper.x - pLower.x, pUpper.y - pLower.y);
        const distH = Math.hypot(pOuter.x - pInner.x, pOuter.y - pInner.y);

        return distH > 0 ? (distV / distH) : 0;
    }

    function startBlinkLivenessDetection(video) {
        isLivenessVerified = false;
        eyeBlinkState = 'OPEN';

        const statusEl = document.getElementById('livenessStatusText');
        const overlay = document.getElementById('livenessStatusOverlay');
        if (overlay) overlay.classList.remove('hidden');
        if (statusEl) statusEl.innerHTML = '<span class="text-amber-300 font-bold">Scanning face...</span>';

        try {
            faceMeshInstance = new FaceMesh({
                locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`
            });

            faceMeshInstance.setOptions({
                maxNumFaces: 1,
                refineLandmarks: true,
                minDetectionConfidence: 0.5,
                minTrackingConfidence: 0.5
            });

            faceMeshInstance.onResults((results) => {
                if (isLivenessVerified) return;

                const videoContainer = document.getElementById('videoContainer');
                if (!results.multiFaceLandmarks || results.multiFaceLandmarks.length === 0) {
                    if (statusEl) statusEl.innerHTML = '<span class="text-rose-400 font-bold">❌ No Face Detected</span><br/><span class="text-[10px] text-slate-300">Position face in circle</span>';
                    videoContainer.classList.remove('border-emerald-500', 'border-green-400');
                    videoContainer.classList.add('border-rose-400');
                    eyeBlinkState = 'OPEN';
                    return;
                }

                videoContainer.classList.remove('border-rose-400');
                videoContainer.classList.add('border-emerald-500');

                const landmarks = results.multiFaceLandmarks[0];
                const leftEAR = calculateEAR(landmarks, [159, 145, 33, 133]);
                const rightEAR = calculateEAR(landmarks, [386, 374, 362, 263]);
                const avgEAR = (leftEAR + rightEAR) / 2;

                if (avgEAR < 0.16) {
                    if (eyeBlinkState === 'OPEN') {
                        eyeBlinkState = 'CLOSING';
                    }
                } else if (avgEAR > 0.22) {
                    if (eyeBlinkState === 'CLOSING') {
                        eyeBlinkState = 'BLINKED';
                        isLivenessVerified = true;

                        if (statusEl) statusEl.innerHTML = '<span class="text-emerald-400 font-bold text-sm">✓ Blink Verified!</span>';
                        videoContainer.classList.remove('border-emerald-500');
                        videoContainer.classList.add('border-green-400');

                        setTimeout(() => {
                            executeSnapshot();
                        }, 250);
                        return;
                    }
                    eyeBlinkState = 'OPEN';
                }

                if (eyeBlinkState === 'OPEN' || eyeBlinkState === 'CLOSING') {
                    if (statusEl) statusEl.innerHTML = '<span class="text-amber-300 font-bold">👁️ Face Detected</span><br/><span class="text-[11px] text-white">BLINK EYES to capture</span>';
                }
            });

            cameraUtilsInstance = new Camera(video, {
                onFrame: async () => {
                    if (webcamStream && !isLivenessVerified && faceMeshInstance) {
                        try {
                            await faceMeshInstance.send({ image: video });
                        } catch(e){}
                    }
                },
                width: 640,
                height: 640
            });
            cameraUtilsInstance.start();

            // Timeout: if no blink after 6 seconds, allow manual snap
            setTimeout(() => {
                if (!isLivenessVerified) {
                    document.getElementById('manualSnapBtn')?.classList.remove('hidden');
                }
            }, 6000);

        } catch(e) {
            console.error("Liveness init error:", e);
            document.getElementById('manualSnapBtn')?.classList.remove('hidden');
        }
    }

    function executeSnapshot() {
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('webcamCanvas');
        const overlay = document.getElementById('livenessStatusOverlay');
        
        if (overlay) overlay.classList.add('hidden');

        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 640;

        context.save();
        context.scale(-1, 1);
        context.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
        context.restore();

        capturedPhotoBase64 = canvas.toDataURL('image/jpeg', 0.88);

        video.classList.add('hidden');
        canvas.classList.remove('hidden');
        document.getElementById('retakeBtn')?.classList.remove('hidden');
        document.getElementById('manualSnapBtn')?.classList.add('hidden');
    }

    function retakePhoto() {
        capturedPhotoBase64 = null;
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('webcamCanvas');
        
        canvas.classList.add('hidden');
        video.classList.remove('hidden');
        document.getElementById('retakeBtn')?.classList.add('hidden');
        
        if (typeof FaceMesh !== 'undefined' && video) {
            startBlinkLivenessDetection(video);
        } else {
            document.getElementById('manualSnapBtn')?.classList.remove('hidden');
        }
    }

    async function submitQrPunch(punchType) {
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('webcamCanvas');
        
        if (!capturedPhotoBase64) {
            executeSnapshot();
        }

        const photoData = capturedPhotoBase64;
        if (!photoData) {
            alert('Please allow camera snapshot before submitting.');
            return;
        }

        const btn = punchType === 'check_in' ? document.getElementById('btnPunchIn') : document.getElementById('btnPunchOut');
        const origText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-1"></i> Submitting...`;

        try {
            const res = await fetch("{{ route('employee.attendance.punch-via-qr') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    token: "{{ $token }}",
                    punch_type: punchType,
                    photo: photoData,
                    browser: navigator.userAgent,
                    device: 'Mobile Web Scanner'
                })
            });

            const data = await res.json();

            if (data.success) {
                alert(data.message);
                window.location.href = "{{ route('employee.dashboard') }}";
            } else {
                alert(data.message || "Punch failed. Please try again.");
                btn.disabled = false;
                btn.innerHTML = origText;
            }
        } catch (err) {
            console.error("Punch submission error:", err);
            alert("An error occurred during submission. Please check your network connection.");
            btn.disabled = false;
            btn.innerHTML = origText;
        }
    }
    @endif

    // In-Browser QR Scanner Logic
    let qrScanStream = null;
    let qrScanInterval = null;

    async function initQrScanner() {
        document.getElementById('scannerOverlay').classList.add('hidden');
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: "environment" }
            });
            qrScanStream = stream;
            const video = document.getElementById('qrScannerVideo');
            video.srcObject = stream;

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            qrScanInterval = setInterval(() => {
                if (video.readyState === video.HAVE_ENOUGH_DATA) {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "dontInvert",
                    });

                    if (code && code.data) {
                        clearInterval(qrScanInterval);
                        if (qrScanStream) {
                            qrScanStream.getTracks().forEach(track => track.stop());
                        }
                        window.location.href = code.data;
                    }
                }
            }, 300);
        } catch (err) {
            console.error("QR scanner error:", err);
            alert("Could not access camera for scanning. Please use your phone's native camera app.");
        }
    }
</script>
@endsection
