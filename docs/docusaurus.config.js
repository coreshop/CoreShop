// @ts-check
// Note: type annotations allow type checking and IDEs autocompletion

const lightCodeTheme = require('prism-react-renderer').themes.github;
const darkCodeTheme = require('prism-react-renderer').themes.dracula;

/** @type {import('@docusaurus/types').Config} */
const config = {
  title: 'CoreShop 4.0.x - Pimcore eCommerce - Documentation',
  tagline: 'CoreShop - Pimcore eCommerce - Documentation',
  favicon: 'img/favicon.png',

  // Set the production url of your site here
  url: 'https://docs.coreshop.org',
  // Set the /<baseUrl>/ pathname under which your site is served
  // For GitHub pages deployment, it is often '/<projectName>/'
  baseUrl: '/4.0.0/',

  // GitHub pages deployment config.
  // If you aren't using GitHub pages, you don't need these.
  organizationName: 'coreshop', // Usually your GitHub org/user name.
  projectName: 'CoreShop', // Usually your repo name.

  onBrokenLinks: 'throw',
  onBrokenMarkdownLinks: 'warn',

  // Even if you don't use internalization, you can use this field to set useful
  // metadata like html lang. For example, if your site is Chinese, you may want
  // to replace "en" with "zh-Hans".
  i18n: {
    defaultLocale: 'en',
    locales: ['en'],
  },

  presets: [
    [
      'classic',
      /** @type {import('@docusaurus/preset-classic').Options} */
      ({
        docs: {
          routeBasePath: '/',
          sidebarPath: require.resolve('./sidebars.js'),
          // Please change this to your repo.
          // Remove this to remove the "edit this page" links.
          editUrl:
            'https://github.com/coreshop/CoreShop/tree/4.0/docs',
        },
        blog: false,
        theme: {
          customCss: require.resolve('./src/css/custom.css'),
        },
      }),
    ],
  ],

  themeConfig:
    /** @type {import('@docusaurus/preset-classic').ThemeConfig} */
    ({
      // Replace with your project's social card
      image: 'img/logo-white.svg',
      announcementBar: {
        id: 'support_us',
        content:
            'We launched our new Website, please check it out here <a target="_blank" rel="noopener noreferrer" href="https://www.coreshop.org">www.coreshop.org</a>',
        backgroundColor: 'rgb(205, 16, 23)',
        textColor: 'white',
        isCloseable: false,
      },
      navbar: {
        title: '',
        logo: {
          alt: 'CoreShop',
          src: 'img/logo-black.svg',
          srcDark: 'img/logo-white.svg',
        },
        items: [
          {
            type: 'docSidebar',
            sidebarId: 'tutorialSidebar',
            position: 'left',
            label: 'Documentation',
          },
          {
            href: 'https://pimcore.com/docs',
            label: 'Pimcore Documentation',
            position: 'left'
          },
          {
            href: 'https://github.com/coreshop/CoreShop',
            label: 'GitHub',
            position: 'right',
          },
        ],
      },
      footer: {
        style: 'dark',
        links: [
          {
            title: 'Docs (maintained)',
            items: [
              {
                label: 'Documentation',
                to: '/',
              },
              {
                label: '3.1',
                to: 'https://docs.coreshop.org/3.1.0/',
              },
              {
                label: 'latest',
                to: 'https://docs.coreshop.org/latest/',
              },
            ],
          },
          {
            title: 'Docs (unmainainted)',
            items: [
              {
                label: '2.0',
                to: 'https://docs.coreshop.org/2.0.0/',
              },
              {
                label: '2.1',
                to: 'https://docs.coreshop.org/2.1.0/',
              },
              {
                label: '2.2',
                to: 'https://docs.coreshop.org/2.2.0/',
              },
              {
                label: '3.0',
                to: 'https://docs.coreshop.org/3.0.0/',
              }
            ],
          },
          {
            title: 'Community',
            items: [
              {
                label: 'Pimcore GitHub',
                href: 'https://github.com/pimcore/pimcore',
              },
              {
                label: 'CoreShop GitHub',
                href: 'https://github.com/coreshop/CoreShop',
              },
            ],
          },
          {
            title: 'More',
            items: [
              {
                label: 'CoreShop Website',
                href: 'https://www.coreshop.org',
              },
            ],
          },
        ],
        copyright: `Copyright © ${new Date().getFullYear()} CoreShop GmbH. Built with Docusaurus.`,
      },
      prism: {
        theme: lightCodeTheme,
        darkTheme: darkCodeTheme,
        additionalLanguages: ['php'],
      },
    }),
  plugins: [
    [
      require.resolve("@easyops-cn/docusaurus-search-local"),
      {
        indexBlog: false,
        indexPages: true,
        hashed: true
      },
    ],
  ],
};

module.exports = config;
