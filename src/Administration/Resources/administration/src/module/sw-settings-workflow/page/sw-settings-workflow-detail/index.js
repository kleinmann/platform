import { Component, Mixin, State } from 'src/core/shopware';
import { warn } from 'src/core/service/utils/debug.utils';
import template from './sw-settings-workflow-detail.html.twig';
import './sw-settings-workflow-detail.scss';

Component.register('sw-settings-workflow-detail', {
    template,

    inject: [
        'repositoryFactory',
        'context'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            repository: null,
            workflow: null,
            triggerValue: null,
            actionValue: null
        };
    },

    computed: {
        workflowActionStore() {
            return State.getStore('workflow_action');
        },

        // TODO: change to service
        triggerValues() {
            return [
                {
                    key: 'register',
                    value: this.$tc('sw-settings-workflow.triggerStore.register')
                },
                {
                    key: 'afterOrder',
                    value: this.$tc('sw-settings-workflow.triggerStore.afterOrder')
                }
            ];
        },
        actionValues() {
            return [
                {
                    key: 'slack',
                    value: this.$tc('sw-settings-workflow.actionStore.slack')
                },
                {
                    key: 'email',
                    value: this.$tc('sw-settings-workflow.actionStore.email')
                }
            ];
        },
        actions() {
            return this.workflow.workflowActions;
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.repository = this.repositoryFactory.create('workflow');

            this.getWorkflow();
        },

        getWorkflow() {
            this.repository
                .get(this.$route.params.id, this.context)
                .then((entity) => {
                    this.workflow = entity;
                    this.triggerValue = this.workflow.trigger;
                });
        },

        onSave() {
            this.isLoading = true;

            const titleSaveSuccess = this.$tc('sw-settings-workflow.detail.titleSaveSuccess');
            const messageSaveSuccess = this.$tc(
                'sw-settings-workflow.detail.messageSaveSuccess',
                0,
                { name: this.workflow.name }
            );

            const titleSaveError = this.$tc('sw-settings-workflow.detail.titleSaveError');
            const messageSaveError = this.$tc(
                'sw-settings-workflow.detail.messageSaveError', 0, { name: this.workflow.name }
            );

            this.workflow.trigger = this.triggerValue;

            // sends the request immediately
            this.repository
                .save(this.workflow, this.context)
                .then(() => {
                    // the entity is stateless, the new data has to be fetched from the server, if required
                    this.getWorkflow();
                    this.isLoading = false;
                    this.createNotificationSuccess({
                        title: titleSaveSuccess,
                        message: messageSaveSuccess
                    });
                }).catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: titleSaveError,
                        message: messageSaveError
                    });
                    warn(this._name, exception.message, exception.response);
                });
        },

        onAddAction() {
            this.actions.push(
                this.workflowActionStore.create()
            );
        }
    }
});
