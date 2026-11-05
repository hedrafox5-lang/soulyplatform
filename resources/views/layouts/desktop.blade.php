@extends('layouts.master')

@section('layout-body')
<div class="min-h-screen flex flex-col w-full bg-obsidian-base">
    <!-- Clean, Sticky Cyber-Glassmorphic Desktop Navigation Header -->
    <header class="fixed top-0 inset-x-0 z-50 w-full backdrop-blur-xl bg-[#05050A]/90 border-b border-[#1C1F36] px-4 sm:px-6 py-3.5 transition-all">
        <div class="max-w-[1920px] mx-auto flex items-center justify-between gap-4">
            
            <!-- Right (RTL): Brand Logo with Animated Souly Emblem -->
            <div class="flex items-center shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-none">
                    <x-souly-logo size="sm" :showText="true" :animated="true" />
                </a>
            </div>

            <!-- Center: Role-Aware Context Navigation Links -->
            <nav class="hidden md:flex items-center gap-1.5 lg:gap-2">
                @guest
                    <a href="{{ route('home') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->routeIs('home') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30 shadow-neon-cyan' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                        الصفحة الرئيسية
                    </a>
                    <a href="{{ route('services.index') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->routeIs('services.*') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30 shadow-neon-cyan' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                        خدمات سولي
                    </a>
                    <a href="{{ url('/store/branderp') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->is('store/branderp*') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30 shadow-neon-cyan' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                        متجر BrandERP
                    </a>
                    <a href="{{ url('/store/hardware') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->is('store/hardware*') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30 shadow-neon-cyan' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                        متجر الهاردوير
                    </a>
                    <button type="button" 
                            @click="$store.citadel && $store.citadel.toggleSoulyAi()"
                            class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo text-cyber-cyan hover:text-white hover:bg-cyber-cyan/10 border border-cyber-cyan/20 hover:border-cyber-cyan/40 transition-all flex items-center gap-2 group cursor-pointer">
                        <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></span>
                        <span>تحدث مع سولي AI</span>
                    </button>
                @else
                    @if(in_array(auth()->user()->role, ['admin', 'staff']))
                        <a href="{{ route('home') }}" 
                            class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->routeIs('home') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30 shadow-neon-cyan' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                            الصفحة الرئيسية
                        </a>
                        <a href="{{ route('services.index') }}" 
                            class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->routeIs('services.*') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30 shadow-neon-cyan' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                            خدمات سولي
                        </a>
                        <a href="{{ route('admin.index') }}" 
                            class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->routeIs('admin.index') ? 'bg-cyber-gold/20 text-cyber-gold border border-cyber-gold/40 shadow-neon-gold' : 'text-slate-300 hover:text-cyber-gold hover:bg-cyber-gold/10 border border-transparent' }}">
                            غرفة العمليات
                        </a>
                        <a href="{{ route('admin.bookings.index') }}" 
                            class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->routeIs('admin.bookings.*') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                            الحجوزات
                        </a>
                        <a href="{{ route('admin.leads.index') }}" 
                            class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->routeIs('admin.leads.*') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                            العملاء CRM
                        </a>
                        <a href="{{ route('admin.wallets.index') }}" 
                            class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->routeIs('admin.wallets.*') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                            المالية
                        </a>
                    @else
                        <a href="{{ route('home') }}" 
                            class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->routeIs('home') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30 shadow-neon-cyan' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                            الصفحة الرئيسية
                        </a>
                        <a href="{{ route('services.index') }}" 
                            class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->routeIs('services.*') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30 shadow-neon-cyan' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                            خدمات سولي
                        </a>
                        <a href="{{ url('/store/branderp') }}" 
                            class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->is('store/branderp*') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30 shadow-neon-cyan' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                            متجر BrandERP
                        </a>
                        <a href="{{ url('/store/hardware') }}" 
                            class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->is('store/hardware*') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30 shadow-neon-cyan' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                            متجر الهاردوير
                        </a>
                        <a href="{{ route('portal.index') }}" 
                            class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->routeIs('portal.index') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                            لوحة حسابي
                        </a>
                        <a href="{{ route('portal.bookings') }}" 
                            class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->routeIs('portal.bookings*') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                            حجوزاتي
                        </a>
                        <a href="{{ route('portal.tickets') }}" 
                            class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium font-cairo transition-all {{ request()->routeIs('portal.tickets*') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent' }}">
                            الدعم الفني
                        </a>
                    @endif
                @endguest
            </nav>

            <!-- Left (RTL): State Actions, Controls, and Modals -->
            <div class="flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="btn-citadel-secondary btn-souly-compact">
                        تسجيل الدخول
                    </a>
                    <button type="button" 
                            @click="$dispatch('open-express-booking')" 
                            class="btn-citadel-primary btn-souly-compact">
                        <span>احجز الآن / استشارة</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </button>
                @else
                    @if(in_array(auth()->user()->role, ['admin', 'staff']))
                        <div class="relative" x-data="{ adminMenuOpen: false }">
                            <button @click="adminMenuOpen = !adminMenuOpen" 
                                    @click.outside="adminMenuOpen = false"
                                    type="button"
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/40 hover:bg-cyber-gold/25 text-xs font-bold font-cairo transition-all shadow-sm cursor-pointer">
                                <span>إدارة المنصة ⚙️</span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="adminMenuOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="adminMenuOpen"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                 class="absolute left-0 mt-2 w-56 rounded-xl bg-obsidian-surface/95 backdrop-blur-xl border border-obsidian-border shadow-2xl py-1.5 z-50 font-cairo"
                                 style="display: none;">
                                <div class="px-3 py-1.5 text-[10px] uppercase font-mono text-slate-400 border-b border-obsidian-border">أدوات الإدارة والتحكم</div>
                                <a href="{{ route('admin.erp_store.index') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-200 hover:text-cyber-cyan hover:bg-white/5 transition-colors">
                                    <span>🏪</span>
                                    <span>إدارة متجر BrandERP (500 ميزة)</span>
                                </a>
                                <a href="{{ route('admin.hardware.index') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-200 hover:text-cyber-cyan hover:bg-white/5 transition-colors">
                                    <span>🖥️</span>
                                    <span>إدارة متجر الهاردوير والأوردرات</span>
                                </a>
                                <a href="{{ route('admin.services.index') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-200 hover:text-cyber-cyan hover:bg-white/5 transition-colors">
                                    <svg class="w-4 h-4 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                    <span>إدارة الخدمات والباقات</span>
                                </a>
                                <a href="{{ route('admin.canned-responses.index') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-200 hover:text-cyber-purple hover:bg-white/5 transition-colors">
                                    <svg class="w-4 h-4 text-cyber-purple" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                    <span>خزينة الردود</span>
                                </a>
                                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-200 hover:text-cyber-gold hover:bg-white/5 transition-colors">
                                    <svg class="w-4 h-4 text-cyber-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>إعدادات النظام</span>
                                </a>
                            </div>
                        </div>

                        <button @click="$store.citadel && $store.citadel.openNotificationCenter()" 
                                type="button" 
                                title="مركز الإشعارات والتنبيهات"
                                class="relative p-2 rounded-xl bg-obsidian-surface border border-obsidian-border text-slate-300 hover:text-cyber-gold hover:border-cyber-gold/40 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <template x-if="$store.citadel && $store.citadel.unreadNotificationsCount > 0">
                                <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-[10px] font-bold text-white flex items-center justify-center font-mono shadow-[0_0_10px_rgba(244,63,94,0.7)] animate-pulse" 
                                      x-text="$store.citadel.unreadNotificationsCount"></span>
                            </template>
                            <template x-if="!$store.citadel || !$store.citadel.unreadNotificationsCount || $store.citadel.unreadNotificationsCount === 0">
                                <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                            </template>
                        </button>

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="تسجيل الخروج" class="p-2 text-slate-400 hover:text-rose-400 rounded-xl hover:bg-rose-500/10 border border-transparent hover:border-rose-500/30 transition-all cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('portal.wallet') }}" 
                           title="محفظة سولي الرقمية"
                           class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-obsidian-surface border border-cyber-cyan/40 hover:border-cyber-cyan text-xs font-mono shadow-sm transition-all group">
                            <div class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></div>
                            <span class="text-slate-400 text-xs font-cairo">المحفظة:</span>
                            <span class="text-cyber-cyan font-bold font-mono">{{ number_format(Auth::user()->wallet->balance ?? 0, 2) }} ج.م</span>
                        </a>

                        <button @click="$store.citadel && $store.citadel.openNotificationCenter()" 
                                type="button" 
                                title="مركز الإشعارات والتنبيهات"
                                class="relative p-2 rounded-xl bg-obsidian-surface border border-obsidian-border text-slate-300 hover:text-cyber-gold hover:border-cyber-gold/40 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <template x-if="$store.citadel && $store.citadel.unreadNotificationsCount > 0">
                                <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-[10px] font-bold text-white flex items-center justify-center font-mono shadow-[0_0_10px_rgba(244,63,94,0.7)] animate-pulse" 
                                      x-text="$store.citadel.unreadNotificationsCount"></span>
                            </template>
                            <template x-if="!$store.citadel || !$store.citadel.unreadNotificationsCount || $store.citadel.unreadNotificationsCount === 0">
                                <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                            </template>
                        </button>

                        <div class="relative" x-data="{ userMenuOpen: false }">
                            <button @click="userMenuOpen = !userMenuOpen" 
                                    @click.outside="userMenuOpen = false"
                                    type="button"
                                    class="flex items-center gap-2.5 p-1.5 pr-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border hover:border-cyber-cyan/40 transition-all text-right cursor-pointer">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-cyber-purple to-cyber-cyan p-[1px] shrink-0">
                                    <div class="w-full h-full bg-obsidian-surface rounded-lg flex items-center justify-center font-bold text-xs text-cyber-cyan">
                                        {{ mb_substr(Auth::user()->name, 0, 2) }}
                                    </div>
                                </div>
                                <div class="hidden sm:block text-right">
                                    <div class="text-xs font-bold text-white font-cairo leading-none truncate max-w-[120px]">{{ Auth::user()->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-cairo mt-0.5">عميل معتمد</div>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="userMenuOpen"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                 class="absolute left-0 mt-2 w-48 rounded-xl bg-obsidian-surface/95 backdrop-blur-xl border border-obsidian-border shadow-2xl py-1.5 z-50 font-cairo"
                                 style="display: none;">
                                <div class="px-3.5 py-2 border-b border-obsidian-border text-xs">
                                    <p class="text-white font-bold truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('portal.index') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-200 hover:text-cyber-cyan hover:bg-white/5 transition-colors">
                                    <svg class="w-4 h-4 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>لوحة حسابي</span>
                                </a>
                                <a href="{{ route('portal.erp_store') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-200 hover:text-cyber-cyan hover:bg-white/5 transition-colors">
                                    <span>🏪</span>
                                    <span>منظومتي في BrandERP</span>
                                </a>
                                <a href="{{ route('portal.hardware') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-200 hover:text-cyber-cyan hover:bg-white/5 transition-colors">
                                    <span>🖥️</span>
                                    <span>أوردرات الهاردوير</span>
                                </a>
                                <a href="{{ route('portal.wallet') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-200 hover:text-cyber-cyan hover:bg-white/5 transition-colors">
                                    <svg class="w-4 h-4 text-cyber-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    <span>شحن المحفظة</span>
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="border-t border-obsidian-border mt-1 pt-1">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-3.5 py-2 text-xs text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-colors text-right cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        <span>تسجيل الخروج</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endguest
            </div>

        </div>
    </header>

    <!-- Main Operational Content Viewport -->
    <main class="w-full flex-1 pt-24 pb-12 px-4 sm:px-6 lg:px-8 max-w-[1920px] mx-auto">
        @yield('content')
    </main>

    <!-- Command Center Status Ribbon -->
    <footer class="w-full glass-obsidian border-t border-obsidian-border/80 px-6 py-2.5 text-xs text-slate-400">
        <div class="max-w-[1920px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 font-mono">
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 group">
                    <x-souly-logo size="xs" :showText="false" />
                    <span class="text-white font-black font-cairo text-sm">سولي</span>
                </a>
                <span class="text-obsidian-border">|</span>
                <span class="text-slate-400 font-cairo">&copy; {{ date('Y') }} كافة الحقوق محفوظة</span>
                <span class="hidden md:inline text-obsidian-border">|</span>
                <span class="hidden md:inline text-slate-400 font-cairo">منظومة برمجية سحابية متطورة</span>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-cyber-gold">المنطقة الزمنية: Africa/Cairo</span>
                <span class="text-obsidian-border">|</span>
                <span class="text-cyber-purple">محرك التخزين: SoulyDB</span>
                <span class="text-obsidian-border">|</span>
                <span class="text-slate-300">الاتجاه: RTL (العربية)</span>
            </div>
        </div>
    </footer>
</div>
@endsection