import { ConvexClient } from "convex/browser";
import Alpine from 'alpinejs';

// Read the Convex URL from your .env file
const convexUrl = import.meta.env.VITE_CONVEX_URL;
const convex = new ConvexClient(convexUrl);

// Attach convex and Alpine to the global window object so Blade can use them
window.convex = convex;
window.Alpine = Alpine;

Alpine.start();