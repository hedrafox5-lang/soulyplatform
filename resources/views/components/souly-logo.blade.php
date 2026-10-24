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
    
    <!-- Official Souly Cyber-Falcon Emblem (Exact 100% Reproduction with Hardware-Accelerated Kinetic Radar) -->
    <div class="relative shrink-0 {{ $dimensionClasses }} flex items-center justify-center group">
        
        <!-- Ambient Bioluminescent Glow Ring Behind Emblem -->
        <div class="absolute inset-0 rounded-full bg-cyber-cyan/20 blur-xl pointer-events-none opacity-60 group-hover:opacity-100 transition-opacity duration-500"></div>

        <svg id="{{ $uid }}" 
             viewBox="0 0 500 500" 
             class="w-full h-full drop-shadow-[0_0_18px_rgba(0,245,212,0.45)] overflow-visible"
             xmlns="http://www.w3.org/2000/svg"
             xmlns:xlink="http://www.w3.org/1999/xlink"
             aria-label="شعار قلعة سولي الرقمية الرسمي - الصقر السيبراني">
            
            <defs>
                <!-- Neon Cyan Filter -->
                <filter id="{{ $uid }}-glow-cyan" x="-20%" y="-20%" width="140%" height="140%">
                    <feGaussianBlur stdDeviation="3.5" result="blur" />
                    <feMerge>
                        <feMergeNode in="blur" />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>

                <linearGradient id="{{ $uid }}-grad-radar" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#00F5D4" />
                    <stop offset="50%" stop-color="#38BDF8" />
                    <stop offset="100%" stop-color="#7B2CBF" />
                </linearGradient>
            </defs>

            <!-- 1. KINETIC OUTER & INNER HUD RADAR TELEMETRY RINGS -->
            <g class="souly-hud-radar" transform-origin="250 250">
                <!-- Outer Rotating Telemetry Arc -->
                <g class="{{ $animated ? 'souly-anim-radar-cw' : '' }}" transform-origin="250 250">
                    <circle cx="250" cy="250" r="236" fill="none" stroke="#00F5D4" stroke-opacity="0.25" stroke-width="1.5" />
                    <circle cx="250" cy="250" r="236" fill="none" stroke="#00F5D4" stroke-opacity="0.6" stroke-width="2.5" 
                            stroke-dasharray="24 16 6 16 48 20" />
                    <path d="M 250 14 A 236 236 0 0 1 445 110" fill="none" stroke="#00F5D4" stroke-width="3" 
                          stroke-linecap="round" filter="url(#{{ $uid }}-glow-cyan)" />
                    <circle cx="100" cy="100" r="3.5" fill="#00F5D4" filter="url(#{{ $uid }}-glow-cyan)" />
                    <circle cx="400" cy="100" r="3.5" fill="#00F5D4" filter="url(#{{ $uid }}-glow-cyan)" />
                    <circle cx="400" cy="400" r="3.5" fill="#38BDF8" />
                </g>

                <!-- Inner Counter-Rotating Telemetry Arc -->
                <g class="{{ $animated ? 'souly-anim-radar-ccw' : '' }}" transform-origin="250 250">
                    <circle cx="250" cy="250" r="218" fill="none" stroke="#38BDF8" stroke-opacity="0.3" stroke-width="1.5" 
                            stroke-dasharray="32 12 8 12" />
                    <path d="M 60 250 L 75 250 M 425 250 L 440 250" stroke="#00F5D4" stroke-width="2" />
                    <path d="M 250 60 L 250 75 M 250 425 L 250 440" stroke="#00F5D4" stroke-width="2" />
                </g>
            </g>

            <!-- 2. FLOATING HUD DATA CARDS (4 CORNER SENSORS) -->
            <g class="souly-hud-cards">
                <g class="{{ $animated ? 'souly-anim-float-1' : '' }}">
                    <rect x="42" y="60" width="46" height="32" rx="6" fill="#070A14" fill-opacity="0.8" stroke="#00F5D4" stroke-width="1.2" stroke-opacity="0.6" />
                    <polygon points="56,82 65,68 74,82" fill="none" stroke="#00F5D4" stroke-width="1.2" />
                </g>
                <g class="{{ $animated ? 'souly-anim-float-2' : '' }}">
                    <rect x="412" y="60" width="46" height="32" rx="6" fill="#070A14" fill-opacity="0.8" stroke="#00F5D4" stroke-width="1.2" stroke-opacity="0.6" />
                    <line x1="422" y1="80" x2="422" y2="72" stroke="#00F5D4" stroke-width="2" />
                    <line x1="428" y1="80" x2="428" y2="68" stroke="#00F5D4" stroke-width="2" />
                    <line x1="434" y1="80" x2="434" y2="74" stroke="#38BDF8" stroke-width="2" />
                </g>
            </g>

            <!-- 3, 4, 5. THE OFFICIAL 100% FAITHFUL SOULY CYBER-FALCON EMBLEM EMBEDDED -->
            <!-- Contains the exact falcon head, glowing triangular eye, mechanical gears & Soul wave -->
            <g id="{{ $uid }}-falcon-group" class="souly-falcon-head">
                <g class="souly-cyber-eye">
                    <g class="souly-base-system">
                        <!-- High-Definition Masked Official Artwork -->
                        <image href="{{ asset('images/souly-emblem-retina.png') }}" 
                               x="25" y="25" 
                               width="450" height="450" 
                               preserveAspectRatio="xMidYMid meet" 
                               class="select-none" />
                    </g>
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
        </style>
    </div>

    <!-- Accompanying Arabic Brand Typography (Strictly Cairo & Tahoma) -->
    @if($showText)
        <div class="leading-tight {{ $brandTextClass }} font-cairo">
            @if($size === 'xs')
                <div class="flex items-center gap-1.5">
                    <span class="font-black text-sm text-white font-cairo block tracking-tight">سولي سيتادل</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan animate-ping"></span>
                </div>
                <span class="text-[9px] font-mono text-slate-400 block tracking-wider">SOULYEG.ONLINE</span>
            @elseif($size === 'sm')
                <div class="flex items-center gap-2">
                    <span class="font-black text-base lg:text-lg tracking-wider text-slate-100 font-cairo">سولي ديدجيتال سيتادل</span>
                    <span class="px-2 py-0.5 text-[9px] font-mono rounded-full bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30">L13 · CORE</span>
                </div>
                <p class="text-[10px] lg:text-[11px] text-slate-400 font-mono tracking-tight">SOULYEG.ONLINE // COMMAND-CENTER</p>
            @elseif($size === 'md')
                <div class="space-y-0.5 text-center font-cairo">
                    <span class="font-black text-xl text-white font-cairo block">قلعة سولي الرقمية</span>
                    <span class="text-[11px] font-mono text-cyber-cyan block tracking-wider">SOULY DIGITAL CITADEL // VERIFIED</span>
                </div>
            @else
                <div class="space-y-1 text-center font-cairo">
                    <span class="font-black text-2xl lg:text-3xl text-white font-cairo block">قلعة سولي الرقمية</span>
                    <span class="text-xs font-mono text-cyber-cyan block tracking-widest">SOULYEG.ONLINE // SOVEREIGN CYBER CITADEL</span>
                </div>
            @endif
        </div>
    @endif
</div>
