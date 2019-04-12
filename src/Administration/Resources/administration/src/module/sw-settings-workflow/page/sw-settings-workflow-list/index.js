import { Component } from 'src/core/shopware';
import Criteria from 'src/core/data-new/criteria.data';
import './sw-settings-workflow-list.scss';
import template from './sw-settings-workflow-list.html.twig';

Component.register('sw-settings-workflow-list', {
    template,

    inject: [
        'repositoryFactory',
        'context'
    ],

    data() {
        return {
            repository: null,
            workflows: null
        };
    },

    computed: {
        columns() {
            return [{
                property: 'name',
                dataIndex: 'name',
                label: 'Name',
                routerLink: 'sw.settings.workflow.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'trigger',
                dataIndex: 'trigger',
                label: 'TriggerType',
                allowResize: true
            }, {
                property: 'priority',
                dataIndex: 'priority',
                label: 'prio',
                allowResize: true
            }];
        }
    },

    created() {
        this.repository = this.repositoryFactory.create('workflow');
        this.isLoading = true;

        this.repository
            .search(new Criteria(), this.context)
            .then((result) => {
                this.workflows = result;
                this.isLoading = false;
            });
    }
});
