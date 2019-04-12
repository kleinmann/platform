import { Component, Mixin, State } from 'src/core/shopware';
import { warn } from 'src/core/service/utils/debug.utils';
import template from './sw-settings-workflow-detail.html.twig';
import './sw-settings-workflow-detail.scss';
import Criteria from '../../../../core/data-new/criteria.data';

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
            actionRepository: null,
            ruleRepository: null,
            workflow: null,
            triggerValue: null,
            ruleValue: [],
            actionValue: []
        };
    },

    computed: {
        workflowActionStore() {
            return State.getStore('workflow_action');
        },

        workflowStore() {
            return State.getStore('workflow');
        },

        ruleStore() {
            return State.getStore('rule');
        },
        ruleAssociationsStore() {
            return this.workflowStore.getById(this.$route.params.id).getAssociation('workflowRules');
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
            this.actionRepository = this.repositoryFactory.create('workflow_action');

            this.getWorkflow();
        },

        getWorkflow() {
            const criteria = new Criteria();

            this.repository
                .get(this.$route.params.id, this.context, criteria)
                .then((entity) => {
                    this.workflow = entity;
                    this.triggerValue = this.workflow.trigger;

                    this.actionRepository = this.repositoryFactory.create(
                        this.workflow.workflowActions.entity,
                        this.workflow.workflowActions.source
                    );
                    this.ruleRepository = this.repositoryFactory.create(
                        this.workflow.workflowRules.entity,
                        this.workflow.workflowRules.source
                    );

                    this.actionRepository.search(new Criteria(), this.context).then((actions) => {
                        this.workflow.workflowActions = actions;

                        Object.keys(actions).forEach((action) => {
                            this.actionValue.push(actions[action].handlerIdentifier);
                        });
                    });
                    this.ruleRepository.search(new Criteria(), this.context).then((rules) => {
                        this.workflow.workflowRules = rules;
                    });

                    console.log(entity);
                });

            this.rules = this.ruleStore.getList().then((response) => {
                this.total = response.total;
                this.rules = response.items;
                this.isLoading = false;

                return this.rules;
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

            this.actionValue = this.actionValue.filter(word => word.length > 0);
            this.actionValue.forEach((action, index) => {
                const actionVars = this.actionRepository.create(this.context);

                actionVars.handlerIdentifier = action;
                actionVars.priority = index;
                actionVars.configuration = {};

                this.workflow.workflowActions[index] = actionVars;
            });

            // sends the request immediately
            return this.repository
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
            this.actionValue.push('');
        }
    }
});
