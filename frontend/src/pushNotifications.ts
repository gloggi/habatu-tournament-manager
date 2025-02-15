import { useApi } from "@/api";

const { createData: createSubscription } = useApi<PushSubscription>("messages/subscribe");

export async function requestNotificationPermission() {
    const permission = await Notification.requestPermission();
    if (permission === "granted") {
      console.log("Notification permission granted.");
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
      console.log("Already subscribed:", existingSubscription);
      return existingSubscription;
    }
  
    const subscription = await registration.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array("BOk8LrIuyRfp-K8kQ43eY5ve9KOakfjAYlnv3hf7Rfv2bKILvzhVi0FqjyoqbwmJo0vFKaNANBSLrWd9Jzagioc"),
    });

    console.log("Subscribing to push notifications...");

    console.log(subscription);
  
    console.log("Push Subscription:", JSON.stringify(subscription));
  
    // Send this subscription to the backend
    await createSubscription(subscription);
  
    return subscription;
  }
  
  function urlBase64ToUint8Array(base64String: string) {
    const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding)
      .replace(/-/g, "+")
      .replace(/_/g, "/");
  
    const rawData = window.atob(base64);
    return new Uint8Array([...rawData].map((char) => char.charCodeAt(0)));
  }