import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
import {
    getMessaging,
    getToken,
    isSupported as isFcmSupported,
} from "https://www.gstatic.com/firebasejs/11.6.1/firebase-messaging.js";

const firebaseConfig = {
    apiKey: "AIzaSyCY0fAuyQ2CIvvkbEXNzxeXDi1PJeGQkPs",
    authDomain: "notif-perpustakaan.firebaseapp.com",
    projectId: "notif-perpustakaan",
    storageBucket: "notif-perpustakaan.firebasestorage.app",
    messagingSenderId: "760079496210",
    appId: "1:760079496210:web:3bd91c937c07c24ed214cb",
};

let app;
let messaging;
let isFcmInitialized = false;
const vapidKey =
    "BAcP6XZ9UX1xRzUF2P4i13f6jZWHGjFuHn8OhN_JDPsQocnYb_e4cgJHARaWk4kxpY6jH7-yqIzPR1VwjT1bMfw";

let fcmButtonContainer = null;
let enableFcmButton = null;
let fcmButtonTextSpan = null;
let fcmButtonIcon = null;

try {
    app = initializeApp(firebaseConfig);
    console.log("[FCM] Firebase App Initialized (Module).");

    initializeFcm();
} catch (e) {
    console.error("[FCM] Firebase Initialization Error:", e);
    disableFcmFeatures(); // Tetap disable jika init awal gagal
}

async function initializeFcm() {
    try {
        const isSupported = await isFcmSupported();
        fcmButtonContainer = document.getElementById("fcm-button-container");
        enableFcmButton = document.getElementById("enable-fcm-button");

        if (!enableFcmButton || !fcmButtonContainer) {
            console.warn(
                "[FCM] Button element (#enable-fcm-button or #fcm-button-container) not found."
            );
            return;
        }
        fcmButtonTextSpan = enableFcmButton.querySelector("span");
        fcmButtonIcon = enableFcmButton.querySelector("i");

        if (isSupported) {
            messaging = getMessaging(app);
            console.log("[FCM] Firebase Messaging Initialized (Module).");
            isFcmInitialized = true;
            checkAndHandlePermission();
        } else {
            console.warn(
                "[FCM] Push messaging is not supported in this browser."
            );
            disableFcmFeatures("Notifikasi Tidak Didukung Browser Ini", true);
            // Sembunyikan tombol jika tidak didukung
            if (fcmButtonContainer) {
                fcmButtonContainer.style.display = "none";
            }
        }
    } catch (err) {
        console.error(
            "[FCM] Error checking FCM support or initializing messaging:",
            err
        );
        disableFcmFeatures("Error Cek Notifikasi", true);
        if (fcmButtonContainer) {
            fcmButtonContainer.style.display = "none";
        }
    }
}

function checkAndHandlePermission() {
    if (
        !isFcmInitialized ||
        !messaging ||
        !enableFcmButton ||
        !fcmButtonContainer ||
        !fcmButtonTextSpan ||
        !fcmButtonIcon
    ) {
        console.warn(
            "[FCM] Cannot check permission, FCM not ready or button elements not found."
        );
        return;
    }

    const currentPermission = Notification.permission;
    console.log("[FCM] Current permission status:", currentPermission);

    if (currentPermission === "granted") {
        console.log(
            "[FCM] Permission already granted. Hiding button and getting token."
        );
        fcmButtonContainer.style.display = "none";
        getAndSendFcmToken();
    } else {
        fcmButtonContainer.style.display = "list-item";

        if (currentPermission === "denied") {
            console.log("[FCM] Permission denied by user.");
            setButtonState(
                true, // disabled
                "Notifikasi Diblokir",
                "Anda telah memblokir izin notifikasi. Aktifkan di pengaturan browser.",
                "bi-bell-slash-fill",
                "btn-outline-danger"
            );
            enableFcmButton.removeEventListener(
                "click",
                requestNotificationPermission
            );
        } else {
            console.log(
                "[FCM] Permission default. Button enabled for request."
            );
            setButtonState(
                false, // enabled
                "Aktifkan Notifikasi Browser",
                "Klik untuk mengizinkan notifikasi dari SIMPerpus.",
                "bi-bell",
                "btn-outline-info"
            );
            enableFcmButton.removeEventListener(
                "click",
                requestNotificationPermission
            );
            enableFcmButton.addEventListener(
                "click",
                requestNotificationPermission
            );
        }
    }
}

function setButtonState(disabled, text, title, iconClass, buttonClass) {
    if (!enableFcmButton || !fcmButtonTextSpan || !fcmButtonIcon) return;

    enableFcmButton.disabled = disabled;
    fcmButtonTextSpan.textContent = text;
    enableFcmButton.title = title;

    fcmButtonIcon.className = `me-1 ${iconClass}`;

    enableFcmButton.classList.remove(
        "btn-outline-info",
        "btn-outline-danger",
        "btn-outline-success"
    );
    if (buttonClass) {
        enableFcmButton.classList.add(buttonClass);
    }
}

function disableFcmFeatures(
    message = "Notifikasi Tidak Didukung",
    hideContainer = false
) {
    console.log(`[FCM] Disabling FCM features: ${message}`);
    if (enableFcmButton) {
        setButtonState(
            true,
            message,
            "Fitur notifikasi tidak tersedia.",
            "bi-bell-slash-fill",
            "btn-outline-secondary"
        );
    }
    if (hideContainer && fcmButtonContainer) {
        fcmButtonContainer.style.display = "none";
    }
}

function requestNotificationPermission() {
    if (!isFcmInitialized || !messaging) {
        console.error("[FCM] Cannot request permission, FCM not initialized.");
        alert("Fitur notifikasi tidak didukung atau gagal diinisialisasi.");
        return;
    }
    console.log("[FCM] Requesting notification permission...");
    setButtonState(
        true,
        "Meminta Izin...",
        "Menunggu respon Anda...",
        "bi-hourglass-split",
        "btn-outline-secondary"
    );

    Notification.requestPermission()
        .then((permission) => {
            console.log("[FCM] Permission request result:", permission);
            if (permission === "granted") {
                console.log("[FCM] Notification permission granted.");
                if (fcmButtonContainer) {
                    fcmButtonContainer.style.display = "none";
                }
                getAndSendFcmToken();
            } else if (permission === "denied") {
                console.log("[FCM] User denied notification permission.");
                alert(
                    "Anda memilih untuk tidak mengizinkan notifikasi. Anda bisa mengaktifkannya nanti di pengaturan browser."
                );
                setButtonState(
                    true,
                    "Notifikasi Diblokir",
                    "Izin notifikasi ditolak. Aktifkan di pengaturan browser.",
                    "bi-bell-slash-fill",
                    "btn-outline-danger"
                );
            } else {
                console.log("[FCM] Permission request dismissed or ignored.");
                alert("Anda belum memberikan izin notifikasi.");
                setButtonState(
                    false,
                    "Aktifkan Notifikasi Browser",
                    "Klik untuk mengizinkan notifikasi dari SIMPerpus.",
                    "bi-bell",
                    "btn-outline-info"
                );
            }
        })
        .catch((err) => {
            console.error("[FCM] Error requesting permission:", err);
            alert(
                "Gagal meminta izin notifikasi. Silakan coba lagi atau cek pengaturan browser."
            );
            setButtonState(
                false,
                "Aktifkan Notifikasi Browser",
                "Klik untuk mengizinkan notifikasi.",
                "bi-bell",
                "btn-outline-info"
            );
        });
}

async function getAndSendFcmToken() {
    if (!isFcmInitialized || !messaging) {
        console.error(
            "[FCM] Cannot get token, messaging service not available."
        );
        return;
    }

    try {
        console.log("[FCM] Attempting to get token with VAPID key...");
        const currentToken = await getToken(messaging, { vapidKey: vapidKey });
        if (currentToken) {
            console.log("[FCM] Token obtained:", currentToken);
            sendTokenToServer(currentToken);
            if (fcmButtonContainer) {
                fcmButtonContainer.style.display = "none";
            }
        } else {
            console.warn(
                "[FCM] No registration token available. Permission might be revoked or SW issue."
            );
            if (Notification.permission === "granted") {
                console.warn(
                    "[FCM] Permission is granted but no token. Re-showing button."
                );
                if (fcmButtonContainer) {
                    fcmButtonContainer.style.display = "list-item";
                }
                setButtonState(
                    false,
                    "Dapatkan Ulang Token",
                    "Gagal mendapatkan token, coba lagi.",
                    "bi-arrow-clockwise",
                    "btn-outline-warning"
                );
                enableFcmButton.removeEventListener(
                    "click",
                    requestNotificationPermission
                );
                enableFcmButton.addEventListener(
                    "click",
                    requestNotificationPermission
                );
            } else {
                checkAndHandlePermission();
            }
        }
    } catch (err) {
        console.error("[FCM] An error occurred while retrieving token: ", err);
        let errorMessage = "Gagal mendapatkan token notifikasi. ";
        let showAlert = true;

        if (
            err.code === "messaging/notifications-blocked" ||
            err.code === "messaging/permission-blocked"
        ) {
            errorMessage +=
                "Izin notifikasi diblokir. Aktifkan di pengaturan browser Anda.";
            setButtonState(
                true,
                "Notifikasi Diblokir",
                errorMessage,
                "bi-bell-slash-fill",
                "btn-outline-danger"
            );
            if (fcmButtonContainer)
                fcmButtonContainer.style.display = "list-item";
        } else if (
            err.code === "messaging/failed-service-worker-registration"
        ) {
            errorMessage +=
                "Gagal mendaftarkan service worker. Pastikan file firebase-messaging-sw.js ada dan dapat diakses.";
            disableFcmFeatures("Error Service Worker", false);
            if (fcmButtonContainer)
                fcmButtonContainer.style.display = "list-item";
        } else if (err.code === "messaging/permission-default") {
            errorMessage += "Anda belum memberikan izin notifikasi.";
            showAlert = false;
            checkAndHandlePermission();
        } else if (err.code === "messaging/token-subscribe-failed") {
            errorMessage +=
                "Gagal mendaftarkan token ke server Firebase. Cek koneksi internet atau coba lagi nanti.";
            setButtonState(
                false,
                "Gagal Daftar Token",
                errorMessage,
                "bi-exclamation-triangle",
                "btn-outline-warning"
            );
            if (fcmButtonContainer)
                fcmButtonContainer.style.display = "list-item";
        } else {
            errorMessage += `Terjadi kesalahan (${err.code || "unknown"}).`;
            disableFcmFeatures("Error Token", false);
            if (fcmButtonContainer)
                fcmButtonContainer.style.display = "list-item";
        }
        console.error(errorMessage);
        if (showAlert) alert(errorMessage);
    }
}

function sendTokenToServer(token) {
    if (typeof window.fcmTokenStoreUrl === "undefined") {
        console.error(
            "[FCM] Backend URL (window.fcmTokenStoreUrl) is not defined."
        );
        setButtonState(
            false,
            "Error Konfigurasi",
            "URL Backend tidak ditemukan.",
            "bi-exclamation-triangle",
            "btn-outline-warning"
        );
        if (fcmButtonContainer) fcmButtonContainer.style.display = "list-item";
        return;
    }
    const url = window.fcmTokenStoreUrl;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!csrfToken) {
        console.error("[FCM] CSRF token not found.");
        setButtonState(
            false,
            "Error Keamanan",
            "Token CSRF tidak ditemukan.",
            "bi-exclamation-triangle",
            "btn-outline-warning"
        );
        if (fcmButtonContainer) fcmButtonContainer.style.display = "list-item";
        return;
    }

    console.log(`[FCM] Sending token to: ${url}`);
    fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
        body: JSON.stringify({ fcm_token: token }),
    })
        .then(async (response) => {
            if (!response.ok) {
                let errorData = {
                    message: `Server responded with status ${response.status}`,
                };
                try {
                    // Coba parse error dari JSON response server
                    errorData = await response.json();
                } catch (e) {
                    console.warn(
                        "[FCM] Could not parse error response from server."
                    );
                }
                throw new Error(
                    errorData.message ||
                        `Server responded with status ${response.status}`
                );
            }
            return response.json();
        })
        .then((data) => {
            console.log(
                "[FCM] Token successfully sent to server:",
                data.message || "Success"
            );
            if (fcmButtonContainer) {
                fcmButtonContainer.style.display = "none";
            }
        })
        .catch((error) => {
            console.error("[FCM] Error sending token to server:", error);
            alert(
                `Gagal menyimpan token notifikasi ke server: ${error.message}`
            );
            setButtonState(
                false,
                "Gagal Simpan Token",
                `Gagal mengirim token ke server. ${error.message}. Coba lagi?`,
                "bi-arrow-clockwise",
                "btn-outline-warning"
            );
            if (fcmButtonContainer)
                fcmButtonContainer.style.display = "list-item";
            enableFcmButton.removeEventListener("click", getAndSendFcmToken);
            enableFcmButton.addEventListener("click", getAndSendFcmToken);
        });
}

// Hapus listener DOMContentLoaded yang lama, karena inisialisasi dipanggil dari dalam try/catch utama
// document.addEventListener("DOMContentLoaded", () => { ... });
// Logika pengecekan awal sekarang ada di dalam initializeFcm -> checkAndHandlePermission

// Listener untuk pesan foreground (opsional, uncomment jika diperlukan)
// import { onMessage } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-messaging.js";
// ... (setelah isFcmInitialized = true)
// if (isFcmInitialized && messaging) {
//   onMessage(messaging, (payload) => {
//     console.log("[FCM] Message received while app is foregrounded: ", payload);
//     // Tampilkan notifikasi custom di dalam halaman, bukan alert
//     // Contoh: gunakan library toast/snackbar
//     // showCustomNotification(payload.notification?.title, payload.notification?.body);
//     alert(
//        `Notifikasi Baru: ${payload.notification?.title}\n${payload.notification?.body}`
//     );
//   });
// }

// Helper function contoh untuk notifikasi foreground (ganti dengan implementasi Anda)
// function showCustomNotification(title, body) {
//     // Implementasi menampilkan notifikasi di halaman (misal: pojok kanan atas)
//     console.log(`[Foreground Notification] Title: ${title}, Body: ${body}`);
//     const notificationElement = document.createElement('div');
//     // Style dan tambahkan ke DOM
// }
