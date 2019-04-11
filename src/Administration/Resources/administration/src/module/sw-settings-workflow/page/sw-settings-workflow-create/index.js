import { Component } from 'src/core/shopware';
import utils from 'src/core/service/util.service';

Component.extend('sw-settings-workflow-create', 'sw-settings-workflow-detail', {
    beforeRouteEnter(to, from, next) {
        if (to.name.includes('sw.settings.workflow.create') && !to.params.id) {
            to.params.id = utils.createId();
        }

        next();
    },

    methods: {
        createdComponent() {
            console.log('CREATE');
            if (this.$route.params.id) {
                this.workflowStore.create(this.$route.params.id);
            }
            this.$super.createdComponent();
        },

        onSave() {
            this.$super.onSave().then((success) => {
                if (!success) {
                    return;
                }

                this.$router.push({ name: 'sw.settings.workflow.detail', params: { id: this.workflow.id } });
            });
        }
    }
});
