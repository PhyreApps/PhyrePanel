import { defineConfig } from 'vitepress'
import { version } from '../package.json';

// https://vitepress.dev/reference/site-config
export default defineConfig({

  locales: {
    root: {
      label: 'English',
      lang: 'en'
    },
    // bg: {
    //   label: 'Български',
    //   lang: 'bg',
    // }
  },
  // base: 'https://docs.phyrepanel.com/',

  sitemap: {
    // hostname: 'https://docs.phyrepanel.com/',
    lastmodDateOnly: false
  },

  lang: 'en-US',
  title: "Phyre Panel",
  description: "Phyre Panel - Documentation",
  themeConfig: {

    search: {
      provider: 'local'
    },

    logo: '/phyre-logo-icon.svg',
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Install', link: '/install' },
      { text: 'Introduction', link: '/introduction/getting-started' },
      { text: 'Team', link: '/team' },

      {
        text: `v${version}`,
        items: [
          {
            text: 'Changelog',
            link: 'https://github.com/PhyreApps/PhyrePanel/blob/main/CHANGELOG.md',
          },
          {
            text: 'Contributing',
            link: 'https://github.com/PhyreApps/PhyrePanel/blob/main/CONTRIBUTING.md',
          },
        ]
        }
    ],

    sidebar: [
      {
        text: 'Introduction',
        items: [
          { text: 'Getting Started', link: '/introduction/getting-started' },
          { text: 'Installation', link: '/install' },
          { text: 'Requirements', link: '/introduction/requirements' },
          { text: 'Features', link: '/introduction/features' }
        ]
      },
      {
        text: 'Commands',
        items: [
          { text: 'System Commands', link: '/commands/system-commands' },
          { text: 'Installation Commands', link: '/commands/installation-commands' },
          { text: 'SSL & Domain Management', link: '/commands/ssl-domain-management' },
          { text: 'Backup Management', link: '/commands/backup-management' },
          { text: 'User Management', link: '/commands/user-management' },
          { text: 'System Configuration', link: '/commands/system-configuration' },
          { text: 'System Update', link: '/commands/system-update' },
        ]
      },
      {
        text: 'Integrations',
        items: [
          { text: 'WHMCS', link: '/integrations/whmcs' },
        ]
      },
      {
        text: 'Contributing',
        items: [
          { text: 'Documentation', link: '/contributing/documentation' },
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/PhyreApps/PhyrePanel' }
    ],

    footer: {
      message: 'Released under the GNU License.',
      copyright: 'Copyright © 2024-present Phyre Control Panel',
    },


  }
})
