import { Component, Mixin, State } from 'src/core/shopware';
import { warn } from 'src/core/service/utils/debug.utils';
import template from './sw-settings-workflow-detail.html.twig';
import './sw-settings-workflow-detail.scss';

Component.register('sw-settings-workflow-detail', {
    template,

    mixins: [
        Mixin.getByName('notification'),
    ],

    data() {
        return {
            workflow: {},
            moduleTypes: null,
            nestedConditions: {},
        };
    },

    computed: {
        workflowStore() {
            return State.getStore('workflow');
        }
    },

    created() {
        console.log('YEAH');
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if (!this.$route.params.id) {
                return;
            }

            this.workflow = this.workflowStore.getById(this.$route.params.id);
        },

        onSave() {
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

            if (this.moduleTypes.length === 0) {
                this.workflow.moduleTypes = null;
            } else {
                this.workflow.moduleTypes = { types: this.moduleTypes };
            }

            if (this.conditionsClientValidation(this.workflow.conditions, false)) {
                this.createNotificationError({
                    title: titleSaveError,
                    message: messageSaveError
                });
                warn(this._name, 'client validation failure');
                this.$refs.conditionTree.$emit('on-save');

                return null;
            }

            this.removeOriginalConditionTypes(this.workflow.conditions);

            return this.workflow.save().then(() => {
                this.createNotificationSuccess({
                    title: titleSaveSuccess,
                    message: messageSaveSuccess
                });
                this.$refs.conditionTree.$emit('on-save');

                this.checkModuleType();

                return true;
            }).catch((exception) => {
                this.createNotificationError({
                    title: titleSaveError,
                    message: messageSaveError
                });
                warn(this._name, exception.message, exception.response);
                this.$refs.conditionTree.$emit('on-save');
            });
        },

        removeOriginalConditionTypes(conditions) {
            conditions.forEach((condition) => {
                if (condition.children) {
                    this.removeOriginalConditionTypes(condition.children);
                }

                const changes = Object.keys(condition.getChanges()).length;
                if (condition.isDeleted === false
                    && (changes || !this.areConditionsValueEqual(condition, condition.original))) {
                    condition.original.type = '';
                    condition.original.value = {};
                }
            });
        },

        conditionsClientValidation(conditions, error) {
            conditions.forEach((condition) => {
                if (condition.children) {
                    error = this.conditionsClientValidation(condition.children, error);
                }

                if (this.treeConfig.isAndContainer(condition) || this.treeConfig.isOrContainer(condition)) {
                    return;
                }

                if (condition.errors.map(obj => obj.id).includes('clientValidationError')) {
                    return;
                }

                if (this.treeConfig.isPlaceholder(condition)) {
                    condition.errors.push({
                        id: 'clientValidationError',
                        type: 'placeholder'
                    });

                    error = true;
                }

                if (!this.treeConfig.isDataSet(condition)) {
                    condition.errors.push({
                        id: 'clientValidationError',
                        type: 'data'
                    });

                    error = true;
                }
            });

            return error;
        },

        areConditionsValueEqual(conditionA, conditionB) {
            if (!(conditionA.value && conditionB.value)) {
                return true;
            }

            const propsA = Object.keys(conditionA.value);
            const propsB = Object.keys(conditionB.value);

            if (propsA.length !== propsB.length) {
                return false;
            }

            return !propsA.find(property => {
                return conditionA.value[property].toString() !== conditionB.value[property].toString();
            });
        }
    }
});
