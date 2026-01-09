import axios from "axios";

/**
 * Axios NORMAL (sem /api) → para Sanctum
 */
const sanctum = axios.create({
    withCredentials: true,
    headers: {
        "X-Requested-With": "XMLHttpRequest",
    },
});

/**
 * Axios da API → TODAS as chamadas /api
 */
const api = axios.create({
    baseURL: "/api",
    withCredentials: true,
    headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
    },
});

/**
 * Inicializa Sanctum corretamente
 */
export const initSanctum = async () => {
    await sanctum.get("/sanctum/csrf-cookie");
};

export default api;
