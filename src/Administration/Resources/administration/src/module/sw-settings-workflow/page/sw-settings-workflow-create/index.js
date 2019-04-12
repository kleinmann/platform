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
        getWorkflow() {
            this.workflow = this.repository.create(this.context);
        }
    }
});
