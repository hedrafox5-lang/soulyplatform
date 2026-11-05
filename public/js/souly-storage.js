/**
 * Souly Digital Citadel - Bulletproof Storage Engine (SoulyDB) v2.5
 * IndexedDB Wrapper with L1 0ms Memory Cache & Graceful Multi-Tier Fallback (IndexedDB -> LocalStorage -> In-Memory)
 * Engineered for High-Availability in Mobile WebViews (FB/IG), Safari iOS & Private Modes
 */
(function (global) {
    'use strict';

    class SoulyMemoryDriver {
        constructor() {
            this.store = new Map();
        }

        async get(key) {
            return this.store.has(key) ? this.store.get(key) : null;
        }

        async set(key, value) {
            this.store.set(key, value);
            return value;
        }

        async setMany(entries) {
            if (!Array.isArray(entries)) return false;
            for (const [k, v] of entries) {
                this.store.set(k, v);
            }
            return true;
        }

        async delete(key) {
            return this.store.delete(key);
        }

        async clear() {
            this.store.clear();
            return true;
        }

        async keys() {
            return Array.from(this.store.keys());
        }

        async has(key) {
            return this.store.has(key);
        }
    }

    class SoulyLocalStorageDriver {
        constructor(prefix = 'souly_') {
            this.prefix = prefix;
        }

        _k(key) {
            return `${this.prefix}${key}`;
        }

        async get(key) {
            try {
                const item = localStorage.getItem(this._k(key));
                if (item === null) return null;
                return JSON.parse(item);
            } catch (e) {
                return null;
            }
        }

        async set(key, value) {
            try {
                localStorage.setItem(this._k(key), JSON.stringify(value));
                return value;
            } catch (e) {
                throw e;
            }
        }

        async setMany(entries) {
            if (!Array.isArray(entries)) return false;
            for (const [k, v] of entries) {
                try {
                    localStorage.setItem(this._k(k), JSON.stringify(v));
                } catch (e) {
                    throw e;
                }
            }
            return true;
        }

        async delete(key) {
            try {
                localStorage.removeItem(this._k(key));
                return true;
            } catch (e) {
                return false;
            }
        }

        async clear() {
            try {
                const keysToRemove = [];
                for (let i = 0; i < localStorage.length; i++) {
                    const k = localStorage.key(i);
                    if (k && k.startsWith(this.prefix)) {
                        keysToRemove.push(k);
                    }
                }
                keysToRemove.forEach(k => localStorage.removeItem(k));
                return true;
            } catch (e) {
                return false;
            }
        }

        async keys() {
            try {
                const results = [];
                for (let i = 0; i < localStorage.length; i++) {
                    const k = localStorage.key(i);
                    if (k && k.startsWith(this.prefix)) {
                        results.push(k.slice(this.prefix.length));
                    }
                }
                return results;
            } catch (e) {
                return [];
            }
        }

        async has(key) {
            try {
                return localStorage.getItem(this._k(key)) !== null;
            } catch (e) {
                return false;
            }
        }
    }

    class SoulyIndexedDBDriver {
        constructor(dbName = 'SoulyCitadelDB', storeName = 'citadel_kv', version = 1) {
            this.dbName = dbName;
            this.storeName = storeName;
            this.version = version;
            this.db = null;
            this.initPromise = null;
        }

        async init() {
            if (this.db) return this.db;
            if (this.initPromise) return this.initPromise;

            this.initPromise = new Promise((resolve, reject) => {
                if (typeof window === 'undefined' || !window.indexedDB) {
                    return reject(new Error('IndexedDB unsupported in current environment'));
                }

                // 1500ms Watchdog Timeout for Sandboxed Mobile WebViews
                const timeoutId = setTimeout(() => {
                    reject(new Error('IndexedDB initialization timed out in WebView'));
                }, 1500);

                try {
                    const request = window.indexedDB.open(this.dbName, this.version);

                    request.onupgradeneeded = (event) => {
                        const db = event.target.result;
                        if (!db.objectStoreNames.contains(this.storeName)) {
                            db.createObjectStore(this.storeName);
                        }
                    };

                    request.onsuccess = (event) => {
                        clearTimeout(timeoutId);
                        this.db = event.target.result;
                        this.db.onversionchange = () => {
                            this.db.close();
                            this.db = null;
                            this.initPromise = null;
                        };
                        resolve(this.db);
                    };

                    request.onerror = (event) => {
                        clearTimeout(timeoutId);
                        reject(event.target.error || new Error('Failed to open IndexedDB'));
                    };

                    request.onblocked = () => {
                        clearTimeout(timeoutId);
                        console.warn('[SoulyDB] Database open blocked by another tab');
                        reject(new Error('IndexedDB blocked'));
                    };
                } catch (err) {
                    clearTimeout(timeoutId);
                    reject(err);
                }
            });

            return this.initPromise;
        }

        async _getStore(mode = 'readonly') {
            const db = await this.init();
            const tx = db.transaction(this.storeName, mode);
            return tx.objectStore(this.storeName);
        }

        async get(key) {
            const store = await this._getStore('readonly');
            return new Promise((resolve, reject) => {
                const request = store.get(key);
                request.onsuccess = () => resolve(request.result !== undefined ? request.result : null);
                request.onerror = () => reject(request.error);
            });
        }

        async set(key, value) {
            const store = await this._getStore('readwrite');
            return new Promise((resolve, reject) => {
                const request = store.put(value, key);
                request.onsuccess = () => resolve(value);
                request.onerror = () => reject(request.error);
            });
        }

        async setMany(entries) {
            if (!Array.isArray(entries) || entries.length === 0) return true;
            const db = await this.init();
            return new Promise((resolve, reject) => {
                try {
                    const tx = db.transaction(this.storeName, 'readwrite');
                    const store = tx.objectStore(this.storeName);
                    for (const [k, v] of entries) {
                        store.put(v, k);
                    }
                    tx.oncomplete = () => resolve(true);
                    tx.onerror = () => reject(tx.error);
                } catch (err) {
                    reject(err);
                }
            });
        }

        async delete(key) {
            const store = await this._getStore('readwrite');
            return new Promise((resolve, reject) => {
                const request = store.delete(key);
                request.onsuccess = () => resolve(true);
                request.onerror = () => reject(request.error);
            });
        }

        async clear() {
            const store = await this._getStore('readwrite');
            return new Promise((resolve, reject) => {
                const request = store.clear();
                request.onsuccess = () => resolve(true);
                request.onerror = () => reject(request.error);
            });
        }

        async keys() {
            const store = await this._getStore('readonly');
            return new Promise((resolve, reject) => {
                if (typeof store.getAllKeys === 'function') {
                    const req = store.getAllKeys();
                    req.onsuccess = () => resolve(req.result || []);
                    req.onerror = () => reject(req.error);
                } else {
                    const keys = [];
                    const req = store.openKeyCursor();
                    req.onsuccess = (e) => {
                        const cursor = e.target.result;
                        if (cursor) {
                            keys.push(cursor.key);
                            cursor.continue();
                        } else {
                            resolve(keys);
                        }
                    };
                    req.onerror = () => reject(req.error);
                }
            });
        }

        async has(key) {
            const val = await this.get(key);
            return val !== null;
        }
    }

    class SoulyDBManager {
        constructor() {
            this.driverType = 'unknown';
            this.activeDriver = null;
            this.l1Cache = new Map(); // 0ms Instant L1 RAM Cache
            this.readyPromise = this._bootstrap();
        }

        async _bootstrap() {
            // Attempt Tier 1: IndexedDB
            try {
                const idb = new SoulyIndexedDBDriver();
                await idb.init();
                const testKey = '__souly_probe__';
                await idb.set(testKey, 1);
                await idb.delete(testKey);

                this.activeDriver = idb;
                this.driverType = 'indexeddb';
                return this.activeDriver;
            } catch (idbErr) {
                console.warn('[SoulyDB] IndexedDB unavailable or blocked:', idbErr.message || idbErr);
            }

            // Attempt Tier 2: LocalStorage
            try {
                if (typeof window !== 'undefined' && window.localStorage) {
                    const ls = new SoulyLocalStorageDriver();
                    const testKey = '__souly_probe__';
                    await ls.set(testKey, 1);
                    await ls.delete(testKey);

                    this.activeDriver = ls;
                    this.driverType = 'localstorage';
                    return this.activeDriver;
                }
            } catch (lsErr) {
                console.warn('[SoulyDB] LocalStorage restricted or quota exceeded:', lsErr.message || lsErr);
            }

            // Attempt Tier 3: In-Memory Map (Always Succeeds)
            this.activeDriver = new SoulyMemoryDriver();
            this.driverType = 'memory';
            return this.activeDriver;
        }

        async getDriver() {
            if (!this.activeDriver) {
                await this.readyPromise;
            }
            return this.activeDriver;
        }

        getSync(key, defaultValue = null) {
            return this.l1Cache.has(key) ? this.l1Cache.get(key) : defaultValue;
        }

        async get(key, defaultValue = null) {
            if (this.l1Cache.has(key)) {
                return this.l1Cache.get(key);
            }
            try {
                const driver = await this.getDriver();
                const res = await driver.get(key);
                if (res !== null && res !== undefined) {
                    this.l1Cache.set(key, res);
                    return res;
                }
                return defaultValue;
            } catch (e) {
                return defaultValue;
            }
        }

        async set(key, value) {
            this.l1Cache.set(key, value);
            try {
                const driver = await this.getDriver();
                return await driver.set(key, value);
            } catch (e) {
                if (this.driverType !== 'memory') {
                    this.activeDriver = new SoulyMemoryDriver();
                    this.driverType = 'memory';
                    return await this.activeDriver.set(key, value);
                }
                return value;
            }
        }

        async setMany(entries) {
            if (!Array.isArray(entries) || entries.length === 0) return true;
            for (const [k, v] of entries) {
                this.l1Cache.set(k, v);
            }
            try {
                const driver = await this.getDriver();
                if (typeof driver.setMany === 'function') {
                    return await driver.setMany(entries);
                }
                for (const [k, v] of entries) {
                    await driver.set(k, v);
                }
                return true;
            } catch (e) {
                return false;
            }
        }

        async remove(key) {
            return this.delete(key);
        }

        async delete(key) {
            this.l1Cache.delete(key);
            try {
                const driver = await this.getDriver();
                return await driver.delete(key);
            } catch (e) {
                return false;
            }
        }

        async clear() {
            this.l1Cache.clear();
            try {
                const driver = await this.getDriver();
                return await driver.clear();
            } catch (e) {
                return false;
            }
        }

        async keys() {
            try {
                const driver = await this.getDriver();
                return await driver.keys();
            } catch (e) {
                return Array.from(this.l1Cache.keys());
            }
        }

        async has(key) {
            if (this.l1Cache.has(key)) return true;
            try {
                const driver = await this.getDriver();
                return await driver.has(key);
            } catch (e) {
                return false;
            }
        }

        getEngine() {
            return this.driverType;
        }
    }

    const instance = new SoulyDBManager();

    if (typeof module !== 'undefined' && module.exports) {
        module.exports = instance;
    }
    global.SoulyDB = instance;

})(typeof window !== 'undefined' ? window : globalThis);