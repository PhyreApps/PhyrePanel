import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({

  locales: {
    root: {
      label: 'English',
      lang: 'en'
    },
    bg: {
      label: 'Български',
      lang: 'bg',
    }
  },

  lang: 'en-US',
  title: "Phyre Panel",
  description: "Phyre Panel - Documentation",
  themeConfig: {

    search: {
      provider: 'local'
    },

    logo: 'phyre-logo-icon.svg',
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Examples', link: '/markdown-examples' }
    ],

    sidebar: [
      {
        text: 'Examples',
        items: [
          { text: 'Markdown Examples', link: '/markdown-examples' },
          { text: 'Runtime API Examples', link: '/api-examples' }
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/vuejs/vitepress' }
    ],

    footer: {
      message: 'Released under the GNU License.',
      copyright: 'Copyright © 2024 Cloud Vision Ltd.'
    }

  }
})
