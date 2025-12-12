function getCryptoKey(): Promise<CryptoKey>;

export function encryptData(
    data: string | Uint8Array | ArrayBuffer
): Promise<string>;

export function decryptData(
    cipherData: string | Uint8Array | ArrayBuffer
): Promise<string>;
