import { registerSW } from 'virtual:pwa-register'

const updateSW = registerSW({
  onNeedRefresh() {
    console.log("New service worker detected.");

    if (!localStorage.getItem("swUpdated")) {
      localStorage.setItem("swUpdated", "true");

      // Show a prompt to reload
      if (confirm("New version available! Reload now?")) {
        updateSW(true);
      }
    }
  },
  onOfflineReady() {
    console.log("App is ready to work offline.");
  },
});
