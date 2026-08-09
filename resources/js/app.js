import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.automationManager = function(storeUrl) {
    return {
        showPanel: false,
        isEdit: false,
        formAction: storeUrl,
        
        openCreate() {
            this.isEdit = false;
            this.formAction = storeUrl;
            document.getElementById('automation-form').reset();
            
            window.dispatchEvent(new CustomEvent('load-automation', {
                detail: {
                    triggerType: 'github_push',
                    conditions: [],
                    actions: []
                }
            }));
            
            this.showPanel = true;
        },
        
        openEdit(automation) {
            this.isEdit = true;
            this.formAction = '/automations/' + automation.id;
            
            document.getElementById('name').value = automation.name;
            document.getElementById('description').value = automation.description || '';
            document.getElementById('is_active').checked = automation.is_active;
            
            if(automation.trigger && automation.trigger.cron_expression) {
                document.getElementById('cron_expression').value = automation.trigger.cron_expression;
            } else {
                document.getElementById('cron_expression').value = '* * * * *';
            }
            
            // Enviar datos al formulario de Alpine
            window.dispatchEvent(new CustomEvent('load-automation', {
                detail: {
                    triggerType: automation.trigger ? automation.trigger.type : 'github_push',
                    conditions: automation.conditions || [],
                    actions: automation.actions || []
                }
            }));
            
            this.showPanel = true;
        }
    }
}

window.automationForm = function(config) {
    return {
        triggerType: config.triggerType || 'github_push',
        conditions: [],
        actions: [{ id: Date.now(), type: '', service_connection_id: '', config: {} }],
        
        loadData(data) {
            this.triggerType = data.triggerType;
            this.conditions = (data.conditions || []).map(c => ({
                id: c.id || Date.now() + Math.random(),
                field: c.field,
                operator: c.operator,
                value: c.value
            }));
            
            if (data.actions && data.actions.length > 0) {
                this.actions = data.actions.map(a => {
                    let configObj = typeof a.config === 'string' ? JSON.parse(a.config || '{}') : (a.config || {});
                    return {
                        id: a.id || Date.now() + Math.random(),
                        type: a.type,
                        service_connection_id: a.service_connection_id || '',
                        config: configObj
                    };
                });
            } else {
                this.actions = [{ id: Date.now(), type: '', service_connection_id: '', config: {} }];
            }
        },
        
        addCondition() {
            this.conditions.push({
                id: Date.now(),
                field: '',
                operator: 'equals',
                value: ''
            });
        },
        
        removeCondition(id) {
            this.conditions = this.conditions.filter(c => c.id !== id);
        },
        
        addAction() {
            this.actions.push({
                id: Date.now(),
                type: '',
                service_connection_id: '',
                config: {}
            });
        },
        
        removeAction(id) {
            this.actions = this.actions.filter(a => a.id !== id);
        }
    }
}

Alpine.start();
