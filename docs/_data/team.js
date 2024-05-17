/** @type {import("vitepress").DefaultTheme.TeamMember[]} */

const globeIcon = {
    svg: '<svg role="img" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><title>Website</title><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>',
};

/** @type {import("vitepress").DefaultTheme.TeamMember[]} */
export const teamMembers = [
    {
        avatar: 'https://avatars.githubusercontent.com/u/50577633?v=4',
        name: 'Bozhidar Slaveykov 🇧🇬',
        title: 'Developer',
        org: 'CloudVision',
        orgLink: 'https://phyrepanel.com',
        links: [
            { icon: 'github', link: 'https://github.com/bobicloudvision' },
            { icon: 'linkedin', link: 'https://www.linkedin.com/in/bozhidar.slaveykov' },
        ],
    },
    {
        avatar: 'https://avatars.githubusercontent.com/u/5698247?v=4',
        name: 'Peter Ivanov 🇧🇬',
        title: 'Developer',
        org: 'Microweber',
        orgLink: 'https://microweber.com.com',
        links: [
            { icon: 'github', link: 'https://github.com/peter-mw' },
        ]
    }
];
