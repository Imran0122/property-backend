// for simple fetch wrapper
export const API_BASE = process.env.NEXT_PUBLIC_API_BASE || "http://127.0.0.1:8000";

function getAuthHeaders() {
  if (typeof window === "undefined") return {};
  const token = localStorage.getItem("token");
  return token ? { Authorization: `Bearer ${token}` } : {};
}

export async function apiFetch(path, options = {}) {
  const headers = {
    Accept: "application/json",
    ...(options.body && !(options.body instanceof FormData) ? { "Content-Type": "application/json" } : {}),
    ...getAuthHeaders(),
    ...options.headers,
  };

  const res = await fetch(`${API_BASE}${path}`, {
    ...options,
    headers,
    credentials: options.credentials ?? "same-origin",
  });

  if (!res.ok) {
    const text = await res.text().catch(() => null);
    let json = null;
    try { json = JSON.parse(text || "{}"); } catch {}
    const err = new Error(json?.message || res.statusText || "Request failed");
    err.status = res.status;
    err.payload = json;
    throw err;
  }
  if (res.status === 204) return null;
  return res.json();
}
