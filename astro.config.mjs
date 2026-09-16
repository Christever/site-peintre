import { defineConfig } from "astro/config";
import tailwindcss from "@tailwindcss/vite";
import { fileURLToPath } from "node:url";

import sitemap from "@astrojs/sitemap";

export default defineConfig({
  base: "/mspeinture",

  vite: {
      plugins: [tailwindcss()],
      resolve: {
          alias: {
              "@": fileURLToPath(new URL("./src", import.meta.url)),
          },
      },
  },

  integrations: [sitemap()],
});