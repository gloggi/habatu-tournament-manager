import { useApi } from "@/api";

const { createData: createSubscription } =
  useApi<PushSubscription>("messages/subscribe");

export async function requestNotificationPermission() {
  const permission = await Notification.requestPermission();
  if (permission === "granted") {
    return true;
  } else {
    console.error("Notification permission denied.");
    return false;
  }
}

export async function subscribeUserToPush() {
  if (!("serviceWorker" in navigator)) {
    console.error("Service Worker not supported.");
    return;
  }

  const registration = await navigator.serviceWorker.ready;

  const existingSubscription = await registration.pushManager.getSubscription();
  if (existingSubscription) {
    return existingSubscription;
  }

  const subscription = await registration.pushManager.subscribe({
    userVisibleOnly: true,
    applicationServerKey: urlBase64ToUint8Array(
      import.meta.env.VITE_VAPID_PUBLIC_KEY,
    ),
  });

  await createSubscription(subscription);

  return subscription;
}

function urlBase64ToUint8Array(base64String: string) {
  const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
  const base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/");

  const rawData = window.atob(base64);
  return new Uint8Array([...rawData].map((char) => char.charCodeAt(0)));
}
