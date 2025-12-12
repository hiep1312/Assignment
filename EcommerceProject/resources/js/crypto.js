const encoder = new TextEncoder();
const decoder = new TextDecoder('utf-8', { fatal: false, ignoreBOM: false });
const rawKey = import.meta.env.VITE_CRYPTO_KEY;
const rawIV = import.meta.env.VITE_CRYPTO_IV;

const keyBytes = encoder.encode(rawKey);
const ivBytes = encoder.encode(rawIV);

async function getCryptoKey() {
    return crypto.subtle.importKey(
        'raw',
        keyBytes,
        { name: 'AES-GCM' },
        false,
        ['encrypt', 'decrypt']
    );
}

Object.defineProperties(window, {
    encrypt: {
        configurable: false,
        enumerable: false,
        value: async function(data) {
            const cryptoKey = await getCryptoKey();
            const encryptedBuffer = await crypto.subtle.encrypt(
                { name: 'AES-GCM', iv: ivBytes },
                cryptoKey,
                (data instanceof Uint8Array || data instanceof ArrayBuffer) ? data : encoder.encode(String(data))
            );

            return new Uint8Array(encryptedBuffer).toBase64();
        },
        writable: false
    },

    decrypt: {
        configurable: false,
        enumerable: false,
        value: async function(cipherData) {
            const cryptoKey = await getCryptoKey();
            const decryptedBuffer = await crypto.subtle.decrypt(
                { name: 'AES-GCM', iv: encoder.encode(rawIV) },
                cryptoKey,
                (cipherData instanceof Uint8Array || cipherData instanceof ArrayBuffer) ? cipherData : Uint8Array.fromBase64(String(cipherData))
            );

            return decoder.decode(decryptedBuffer);
        },
        writable: false
    },
});

window.encrypt('hello world').then(result => window.decrypt(result)).then(result => console.log(result));
