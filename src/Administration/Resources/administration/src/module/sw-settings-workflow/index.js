import { Module } from 'src/core/shopware';

import './page/sw-settings-workflow-list';
// import './page/sw-settings-workflow-detail';
// import './page/sw-settings-workflow-create';

import deDE from './snippet/de_DE.json';
import enGB from './snippet/en_GB.json';

Module.register('sw-settings-workflow', {
    type: 'core',
    name: 'Workflow',
    description: 'sw-settings-workflow.general.descriptionTextModule',
    color: '#9AA8B5',
    icon: 'default-action-settings',
    favicon: 'icon-module-settings.png',
    entity: 'workflow',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        index: {
            component: 'sw-settings-workflow-list',
            path: 'index',
            meta: {
                parentPath: 'sw.settings.index'
            }
        },
        detail: {
            component: 'sw-settings-workflow-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'sw.settings.workflow.index'
            }
        },
        create: {
            component: 'sw-settings-workflow-create',
            path: 'create',
            meta: {
                parentPath: 'sw.settings.workflow.index'
            }
        }
    },
    navigation: [{
        label: 'sw-settings-workflow.general.mainMenuItemGeneral',
        id: 'sw-settings-workflow',
        color: '#9AA8B5',
        icon: 'default-action-settings',
        path: 'sw.settings.workflow.index',
        parent: 'sw-settings'
    }]
});
