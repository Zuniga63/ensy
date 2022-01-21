require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { Inertia } from '@inertiajs/inertia'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .mixin({ methods: { route } })
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });

// https://github.com/inertiajs/inertia/issues/247

let lastRequestMethod = null

Inertia.on('start', (event) => {
  lastRequestMethod = event.detail.visit.method
})

window.addEventListener('popstate', (event) => {
  if (lastRequestMethod !== 'get') {
    event.stopImmediatePropagation()
    window.location.href = event.state.url
  }
})
