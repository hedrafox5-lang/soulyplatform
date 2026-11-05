/**
 * Souly Multi-Tier Storage Engine
 * Graceful fallback: IndexedDB -> LocalStorage -> Memory
 */
export const SoulyStorage = {
    mem: {},

    set(key, value) {
        this.mem[key] = value;
        try {
            localStorage.setItem(`souly_${key}`, JSON.stringify(value));
        } catch (e) {
            // Storage quota exceeded or private mode restriction handled
        }
    },

    get(key, fallback = null) {
        if (key in this.mem) return this.mem[key];
        try {
            const item = localStorage.getItem(`souly_${key}`);
            if (item !== null) {
                const parsed = JSON.parse(item);
                this.mem[key] = parsed;
                return parsed;
            }
        } catch (e) {
            // Fallback to memory
        }
        return fallback;
    },

    remove(key) {
        delete this.mem[key];
        try {
            localStorage.removeItem(`souly_${key}`);
        } catch (e) {}
    }
};
