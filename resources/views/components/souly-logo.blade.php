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

    $isHero = $size === 'hero' || $size === 'lg';
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-3 select-none']) }}>
    
    <!-- Cybernetic Falcon Emblem (Pure Vector SVG + CSS3 + GSAP Hardware Acceleration) -->
    <div class="relative shrink-0 {{ $dimensionClasses }} flex items-center justify-center group">
        
        <!-- Ambient Bioluminescent Glow Ring Behind Emblem -->
        <div class="absolute inset-0 rounded-full bg-cyber-cyan/15 blur-xl pointer-events-none opacity-60 group-hover:opacity-100 transition-opacity duration-500"></div>

        <svg id="{{ $uid }}" 
             viewBox="0 0 500 500" 
             class="w-full h-full drop-shadow-[0_0_15px_rgba(0,245,212,0.4)] overflow-visible"
             xmlns="http://www.w3.org/2000/svg"
             aria-label="شعار قلعة سولي الرقمية - الصقر السيبراني">
            
            <defs>
                <!-- Neon Cyan Filter -->
                <filter id="{{ $uid }}-glow-cyan" x="-20%" y="-20%" width="140%" height="140%">
                    <feGaussianBlur stdDeviation="3.5" result="blur" />
                    <feMerge>
                        <feMergeNode in="blur" />
                        <feMergeNode in="blur" />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>

                <!-- High-Intensity Radar Filter -->
                <filter id="{{ $uid }}-glow-intense" x="-30%" y="-30%" width="160%" height="160%">
                    <feGaussianBlur stdDeviation="6" result="blur" />
                    <feMerge>
                        <feMergeNode in="blur" />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>

                <!-- Gold Glow Filter -->
                <filter id="{{ $uid }}-glow-gold" x="-20%" y="-20%" width="140%" height="140%">
                    <feGaussianBlur stdDeviation="2.5" result="blur" />
                    <feMerge>
                        <feMergeNode in="blur" />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>

                <!-- Color Gradients -->
                <linearGradient id="{{ $uid }}-grad-cyan-teal" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#00F5D4" />
                    <stop offset="60%" stop-color="#38BDF8" />
                    <stop offset="100%" stop-color="#0284C7" />
                </linearGradient>

                <linearGradient id="{{ $uid }}-grad-gold" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#FFB800" />
                    <stop offset="100%" stop-color="#FF5500" />
                </linearGradient>

                <linearGradient id="{{ $uid }}-grad-facet-light" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#243354" />
                    <stop offset="50%" stop-color="#18233C" />
                    <stop offset="100%" stop-color="#0E1626" />
                </linearGradient>

                <linearGradient id="{{ $uid }}-grad-facet-mid" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#162035" />
                    <stop offset="100%" stop-color="#0A0E1A" />
                </linearGradient>

                <linearGradient id="{{ $uid }}-grad-facet-dark" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#0E1626" />
                    <stop offset="100%" stop-color="#05070E" />
                </linearGradient>

                <linearGradient id="{{ $uid }}-grad-wave" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#060913" />
                    <stop offset="45%" stop-color="#0F172A" />
                    <stop offset="100%" stop-color="#0284C7" stop-opacity="0.2" />
                </linearGradient>

                <linearGradient id="{{ $uid }}-grad-card-bg" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#00F5D4" stop-opacity="0.18" />
                    <stop offset="100%" stop-color="#0B0C16" stop-opacity="0.85" />
                </linearGradient>

                <linearGradient id="{{ $uid }}-grad-swoosh" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#00F5D4" />
                    <stop offset="50%" stop-color="#38BDF8" />
                    <stop offset="100%" stop-color="#7B2CBF" />
                </linearGradient>
            </defs>

            <!-- ======================================================== -->
            <!-- 1. OUTER & INNER CONCENTRIC HUD RADAR RINGS               -->
            <!-- ======================================================== -->
            <g class="souly-hud-radar" transform-origin="250 250">
                
                <!-- Outer Radar Ring (Rotates Clockwise) -->
                <g class="{{ $animated ? 'souly-anim-radar-cw' : '' }}" transform-origin="250 250">
                    <!-- Base Track -->
                    <circle cx="250" cy="250" r="222" fill="none" stroke="#00F5D4" stroke-opacity="0.18" stroke-width="1.5" />
                    
                    <!-- Dashed Telemetry Arc -->
                    <circle cx="250" cy="250" r="222" fill="none" stroke="#00F5D4" stroke-opacity="0.45" stroke-width="2.5" 
                            stroke-dasharray="14 10 3 10 32 18 6 12" />

                    <!-- Primary Glow Arcs -->
                    <path d="M 250 28 A 222 222 0 0 1 425 110" fill="none" stroke="#00F5D4" stroke-width="3" 
                          stroke-linecap="round" filter="url(#{{ $uid }}-glow-cyan)" />
                    <path d="M 75 390 A 222 222 0 0 1 35 285" fill="none" stroke="#38BDF8" stroke-width="2.5" 
                          stroke-linecap="round" opacity="0.8" />
                    <path d="M 465 295 A 222 222 0 0 1 390 415" fill="none" stroke="#00F5D4" stroke-width="2" 
                          stroke-linecap="round" stroke-dasharray="6 6" />

                    <!-- Perimeter Degree Ticks -->
                    <line x1="250" y1="20" x2="250" y2="34" stroke="#00F5D4" stroke-width="2" />
                    <line x1="250" y1="466" x2="250" y2="480" stroke="#00F5D4" stroke-width="2" opacity="0.5" />
                    <line x1="20" y1="250" x2="34" y2="250" stroke="#00F5D4" stroke-width="2" />
                    <line x1="466" y1="250" x2="480" y2="250" stroke="#00F5D4" stroke-width="2" />
                    
                    <!-- 45-Degree Micro Circuit Nodes -->
                    <circle cx="93" cy="93" r="3.5" fill="#00F5D4" filter="url(#{{ $uid }}-glow-cyan)" />
                    <circle cx="407" cy="93" r="3.5" fill="#00F5D4" filter="url(#{{ $uid }}-glow-cyan)" />
                    <circle cx="93" cy="407" r="3" fill="#38BDF8" />
                    <circle cx="407" cy="407" r="3.5" fill="#00F5D4" filter="url(#{{ $uid }}-glow-cyan)" />
                </g>

                <!-- Inner Radar Ring (Rotates Counter-Clockwise) -->
                <g class="{{ $animated ? 'souly-anim-radar-ccw' : '' }}" transform-origin="250 250">
                    <circle cx="250" cy="250" r="190" fill="none" stroke="#38BDF8" stroke-opacity="0.2" stroke-width="1.2" />
                    <circle cx="250" cy="250" r="190" fill="none" stroke="#00F5D4" stroke-opacity="0.4" stroke-width="2"
                            stroke-dasharray="40 12 8 12" />
                    
                    <!-- Inner Cardinal Reticle Crosses -->
                    <path d="M 245 60 L 255 60 M 250 55 L 250 65" stroke="#00F5D4" stroke-width="1.5" />
                    <path d="M 435 245 L 445 245 M 440 240 L 440 250" stroke="#00F5D4" stroke-width="1.5" />
                    <path d="M 55 245 L 65 245 M 60 240 L 60 250" stroke="#38BDF8" stroke-width="1.5" />
                    
                    <!-- Micro Orbiting Node -->
                    <circle cx="250" cy="60" r="2.5" fill="#00F5D4" filter="url(#{{ $uid }}-glow-cyan)" />
                </g>
            </g>

            <!-- ======================================================== -->
            <!-- 2. FLOATING HOLOGRAPHIC DATA CARDS (4 HUD PERIMETER CARDS)-->
            <!-- ======================================================== -->
            <g class="souly-hud-cards">
                <!-- Card 1: Top-Left (Telemetry & Micro-Pyramid) -->
                <g class="{{ $animated ? 'souly-anim-float-1' : '' }}">
                    <rect x="42" y="65" width="76" height="48" rx="8" 
                          fill="url(#{{ $uid }}-grad-card-bg)" stroke="#00F5D4" stroke-width="1.2" stroke-opacity="0.6" />
                    <!-- Corner Brackets -->
                    <path d="M 42 75 L 42 65 L 52 65" fill="none" stroke="#00F5D4" stroke-width="2" />
                    <path d="M 118 103 L 118 113 L 108 113" fill="none" stroke="#00F5D4" stroke-width="2" />
                    <!-- Mini Pyramid -->
                    <polygon points="62,98 72,78 82,98" fill="none" stroke="#00F5D4" stroke-width="1.5" filter="url(#{{ $uid }}-glow-cyan)" />
                    <polygon points="67,98 72,86 77,98" fill="#00F5D4" opacity="0.4" />
                    <!-- Telemetry Wave Lines -->
                    <path d="M 88 84 L 94 80 L 100 88 L 108 82" fill="none" stroke="#38BDF8" stroke-width="1.5" />
                    <circle cx="108" cy="82" r="1.5" fill="#00F5D4" />
                    <line x1="88" y1="94" x2="110" y2="94" stroke="#00F5D4" stroke-width="1" stroke-dasharray="2 2" opacity="0.7" />
                </g>

                <!-- Card 2: Top-Right (256-bit Security HUD) -->
                <g class="{{ $animated ? 'souly-anim-float-2' : '' }}">
                    <rect x="382" y="65" width="76" height="48" rx="8" 
                          fill="url(#{{ $uid }}-grad-card-bg)" stroke="#00F5D4" stroke-width="1.2" stroke-opacity="0.6" />
                    <path d="M 458 75 L 458 65 L 448 65" fill="none" stroke="#00F5D4" stroke-width="2" />
                    <path d="M 382 103 L 382 113 L 392 113" fill="none" stroke="#00F5D4" stroke-width="2" />
                    <!-- Mini Pyramid Core -->
                    <polygon points="400,98 409,78 418,98" fill="none" stroke="#FFB800" stroke-width="1.5" filter="url(#{{ $uid }}-glow-gold)" />
                    <!-- Signal Bars -->
                    <line x1="426" y1="96" x2="426" y2="88" stroke="#00F5D4" stroke-width="2" />
                    <line x1="432" y1="96" x2="432" y2="82" stroke="#00F5D4" stroke-width="2" />
                    <line x1="438" y1="96" x2="438" y2="76" stroke="#00F5D4" stroke-width="2" />
                    <line x1="444" y1="96" x2="444" y2="80" stroke="#38BDF8" stroke-width="2" />
                    <circle cx="444" cy="74" r="1.5" fill="#00F5D4" />
                </g>

                <!-- Card 3: Mid-Left (Network & Node Link) -->
                <g class="{{ $animated ? 'souly-anim-float-3' : '' }}">
                    <rect x="18" y="210" width="68" height="42" rx="7" 
                          fill="url(#{{ $uid }}-grad-card-bg)" stroke="#38BDF8" stroke-width="1.2" stroke-opacity="0.5" />
                    <path d="M 18 218 L 18 210 L 26 210" fill="none" stroke="#38BDF8" stroke-width="2" />
                    <!-- Micro Graph -->
                    <circle cx="36" cy="231" r="3" fill="#00F5D4" />
                    <circle cx="58" cy="225" r="3" fill="#38BDF8" />
                    <circle cx="68" cy="237" r="2.5" fill="#00F5D4" />
                    <line x1="36" y1="231" x2="58" y2="225" stroke="#00F5D4" stroke-width="1.2" />
                    <line x1="58" y1="225" x2="68" y2="237" stroke="#38BDF8" stroke-width="1.2" />
                </g>

                <!-- Card 4: Mid-Right (Target Lock & Sector Matrix) -->
                <g class="{{ $animated ? 'souly-anim-float-4' : '' }}">
                    <rect x="414" y="210" width="68" height="42" rx="7" 
                          fill="url(#{{ $uid }}-grad-card-bg)" stroke="#00F5D4" stroke-width="1.2" stroke-opacity="0.5" />
                    <path d="M 482 218 L 482 210 L 474 210" fill="none" stroke="#00F5D4" stroke-width="2" />
                    <!-- Crosshair Reticle -->
                    <circle cx="448" cy="231" r="10" fill="none" stroke="#00F5D4" stroke-width="1.2" stroke-dasharray="3 3" />
                    <circle cx="448" cy="231" r="2.5" fill="#FFB800" filter="url(#{{ $uid }}-glow-gold)" />
                    <line x1="448" y1="217" x2="448" y2="245" stroke="#00F5D4" stroke-width="1" opacity="0.6" />
                    <line x1="434" y1="231" x2="462" y2="231" stroke="#00F5D4" stroke-width="1" opacity="0.6" />
                </g>
            </g>

            <!-- ======================================================== -->
            <!-- 3. THE CYBERNETIC FALCON HEAD (RIGHT-FACING MECHA PROFILE) -->
            <!-- ======================================================== -->
            <g id="{{ $uid }}-falcon-head" class="souly-falcon-head">
                
                <!-- Crown & Angular Feathers (Stealth Facets) -->
                <!-- Feather 1 (Topmost Sharp Spike) -->
                <polygon points="190,135 258,185 220,192 165,152" 
                         fill="url(#{{ $uid }}-grad-facet-light)" stroke="#00F5D4" stroke-width="1.6" stroke-opacity="0.85" />
                <!-- Feather 2 (Mid Crest Spire) -->
                <polygon points="165,152 220,192 188,212 142,185" 
                         fill="url(#{{ $uid }}-grad-facet-mid)" stroke="#00F5D4" stroke-width="1.4" stroke-opacity="0.75" />
                <!-- Feather 3 (Lower Crest Spire) -->
                <polygon points="142,185 188,212 164,242 128,225" 
                         fill="url(#{{ $uid }}-grad-facet-dark)" stroke="#00F5D4" stroke-width="1.4" stroke-opacity="0.65" />
                
                <!-- Forehead & Brow Facets -->
                <polygon points="258,185 315,215 285,228 220,192" 
                         fill="url(#{{ $uid }}-grad-facet-light)" stroke="#00F5D4" stroke-width="1.5" />
                <polygon points="315,215 348,235 322,246 285,228" 
                         fill="url(#{{ $uid }}-grad-facet-mid)" stroke="#00F5D4" stroke-width="1.8" />

                <!-- Upper Beak: Powerful Stealth Mecha Hook -->
                <polygon points="348,235 398,252 368,280 326,262 322,246" 
                         fill="url(#{{ $uid }}-grad-facet-light)" stroke="#00F5D4" stroke-width="2.2" 
                         filter="url(#{{ $uid }}-glow-cyan)" />
                <!-- Beak Razor Cutting Edge -->
                <path d="M 348 235 L 400 252 L 368 280" fill="none" stroke="#FFFFFF" stroke-width="1.8" opacity="0.9" />

                <!-- Lower Beak -->
                <polygon points="326,262 364,280 340,288 312,274" 
                         fill="url(#{{ $uid }}-grad-facet-dark)" stroke="#00F5D4" stroke-width="1.5" />

                <!-- Central Cheek Armor & Mecha Plates -->
                <polygon points="285,228 322,246 312,274 265,268 245,248" 
                         fill="url(#{{ $uid }}-grad-facet-mid)" stroke="#00F5D4" stroke-width="1.5" />
                <polygon points="265,268 312,274 326,306 280,314 242,294" 
                         fill="url(#{{ $uid }}-grad-facet-dark)" stroke="#38BDF8" stroke-width="1.5" />

                <!-- Ear Guard & Rear Head Armor -->
                <polygon points="220,192 245,248 205,272 164,242" 
                         fill="url(#{{ $uid }}-grad-facet-dark)" stroke="#00F5D4" stroke-width="1.4" />
                <polygon points="205,272 242,294 214,332 172,305 164,242" 
                         fill="url(#{{ $uid }}-grad-facet-mid)" stroke="#00F5D4" stroke-width="1.4" />

                <!-- Neck Armor & Branching Micro-Circuit Board -->
                <polygon points="242,294 280,314 268,362 214,332" 
                         fill="url(#{{ $uid }}-grad-facet-light)" stroke="#00F5D4" stroke-width="1.5" />
                
                <!-- Glowing Amber / Crimson Micro-Circuits on Neck Seams -->
                <g class="souly-neck-circuits">
                    <path d="M 164 242 L 205 272 L 242 294 L 280 314" 
                          fill="none" stroke="#FFB800" stroke-width="1.8" stroke-dasharray="4 3" filter="url(#{{ $uid }}-glow-gold)" />
                    <path d="M 205 272 L 214 332 L 268 362" 
                          fill="none" stroke="#FF2A55" stroke-width="1.5" opacity="0.85" />
                    <!-- Circuit Nodes -->
                    <circle cx="205" cy="272" r="3" fill="#FFB800" filter="url(#{{ $uid }}-glow-gold)" />
                    <circle cx="242" cy="294" r="2.5" fill="#FFB800" />
                    <circle cx="214" cy="332" r="2.5" fill="#FF2A55" />
                    <circle cx="280" cy="314" r="3" fill="#00F5D4" filter="url(#{{ $uid }}-glow-cyan)" />
                </g>

                <!-- ======================================================== -->
                <!-- 4. THE GLOWING TRIANGULAR CYBER EYE                      -->
                <!-- ======================================================== -->
                <g id="{{ $uid }}-cyber-eye" class="souly-cyber-eye">
                    <!-- Dark Eye Socket Frame -->
                    <polygon points="280,222 310,230 298,242 274,234" fill="#05070E" stroke="#00F5D4" stroke-width="1.8" />
                    
                    <!-- Ambient Eye Glow -->
                    <ellipse cx="292" cy="231" rx="8" ry="5" fill="#00F5D4" opacity="0.3" filter="url(#{{ $uid }}-glow-intense)" />
                    
                    <!-- Pulsing Triangular Core Eye -->
                    <polygon points="285,225 304,228 293,239" 
                             fill="#00F5D4" filter="url(#{{ $uid }}-glow-intense)"
                             class="{{ $animated ? 'souly-anim-eye-pulse' : '' }}" />
                    
                    <!-- Piercing White Pupil Center & Radar Scan Glint -->
                    <polygon points="289,227 299,229 293,235" fill="#FFFFFF" />
                    <line x1="270" y1="231" x2="315" y2="231" stroke="#FFFFFF" stroke-width="1" opacity="0.8" 
                          class="{{ $animated ? 'souly-anim-scan-glint' : '' }}" />
                </g>
            </g>

            <!-- ======================================================== -->
            <!-- 5. DYNAMIC WAVE BASE & MECHANICAL ROTATING GEARS          -->
            <!-- ======================================================== -->
            <g class="souly-base-system">
                
                <!-- Aerodynamic Cyber Wave Sweep -->
                <path d="M 65 348 C 120 320, 195 332, 260 368 C 315 398, 385 400, 445 378 L 440 398 C 378 422, 308 418, 252 388 C 190 352, 120 348, 65 376 Z" 
                      fill="url(#{{ $uid }}-grad-wave)" stroke="#00F5D4" stroke-width="1.5" />
                
                <!-- Secondary Luminous Wave Trace -->
                <path d="M 85 365 C 145 342, 210 354, 270 384 C 322 410, 390 412, 435 395" 
                      fill="none" stroke="url(#{{ $uid }}-grad-swoosh)" stroke-width="2.5" stroke-linecap="round"
                      filter="url(#{{ $uid }}-glow-cyan)" />

                <!-- 3 INTERLOCKING MECHANICAL GEARS (ON THE LEFT OF THE WAVE) -->
                <!-- Gear 1: Primary Large Gear (Rotates Clockwise) -->
                <g class="{{ $animated ? 'souly-anim-gear-cw' : '' }}" transform-origin="122 362">
                    <circle cx="122" cy="362" r="22" fill="#0B132B" stroke="#00F5D4" stroke-width="1.8" />
                    <!-- Gear Cogs (8 Teeth) -->
                    <rect x="119" y="336" width="6" height="6" rx="1" fill="#00F5D4" />
                    <rect x="119" y="382" width="6" height="6" rx="1" fill="#00F5D4" />
                    <rect x="96" y="359" width="6" height="6" rx="1" fill="#00F5D4" />
                    <rect x="142" y="359" width="6" height="6" rx="1" fill="#00F5D4" />
                    <rect x="105" y="345" width="6" height="6" rx="1" transform="rotate(45 108 348)" fill="#00F5D4" />
                    <rect x="133" y="373" width="6" height="6" rx="1" transform="rotate(45 136 376)" fill="#00F5D4" />
                    <rect x="105" y="373" width="6" height="6" rx="1" transform="rotate(-45 108 376)" fill="#00F5D4" />
                    <rect x="133" y="345" width="6" height="6" rx="1" transform="rotate(-45 136 348)" fill="#00F5D4" />
                    <!-- Inner Hub -->
                    <circle cx="122" cy="362" r="11" fill="#05070E" stroke="#38BDF8" stroke-width="1.2" />
                    <circle cx="122" cy="362" r="4.5" fill="#00F5D4" filter="url(#{{ $uid }}-glow-cyan)" />
                </g>

                <!-- Gear 2: Upper-Right Interlocking Gear (Rotates Counter-Clockwise) -->
                <g class="{{ $animated ? 'souly-anim-gear-ccw' : '' }}" transform-origin="156 345">
                    <circle cx="156" cy="345" r="14" fill="#0B132B" stroke="#38BDF8" stroke-width="1.5" />
                    <!-- 6 Teeth -->
                    <rect x="154" y="328" width="4" height="4" fill="#38BDF8" />
                    <rect x="154" y="358" width="4" height="4" fill="#38BDF8" />
                    <rect x="139" y="343" width="4" height="4" fill="#38BDF8" />
                    <rect x="169" y="343" width="4" height="4" fill="#38BDF8" />
                    <!-- Inner Hub -->
                    <circle cx="156" cy="345" r="7" fill="#05070E" stroke="#00F5D4" stroke-width="1" />
                    <circle cx="156" cy="345" r="3" fill="#38BDF8" />
                </g>

                <!-- Gear 3: Lower-Right Interlocking Gear (Rotates Clockwise) -->
                <g class="{{ $animated ? 'souly-anim-gear-cw' : '' }}" transform-origin="158 378">
                    <circle cx="158" cy="378" r="13" fill="#0B132B" stroke="#00F5D4" stroke-width="1.4" />
                    <rect x="156" y="362" width="4" height="4" fill="#00F5D4" />
                    <rect x="156" y="390" width="4" height="4" fill="#00F5D4" />
                    <rect x="142" y="376" width="4" height="4" fill="#00F5D4" />
                    <rect x="170" y="376" width="4" height="4" fill="#00F5D4" />
                    <circle cx="158" cy="378" r="6" fill="#05070E" stroke="#38BDF8" stroke-width="1" />
                    <circle cx="158" cy="378" r="2.5" fill="#00F5D4" />
                </g>

                <!-- Branching Circuit Traces Along Base Wave -->
                <g class="souly-base-circuits">
                    <!-- Microchip Box -->
                    <rect x="185" y="354" width="16" height="16" rx="3" fill="#0A0F1D" stroke="#00F5D4" stroke-width="1.2" />
                    <rect x="189" y="358" width="8" height="8" rx="1" fill="#00F5D4" opacity="0.6" />
                    <!-- Microchip Pins -->
                    <line x1="181" y1="358" x2="185" y2="358" stroke="#00F5D4" stroke-width="1" />
                    <line x1="181" y1="366" x2="185" y2="366" stroke="#00F5D4" stroke-width="1" />
                    <line x1="201" y1="358" x2="205" y2="358" stroke="#00F5D4" stroke-width="1" />
                    <line x1="201" y1="366" x2="205" y2="366" stroke="#00F5D4" stroke-width="1" />

                    <!-- Branching Bus Lines & Nodes -->
                    <path d="M 205 362 L 225 362 L 245 382 L 290 382" fill="none" stroke="#00F5D4" stroke-width="1.4" stroke-dasharray="4 3" />
                    <circle cx="290" cy="382" r="3" fill="#00F5D4" filter="url(#{{ $uid }}-glow-cyan)" />
                    <circle cx="245" cy="382" r="2.5" fill="#38BDF8" />
                    <circle cx="225" cy="362" r="2.5" fill="#FFB800" filter="url(#{{ $uid }}-glow-gold)" />
                </g>

                <!-- ======================================================== -->
                <!-- 6. "SOULY" VECTOR EMBLEM TYPOGRAPHY (BOTTOM-RIGHT WAVE)   -->
                <!-- ======================================================== -->
                <g class="souly-emblem-brand" transform="translate(315, 372)">
                    <!-- Futuristic Brandmark Letters -->
                    <text x="0" y="24" 
                          font-family="'Readex Pro', 'Cairo', 'JetBrains Mono', sans-serif" 
                          font-size="28" 
                          font-weight="900" 
                          letter-spacing="4" 
                          fill="url(#{{ $uid }}-grad-cyan-teal)"
                          filter="url(#{{ $uid }}-glow-cyan)">SOULY</text>
                    
                    <text x="2" y="35" 
                          font-family="'JetBrains Mono', monospace" 
                          font-size="9" 
                          font-weight="700" 
                          letter-spacing="2.5" 
                          fill="#38BDF8" 
                          opacity="0.85">CITADEL</text>

                    <!-- Underlying Glowing Swoosh Arc -->
                    <path d="M -5 38 Q 45 48 95 36" fill="none" stroke="url(#{{ $uid }}-grad-swoosh)" stroke-width="2.5" stroke-linecap="round" />
                    <circle cx="95" cy="36" r="2.5" fill="#00F5D4" filter="url(#{{ $uid }}-glow-cyan)" />
                </g>
            </g>
        </svg>

        <!-- Scoped Hardware-Accelerated CSS3 Animations (Native & Zero-Dependency) -->
        <style>
            @keyframes {{ $uid }}-spin-cw {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            @keyframes {{ $uid }}-spin-ccw {
                from { transform: rotate(0deg); }
                to { transform: rotate(-360deg); }
            }
            @keyframes {{ $uid }}-float-hud {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-4px); }
            }
            @keyframes {{ $uid }}-float-hud-alt {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(4px); }
            }
            @keyframes {{ $uid }}-eye-pulse {
                0%, 100% { opacity: 0.9; transform: scale(1); }
                50% { opacity: 1; transform: scale(1.12); }
            }
            @keyframes {{ $uid }}-scan-glint {
                0%, 100% { opacity: 0.3; transform: scaleX(0.7); }
                50% { opacity: 1; transform: scaleX(1.1); }
            }

            #{{ $uid }} .souly-anim-radar-cw {
                animation: {{ $uid }}-spin-cw 40s linear infinite;
            }
            #{{ $uid }} .souly-anim-radar-ccw {
                animation: {{ $uid }}-spin-ccw 30s linear infinite;
            }
            #{{ $uid }} .souly-anim-gear-cw {
                animation: {{ $uid }}-spin-cw 12s linear infinite;
            }
            #{{ $uid }} .souly-anim-gear-ccw {
                animation: {{ $uid }}-spin-ccw 9s linear infinite;
            }
            #{{ $uid }} .souly-anim-float-1 {
                animation: {{ $uid }}-float-hud 4s ease-in-out infinite;
            }
            #{{ $uid }} .souly-anim-float-2 {
                animation: {{ $uid }}-float-hud-alt 4.5s ease-in-out infinite 0.5s;
            }
            #{{ $uid }} .souly-anim-float-3 {
                animation: {{ $uid }}-float-hud 5s ease-in-out infinite 1s;
            }
            #{{ $uid }} .souly-anim-float-4 {
                animation: {{ $uid }}-float-hud-alt 4.2s ease-in-out infinite 1.5s;
            }
            #{{ $uid }} .souly-anim-eye-pulse {
                transform-origin: 292px 231px;
                animation: {{ $uid }}-eye-pulse 2.2s ease-in-out infinite;
            }
            #{{ $uid }} .souly-anim-scan-glint {
                transform-origin: 292px 231px;
                animation: {{ $uid }}-scan-glint 3.5s ease-in-out infinite;
            }
        </style>

        @if($animated)
        <script>
            // GSAP Enhanced Bioluminescence & Kinetic Telemetry (Runs if GSAP is available)
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof gsap !== 'undefined') {
                    const logoEl = document.getElementById('{{ $uid }}');
                    if (logoEl && !logoEl.dataset.gsapInit) {
                        logoEl.dataset.gsapInit = 'true';
                        // Radar Scanning Pulse
                        gsap.to('#{{ $uid }} .souly-falcon-head', {
                            filter: 'drop-shadow(0 0 12px rgba(0, 245, 212, 0.45))',
                            duration: 2.8,
                            repeat: -1,
                            yoyo: true,
                            ease: 'sine.inOut'
                        });
                    }
                }
            });
        </script>
        @endif
    </div>

    <!-- Optional Accompanying Arabic Brand Typography -->
    @if($showText)
        <div class="leading-tight {{ $brandTextClass }}">
            @if($size === 'xs')
                <div class="flex items-center gap-1.5">
                    <span class="font-extrabold text-sm text-white font-cairo block tracking-tight">سولي سيتادل</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan animate-ping"></span>
                </div>
                <span class="text-[9px] font-mono text-slate-400 block tracking-wider">SOULYEG.ONLINE</span>
            @elseif($size === 'sm')
                <div class="flex items-center gap-2">
                    <span class="font-extrabold text-base lg:text-lg tracking-wider text-slate-100 font-cairo">سولي ديدجيتال سيتادل</span>
                    <span class="px-2 py-0.5 text-[9px] font-mono rounded-full bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30">L13 · CORE</span>
                </div>
                <p class="text-[10px] lg:text-[11px] text-slate-400 font-mono tracking-tight">SOULYEG.ONLINE // COMMAND-CENTER</p>
            @elseif($size === 'md')
                <div class="space-y-0.5 text-center">
                    <span class="font-black text-xl text-white font-cairo block">قلعة سولي الرقمية</span>
                    <span class="text-[11px] font-mono text-cyber-cyan block tracking-wider">SOULY DIGITAL CITADEL // VERIFIED</span>
                </div>
            @else
                <div class="space-y-1 text-center">
                    <span class="font-black text-2xl lg:text-3xl text-white font-cairo block">قلعة سولي الرقمية</span>
                    <span class="text-xs font-mono text-cyber-cyan block tracking-widest">SOULYEG.ONLINE // SOVEREIGN CYBER CITADEL</span>
                </div>
            @endif
        </div>
    @endif
</div>