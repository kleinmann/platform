import { Component, Mixin, State } from 'src/core/shopware';
import './sw-settings-workflow-list.scss';
import template from './sw-settings-workflow-list.html.twig';

Component.register('sw-settings-workflow-list', {
    template,

    mixins: [
        Mixin.getByName('sw-settings-list')
    ],

    data() {
        return {
            workflow: [],
            showDeleteModal: false,
            isLoading: false,
            entityName: 'workflow',
            sortBy: 'name'
        };
    },

    computed: {
        workflowStore() {
            return State.getStore('workflow');
        },

        filters() {
            return [];
        }
    },

    methods: {
        getList() {
            this.isLoading = true;
            if (!this.sortBy) {
                this.sortBy = 'createdAt';
            }

            const params = this.getListingParams();

            this.workflow = [];

            return this.workflowStore.getList(params).then((response) => {
                this.total = response.total;
                this.workflow = response.items;
                this.isLoading = false;

                return this.workflow;
            });
        },

        onDelete(id) {
            this.showDeleteModal = id;
        },

        onBulkDelete() {
            this.showDeleteModal = true;
        },

        onConfirmDelete(id) {
            this.showDeleteModal = false;

            this.isLoading = true;
            return this.workflowStore.getById(id).delete(true).then(() => {
                this.isLoading = false;
                return this.getList();
            });
        },

        onConfirmBulkDelete() {
            this.showDeleteModal = false;

            const selectedWorkflows = this.$refs.workflowGrid.getSelection();

            if (!selectedWorkflows) {
                return;
            }

            this.isLoading = true;

            Object.values(selectedWorkflows).forEach((workflow) => {
                workflow.delete();
            });

            this.workflowStore.sync(true).then(() => {
                this.isLoading = false;
                return this.getList();
            });
        },

        onCloseDeleteModal() {
            this.showDeleteModal = false;
        },

        onDuplicate(id) {
            this.workflowStore.apiService.clone(id).then((workflow) => {
                this.$router.push(
                    {
                        name: 'sw.settings.workflow.detail',
                        params: { id: workflow.id }
                    }
                );
            });
        },

        onInlineEditSave(params) {
            this.isLoading = true;
            const workflow = this.workflowStore.store[params.id];

            workflow.save().then(() => {
                this.isLoading = false;
            }).catch(() => {
                this.getList();
                this.isLoading = false;
            });
        }
    }
});
