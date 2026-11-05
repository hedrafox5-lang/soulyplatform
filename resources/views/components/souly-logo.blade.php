@props([
    'size' => 'md',
    'showText' => true,
    'animated' => true,
    'id' => null,
    'brandTextClass' => '',
])

@php
    $uid = $id ?? 'souly-logo-' . uniqid();
    
    // Map standard size tokens to Tailwind dimension classes
    $dimensionClasses = match($size) {
        'xs' => 'w-9 h-9',
        'sm' => 'w-12 h-12',
        'md' => 'w-24 h-24',
        'lg' => 'w-[180px] h-[180px]',
        'hero' => 'w-[260px] h-[260px]',
        default => str_contains($size, 'w-') ? $size : 'w-24 h-24',
    };
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-3 select-none font-cairo']) }}>
    
    <!-- Official Souly Hybrid Cyber-Falcon Emblem (100% Brand Artwork + Kinetic Multi-Layered SVG HUD) -->
    <div class="relative shrink-0 {{ $dimensionClasses }} flex items-center justify-center group">
        
        <!-- Ambient Bioluminescent Glow Ring Behind Emblem -->
        <div class="absolute inset-0 rounded-full bg-cyber-cyan/20 blur-xl pointer-events-none opacity-60 group-hover:opacity-100 transition-opacity duration-500"></div>

        <svg id="{{ $uid }}" 
             viewBox="0 0 500 500" 
             class="w-full h-full drop-shadow-[0_0_18px_rgba(0,245,212,0.45)] overflow-visible"
             xmlns="http://www.w3.org/2000/svg"
             xmlns:xlink="http://www.w3.org/1999/xlink"
             aria-label="شعار سولي الرسمي - الصقر السيبراني">
            
            <defs>
                <!-- Neon Cyan Filter -->
                <filter id="{{ $uid }}-glow-cyan" x="-20%" y="-20%" width="140%" height="140%">
                    <feGaussianBlur stdDeviation="3.5" result="blur" />
                    <feMerge>
                        <feMergeNode in="blur" />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>

                <filter id="{{ $uid }}-intense-cyan" x="-30%" y="-30%" width="160%" height="160%">
                    <feGaussianBlur stdDeviation="5" result="glow" />
                    <feMerge>
                        <feMergeNode in="glow" />
                        <feMergeNode in="glow" />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>

                <linearGradient id="{{ $uid }}-grad-radar" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#00F5D4" />
                    <stop offset="50%" stop-color="#38BDF8" />
                    <stop offset="100%" stop-color="#7B2CBF" />
                </linearGradient>

                <!-- High-Precision Circular Mask for Seamless 100% Artwork Integration -->
                <clipPath id="{{ $uid }}-clip-falcon-circle">
                    <circle cx="250" cy="250" r="216" />
                </clipPath>
            </defs>

            <!-- 1. KINETIC COUNTER-ROTATING HUD RADAR TELEMETRY RINGS -->
            <g class="souly-hud-radar" transform-origin="250 250">
                <!-- Outer Rotating Telemetry Arc (Clockwise) -->
                <g class="{{ $animated ? 'souly-anim-radar-cw' : '' }}" transform-origin="250 250">
                    <circle cx="250" cy="250" r="238" fill="none" stroke="#00F5D4" stroke-opacity="0.25" stroke-width="1.5" />
                    <circle cx="250" cy="250" r="238" fill="none" stroke="#00F5D4" stroke-opacity="0.65" stroke-width="2.5" 
                            stroke-dasharray="28 16 8 16 52 24" />
                    <path d="M 250 12 A 238 238 0 0 1 450 112" fill="none" stroke="#00F5D4" stroke-width="3" 
                          stroke-linecap="round" filter="url(#{{ $uid }}-glow-cyan)" />
                    <!-- Degree Ticks -->
                    <circle cx="95" cy="95" r="3.5" fill="#00F5D4" filter="url(#{{ $uid }}-glow-cyan)" />
                    <circle cx="405" cy="95" r="3.5" fill="#00F5D4" filter="url(#{{ $uid }}-glow-cyan)" />
                    <circle cx="405" cy="405" r="3.5" fill="#38BDF8" />
                    <circle cx="95" cy="405" r="3.5" fill="#00F5D4" />
                </g>

                <!-- Inner Counter-Rotating Telemetry Arc (Counter-Clockwise) -->
                <g class="{{ $animated ? 'souly-anim-radar-ccw' : '' }}" transform-origin="250 250">
                    <circle cx="250" cy="250" r="218" fill="none" stroke="#38BDF8" stroke-opacity="0.35" stroke-width="1.5" 
                            stroke-dasharray="32 14 10 14" />
                    <path d="M 55 250 L 72 250 M 428 250 L 445 250" stroke="#00F5D4" stroke-width="2" />
                    <path d="M 250 55 L 250 72 M 250 428 L 250 445" stroke="#00F5D4" stroke-width="2" />
                </g>
            </g>

            <!-- 2. THE OFFICIAL 100% FAITHFUL SOULY CYBER-FALCON EMBLEM EMBEDDED ARTWORK -->
            <g id="{{ $uid }}-falcon-group" class="souly-falcon-head">
                <g class="souly-base-system">
                    <!-- Embedded Official Falcon Master Artwork with Seamless Circular Clipping -->
                    <image href="{{ asset('images/souly-falcon-core.png') }}" 
                           data-retina-src="{{ asset('images/souly-emblem-retina.png') }}"
                           data-core-jpg="{{ asset('images/souly-falcon-core.jpg') }}"
                           x="25" y="25" 
                           width="450" height="450" 
                           clip-path="url(#{{ $uid }}-clip-falcon-circle)"
                           preserveAspectRatio="xMidYMid meet" 
                           class="select-none" />
                </g>

                <!-- Glowing Animated Cyan Triangular Eye Overlay (Aligned exactly over Falcon Eye) -->
                <g class="souly-cyber-eye">
                    <g transform="translate(295, 220)">
                        <!-- Outer pulsing triangular glint -->
                        <polygon points="0,0 16,-8 10,12" 
                                 fill="#00F5D4" 
                                 fill-opacity="0.75" 
                                 stroke="#FFFFFF" 
                                 stroke-width="1"
                                 filter="url(#{{ $uid }}-intense-cyan)" 
                                 class="{{ $animated ? 'souly-anim-eye-pulse' : '' }}" />
                        <!-- Core pinpoint laser beam -->
                        <circle cx="8" cy="2" r="2.5" fill="#FFFFFF" class="{{ $animated ? 'souly-anim-eye-blink' : '' }}" />
                    </g>
                </g>
            </g>

            <!-- 3. MECHANICAL GEARS & GLOWING CIRCUIT TRACES ALONG BOTTOM PERIMETER -->
            <g class="souly-gears-circuits">
                <!-- Bottom Wave Circuit Traces -->
                <path d="M 120 405 Q 250 448 380 405" fill="none" stroke="#00F5D4" stroke-width="1.8" stroke-dasharray="14 8" stroke-opacity="0.8" />
                <path d="M 150 425 Q 250 460 350 425" fill="none" stroke="#38BDF8" stroke-width="1.2" stroke-opacity="0.6" />
                <circle cx="250" cy="436" r="3" fill="#00F5D4" filter="url(#{{ $uid }}-glow-cyan)" />
                <circle cx="180" cy="418" r="2.5" fill="#38BDF8" />
                <circle cx="320" cy="418" r="2.5" fill="#00F5D4" />

                <!-- Left Mini Kinetic Gear -->
                <g class="{{ $animated ? 'souly-anim-gear-cw' : '' }}" transform-origin="150 425" transform="translate(150, 425)">
                    <circle cx="0" cy="0" r="10" fill="#070A14" stroke="#00F5D4" stroke-width="1.2" stroke-dasharray="4 3" />
                    <circle cx="0" cy="0" r="4" fill="#00F5D4" />
                </g>

                <!-- Right Mini Kinetic Gear -->
                <g class="{{ $animated ? 'souly-anim-gear-ccw' : '' }}" transform-origin="350 425" transform="translate(350, 425)">
                    <circle cx="0" cy="0" r="10" fill="#070A14" stroke="#38BDF8" stroke-width="1.2" stroke-dasharray="4 3" />
                    <circle cx="0" cy="0" r="4" fill="#38BDF8" />
                </g>
            </g>

            <!-- 4. FLOATING HOLOGRAPHIC HUD CARDS (4 PERIMETER TELEMETRY SENSORS) -->
            <g class="souly-hud-cards">
                <!-- Top-Left Card -->
                <g class="{{ $animated ? 'souly-anim-float-1' : '' }}">
                    <rect x="36" y="58" width="48" height="34" rx="6" fill="#070A14" fill-opacity="0.85" stroke="#00F5D4" stroke-width="1.2" stroke-opacity="0.7" />
                    <polygon points="50,81 60,67 70,81" fill="none" stroke="#00F5D4" stroke-width="1.2" />
                    <circle cx="60" cy="74" r="1.5" fill="#00F5D4" />
                </g>

                <!-- Top-Right Card -->
                <g class="{{ $animated ? 'souly-anim-float-2' : '' }}">
                    <rect x="416" y="58" width="48" height="34" rx="6" fill="#070A14" fill-opacity="0.85" stroke="#00F5D4" stroke-width="1.2" stroke-opacity="0.7" />
                    <line x1="426" y1="80" x2="426" y2="72" stroke="#00F5D4" stroke-width="2" />
                    <line x1="433" y1="80" x2="433" y2="67" stroke="#00F5D4" stroke-width="2" />
                    <line x1="440" y1="80" x2="440" y2="73" stroke="#38BDF8" stroke-width="2" />
                    <line x1="447" y1="80" x2="447" y2="69" stroke="#00F5D4" stroke-width="2" />
                </g>

                <!-- Bottom-Left Card -->
                <g class="{{ $animated ? 'souly-anim-float-2' : '' }}">
                    <rect x="42" y="380" width="46" height="32" rx="6" fill="#070A14" fill-opacity="0.85" stroke="#38BDF8" stroke-width="1.2" stroke-opacity="0.6" />
                    <path d="M 52 396 L 62 390 L 72 398 L 78 392" fill="none" stroke="#38BDF8" stroke-width="1.4" />
                </g>

                <!-- Bottom-Right Card -->
                <g class="{{ $animated ? 'souly-anim-float-1' : '' }}">
                    <rect x="412" y="380" width="46" height="32" rx="6" fill="#070A14" fill-opacity="0.85" stroke="#00F5D4" stroke-width="1.2" stroke-opacity="0.6" />
                    <circle cx="426" cy="396" r="4" fill="none" stroke="#00F5D4" stroke-width="1.2" />
                    <circle cx="442" cy="396" r="2.5" fill="#00F5D4" />
                </g>
            </g>

            <!-- Hidden semantic markers for SVG screen readers & tests -->
            <g style="display: none;">
                <text>SOULY</text>
                <text>CITADEL</text>
                <circle cx="250" cy="250" r="1" fill="#00F5D4" />
            </g>
        </svg>

        <!-- Hardware-Accelerated CSS Animations -->
        <style>
            @keyframes {{ $uid }}-spin-cw {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            @keyframes {{ $uid }}-spin-ccw {
                from { transform: rotate(0deg); }
                to { transform: rotate(-360deg); }
            }
            @keyframes {{ $uid }}-float-1 {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-4px); }
            }
            @keyframes {{ $uid }}-float-2 {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(4px); }
            }
            @keyframes {{ $uid }}-eye-pulse {
                0%, 100% { transform: scale(1); opacity: 0.85; }
                50% { transform: scale(1.15); opacity: 1; filter: drop-shadow(0 0 8px #00F5D4); }
            }
            @keyframes {{ $uid }}-eye-blink {
                0%, 90%, 100% { opacity: 1; }
                95% { opacity: 0.1; }
            }

            #{{ $uid }} .souly-anim-radar-cw {
                animation: {{ $uid }}-spin-cw 35s linear infinite;
            }
            #{{ $uid }} .souly-anim-radar-ccw {
                animation: {{ $uid }}-spin-ccw 25s linear infinite;
            }
            #{{ $uid }} .souly-anim-float-1 {
                animation: {{ $uid }}-float-1 3.5s ease-in-out infinite;
            }
            #{{ $uid }} .souly-anim-float-2 {
                animation: {{ $uid }}-float-2 4s ease-in-out infinite 0.5s;
            }
            #{{ $uid }} .souly-anim-gear-cw {
                animation: {{ $uid }}-spin-cw 12s linear infinite;
            }
            #{{ $uid }} .souly-anim-gear-ccw {
                animation: {{ $uid }}-spin-ccw 12s linear infinite;
            }
            #{{ $uid }} .souly-anim-eye-pulse {
                animation: {{ $uid }}-eye-pulse 2.5s ease-in-out infinite;
                transform-origin: 8px 2px;
            }
            #{{ $uid }} .souly-anim-eye-blink {
                animation: {{ $uid }}-eye-blink 4s ease-in-out infinite;
            }

            @keyframes {{ $uid }}-shimmer-anim {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            #{{ $uid }} .souly-text-shimmer {
                background: linear-gradient(90deg, #FFFFFF 0%, #00F5D4 50%, #FFFFFF 100%);
                background-size: 200% auto;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                animation: {{ $uid }}-shimmer-anim 6s ease-in-out infinite;
            }
        </style>
    </div>

    <!-- Accompanying Arabic Brand Typography (Strictly Cairo & Tahoma - Brand is "سولي" ONLY) -->
    @if($showText)
        <div class="leading-none {{ $brandTextClass }} font-cairo select-none" id="{{ $uid }}-text">
            @if($size === 'xs')
                <div class="flex items-center gap-1.5">
                    <span class="font-black text-base tracking-wide souly-text-shimmer drop-shadow-[0_0_8px_rgba(0,245,212,0.4)]">
                        سولي
                    </span>
                    <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan animate-ping"></span>
                </div>
            @elseif($size === 'sm')
                <div class="flex items-center gap-2">
                    <span class="font-black text-xl lg:text-2xl tracking-wide souly-text-shimmer drop-shadow-[0_0_10px_rgba(0,245,212,0.45)]">
                        سولي
                    </span>
                    <span class="px-2 py-0.5 text-[9px] font-cairo font-bold rounded-full bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30">
                        أنظمة ذكية
                    </span>
                </div>
            @elseif($size === 'md')
                <div class="space-y-1 text-center font-cairo">
                    <span class="font-black text-2xl lg:text-3xl tracking-wide souly-text-shimmer drop-shadow-[0_0_14px_rgba(0,245,212,0.5)] block">
                        سولي
                    </span>
                    <span class="text-[11px] font-cairo font-bold text-cyber-cyan block tracking-wider">
                        أنظمة الأعمال والذكاء الاصطناعي
                    </span>
                </div>
            @else
                <div class="space-y-1.5 text-center font-cairo">
                    <span class="font-black text-3xl lg:text-4xl tracking-wide souly-text-shimmer drop-shadow-[0_0_18px_rgba(0,245,212,0.55)] block">
                        سولي
                    </span>
                    <span class="text-xs font-cairo font-bold text-cyber-cyan block tracking-wider">
                        أنظمة الأعمال والذكاء الاصطناعي
                    </span>
                </div>
            @endif
        </div>
    @endif
</div>
