import { precacheAndRoute } from "workbox-precaching";

precacheAndRoute(self.__WB_MANIFEST || []);

self.addEventListener("beforeinstallprompt", (event) => {
  event.preventDefault();
  event.prompt();
});

self.addEventListener("push", function (event) {
  console.log("[Service Worker] Push Received.");

  if (event.data) {
    let options;
    try {
      options = event.data.json(); // Preferred method
      console.log(options);
    } catch (error) {
      console.warn(
        "Error parsing push data as JSON, falling back to text:",
        error,
      );
      options = { body: event.data.text() }; // Safari fallback
    }

    event.waitUntil(self.registration.showNotification(options.title, options));
  } else {
    console.log("[Service Worker] Push event but no data.");
  }
});

self.addEventListener("notificationclick", function (event) {
  event.notification.close();

  event.waitUntil(clients.openWindow("/"));
});
