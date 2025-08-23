const ALGORITHM = "AES-GCM";
let SECRET_KEY;

const get_secret_key = async () => {
  if (typeof window !== "undefined") {
    const response = await fetch("/auth-api/auth-secret-key");
    const data = await response.json();
    SECRET_KEY = data.auth_secret_key;
  } else {
    SECRET_KEY = process.env.AUTH_SECRET_KEY;
  }
};

export function validateSessionData(data) {
  // Check that the data is an object
  if (typeof data !== "object" || data === null) {
    return null;
  }

  // Validate user_id (should be a non-empty string or number)
  if (typeof data.user_id !== "string" && typeof data.user_id !== "number") {
    return null;
  }
  data.user_id = String(data.user_id).trim(); // Convert to string and remove extra spaces

  // Validate user_role (should be a non-empty string)
  if (typeof data.user_role !== "string" || data.user_role.trim() === "") {
    return null;
  }
  data.user_role = data.user_role.trim();

  // Validate access_token (should be a non-empty string)
  if (
    typeof data.token !== "string" ||
    data.token.trim() === ""
  ) {
    return null;
  }
  data.token = data.token.trim();

  if (
    typeof data.token_expiry !== "string" ||
    data.token_expiry.trim() === ""
  ) {
    return null;
  }
  data.token_expiry = data.token_expiry.trim();

  // Return sanitized and valid data
  return {
    user_id: data.user_id,
    user_role: data.user_role,
    token: data.token,
    token_expiry: data.token_expiry,
  };
}

/**
 * Encrypts the session data using Web Crypto API
 * @param {Object} data - The session data to encrypt
 * @returns {Promise<string>} - Encrypted data in base64 format
 */
export async function encrypt(data) {
  if (!SECRET_KEY) {
    await get_secret_key();
    if (!SECRET_KEY || SECRET_KEY.length !== 64) {
      throw new Error(
        "Invalid SECRET_KEY. Ensure it is a 64-character hex string.",
      );
    }
  }

  // Convert SECRET_KEY to ArrayBuffer
  const keyBuffer = Uint8Array.from(Buffer.from(SECRET_KEY, "hex"));

  // Generate a random IV
  const iv = crypto.getRandomValues(new Uint8Array(12)); // 12 bytes IV for AES-GCM (recommended)

  // Import the encryption key
  const cryptoKey = await crypto.subtle.importKey(
    "raw",
    keyBuffer,
    { name: ALGORITHM },
    false,
    ["encrypt"],
  );

  // Encode the data into a byte array
  const jsonData = new TextEncoder().encode(JSON.stringify(data));

  // Encrypt the data
  const encryptedBuffer = await crypto.subtle.encrypt(
    { name: ALGORITHM, iv },
    cryptoKey,
    jsonData,
  );

  // Combine IV and encrypted data
  const encryptedData = Buffer.concat([
    Buffer.from(iv),
    Buffer.from(encryptedBuffer),
  ]);

  // Return as base64 string
  return encryptedData.toString("base64");
}

/**
 * Decrypts the encrypted session data using Web Crypto API
 * @param {string} encryptedData - The encrypted data in base64 format
 * @returns {Promise<Object>} - The decrypted session data
 */
export async function decrypt(encryptedData) {
  if (!SECRET_KEY) {
    await get_secret_key();
    if (!SECRET_KEY || SECRET_KEY.length !== 64) {
      throw new Error(
        "Invalid SECRET_KEY. Ensure it is a 64-character hex string.",
      );
    }
  }

  // Convert SECRET_KEY to ArrayBuffer
  const keyBuffer = Uint8Array.from(Buffer.from(SECRET_KEY, "hex"));

  // Decode base64 encrypted data
  const encryptedBuffer = Buffer.from(encryptedData, "base64");

  // Extract IV (first 12 bytes) and actual encrypted data
  const iv = encryptedBuffer.subarray(0, 12);
  const dataBuffer = encryptedBuffer.subarray(12);

  // Import the decryption key
  const cryptoKey = await crypto.subtle.importKey(
    "raw",
    keyBuffer,
    { name: ALGORITHM },
    false,
    ["decrypt"],
  );

  // Decrypt the data
  const decryptedBuffer = await crypto.subtle.decrypt(
    { name: ALGORITHM, iv },
    cryptoKey,
    dataBuffer,
  );

  // Decode the decrypted data
  const decryptedText = new TextDecoder().decode(decryptedBuffer);

  // Parse JSON and return
  return JSON.parse(decryptedText);
}
